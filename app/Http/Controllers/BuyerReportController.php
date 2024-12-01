<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BuyerReportController extends Controller
{
    /**
     * Fetch buyer report data for the frontend.
     */
    public function fetchBuyerData(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $buyerId = $request->user()->buyer->id; // Ensure only the authenticated buyer's data is fetched
            if (!$buyerId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied. Only buyers can access this report.',
                ], 403);
            }

            $reportData = $this->fetchReportData($validated, $buyerId);

            return response()->json([
                'status' => 'success',
                'data' => $reportData,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch buyer report data', [
                'error' => $e->getMessage(),
                'start_date' => $request->get('start_date', 'N/A'),
                'end_date' => $request->get('end_date', 'N/A'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch buyer report data. Please try again later.',
            ], 500);
        }
    }

    /**
     * Fetch buyer report data.
     */
    private function fetchReportData(array $filters, int $buyerId)
    {
        $startDate = $filters['start_date'];
        $endDate = $filters['end_date'];

        // Fetch purchase history, including all order statuses
        $purchases = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.buyer_id', $buyerId)
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->selectRaw('
                products.product_name,
                COUNT(order_items.id) AS purchase_count,
                SUM(order_items.total) AS total_spent,
                orders.order_status
            ')
            ->groupBy('products.product_name', 'orders.order_status')
            ->get();

        // Spending summary with categorized order statuses
        $spendingSummary = DB::table('orders')
            ->where('buyer_id', $buyerId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->selectRaw('
                SUM(total_amount) AS total_spent,
                SUM(CASE WHEN order_status = \'Completed\' THEN total_amount ELSE 0 END) AS total_completed,
                SUM(CASE WHEN order_status = \'Pending\' THEN total_amount ELSE 0 END) AS total_pending,
                SUM(CASE WHEN order_status = \'Processing\' THEN total_amount ELSE 0 END) AS total_processing,
                SUM(CASE WHEN order_status = \'Cancelled\' THEN total_amount ELSE 0 END) AS total_cancelled,
                COUNT(id) AS total_orders
            ')
            ->first();

        // Provide default values if spendingSummary is empty
        if (!$spendingSummary) {
            $spendingSummary = (object) [
                'total_spent' => 0,
                'total_completed' => 0,
                'total_pending' => 0,
                'total_processing' => 0,
                'total_cancelled' => 0,
                'total_orders' => 0,
            ];
        }

        return [
            'spending_summary' => $spendingSummary,
            'purchases' => $purchases,
        ];
    }



    /**
     * Generate PDF buyer report.
     */
    public function generatePdfReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $buyerId = $request->user()->buyer->id;
            if (!$buyerId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied. Only buyers can generate this report.',
                ], 403);
            }

            $reportData = $this->fetchReportData($validated, $buyerId);

            // Include `start_date` and `end_date` in the data sent to the Blade view
            $pdf = Pdf::loadView('reports.buyer', [
                'reportData' => $reportData,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            return $pdf->download('buyer_report.pdf');
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF buyer report', [
                'error' => $e->getMessage(),
                'start_date' => $request->get('start_date', 'N/A'),
                'end_date' => $request->get('end_date', 'N/A'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate the PDF buyer report. Please try again later.',
            ], 500);
        }
    }


    /**
     * Generate CSV buyer report.
     */
    public function generateCsvReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $buyerId = $request->user()->buyer->id;
            if (!$buyerId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied. Only buyers can generate this report.',
                ], 403);
            }

            $reportData = $this->fetchReportData($validated, $buyerId);

            $response = new StreamedResponse(function () use ($reportData) {
                $handle = fopen('php://output', 'w');

                // Add headers
                fputcsv($handle, ['Product Name', 'Purchase Count', 'Total Spent', 'Order Status']);
                foreach ($reportData['purchases'] as $purchase) {
                    fputcsv($handle, [
                        $purchase->product_name,
                        $purchase->purchase_count,
                        $purchase->total_spent,
                        $purchase->order_status,
                    ]);
                }

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="buyer_report.csv"');

            return $response;
        } catch (\Exception $e) {
            \Log::error('Failed to generate CSV buyer report', [
                'error' => $e->getMessage(),
                'start_date' => $request->get('start_date', 'N/A'),
                'end_date' => $request->get('end_date', 'N/A'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate the CSV buyer report. Please try again later.',
            ], 500);
        }
    }
}

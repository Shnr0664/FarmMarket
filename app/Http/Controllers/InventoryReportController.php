<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryReportController extends Controller
{
    /**
     * Fetch inventory data for the frontend.
     */
    public function fetchInventoryData(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $farmer = $request->user()->farmer;
            if (!$farmer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied. Only farmers can access inventory reports.',
                ], 403);
            }
            $reportData = $this->fetchReportData($validated, $farmer->id);

            $formattedData = $reportData->map(function ($item) {
                return [
                    'product_name' => $item->product_name,
                    'stock_level' => $item->stock_level,
                    'status' => [
                        'total_sold' => (int) $item->total_sold,
                        'pending' => (int) $item->pending_quantity,
                        'processing' => (int) $item->processing_quantity,
                        'cancelled' => (int) $item->cancelled_quantity,
                    ],
                    'restocking_alert' => $item->restocking_alert,
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $formattedData,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch inventory report data', [
                'error' => $e->getMessage(),
                'start_date' => $request->get('start_date', 'N/A'),
                'end_date' => $request->get('end_date', 'N/A'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch inventory report data. Please try again later.',
            ], 500);
        }
    }

    /**
     * Generate a PDF inventory report.
     */
    public function generatePdfReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $farmerId = $request->user()->farmer->id; // Fetch the authenticated farmer's ID

            $reportData = $this->fetchReportData($validated, $farmerId);

            $pdf = Pdf::loadView('reports.inventory', ['reportData' => $reportData]);

            return $pdf->download('inventory_report.pdf');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF inventory report', [
                'error' => $e->getMessage(),
                'start_date' => $request->get('start_date', 'N/A'),
                'end_date' => $request->get('end_date', 'N/A'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate the PDF inventory report. Please try again later.',
            ], 500);
        }
    }

    /**
     * Generate a CSV inventory report.
     */
    public function generateCsvReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $farmerId = $request->user()->farmer->id; // Fetch the authenticated farmer's ID

            $reportData = $this->fetchReportData($validated, $farmerId);

            $response = new StreamedResponse(function () use ($reportData) {
                $handle = fopen('php://output', 'w');

                // Add headers
                fputcsv($handle, ['Product Name', 'Stock Level', 'Total Sold', 'Pending Quantity', 'Processing Quantity', 'Cancelled Quantity', 'Restocking Alerts']);
                foreach ($reportData as $row) {
                    fputcsv($handle, [
                        $row->product_name,
                        $row->stock_level,
                        $row->total_sold,
                        $row->pending_quantity,
                        $row->processing_quantity,
                        $row->cancelled_quantity,
                        $row->restocking_alert,
                    ]);
                }

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="inventory_report.csv"');

            return $response;
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 400);
        } catch (\Exception $e) {
            \Log::error('Failed to generate CSV inventory report', [
                'error' => $e->getMessage(),
                'start_date' => $request->get('start_date', 'N/A'),
                'end_date' => $request->get('end_date', 'N/A'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate the CSV inventory report. Please try again later.',
            ], 500);
        }
    }

    /**
     * Fetch inventory report data.
     */
    private function fetchReportData(array $filters, int $farmerId)
    {
        $startDate = $filters['start_date'];
        $endDate = $filters['end_date'];

        return DB::table('products')
            ->leftJoin('farms', 'products.farm_id', '=', 'farms.id')
            ->leftJoin('farmers', 'farms.farmer_id', '=', 'farmers.id')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw('
        products.product_name,
        products.product_quantity AS stock_level,
        COALESCE(SUM(CASE WHEN orders.order_status = \'Completed\' THEN order_items.quantity ELSE 0 END), 0) AS total_sold,
        COALESCE(SUM(CASE WHEN orders.order_status = \'Pending\' THEN order_items.quantity ELSE 0 END), 0) AS pending_quantity,
        COALESCE(SUM(CASE WHEN orders.order_status = \'Processing\' THEN order_items.quantity ELSE 0 END), 0) AS processing_quantity,
        COALESCE(SUM(CASE WHEN orders.order_status = \'Cancelled\' THEN order_items.quantity ELSE 0 END), 0) AS cancelled_quantity,
        CASE
            WHEN products.product_quantity <= 10 THEN \'Low Stock\'
            ELSE \'Sufficient Stock\'
        END AS restocking_alert
    ')
            ->where('farmers.id', $farmerId)
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->groupBy('products.id', 'products.product_name', 'products.product_quantity')
            ->get();

    }
}

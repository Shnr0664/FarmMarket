<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class SalesReportController extends Controller
{
    /**
     * Fetch sales data for the frontend.
     */
    public function fetchSalesData(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'report_type' => 'required|in:daily,weekly,monthly',
            ]);

            $farmer = $request->user()->farmer;
            if (!$farmer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied. Only farmers can access sales reports.',
                ], 403);
            }

            $reportData = $this->fetchReportData($validated, $farmer->id);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'sales_summary' => $reportData['sales_summary']->map(function ($item) {
                        return [
                            'period' => $item->period,
                            'total_revenue' => number_format($item->total_revenue, 2),
                            'total_orders' => $item->total_orders,
                        ];
                    }),
                    'product_popularity' => $reportData['product_popularity']->map(function ($item) {
                        return [
                            'product_name' => $item->product_name,
                            'total_sold' => $item->total_sold,
                            'total_revenue' => number_format($item->total_revenue, 2),
                        ];
                    }),
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input values.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to fetch sales report data', [
                'error' => $e->getMessage(),
                'start_date' => $request->get('start_date', 'N/A'),
                'end_date' => $request->get('end_date', 'N/A'),
                'report_type' => $request->get('report_type', 'N/A'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch sales report data. Please check your inputs and try again.',
            ], 500);
        }
    }

    /**
     * Generate a sales report in PDF format.
     */
    public function generatePdfReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'report_type' => 'required|in:daily,weekly,monthly',
            ]);

            $farmer = $request->user()->farmer;
            if (!$farmer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied. Only farmers can generate sales reports.',
                ], 403);
            }

            $reportData = $this->fetchReportData($validated, $farmer->id);

            // Pass the data directly to the Blade view
            $pdf = Pdf::loadView('reports.sales', [
                'reportData' => $reportData,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'report_type' => $validated['report_type'],
            ]);

            return $pdf->download('sales_report.pdf');
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input values.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to generate PDF report', [
                'error' => $e->getMessage(),
                'start_date' => $request->get('start_date', 'N/A'),
                'end_date' => $request->get('end_date', 'N/A'),
                'report_type' => $request->get('report_type', 'N/A'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate the PDF report. Please try again later.',
            ], 500);
        }
    }

    /**
     * Generate a sales report in CSV format.
     */
    public function generateCsvReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'report_type' => 'required|in:daily,weekly,monthly',
            ]);

            $farmer = $request->user()->farmer;
            if (!$farmer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access denied. Only farmers can generate sales reports.',
                ], 403);
            }

            $reportData = $this->fetchReportData($validated, $farmer->id);

            $response = new StreamedResponse(function () use ($reportData) {
                $handle = fopen('php://output', 'w');

                // Add headers for Sales Summary
                fputcsv($handle, ['Period', 'Total Revenue', 'Total Orders']);
                foreach ($reportData['sales_summary'] as $row) {
                    fputcsv($handle, [$row->period, $row->total_revenue, $row->total_orders]);
                }

                // Add headers for Product Popularity
                fputcsv($handle, []);
                fputcsv($handle, ['Product Name', 'Total Sold', 'Total Revenue']);
                foreach ($reportData['product_popularity'] as $product) {
                    fputcsv($handle, [$product->product_name, $product->total_sold, $product->total_revenue]);
                }

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="sales_report.csv"');

            return $response;
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input values.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to generate CSV report', [
                'error' => $e->getMessage(),
                'start_date' => $request->get('start_date', 'N/A'),
                'end_date' => $request->get('end_date', 'N/A'),
                'report_type' => $request->get('report_type', 'N/A'),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate the CSV report. Please try again later.',
            ], 500);
        }
    }

    /**
     * Fetch sales report data.
     */
    private function fetchReportData(array $filters, int $farmerId)
    {
        $groupByFormat = match ($filters['report_type']) {
            'daily' => 'YYYY-MM-DD',
            'weekly' => 'I', // ISO week number
            'monthly' => 'YYYY-MM',
        };

        $startDate = $filters['start_date'];
        $endDate = $filters['end_date'];

        // Sales Summary Query
        $salesSummary = DB::table('orders')
            ->join('buyers', 'orders.buyer_id', '=', 'buyers.id')
            ->join('farms', 'buyers.id', '=', 'farms.id')
            ->join('farmers', 'farms.farmer_id', '=', 'farmers.id')
            ->selectRaw("
                TO_CHAR(order_date, '$groupByFormat') AS period,
                SUM(total_amount) AS total_revenue,
                COUNT(*) AS total_orders
            ")
            ->where('farmers.id', $farmerId)
            ->whereBetween('order_date', [$startDate, $endDate])
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get();

        // Product Popularity Query (Exclude Canceled Orders)
        $productPopularity = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id') // Join with orders table
            ->join('farms', 'products.farm_id', '=', 'farms.id')
            ->join('farmers', 'farms.farmer_id', '=', 'farmers.id')
            ->selectRaw("
        products.product_name,
        SUM(order_items.quantity) AS total_sold,
        SUM(order_items.total) AS total_revenue
    ")
            ->where('farmers.id', $farmerId)
            ->where('orders.order_status', '=', 'Completed') // Only consider completed orders
            ->whereBetween('orders.order_date', [$startDate, $endDate]) // Filter by date range
            ->groupBy('products.product_name')
            ->orderBy('total_sold', 'desc')
            ->get();

        return [
            'sales_summary' => $salesSummary,
            'product_popularity' => $productPopularity,
        ];
    }


}

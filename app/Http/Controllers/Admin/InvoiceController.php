<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $query = Booking::with(['customer', 'employee', 'service', 'consultation'])
            ->where('payment_status', 'paid');

        // Filters
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->orderBy('paid_at', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total_invoices' => Booking::where('payment_status', 'paid')->count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_price'),
            'today_invoices' => Booking::where('payment_status', 'paid')
                ->whereDate('paid_at', today())
                ->count(),
            'today_revenue' => Booking::where('payment_status', 'paid')
                ->whereDate('paid_at', today())
                ->sum('total_price'),
            'month_invoices' => Booking::where('payment_status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->count(),
            'month_revenue' => Booking::where('payment_status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('total_price'),
        ];

        // Get customers and employees for filters
        $customers = \App\Models\User::where('role', 'customer')
            ->orderBy('name')
            ->get(['id', 'name', 'phone']);
        
        $employees = \App\Models\Employee::with('user')
            ->orderBy('id')
            ->get();

        return view('admin.invoices.index', compact('invoices', 'stats', 'customers', 'employees'));
    }

    /**
     * Display the specified invoice
     */
    public function show(Booking $booking)
    {
        // Only show paid bookings as invoices
        if ($booking->payment_status !== 'paid') {
            return redirect()->route('admin.invoices.index')
                ->with('error', __('messages.invoice_not_paid'));
        }

        $booking->load(['customer', 'employee.user', 'service.subCategory.category', 'consultation', 'timeSlots']);

        return view('admin.invoices.show', compact('booking'));
    }

    /**
     * Download invoice as PDF
     */
    public function download(Booking $booking)
    {
        if ($booking->payment_status !== 'paid') {
            return redirect()->route('admin.invoices.index')
                ->with('error', __('messages.invoice_not_paid'));
        }

        $booking->load(['customer', 'employee.user', 'service.subCategory.category', 'consultation', 'timeSlots']);

        // Generate HTML for PDF
        $html = view('admin.invoices.pdf', compact('booking'))->render();
        
        // Generate filename
        $filename = 'invoice-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT) . '.pdf';
        
        // Use mPDF with Arabic support
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'margin_header' => 0,
            'margin_footer' => 0,
            'default_font' => 'dejavusans',
            'default_font_size' => 9,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'biDirectional' => true,
            'useSubstitutions' => true,
            'useArabic' => true,
            'allowCJK' => true,
        ]);
        
        // Set RTL direction for Arabic
        $mpdf->SetDirectionality('rtl');
        
        // Write HTML content
        $mpdf->WriteHTML($html);
        
        // Download PDF
        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename*=UTF-8''" . rawurlencode($filename),
        ]);
    }

    /**
     * Export invoices to Excel/CSV
     */
    public function export(Request $request)
    {
        $query = Booking::with(['customer', 'employee', 'service', 'consultation'])
            ->where('payment_status', 'paid');

        // Apply same filters as index
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('paid_at', 'desc')->get();

        $filename = 'invoices_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers - Use translations based on current locale
            fputcsv($file, [
                __('messages.invoice_number'),
                __('messages.booking_id'),
                __('messages.customer'),
                __('messages.phone'),
                __('messages.email'),
                __('messages.service'),
                __('messages.employee'),
                __('messages.date'),
                __('messages.time'),
                __('messages.amount'),
                __('messages.booking_status'),
                __('messages.paid_at'),
                __('messages.transaction_id')
            ]);

            // Data
            foreach ($invoices as $invoice) {
                $serviceName = $invoice->booking_type === 'consultation' 
                    ? ($invoice->consultation ? $invoice->consultation->trans('name') : 'N/A')
                    : ($invoice->service ? $invoice->service->trans('name') : 'N/A');
                
                $employeeName = $invoice->employee && $invoice->employee->user 
                    ? $invoice->employee->user->name 
                    : 'N/A';

                // Translate status
                $status = match($invoice->status) {
                    'pending' => __('messages.pending'),
                    'confirmed' => __('messages.confirmed'),
                    'completed' => __('messages.completed'),
                    'cancelled' => __('messages.cancelled'),
                    default => $invoice->status,
                };

                fputcsv($file, [
                    'INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT),
                    $invoice->id,
                    $invoice->customer->name ?? 'N/A',
                    $invoice->customer->phone ?? 'N/A',
                    $invoice->customer->email ?? 'N/A',
                    $serviceName,
                    $employeeName,
                    $invoice->booking_date ? $invoice->booking_date->format('Y-m-d') : 'N/A',
                    ($invoice->start_time ?? '') . ' - ' . ($invoice->end_time ?? ''),
                    number_format($invoice->total_price, 2),
                    $status,
                    $invoice->paid_at ? $invoice->paid_at->format('Y-m-d H:i:s') : 'N/A',
                    $invoice->payment_id ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get invoice statistics
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth();
        $dateTo = $request->date_to ?? now()->endOfMonth();

        $query = Booking::where('payment_status', 'paid')
            ->whereBetween('paid_at', [$dateFrom, $dateTo]);

        $stats = [
            'total_invoices' => (clone $query)->count(),
            'total_revenue' => (clone $query)->sum('total_price'),
            'average_invoice' => (clone $query)->avg('total_price'),
            'by_status' => (clone $query)->selectRaw('status, COUNT(*) as count, SUM(total_price) as revenue')
                ->groupBy('status')
                ->get(),
            'by_day' => (clone $query)->selectRaw('DATE(paid_at) as date, COUNT(*) as count, SUM(total_price) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}


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

        // Create PDF using dompdf with Arabic font support
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'dejavu sans');
        $options->set('isFontSubsettingEnabled', true);
        
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Load HTML with UTF-8 encoding
        $dompdf->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        
        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Generate filename
        $filename = 'invoice-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT) . '.pdf';
        
        // Download PDF
        return $dompdf->stream($filename, ['Attachment' => true]);
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
            
            // Headers
            fputcsv($file, [
                'رقم الفاتورة',
                'رقم الحجز',
                'العميل',
                'الهاتف',
                'البريد الإلكتروني',
                'الخدمة',
                'الموظف',
                'التاريخ',
                'الوقت',
                'المبلغ',
                'حالة الحجز',
                'تاريخ الدفع',
                'رقم المعاملة'
            ]);

            // Data
            foreach ($invoices as $invoice) {
                $serviceName = $invoice->booking_type === 'consultation' 
                    ? ($invoice->consultation->name ?? 'N/A')
                    : ($invoice->service->name ?? 'N/A');
                
                $employeeName = $invoice->employee && $invoice->employee->user 
                    ? $invoice->employee->user->name 
                    : 'N/A';

                fputcsv($file, [
                    'INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT),
                    $invoice->id,
                    $invoice->customer->name ?? 'N/A',
                    $invoice->customer->phone ?? 'N/A',
                    $invoice->customer->email ?? 'N/A',
                    $serviceName,
                    $employeeName,
                    $invoice->booking_date->format('Y-m-d'),
                    $invoice->start_time . ' - ' . $invoice->end_time,
                    number_format($invoice->total_price, 2),
                    $invoice->status,
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


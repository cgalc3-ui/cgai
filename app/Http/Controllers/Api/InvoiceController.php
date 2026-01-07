<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class InvoiceController extends Controller
{
    use ApiResponseTrait;
    /**
     * Get customer's invoices
     */
    public function index(Request $request)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        $query = Booking::with(['employee.user', 'service.subCategory.category', 'consultation'])
            ->where('customer_id', $customer->id)
            ->where('payment_status', 'paid');

        // Filters
        if ($request->filled('date_from')) {
            $query->where(function($q) use ($request) {
                $q->whereNotNull('paid_at')
                  ->whereDate('paid_at', '>=', $request->date_from)
                  ->orWhere(function($q2) use ($request) {
                      $q2->whereNull('paid_at')
                         ->whereDate('created_at', '>=', $request->date_from);
                  });
            });
        }

        if ($request->filled('date_to')) {
            $query->where(function($q) use ($request) {
                $q->whereNotNull('paid_at')
                  ->whereDate('paid_at', '<=', $request->date_to)
                  ->orWhere(function($q2) use ($request) {
                      $q2->whereNull('paid_at')
                         ->whereDate('created_at', '<=', $request->date_to);
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Order by paid_at if exists, otherwise by created_at
        $invoices = $query->orderBy('paid_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        // Format invoices
        $formattedInvoices = $invoices->getCollection()->map(function ($booking) {
            return [
                'id' => $booking->id,
                'invoice_number' => 'INV-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
                'booking_id' => $booking->id,
                'service' => $booking->booking_type === 'consultation' 
                    ? [
                        'id' => $booking->consultation_id,
                        'name' => $booking->consultation->name ?? null,
                        'name_en' => $booking->consultation->name_en ?? null,
                        'type' => 'consultation',
                        'description' => $booking->consultation->description ?? null,
                        'description_en' => $booking->consultation->description_en ?? null,
                    ]
                    : [
                        'id' => $booking->service_id,
                        'name' => $booking->service->name ?? null,
                        'name_en' => $booking->service->name_en ?? null,
                        'type' => 'service',
                        'description' => $booking->service->description ?? null,
                        'description_en' => $booking->service->description_en ?? null,
                    ],
                'employee' => $booking->employee && $booking->employee->user ? [
                    'id' => $booking->employee->user->id,
                    'name' => $booking->employee->user->name,
                ] : null,
                'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'total_price' => number_format($booking->total_price, 2),
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'paid_at' => $booking->paid_at ? $booking->paid_at->format('Y-m-d H:i:s') : null,
                'payment_id' => $booking->payment_id,
                'payment_data' => $booking->payment_data,
                'created_at' => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : null,
            ];
        });

        // Filter locale columns
        $filteredInvoices = $formattedInvoices->map(function ($invoice) {
            return $this->filterLocaleColumns($invoice);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'invoices' => $filteredInvoices,
                'pagination' => [
                    'current_page' => $invoices->currentPage(),
                    'last_page' => $invoices->lastPage(),
                    'per_page' => $invoices->perPage(),
                    'total' => $invoices->total(),
                ]
            ]
        ]);
    }

    /**
     * Get invoice details
     */
    public function show(Request $request, Booking $booking)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        // Check if booking belongs to customer
        if ($booking->customer_id !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        // Only show paid bookings as invoices
        if ($booking->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => __('messages.invoice_not_paid'),
            ], 422);
        }

        $booking->load(['customer', 'employee.user', 'service.subCategory.category', 'consultation', 'timeSlots']);

        $invoice = [
            'id' => $booking->id,
            'invoice_number' => 'INV-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT),
            'booking_id' => $booking->id,
            'customer' => [
                'id' => $booking->customer->id,
                'name' => $booking->customer->name,
                'phone' => $booking->customer->phone,
                'email' => $booking->customer->email,
            ],
            'service' => $booking->booking_type === 'consultation' 
                ? [
                    'id' => $booking->consultation_id,
                    'name' => $booking->consultation->name ?? null,
                    'name_en' => $booking->consultation->name_en ?? null,
                    'type' => 'consultation',
                    'description' => $booking->consultation->description ?? null,
                    'description_en' => $booking->consultation->description_en ?? null,
                ]
                : [
                    'id' => $booking->service_id,
                    'name' => $booking->service->name ?? null,
                    'name_en' => $booking->service->name_en ?? null,
                    'type' => 'service',
                    'description' => $booking->service->description ?? null,
                    'description_en' => $booking->service->description_en ?? null,
                ],
            'employee' => $booking->employee && $booking->employee->user ? [
                'id' => $booking->employee->user->id,
                'name' => $booking->employee->user->name,
                'phone' => $booking->employee->user->phone,
            ] : null,
            'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'duration' => $booking->formatted_duration,
            'total_price' => number_format($booking->total_price, 2),
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'paid_at' => $booking->paid_at ? $booking->paid_at->format('Y-m-d H:i:s') : null,
            'payment_id' => $booking->payment_id,
            'payment_data' => $booking->payment_data,
            'notes' => $booking->notes,
            'created_at' => $booking->created_at ? $booking->created_at->format('Y-m-d H:i:s') : null,
        ];

        return response()->json([
            'success' => true,
            'data' => $this->filterLocaleColumns($invoice)
        ]);
    }

    /**
     * Download invoice receipt as PDF
     */
    public function download(Request $request, Booking $booking)
    {
        $customer = $request->user();

        if (!$customer->isCustomer()) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        // Check if booking belongs to customer
        if ($booking->customer_id !== $customer->id) {
            return response()->json([
                'success' => false,
                'message' => __('messages.unauthorized_access'),
            ], 403);
        }

        // Only show paid bookings as invoices
        if ($booking->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => __('messages.invoice_not_paid'),
            ], 422);
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
        
        // Output PDF
        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename*=UTF-8''" . rawurlencode($filename),
        ]);
    }
}


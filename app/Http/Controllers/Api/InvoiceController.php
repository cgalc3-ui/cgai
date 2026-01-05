<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
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
                        'type' => 'consultation'
                    ]
                    : [
                        'id' => $booking->service_id,
                        'name' => $booking->service->name ?? null,
                        'name_en' => $booking->service->name_en ?? null,
                        'type' => 'service'
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

        return response()->json([
            'success' => true,
            'data' => [
                'invoices' => $formattedInvoices,
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
                ]
                : [
                    'id' => $booking->service_id,
                    'name' => $booking->service->name ?? null,
                    'name_en' => $booking->service->name_en ?? null,
                    'type' => 'service',
                    'description' => $booking->service->description ?? null,
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
            'data' => $invoice
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
        
        // Return PDF as stream
        return $dompdf->stream($filename, ['Attachment' => true]);
    }
}


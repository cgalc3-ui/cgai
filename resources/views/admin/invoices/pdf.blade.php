<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="UTF-8">
    <title>{{ __('messages.invoice') }} #INV-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @charset "UTF-8";
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        }

        body {
            font-family: 'dejavusans', 'DejaVu Sans', Arial, sans-serif;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            background: #f9fafb;
            color: #1a202c;
            padding: 10px;
            line-height: 1.5;
            font-size: 9pt;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            unicode-bidi: embed;
        }
        
        @page {
            margin: 5mm;
        }
        
        /* Ensure all text elements respect RTL */
        div, p, span, h1, h2, h3, h4, h5, h6, td, th, strong, em {
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            unicode-bidi: embed;
            font-family: 'dejavusans', 'DejaVu Sans', Arial, sans-serif;
        }

        .invoice-container {
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            padding: 15px;
            border-radius: 4px;
            page-break-inside: avoid;
            min-height: 100%;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 15px 18px;
            padding-bottom: 15px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            page-break-inside: avoid;
        }

        .invoice-title {
            font-size: 16pt;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 3px;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .invoice-number {
            font-size: 8.5pt;
            color: #6b7280;
            font-weight: 600;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .invoice-date {
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            color: #4b5563;
            background: #ffffff;
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            min-width: 140px;
        }
        
        .invoice-date > div:first-child {
            color: #6b7280;
            display: block;
            margin-bottom: 6px;
            font-size: 8.5pt;
            font-weight: 600;
        }
        
        .invoice-date > div:last-child {
            font-size: 10pt;
            font-weight: 700;
            color: #1a202c;
            display: block;
        }

        .invoice-info {
            width: 100%;
            margin-bottom: 15px;
            page-break-inside: avoid;
            border-collapse: separate;
            border-spacing: 12px 0;
        }

        .info-section {
            background: #ffffff;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            vertical-align: top;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            min-height: 220px;
        }

        .info-section h3 {
            font-size: 9.5pt;
            font-weight: 700;
            color: #374151;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .info-item {
            margin-bottom: 12px;
            font-size: 8.5pt;
            padding: 10px 8px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
            position: relative;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }
        
        .info-item:not(:last-child)::after {
            content: '';
            position: absolute;
            bottom: -1px;
            {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}: 0;
            width: 30px;
            height: 2px;
            background: #d1d5db;
            border-radius: 2px;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 10px;
        }

        .info-label {
            color: #9ca3af;
            font-weight: 500;
            margin-bottom: 6px;
            font-size: 8pt;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .info-value {
            color: #4b5563;
            font-weight: 600;
            font-size: 9pt;
            margin-top: 4px;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .service-section {
            margin-bottom: 15px;
            padding: 15px;
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            border-left: 4px solid #667eea;
            page-break-inside: avoid;
        }

        .service-section h3 {
            font-size: 8.5pt;
            font-weight: 700;
            margin-bottom: 6px;
            color: #667eea;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .service-name {
            font-size: 9.5pt;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 5px;
            padding: 6px;
            background: #ffffff;
            border-radius: 4px;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .service-meta {
            color: #6b7280;
            font-size: 8pt;
            padding: 4px 6px;
            background: #f9fafb;
            border-radius: 4px;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .payment-section {
            background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%);
            border: 2px solid #22c55e;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }

        .payment-section h3 {
            font-size: 8.5pt;
            font-weight: 700;
            color: #166534;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 1px solid #86efac;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .payment-details {
            display: table;
            width: 100%;
        }
        
        .payment-details .info-item {
            display: table-cell;
            width: 50%;
        }

        .total-amount {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            color: #fff;
            margin-top: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            page-break-inside: avoid;
        }

        .total-label {
            font-size: 8.5pt;
            color: #cbd5e1;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .total-value {
            font-size: 18pt;
            font-weight: 800;
            color: #ffffff;
        }

        .footer {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        /* Ensure numbers and dates render correctly */
        .invoice-date, .info-value:contains("202"), .info-value:contains(":") {
            direction: ltr;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
            unicode-bidi: embed;
        }

        @media print {
            body {
                padding: 0;
            }

            .invoice-container {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div>
                <h1 class="invoice-title">{{ __('messages.invoice') }}</h1>
                <div class="invoice-number">#INV-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="invoice-date">
                <div style="margin-bottom: 6px; font-size: 8.5pt; font-weight: 600; color: #6b7280; display: block !important;">{{ __('messages.issued_date') }}</div>
                <div style="font-size: 10pt; font-weight: 700; color: #1a202c; display: block !important;">
                    @php
                        $issuedDate = null;
                        if ($booking->paid_at) {
                            $issuedDate = $booking->paid_at instanceof \Carbon\Carbon 
                                ? $booking->paid_at 
                                : \Carbon\Carbon::parse($booking->paid_at);
                        } elseif ($booking->created_at) {
                            $issuedDate = $booking->created_at instanceof \Carbon\Carbon 
                                ? $booking->created_at 
                                : \Carbon\Carbon::parse($booking->created_at);
                        } else {
                            $issuedDate = now();
                        }
                    @endphp
                    {{ $issuedDate ? $issuedDate->format('Y-m-d') : now()->format('Y-m-d') }}
                </div>
            </div>
        </div>

        <!-- Customer & Company Info -->
        <table class="invoice-info" style="width: 100%; border-collapse: separate; border-spacing: 8px 0;">
            <tr>
                <td class="info-section" style="width: 50%;">
                    <h3>{{ __('messages.customer_data') }}</h3>
                    <div class="info-item">
                        <div class="info-label">{{ __('messages.customer_name') }}</div>
                        <div class="info-value">{{ $booking->customer->name ?? '---' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">{{ __('messages.phone_number') }}</div>
                        <div class="info-value">{{ $booking->customer->phone ?? '---' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">{{ __('messages.email') }}</div>
                        <div class="info-value">{{ $booking->customer->email ?? '---' }}</div>
                    </div>
                </td>
                <td class="info-section" style="width: 50%;">
                    <h3>{{ __('messages.booking_details') }}</h3>
                    <div class="info-item">
                        <div class="info-label">{{ __('messages.booking_date') }}</div>
                        <div class="info-value">{{ $booking->booking_date->format('Y-m-d') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">{{ __('messages.attendance_time') }}</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</div>
                    </div>
                    @if($booking->employee && $booking->employee->user)
                        <div class="info-item">
                            <div class="info-label">{{ __('messages.employee') }}</div>
                            <div class="info-value">{{ $booking->employee->user->name }}</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Service Details -->
        <div class="service-section">
            <h3>{{ $booking->booking_type === 'consultation' ? __('messages.consultation_details') : __('messages.service_details') }}</h3>
            <div class="service-name">{{ $booking->bookable ? $booking->bookable->trans('name') : '---' }}</div>
            <div class="service-meta">
                @if($booking->booking_type === 'consultation')
                    <span><i class="far fa-folder"></i> {{ $booking->consultation->category->trans('name') ?? '' }}</span>
                    <span><i class="far fa-clock"></i> {{ $booking->formatted_duration }}</span>
                @else
                    <span><i class="far fa-folder"></i> {{ $booking->service->subCategory->trans('name') ?? '' }}</span>
                    <span><i class="far fa-clock"></i> {{ $booking->formatted_duration }}</span>
                @endif
            </div>
        </div>

        <!-- Payment Details -->
        <div class="payment-section">
            <h3>{{ __('messages.payment_details') }}</h3>
            <div class="payment-details">
                <div class="info-item">
                    <div class="info-label">{{ __('messages.payment_status') }}</div>
                    <div class="info-value" style="color: #22c55e;">{{ __('messages.paid') }}</div>
                </div>
                @if($booking->paid_at)
                    <div class="info-item">
                        <div class="info-label">{{ __('messages.paid_at') }}</div>
                        <div class="info-value">
                            @php
                                $paidAt = $booking->paid_at;
                                if (!($paidAt instanceof \Carbon\Carbon)) {
                                    $paidAt = \Carbon\Carbon::parse($paidAt);
                                }
                            @endphp
                            {{ $paidAt ? $paidAt->format('Y-m-d H:i:s') : '---' }}
                        </div>
                    </div>
                @endif
                @if($booking->payment_id)
                    <div class="info-item">
                        <div class="info-label">{{ __('messages.transaction_id') }}</div>
                        <div class="info-value">{{ $booking->payment_id }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Total Amount -->
        <div class="total-amount">
            <div class="total-label">{{ __('messages.total_amount') }}</div>
            <div class="total-value">{{ number_format($booking->total_price, 2) }} {{ __('messages.sar') }}</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin-top: 5px; font-size: 8pt; color: #718096;">{{ __('messages.thank_you_for_business') }}</p>
            <p style="margin-top: 3px; font-size: 7pt; color: #a0aec0;">{{ __('messages.invoice_generated_at') }}: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.invoice') }} #INV-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'dejavu sans', 'Arial', sans-serif;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            background: #fff;
            color: #1a202c;
            padding: 40px;
            line-height: 1.6;
            unicode-bidi: embed;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 40px;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .invoice-number {
            font-size: 18px;
            color: #718096;
            font-weight: 600;
        }

        .invoice-date {
            text-align: {{ app()->getLocale() === 'ar' ? 'left' : 'right' }};
            color: #718096;
        }

        .invoice-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        .info-section {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
        }

        .info-section h3 {
            font-size: 14px;
            font-weight: 700;
            color: #4a5568;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .info-item {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-label {
            color: #718096;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-value {
            color: #1a202c;
            font-weight: 700;
        }

        .service-section {
            margin-bottom: 40px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .service-section h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #1a202c;
        }

        .service-name {
            font-size: 20px;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 10px;
        }

        .service-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            color: #718096;
            font-size: 14px;
        }

        .payment-section {
            background: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
        }

        .payment-section h3 {
            font-size: 16px;
            font-weight: 700;
            color: #166534;
            margin-bottom: 15px;
        }

        .payment-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .total-amount {
            text-align: center;
            padding: 30px;
            background: #1a202c;
            color: #fff;
            border-radius: 8px;
            margin-top: 30px;
        }

        .total-label {
            font-size: 14px;
            color: #cbd5e1;
            margin-bottom: 10px;
        }

        .total-value {
            font-size: 36px;
            font-weight: 800;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #718096;
            font-size: 12px;
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
                <div><strong>{{ __('messages.issued_date') }}</strong></div>
                <div>
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
                        }
                    @endphp
                    {{ $issuedDate ? $issuedDate->format('Y-m-d') : '---' }}
                </div>
            </div>
        </div>

        <!-- Customer & Company Info -->
        <div class="invoice-info">
            <div class="info-section">
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
            </div>

            <div class="info-section">
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
            </div>
        </div>

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
            <div style="margin-bottom: 20px; padding: 20px; background: #f8fafc; border-radius: 8px; border: 1px solid #e5e7eb;">
                <h4 style="font-size: 14px; font-weight: 700; color: #1a202c; margin-bottom: 10px;">{{ __('messages.customer_data') }}</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 12px; color: #4a5568;">
                    <div>
                        <strong>{{ __('messages.customer_name') }}:</strong> {{ $booking->customer->name ?? '---' }}
                    </div>
                    <div>
                        <strong>{{ __('messages.phone_number') }}:</strong> {{ $booking->customer->phone ?? '---' }}
                    </div>
                    <div>
                        <strong>{{ __('messages.email') }}:</strong> {{ $booking->customer->email ?? '---' }}
                    </div>
                    <div>
                        <strong>{{ __('messages.invoice_number') }}:</strong> #INV-{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
            </div>
            <p style="margin-top: 20px; font-size: 13px; color: #718096;">{{ __('messages.thank_you_for_business') }}</p>
            <p style="margin-top: 10px; font-size: 11px; color: #a0aec0;">{{ __('messages.invoice_generated_at') }}: {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>


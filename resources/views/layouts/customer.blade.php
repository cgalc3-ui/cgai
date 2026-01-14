<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'حجز استشارة برمجية') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/toast.css') }}">
    <link rel="stylesheet" href="{{ asset('css/confirm.css') }}">
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="customer-header">
        <div class="container">
            <div class="header-content">
                @php
                    $logo = getNavigationLogo();
                    $menuItems = getNavigationMenuItems();
                    $buttons = getNavigationButtons();
                    $hero = getHeroSection();
                    $companyLogo = getCompanyLogoSection();
                @endphp

                {{-- Logo --}}
                @if($logo)
                <div class="logo">
                    <a href="{{ $logo->link ?? '/' }}" class="logo-link">
                        @if($logo->image)
                            <img src="{{ asset('storage/' . $logo->image) }}" alt="{{ $logo->trans('title') }}" class="logo-image">
                        @endif
                        <span>{{ $logo->trans('title') }}</span>
                    </a>
                </div>
                @else
                <div class="logo">
                    <a href="/">
                        <i class="fas fa-code"></i>
                        <span>{{ config('app.name') }}</span>
                    </a>
                </div>
                @endif

                {{-- Navigation Menu --}}
                <nav class="header-nav">
                    @foreach($menuItems as $item)
                        <a href="{{ $item->link ?? '#' }}" 
                           class="nav-link"
                           target="{{ $item->target }}">
                            @if($item->icon)
                                <i class="{{ $item->icon }}"></i>
                            @endif
                            {{ $item->trans('title') }}
                        </a>
                    @endforeach

                    {{-- Buttons --}}
                    @foreach($buttons as $button)
                        <a href="{{ $button->link }}" 
                           class="btn btn-primary"
                           target="{{ $button->target }}">
                            {{ $button->trans('title') }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    @if($hero)
    <section class="hero-section" style="background-image: url('{{ asset('storage/' . $hero->background_image) }}');">
        <div class="container">
            <div class="hero-content">
                <h1>{{ $hero->trans('heading') }}</h1>
                @if($hero->trans('subheading'))
                    <h2>{{ $hero->trans('subheading') }}</h2>
                @endif
                @if($hero->trans('description'))
                    <p>{{ $hero->trans('description') }}</p>
                @endif
                @if($hero->buttons && count($hero->buttons) > 0)
                    <div class="hero-buttons">
                        @foreach($hero->buttons as $button)
                            <a href="{{ $button['link'] ?? '#' }}" 
                               class="btn btn-{{ $button['style'] ?? 'primary' }}"
                               target="{{ $button['target'] ?? '_self' }}">
                                {{ app()->getLocale() === 'ar' ? ($button['title'] ?? '') : ($button['title_en'] ?? $button['title'] ?? '') }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- Company Logos Section --}}
    @if($companyLogo)
    <section class="company-logos-section" style="padding: 60px 0; background: #f8f9fa;">
        <div class="container">
            @if($companyLogo->trans('heading'))
                <h2 style="text-align: center; font-size: 32px; font-weight: 600; margin-bottom: 40px; color: #1f2937;">
                    {{ $companyLogo->trans('heading') }}
                </h2>
            @endif
            @if($companyLogo->logos && count($companyLogo->logos) > 0)
                <div style="display: flex; flex-wrap: wrap; gap: 40px; justify-content: center; align-items: center;">
                    @foreach($companyLogo->logos as $logo)
                        <div style="text-align: center;">
                            <a href="{{ $logo['link'] ?? '#' }}" 
                               target="_blank" 
                               style="display: inline-block; text-decoration: none; transition: transform 0.3s;"
                               onmouseover="this.style.transform='scale(1.1)'"
                               onmouseout="this.style.transform='scale(1)'">
                                @if(isset($logo['image']) && $logo['image'])
                                    <img src="{{ asset('storage/' . $logo['image']) }}" 
                                         alt="{{ $logo['name'] ?? 'Company Logo' }}"
                                         style="max-height: 80px; max-width: 200px; object-fit: contain; filter: grayscale(100%) opacity(0.6); transition: all 0.3s;">
                                @endif
                                @if(isset($logo['name']) && $logo['name'])
                                    <p style="margin-top: 10px; color: #6b7280; font-size: 14px;">{{ $logo['name'] }}</p>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    @endif

    <!-- Main Content -->
    <main class="customer-main">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="customer-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. جميع الحقوق محفوظة.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/toast.js') }}"></script>
    <script src="{{ asset('js/confirm.js') }}"></script>
    <script src="{{ asset('js/customer.js') }}"></script>
    <script>
        // Set translations for JavaScript
        window.translations = {
            confirm: '{{ __('messages.confirm') }}',
            cancel: '{{ __('messages.cancel') }}',
            ok: '{{ __('messages.ok') }}',
            delete: '{{ __('messages.delete') }}',
            confirm_delete: '{{ __('messages.confirm_delete') }}',
            confirm_delete_title: '{{ __('messages.confirm_delete_title') }}',
            warning: '{{ __('messages.warning') }}',
            info: '{{ __('messages.info') }}'
        };
    </script>
    <script>
        // Show toast notifications from session
        @if(session('success'))
            Toast.success('{{ session('success') }}');
        @endif

        @if(session('error'))
            Toast.error('{{ session('error') }}');
        @endif

        @if(session('warning'))
            Toast.warning('{{ session('warning') }}');
        @endif

        @if(session('info'))
            Toast.info('{{ session('info') }}');
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                Toast.error('{{ $error }}');
            @endforeach
        @endif
    </script>
    @stack('scripts')
</body>
</html>


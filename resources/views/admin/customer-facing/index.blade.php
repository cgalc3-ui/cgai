@extends('layouts.dashboard')

@section('title', __('messages.customer_facing') ?? 'وجهة العميل')
@section('page-title', __('messages.customer_facing') ?? 'وجهة العميل')

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ __('messages.customer_facing') ?? 'وجهة العميل' }}</h2>
            <p>{{ __('messages.manage_customer_facing_content') ?? 'إدارة محتوى وجهة العميل' }}</p>
        </div>
    </div>

    <div class="customer-facing-container">
        <!-- Sections Grid -->
        <div class="sections-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <!-- Navigation -->
            <div class="section-card" data-section="navigation" onclick="loadSection('navigation')">
                <div class="section-icon">
                    <i class="fas fa-bars"></i>
                </div>
                <h3>{{ __('messages.navigation') ?? 'القائمة العلوية' }}</h3>
                <p>{{ __('messages.manage_navigation') ?? 'إدارة القائمة العلوية' }}</p>
            </div>

            <!-- Hero -->
            <div class="section-card" data-section="hero" onclick="loadSection('hero')">
                <div class="section-icon">
                    <i class="fas fa-image"></i>
                </div>
                <h3>{{ __('messages.hero_management') ?? 'قسم الهيرو' }}</h3>
                <p>{{ __('messages.manage_hero') ?? 'إدارة قسم الهيرو' }}</p>
            </div>

            <!-- Company Logo -->
            <div class="section-card" data-section="company-logo" onclick="loadSection('company-logo')">
                <div class="section-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h3>{{ __('messages.company_logo_management') ?? 'لوجو الشركات' }}</h3>
                <p>{{ __('messages.manage_company_logos') ?? 'إدارة لوجو الشركات' }}</p>
            </div>

            <!-- Footer -->
            <div class="section-card" data-section="footer" onclick="loadSection('footer')">
                <div class="section-icon">
                    <i class="fas fa-window-restore"></i>
                </div>
                <h3>{{ __('messages.footer_management') ?? 'الفوتر' }}</h3>
                <p>{{ __('messages.manage_footer') ?? 'إدارة الفوتر' }}</p>
            </div>

            <!-- Consultation Booking Section -->
            <div class="section-card" data-section="consultation-booking-section" onclick="loadSection('consultation-booking-section')">
                <div class="section-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3>{{ __('messages.consultation_booking_section_management') ?? 'حجز استشارة' }}</h3>
                <p>{{ __('messages.manage_consultation_booking') ?? 'إدارة قسم حجز الاستشارة' }}</p>
            </div>

            <!-- Technologies Section -->
            <div class="section-card" data-section="technologies-section" onclick="loadSection('technologies-section')">
                <div class="section-icon">
                    <i class="fas fa-microchip"></i>
                </div>
                <h3>{{ __('messages.technologies_section_management') ?? 'التقنيات' }}</h3>
                <p>{{ __('messages.manage_technologies') ?? 'إدارة قسم التقنيات' }}</p>
            </div>

            <!-- Services Section -->
            <div class="section-card" data-section="services-section" onclick="loadSection('services-section')">
                <div class="section-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3>{{ __('messages.services_section_management') ?? 'قسم الخدمات' }}</h3>
                <p>{{ __('messages.manage_services_section') ?? 'إدارة قسم الخدمات' }}</p>
            </div>

            <!-- Ready Apps Section -->
            <div class="section-card" data-section="ready-apps-section" onclick="loadSection('ready-apps-section')">
                <div class="section-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>{{ __('messages.ready_apps_section_management') ?? 'قسم التطبيقات الجاهزة' }}</h3>
                <p>{{ __('messages.manage_ready_apps_section') ?? 'إدارة قسم التطبيقات الجاهزة' }}</p>
            </div>
        </div>

        <!-- Content Area -->
        <div id="sectionContent" class="section-content" style="display: none;">
            <div class="content-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; background: var(--card-bg); border-radius: 8px; border: 1px solid var(--border-color);">
                <h3 id="sectionTitle" style="margin: 0; color: var(--text-primary);"></h3>
                <button type="button" class="btn btn-secondary" onclick="closeSection()" style="padding: 8px 16px;">
                    <i class="fas fa-times"></i> {{ __('messages.close') ?? 'إغلاق' }}
                </button>
            </div>
            <div id="sectionBody" class="content-body">
                <!-- Content will be loaded here -->
            </div>
        </div>
        
        <!-- Keep sections grid visible (hidden when content is shown) -->
        <style>
            .sections-grid.hidden {
                display: none;
            }
        </style>
    </div>

    <style>
        .customer-facing-container {
            padding: 20px;
        }

        .section-card {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .section-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }

        .section-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
        }

        .section-card h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0 0 8px 0;
        }

        .section-card p {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 0;
        }

        .section-content {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
            margin-top: 30px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .content-body {
            min-height: 400px;
        }

        .loading-spinner {
            text-align: center;
            padding: 60px;
        }

        .loading-spinner i {
            font-size: 48px;
            color: var(--primary-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .sections-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        function loadSection(section) {
            const sectionContent = document.getElementById('sectionContent');
            const sectionBody = document.getElementById('sectionBody');
            const sectionTitle = document.getElementById('sectionTitle');
            const sectionsGrid = document.querySelector('.sections-grid');
            
            // Hide sections grid and show content
            if (sectionsGrid) {
                sectionsGrid.classList.add('hidden');
            }
            
            // Show loading
            sectionContent.style.display = 'block';
            sectionBody.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>';
            
            // Get section title
            const card = document.querySelector(`[data-section="${section}"]`);
            const title = card ? card.querySelector('h3').textContent : section;
            sectionTitle.textContent = title;
            
            // Scroll to content
            sectionContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Map section names to routes
            const routeMap = {
                'navigation': '{{ route("admin.customer-facing.navigation.index") }}',
                'hero': '{{ route("admin.customer-facing.hero.index") }}',
                'company-logo': '{{ route("admin.customer-facing.company-logo.index") }}',
                'footer': '{{ route("admin.customer-facing.footer.index") }}',
                'consultation-booking-section': '{{ route("admin.customer-facing.consultation-booking-section.index") }}',
                'technologies-section': '{{ route("admin.customer-facing.technologies-section.index") }}',
                'services-section': '{{ route("admin.customer-facing.services-section.index") }}',
                'ready-apps-section': '{{ route("admin.customer-facing.ready-apps-section.index") }}',
            };
            
            const url = routeMap[section];
            
            if (!url) {
                sectionBody.innerHTML = '<div class="alert alert-error">Section not found</div>';
                return;
            }
            
            // Use the load-section endpoint to get content only
            const loadUrl = '{{ route("admin.customer-facing.load-section", ":section") }}'.replace(':section', section);
            
            // Load content via AJAX
            fetch(loadUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                if (!html || html.trim() === '') {
                    throw new Error('Empty response');
                }
                
                // The endpoint should return only the content
                // Set the content directly
                sectionBody.innerHTML = html;
                
                // Wait a bit for DOM to update, then process
                setTimeout(() => {
                    // Remove any sidebar, header, or navigation elements that might still be there
                    sectionBody.querySelectorAll('.sidebar, .side-nav, nav.sidebar, .header, header, .top-bar, .navbar, aside, .dashboard-sidebar').forEach(el => {
                        el.remove();
                    });
                    
                    // Re-execute scripts and intercept reloads
                    const scripts = sectionBody.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => {
                            newScript.setAttribute(attr.name, attr.value);
                        });
                        if (oldScript.innerHTML) {
                            // Intercept window.location.reload in scripts
                            let scriptContent = oldScript.innerHTML;
                            
                            // Replace window.location.reload() with our custom function
                            scriptContent = scriptContent.replace(
                                /window\.location\.reload\(\)/g,
                                'window.showSectionsGridAndReload()'
                            );
                            
                            // Replace window.location.href = ... with our custom function
                            scriptContent = scriptContent.replace(
                                /window\.location\.href\s*=\s*['"]([^'"]+)['"]/g,
                                function(match, url) {
                                    if (url.includes('/admin/customer-facing') && !url.includes('/load-section/')) {
                                        return 'window.showSectionsGridAndRedirect("' + url + '")';
                                    }
                                    return match;
                                }
                            );
                            
                            newScript.appendChild(document.createTextNode(scriptContent));
                        }
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                    
                    // Fix relative URLs in images and links
                    const baseUrl = window.location.origin;
                    sectionBody.querySelectorAll('img[src^="/"], a[href^="/"]').forEach(el => {
                        if (el.tagName === 'IMG') {
                            const src = el.getAttribute('src');
                            if (src && !src.startsWith('http') && !src.startsWith('data:')) {
                                el.src = baseUrl + src;
                            }
                        } else if (el.tagName === 'A') {
                            const href = el.getAttribute('href');
                            // Prevent navigation for internal customer-facing links
                            if (href && href.includes('/admin/customer-facing/') && !href.includes('/load-section/')) {
                                el.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    // Extract section name from URL
                                    const match = href.match(/customer-facing\/([^\/]+)/);
                                    if (match && match[1]) {
                                        loadSection(match[1]);
                                    }
                                });
                            }
                        }
                    });
                    
                    // Re-initialize any modals or forms that might need it
                    if (typeof window.initModals === 'function') {
                        window.initModals();
                    }
                    
                    // Intercept all form submissions in the loaded content
                    const forms = sectionBody.querySelectorAll('form');
                    forms.forEach(function(form) {
                        // Store original submit handler if exists
                        const originalSubmit = form.onsubmit;
                        
                        form.addEventListener('submit', function(e) {
                            // Let the form submit normally, but monitor for success
                            const checkInterval = setInterval(function() {
                                // Check for success messages
                                const successMessages = sectionBody.querySelectorAll('.alert-success, .success-message, [class*="success"], .toast-success');
                                if (successMessages.length > 0) {
                                    clearInterval(checkInterval);
                                    // Show sections grid after successful save
                                    setTimeout(showSectionsGrid, 2000);
                                }
                                
                                // Check if page is being reloaded
                                if (document.readyState === 'complete') {
                                    // If we're still on customer-facing page, show grid
                                    if (window.location.pathname.includes('/admin/customer-facing') && 
                                        !window.location.pathname.includes('/load-section/') &&
                                        !window.location.pathname.match(/\/admin\/customer-facing\/(navigation|hero|company-logo|footer|consultation-booking-section|technologies-section|services-section|ready-apps-section)$/)) {
                                        setTimeout(showSectionsGrid, 500);
                                    }
                                }
                            }, 500);
                            
                            // Stop checking after 10 seconds
                            setTimeout(function() {
                                clearInterval(checkInterval);
                            }, 10000);
                        });
                    });
                    
                    // Monitor for any AJAX success responses
                    const originalFetch = window.fetch;
                    window.fetch = function(...args) {
                        return originalFetch.apply(this, args).then(function(response) {
                            if (response.ok) {
                                response.clone().json().then(function(data) {
                                    if (data && data.success) {
                                        // If redirect is to customer-facing main page, show grid
                                        if (data.redirect && data.redirect.includes('/admin/customer-facing') && 
                                            !data.redirect.includes('/load-section/') &&
                                            !data.redirect.match(/\/admin\/customer-facing\/(navigation|hero|company-logo|footer|consultation-booking-section|technologies-section|services-section|ready-apps-section)$/)) {
                                            setTimeout(showSectionsGrid, 1000);
                                        }
                                    }
                                }).catch(function() {
                                    // Not JSON, ignore
                                });
                            }
                            return response;
                        });
                    };
                }, 100);
            })
            .catch(error => {
                console.error('Error loading section:', error);
                sectionBody.innerHTML = '<div class="alert alert-error" style="padding: 20px; background: #fee; color: #c33; border-radius: 8px; text-align: center;">حدث خطأ أثناء تحميل المحتوى. يرجى المحاولة مرة أخرى.<br><small>' + error.message + '</small></div>';
                // Show sections grid on error
                showSectionsGrid();
            });
        
        function showSectionsGrid() {
            const sectionsGrid = document.querySelector('.sections-grid');
            if (sectionsGrid) {
                sectionsGrid.classList.remove('hidden');
            }
            const sectionContent = document.getElementById('sectionContent');
            if (sectionContent) {
                sectionContent.style.display = 'none';
            }
            const sectionBody = document.getElementById('sectionBody');
            if (sectionBody) {
                sectionBody.innerHTML = '';
            }
        }
        
        // Custom reload function that shows sections grid first
        window.showSectionsGridAndReload = function() {
            showSectionsGrid();
            setTimeout(function() {
                window.location.reload();
            }, 300);
        };
        
        // Custom redirect function
        window.showSectionsGridAndRedirect = function(url) {
            showSectionsGrid();
            setTimeout(function() {
                window.location.href = url;
            }, 300);
        };
        }
        
        function closeSection() {
            showSectionsGrid();
            const sectionBody = document.getElementById('sectionBody');
            if (sectionBody) {
                sectionBody.innerHTML = '';
            }
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // Ensure sections grid is visible on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Always show sections grid on page load (if we're on the main customer-facing page)
            const currentPath = window.location.pathname;
            const isMainPage = currentPath === '/admin/customer-facing' || 
                              currentPath.endsWith('/admin/customer-facing') ||
                              (currentPath.includes('/admin/customer-facing') && 
                               !currentPath.includes('/load-section/') &&
                               !currentPath.match(/\/admin\/customer-facing\/(navigation|hero|company-logo|footer|consultation-booking-section|technologies-section|services-section|ready-apps-section)$/));
            
            if (isMainPage) {
                showSectionsGrid();
            }
            
            // Check if we're coming from a form submission
            if (window.location.search.includes('saved=true') || sessionStorage.getItem('showSectionsGrid')) {
                showSectionsGrid();
                sessionStorage.removeItem('showSectionsGrid');
            }
            
            // Also check after a short delay (in case page is still loading)
            setTimeout(function() {
                if (isMainPage) {
                    showSectionsGrid();
                }
            }, 100);
            
            // Override window.location.reload globally to preserve sections grid state
            const originalReload = window.location.reload;
            window.location.reload = function() {
                const currentPath = window.location.pathname;
                if (currentPath.includes('/admin/customer-facing') && !currentPath.includes('/load-section/') &&
                    !currentPath.match(/\/admin\/customer-facing\/(navigation|hero|company-logo|footer|consultation-booking-section|technologies-section|services-section|ready-apps-section)$/)) {
                    sessionStorage.setItem('showSectionsGrid', 'true');
                }
                return originalReload.apply(this, arguments);
            };
            
            // Intercept form submissions in loaded content
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            // Listen for success messages or alerts
                            setTimeout(function() {
                                const successMessages = node.querySelectorAll ? node.querySelectorAll('.alert-success, .success-message, [class*="success"], .toast-success') : [];
                                if (successMessages.length > 0) {
                                    // Show sections grid after successful save
                                    setTimeout(showSectionsGrid, 1500);
                                }
                            }, 500);
                            
                            // Listen for success messages and show grid
                            setTimeout(function() {
                                const successIndicators = node.querySelectorAll ? node.querySelectorAll('.alert-success, .success-message, [class*="success"], .toast-success, [data-success="true"]') : [];
                                if (successIndicators.length > 0) {
                                    // Show sections grid after successful save
                                    setTimeout(function() {
                                        showSectionsGrid();
                                    }, 1500);
                                }
                                
                                // Also check for any reload indicators
                                const reloadIndicators = node.querySelectorAll ? node.querySelectorAll('[onclick*="reload"], [data-reload="true"]') : [];
                                if (reloadIndicators.length > 0) {
                                    setTimeout(showSectionsGrid, 2000);
                                }
                            }, 500);
                        }
                    });
                });
            });
            
            // Observe changes in sectionBody
            const sectionBody = document.getElementById('sectionBody');
            if (sectionBody) {
                observer.observe(sectionBody, {
                    childList: true,
                    subtree: true
                });
            }
            
            // Also listen for messages from iframe or AJAX responses
            window.addEventListener('message', function(event) {
                if (event.data && event.data.type === 'form-success') {
                    showSectionsGrid();
                }
            });
        });
    </script>
@endsection


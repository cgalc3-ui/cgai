<form action="{{ isset($footer) ? route('admin.customer-facing.footer.update', $footer) : route('admin.customer-facing.footer.store') }}" method="POST" id="footerForm" class="modal-form" enctype="multipart/form-data">
    @csrf
    @if(isset($footer))
        @method('PUT')
    @endif

    <h4 style="margin-bottom: 20px; color: var(--primary-color); border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">{{ __('messages.company_info') ?? 'معلومات الشركة' }}</h4>

    <div class="form-group">
        <label for="logo">{{ __('messages.logo') ?? 'الشعار' }} (AR)</label>
        <input type="file" id="logo" name="logo" accept="image/*" class="form-control">
        @if(isset($footer) && $footer->logo)
            <small class="form-help">
                <img src="{{ asset('storage/' . $footer->logo) }}" alt="Current logo" style="max-width: 200px; margin-top: 10px; border-radius: 8px;">
            </small>
        @endif
    </div>

    <div class="form-group">
        <label for="logo_en">{{ __('messages.logo') ?? 'الشعار' }} (EN)</label>
        <input type="file" id="logo_en" name="logo_en" accept="image/*" class="form-control">
        @if(isset($footer) && $footer->logo_en)
            <small class="form-help">
                <img src="{{ asset('storage/' . $footer->logo_en) }}" alt="Current logo EN" style="max-width: 200px; margin-top: 10px; border-radius: 8px;">
            </small>
        @endif
    </div>

    <div class="form-group">
        <label for="description">{{ __('messages.description') ?? 'الوصف' }} (AR)</label>
        <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $footer->description ?? '') }}</textarea>
    </div>

    <div class="form-group">
        <label for="description_en">{{ __('messages.description') ?? 'الوصف' }} (EN)</label>
        <textarea id="description_en" name="description_en" class="form-control" rows="3" style="direction: ltr; text-align: left;">{{ old('description_en', $footer->description_en ?? '') }}</textarea>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
            <label for="email">{{ __('messages.email') ?? 'البريد الإلكتروني' }}</label>
            <input type="email" id="email" name="email" value="{{ old('email', $footer->email ?? '') }}" class="form-control">
        </div>

        <div class="form-group">
            <label for="phone">{{ __('messages.phone') ?? 'الهاتف' }}</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $footer->phone ?? '') }}" class="form-control">
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
        <div class="form-group">
            <label for="working_hours">{{ __('messages.working_hours') ?? 'ساعات العمل' }} (AR)</label>
            <input type="text" id="working_hours" name="working_hours" value="{{ old('working_hours', $footer->working_hours ?? '') }}" class="form-control" placeholder="9 AM : 6 PM">
        </div>

        <div class="form-group">
            <label for="working_hours_en">{{ __('messages.working_hours') ?? 'ساعات العمل' }} (EN)</label>
            <input type="text" id="working_hours_en" name="working_hours_en" value="{{ old('working_hours_en', $footer->working_hours_en ?? '') }}" class="form-control" style="direction: ltr; text-align: left;" placeholder="9 AM : 6 PM">
        </div>
    </div>

    <h4 style="margin-top: 30px; margin-bottom: 20px; color: var(--primary-color); border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">{{ __('messages.quick_links') ?? 'روابط سريعة' }}</h4>

    <div class="form-group">
        <div id="quick-links-container">
            @php
                $quickLinks = old('quick_links', isset($footer) && $footer->quick_links ? $footer->quick_links : []);
            @endphp
            @if(count($quickLinks) > 0)
                @foreach($quickLinks as $index => $link)
                    <div class="link-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong>{{ __('messages.link') ?? 'رابط' }} #{{ $index + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-danger" onclick="window.removeQuickLink(this)">
                                <i class="fas fa-trash"></i> {{ __('messages.remove') ?? 'حذف' }}
                            </button>
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.title') ?? 'العنوان' }} (AR)</label>
                            <input type="text" name="quick_links[{{ $index }}][title]" value="{{ $link['title'] ?? '' }}" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.title') ?? 'العنوان' }} (EN)</label>
                            <input type="text" name="quick_links[{{ $index }}][title_en]" value="{{ $link['title_en'] ?? '' }}" class="form-control" style="direction: ltr; text-align: left;">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.link') ?? 'الرابط' }}</label>
                            <input type="url" name="quick_links[{{ $index }}][link]" value="{{ $link['link'] ?? '' }}" class="form-control">
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="window.addQuickLink()">
            <i class="fas fa-plus"></i> {{ __('messages.add_link') ?? 'إضافة رابط' }}
        </button>
    </div>

    <h4 style="margin-top: 30px; margin-bottom: 20px; color: var(--primary-color); border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">{{ __('messages.content') ?? 'المحتوى' }}</h4>

    <div class="form-group">
        <div id="content-links-container">
            @php
                $contentLinks = old('content_links', isset($footer) && $footer->content_links ? $footer->content_links : []);
            @endphp
            @if(count($contentLinks) > 0)
                @foreach($contentLinks as $index => $link)
                    <div class="link-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong>{{ __('messages.link') ?? 'رابط' }} #{{ $index + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-danger" onclick="window.removeContentLink(this)">
                                <i class="fas fa-trash"></i> {{ __('messages.remove') ?? 'حذف' }}
                            </button>
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.title') ?? 'العنوان' }} (AR)</label>
                            <input type="text" name="content_links[{{ $index }}][title]" value="{{ $link['title'] ?? '' }}" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.title') ?? 'العنوان' }} (EN)</label>
                            <input type="text" name="content_links[{{ $index }}][title_en]" value="{{ $link['title_en'] ?? '' }}" class="form-control" style="direction: ltr; text-align: left;">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.link') ?? 'الرابط' }}</label>
                            <input type="url" name="content_links[{{ $index }}][link]" value="{{ $link['link'] ?? '' }}" class="form-control">
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="window.addContentLink()">
            <i class="fas fa-plus"></i> {{ __('messages.add_link') ?? 'إضافة رابط' }}
        </button>
    </div>

    <h4 style="margin-top: 30px; margin-bottom: 20px; color: var(--primary-color); border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">{{ __('messages.support_help') ?? 'الدعم والمساعدة' }}</h4>

    <div class="form-group">
        <div id="support-links-container">
            @php
                $supportLinks = old('support_links', isset($footer) && $footer->support_links ? $footer->support_links : []);
            @endphp
            @if(count($supportLinks) > 0)
                @foreach($supportLinks as $index => $link)
                    <div class="link-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong>{{ __('messages.link') ?? 'رابط' }} #{{ $index + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-danger" onclick="window.removeSupportLink(this)">
                                <i class="fas fa-trash"></i> {{ __('messages.remove') ?? 'حذف' }}
                            </button>
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.title') ?? 'العنوان' }} (AR)</label>
                            <input type="text" name="support_links[{{ $index }}][title]" value="{{ $link['title'] ?? '' }}" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.title') ?? 'العنوان' }} (EN)</label>
                            <input type="text" name="support_links[{{ $index }}][title_en]" value="{{ $link['title_en'] ?? '' }}" class="form-control" style="direction: ltr; text-align: left;">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.link') ?? 'الرابط' }}</label>
                            <input type="url" name="support_links[{{ $index }}][link]" value="{{ $link['link'] ?? '' }}" class="form-control">
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="window.addSupportLink()">
            <i class="fas fa-plus"></i> {{ __('messages.add_link') ?? 'إضافة رابط' }}
        </button>
    </div>

    <h4 style="margin-top: 30px; margin-bottom: 20px; color: var(--primary-color); border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">{{ __('messages.social_media') ?? 'وسائل التواصل الاجتماعي' }}</h4>

    <div class="form-group">
        <div id="social-media-container">
            @php
                $socialMedia = old('social_media', isset($footer) && $footer->social_media ? $footer->social_media : []);
            @endphp
            @if(count($socialMedia) > 0)
                @foreach($socialMedia as $index => $social)
                    <div class="social-item" style="border: 1px solid var(--border-color); padding: 15px; border-radius: 8px; margin-bottom: 15px; background: var(--card-bg);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <strong>{{ __('messages.social_media') ?? 'وسائل التواصل' }} #{{ $index + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-danger" onclick="window.removeSocialMedia(this)">
                                <i class="fas fa-trash"></i> {{ __('messages.remove') ?? 'حذف' }}
                            </button>
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.platform') ?? 'المنصة' }}</label>
                            <input type="text" name="social_media[{{ $index }}][platform]" value="{{ $social['platform'] ?? '' }}" class="form-control" placeholder="Facebook, Twitter, etc.">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.url') ?? 'الرابط' }}</label>
                            <input type="url" name="social_media[{{ $index }}][url]" value="{{ $social['url'] ?? '' }}" class="form-control">
                        </div>
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label>{{ __('messages.icon') ?? 'الأيقونة' }} (FontAwesome class)</label>
                            <input type="text" name="social_media[{{ $index }}][icon]" value="{{ $social['icon'] ?? '' }}" class="form-control" placeholder="fab fa-facebook">
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <button type="button" class="btn btn-secondary btn-sm" onclick="window.addSocialMedia()">
            <i class="fas fa-plus"></i> {{ __('messages.add_social') ?? 'إضافة وسيلة تواصل' }}
        </button>
    </div>

    <h4 style="margin-top: 30px; margin-bottom: 20px; color: var(--primary-color); border-bottom: 2px solid var(--border-color); padding-bottom: 10px;">{{ __('messages.copyright') ?? 'حقوق النشر' }}</h4>

    <div class="form-group">
        <label for="copyright_text">{{ __('messages.copyright_text') ?? 'نص حقوق النشر' }} (AR)</label>
        <input type="text" id="copyright_text" name="copyright_text" value="{{ old('copyright_text', $footer->copyright_text ?? '') }}" class="form-control" placeholder="جميع الحقوق محفوظة © 2026">
    </div>

    <div class="form-group">
        <label for="copyright_text_en">{{ __('messages.copyright_text') ?? 'نص حقوق النشر' }} (EN)</label>
        <input type="text" id="copyright_text_en" name="copyright_text_en" value="{{ old('copyright_text_en', $footer->copyright_text_en ?? '') }}" class="form-control" style="direction: ltr; text-align: left;" placeholder="All rights reserved © 2026">
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', isset($footer) ? $footer->is_active : true) ? 'checked' : '' }}>
            <span>{{ __('messages.active') ?? 'نشط' }}</span>
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> {{ __('messages.save') ?? 'حفظ' }}
        </button>
        <button type="button" class="btn btn-secondary" onclick="if(window.closeModal) window.closeModal('footerModal'); return false;">
            <i class="fas fa-times"></i> {{ __('messages.cancel') ?? 'إلغاء' }}
        </button>
    </div>
</form>

<style>
    .modal-form .form-group {
        margin-bottom: 20px;
    }

    .modal-form label {
        color: var(--text-primary);
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
    }

    .modal-form .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        background: var(--card-bg);
        color: var(--text-primary);
        font-family: 'Cairo', sans-serif;
        transition: all 0.2s;
        box-sizing: border-box;
    }

    .modal-form .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(102, 88, 221, 0.1);
    }

    .modal-form .form-help {
        color: var(--text-secondary);
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    .modal-form .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-primary);
        cursor: pointer;
    }

    .modal-form .checkbox-label input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--primary-color);
    }

    .modal-form .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .modal-form .btn {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .modal-form .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .modal-form .btn-primary:hover {
        background: var(--primary-dark);
    }

    .modal-form .btn-secondary {
        background: var(--sidebar-active-bg);
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }

    .modal-form .btn-secondary:hover {
        background: var(--bg-light);
        color: var(--text-primary);
    }

    .modal-form .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    .modal-form .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }

    .modal-form h4 {
        font-size: 18px;
        font-weight: 600;
    }
</style>



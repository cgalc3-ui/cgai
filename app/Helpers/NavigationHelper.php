<?php

if (!function_exists('getNavigationLogo')) {
    function getNavigationLogo()
    {
        return \App\Models\NavigationItem::byType('logo')
            ->active()
            ->first();
    }
}

if (!function_exists('getNavigationMenuItems')) {
    function getNavigationMenuItems()
    {
        return \App\Models\NavigationItem::byType('menu_item')
            ->active()
            ->get();
    }
}

if (!function_exists('getNavigationButtons')) {
    function getNavigationButtons()
    {
        return \App\Models\NavigationItem::byType('button')
            ->active()
            ->get();
    }
}

if (!function_exists('getHeroSection')) {
    function getHeroSection()
    {
        return \App\Models\HeroSection::active()->first();
    }
}

if (!function_exists('getCompanyLogoSection')) {
    function getCompanyLogoSection()
    {
        return \App\Models\CompanyLogo::active()->first();
    }
}

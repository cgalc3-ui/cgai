<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'الخدمات البرمجية',
                'description' => 'خدمات تطوير البرمجيات والتطبيقات',
                'image' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'الخدمات التقنية',
                'description' => 'خدمات الدعم التقني والصيانة',
                'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'الخدمات الاستشارية',
                'description' => 'استشارات تقنية وإدارية',
                'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'التصميم',
                'description' => 'خدمات التصميم الجرافيكي والواجهات',
                'image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($category['name'])],
                $category
            );
        }

        $this->command->info('✅ تم إنشاء الفئات بنجاح');
    }
}

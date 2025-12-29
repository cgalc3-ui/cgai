<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subCategories = [
            // الخدمات البرمجية
            [
                'category' => 'الخدمات البرمجية',
                'name' => 'تطوير الويب',
                'description' => 'تطوير مواقع وتطبيقات الويب',
                'is_active' => true,
            ],
            [
                'category' => 'الخدمات البرمجية',
                'name' => 'تطوير التطبيقات',
                'description' => 'تطوير تطبيقات الهواتف المحمولة',
                'is_active' => true,
            ],
            [
                'category' => 'الخدمات البرمجية',
                'name' => 'تطوير الأنظمة',
                'description' => 'تطوير أنظمة إدارة متكاملة',
                'is_active' => true,
            ],
            // الخدمات التقنية
            [
                'category' => 'الخدمات التقنية',
                'name' => 'صيانة الأجهزة',
                'description' => 'صيانة وإصلاح الأجهزة التقنية',
                'is_active' => true,
            ],
            [
                'category' => 'الخدمات التقنية',
                'name' => 'الدعم التقني',
                'description' => 'دعم فني للمستخدمين',
                'is_active' => true,
            ],
            [
                'category' => 'الخدمات التقنية',
                'name' => 'إدارة الشبكات',
                'description' => 'إدارة وصيانة الشبكات',
                'is_active' => true,
            ],
            // الخدمات الاستشارية
            [
                'category' => 'الخدمات الاستشارية',
                'name' => 'استشارات تقنية',
                'description' => 'استشارات في مجال التقنية',
                'is_active' => true,
            ],
            [
                'category' => 'الخدمات الاستشارية',
                'name' => 'استشارات إدارية',
                'description' => 'استشارات في الإدارة والتخطيط',
                'is_active' => true,
            ],
            // التصميم
            [
                'category' => 'التصميم',
                'name' => 'تصميم الواجهات',
                'description' => 'تصميم واجهات المستخدم',
                'is_active' => true,
            ],
            [
                'category' => 'التصميم',
                'name' => 'التصميم الجرافيكي',
                'description' => 'تصميم الشعارات والهويات البصرية',
                'is_active' => true,
            ],
        ];

        foreach ($subCategories as $subCategoryData) {
            $category = Category::where('name', $subCategoryData['category'])->first();
            
            if ($category) {
                SubCategory::firstOrCreate(
                    [
                        'category_id' => $category->id,
                        'slug' => \Illuminate\Support\Str::slug($subCategoryData['name']),
                    ],
                    [
                        'category_id' => $category->id,
                        'name' => $subCategoryData['name'],
                        'description' => $subCategoryData['description'],
                        'is_active' => $subCategoryData['is_active'],
                    ]
                );
            }
        }

        $this->command->info('✅ تم إنشاء الفئات الفرعية بنجاح');
    }
}

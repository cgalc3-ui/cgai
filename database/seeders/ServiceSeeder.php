<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'استشارة برمجية - باك اند',
                'category' => 'backend',
                'description' => 'استشارة متخصصة في تطوير واجهات الخادم والتطبيقات الخلفية',
                'price' => 500.00,
                'duration_minutes' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'استشارة برمجية - فرونت اند',
                'category' => 'frontend',
                'description' => 'استشارة متخصصة في تطوير واجهات المستخدم والتطبيقات الأمامية',
                'price' => 450.00,
                'duration_minutes' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'استشارة برمجية - موبايل',
                'category' => 'mobile',
                'description' => 'استشارة متخصصة في تطوير تطبيقات الهواتف المحمولة',
                'price' => 550.00,
                'duration_minutes' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'استشارة برمجية - قاعدة بيانات',
                'category' => 'database',
                'description' => 'استشارة متخصصة في تصميم وإدارة قواعد البيانات',
                'price' => 400.00,
                'duration_minutes' => 60,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        $this->command->info('✅ تم إنشاء الخدمات بنجاح');
    }
}

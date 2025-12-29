<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceDuration;
use Illuminate\Database\Seeder;

class ServiceDurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على جميع الخدمات
        $services = Service::all();

        foreach ($services as $service) {
            // تحديد نوع المدة والسعر حسب نوع الخدمة
            $durations = $this->getDurationsForService($service->name);

            foreach ($durations as $duration) {
                ServiceDuration::firstOrCreate(
                    [
                        'service_id' => $service->id,
                        'duration_type' => $duration['type'],
                        'duration_value' => $duration['value'],
                    ],
                    [
                        'service_id' => $service->id,
                        'duration_type' => $duration['type'],
                        'duration_value' => $duration['value'],
                        'price' => $duration['price'],
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('✅ تم إنشاء مدة الخدمات بنجاح');
    }

    /**
     * تحديد مدة الخدمات حسب نوع الخدمة
     */
    private function getDurationsForService(string $serviceName): array
    {
        // خدمات التطوير - عادة بالساعات
        if (str_contains($serviceName, 'تطوير') || str_contains($serviceName, 'API') || str_contains($serviceName, 'نظام')) {
            return [
                ['type' => 'hour', 'value' => 1, 'price' => 200.00],
                ['type' => 'hour', 'value' => 2, 'price' => 380.00],
                ['type' => 'hour', 'value' => 4, 'price' => 720.00],
                ['type' => 'day', 'value' => 1, 'price' => 1500.00],
                ['type' => 'day', 'value' => 3, 'price' => 4200.00],
                ['type' => 'week', 'value' => 1, 'price' => 9000.00],
            ];
        }

        // خدمات التصميم - عادة بالساعات أو الأيام
        if (str_contains($serviceName, 'تصميم') || str_contains($serviceName, 'شعار') || str_contains($serviceName, 'هوية')) {
            return [
                ['type' => 'hour', 'value' => 1, 'price' => 150.00],
                ['type' => 'hour', 'value' => 2, 'price' => 280.00],
                ['type' => 'hour', 'value' => 4, 'price' => 520.00],
                ['type' => 'day', 'value' => 1, 'price' => 1000.00],
                ['type' => 'day', 'value' => 3, 'price' => 2700.00],
            ];
        }

        // خدمات الصيانة والدعم - عادة بالساعات
        if (str_contains($serviceName, 'صيانة') || str_contains($serviceName, 'دعم') || str_contains($serviceName, 'شبكات')) {
            return [
                ['type' => 'hour', 'value' => 1, 'price' => 100.00],
                ['type' => 'hour', 'value' => 2, 'price' => 180.00],
                ['type' => 'hour', 'value' => 4, 'price' => 340.00],
                ['type' => 'day', 'value' => 1, 'price' => 700.00],
            ];
        }

        // الاستشارات - عادة بالساعات
        if (str_contains($serviceName, 'استشارة')) {
            return [
                ['type' => 'hour', 'value' => 1, 'price' => 250.00],
                ['type' => 'hour', 'value' => 2, 'price' => 480.00],
                ['type' => 'hour', 'value' => 4, 'price' => 900.00],
            ];
        }

        // القيم الافتراضية
        return [
            ['type' => 'hour', 'value' => 1, 'price' => 150.00],
            ['type' => 'hour', 'value' => 2, 'price' => 280.00],
            ['type' => 'hour', 'value' => 4, 'price' => 520.00],
        ];
    }
}

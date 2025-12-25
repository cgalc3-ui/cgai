<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specializations = [
            [
                'name' => 'Backend Development',
                'slug' => 'backend-development',
                'description' => 'تطوير واجهات الخادم والتطبيقات الخلفية',
                'is_active' => true,
            ],
            [
                'name' => 'Frontend Development',
                'slug' => 'frontend-development',
                'description' => 'تطوير واجهات المستخدم والتطبيقات الأمامية',
                'is_active' => true,
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'description' => 'تطوير تطبيقات الهواتف المحمولة',
                'is_active' => true,
            ],
            [
                'name' => 'Database Administration',
                'slug' => 'database-administration',
                'description' => 'إدارة وتصميم قواعد البيانات',
                'is_active' => true,
            ],
            [
                'name' => 'DevOps',
                'slug' => 'devops',
                'description' => 'إدارة البنية التحتية والنشر',
                'is_active' => true,
            ],
            [
                'name' => 'UI/UX Design',
                'slug' => 'ui-ux-design',
                'description' => 'تصميم واجهات المستخدم وتجربة المستخدم',
                'is_active' => true,
            ],
        ];

        foreach ($specializations as $specialization) {
            Specialization::firstOrCreate(
                ['slug' => $specialization['slug']],
                $specialization
            );
        }

        $this->command->info('✅ تم إنشاء التخصصات بنجاح');
    }
}

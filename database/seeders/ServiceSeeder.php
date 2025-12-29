<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\SubCategory;
use App\Models\Specialization;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // تطوير الويب
            [
                'sub_category' => 'تطوير الويب',
                'specialization' => 'Backend Development',
                'name' => 'تطوير API باستخدام Laravel',
                'description' => 'تطوير واجهات برمجة التطبيقات باستخدام Laravel',
                'is_active' => true,
            ],
            [
                'sub_category' => 'تطوير الويب',
                'specialization' => 'Frontend Development',
                'name' => 'تطوير واجهة المستخدم باستخدام React',
                'description' => 'تطوير واجهات تفاعلية باستخدام React',
                'is_active' => true,
            ],
            [
                'sub_category' => 'تطوير الويب',
                'specialization' => 'Frontend Development',
                'name' => 'تطوير واجهة المستخدم باستخدام Vue.js',
                'description' => 'تطوير واجهات تفاعلية باستخدام Vue.js',
                'is_active' => true,
            ],
            [
                'sub_category' => 'تطوير الويب',
                'specialization' => 'Backend Development',
                'name' => 'تطوير نظام إدارة محتوى',
                'description' => 'تطوير نظام إدارة محتوى مخصص',
                'is_active' => true,
            ],
            // تطوير التطبيقات
            [
                'sub_category' => 'تطوير التطبيقات',
                'specialization' => 'Mobile Development',
                'name' => 'تطوير تطبيق iOS',
                'description' => 'تطوير تطبيقات iOS باستخدام Swift',
                'is_active' => true,
            ],
            [
                'sub_category' => 'تطوير التطبيقات',
                'specialization' => 'Mobile Development',
                'name' => 'تطوير تطبيق Android',
                'description' => 'تطوير تطبيقات Android باستخدام Kotlin',
                'is_active' => true,
            ],
            [
                'sub_category' => 'تطوير التطبيقات',
                'specialization' => 'Mobile Development',
                'name' => 'تطوير تطبيق متعدد المنصات',
                'description' => 'تطوير تطبيق يعمل على iOS و Android باستخدام Flutter',
                'is_active' => true,
            ],
            // تطوير الأنظمة
            [
                'sub_category' => 'تطوير الأنظمة',
                'specialization' => 'Backend Development',
                'name' => 'تطوير نظام إدارة الموارد البشرية',
                'description' => 'تطوير نظام متكامل لإدارة الموارد البشرية',
                'is_active' => true,
            ],
            [
                'sub_category' => 'تطوير الأنظمة',
                'specialization' => 'Backend Development',
                'name' => 'تطوير نظام إدارة المبيعات',
                'description' => 'تطوير نظام متكامل لإدارة المبيعات والمخزون',
                'is_active' => true,
            ],
            [
                'sub_category' => 'تطوير الأنظمة',
                'specialization' => 'Database Administration',
                'name' => 'تصميم قاعدة بيانات',
                'description' => 'تصميم وهيكلة قواعد البيانات',
                'is_active' => true,
            ],
            // صيانة الأجهزة
            [
                'sub_category' => 'صيانة الأجهزة',
                'specialization' => null,
                'name' => 'صيانة أجهزة الكمبيوتر',
                'description' => 'صيانة وإصلاح أجهزة الكمبيوتر',
                'is_active' => true,
            ],
            [
                'sub_category' => 'صيانة الأجهزة',
                'specialization' => null,
                'name' => 'صيانة الطابعات',
                'description' => 'صيانة وإصلاح الطابعات',
                'is_active' => true,
            ],
            // الدعم التقني
            [
                'sub_category' => 'الدعم التقني',
                'specialization' => null,
                'name' => 'دعم فني عن بُعد',
                'description' => 'دعم فني للمستخدمين عن بُعد',
                'is_active' => true,
            ],
            [
                'sub_category' => 'الدعم التقني',
                'specialization' => null,
                'name' => 'دعم فني على الموقع',
                'description' => 'دعم فني مباشر على الموقع',
                'is_active' => true,
            ],
            // إدارة الشبكات
            [
                'sub_category' => 'إدارة الشبكات',
                'specialization' => 'DevOps',
                'name' => 'إعداد وإدارة الشبكات',
                'description' => 'إعداد وصيانة شبكات الحاسوب',
                'is_active' => true,
            ],
            [
                'sub_category' => 'إدارة الشبكات',
                'specialization' => 'DevOps',
                'name' => 'أمان الشبكات',
                'description' => 'تأمين الشبكات وحمايتها',
                'is_active' => true,
            ],
            // استشارات تقنية
            [
                'sub_category' => 'استشارات تقنية',
                'specialization' => 'Backend Development',
                'name' => 'استشارة في تطوير الويب',
                'description' => 'استشارة متخصصة في تطوير الويب',
                'is_active' => true,
            ],
            [
                'sub_category' => 'استشارات تقنية',
                'specialization' => 'Mobile Development',
                'name' => 'استشارة في تطوير التطبيقات',
                'description' => 'استشارة متخصصة في تطوير التطبيقات',
                'is_active' => true,
            ],
            // تصميم الواجهات
            [
                'sub_category' => 'تصميم الواجهات',
                'specialization' => 'UI/UX Design',
                'name' => 'تصميم واجهة المستخدم',
                'description' => 'تصميم واجهات مستخدم جذابة وسهلة الاستخدام',
                'is_active' => true,
            ],
            [
                'sub_category' => 'تصميم الواجهات',
                'specialization' => 'UI/UX Design',
                'name' => 'تحسين تجربة المستخدم',
                'description' => 'تحليل وتحسين تجربة المستخدم',
                'is_active' => true,
            ],
            // التصميم الجرافيكي
            [
                'sub_category' => 'التصميم الجرافيكي',
                'specialization' => 'UI/UX Design',
                'name' => 'تصميم شعار',
                'description' => 'تصميم شعار احترافي للعلامة التجارية',
                'is_active' => true,
            ],
            [
                'sub_category' => 'التصميم الجرافيكي',
                'specialization' => 'UI/UX Design',
                'name' => 'تصميم الهوية البصرية',
                'description' => 'تصميم هوية بصرية كاملة للعلامة التجارية',
                'is_active' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            $subCategory = SubCategory::where('name', $serviceData['sub_category'])->first();
            
            if ($subCategory) {
                $specialization = null;
                if ($serviceData['specialization']) {
                    $specialization = Specialization::where('name', $serviceData['specialization'])->first();
                }

                Service::firstOrCreate(
                    [
                        'sub_category_id' => $subCategory->id,
                        'slug' => \Illuminate\Support\Str::slug($serviceData['name']),
                    ],
                    [
                        'sub_category_id' => $subCategory->id,
                        'specialization_id' => $specialization?->id,
                        'name' => $serviceData['name'],
                        'description' => $serviceData['description'],
                        'is_active' => $serviceData['is_active'],
                    ]
                );
            }
        }

        $this->command->info('✅ تم إنشاء الخدمات بنجاح');
    }
}

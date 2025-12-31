<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * نقل البيانات من employee_specialization إلى employee_category
     * بناءً على services التي يستخدمها الموظف
     */
    public function up(): void
    {
        // نقل البيانات: لكل موظف له specialization، نربطه بالفئات المناسبة
        // من خلال services التي تستخدم هذا التخصص
        
        $employeeSpecializations = DB::table('employee_specialization')->get();
        
        foreach ($employeeSpecializations as $employeeSpecialization) {
            // الحصول على services التي تستخدم هذا التخصص
            $services = DB::table('services')
                ->where('specialization_id', $employeeSpecialization->specialization_id)
                ->get();
            
            // لكل service، الحصول على category_id من خلال sub_category
            $categoryIds = [];
            foreach ($services as $service) {
                $subCategory = DB::table('sub_categories')
                    ->where('id', $service->sub_category_id)
                    ->first();
                
                if ($subCategory && !in_array($subCategory->category_id, $categoryIds)) {
                    $categoryIds[] = $subCategory->category_id;
                }
            }
            
            // إضافة العلاقة بين الموظف والفئات
            foreach ($categoryIds as $categoryId) {
                // التحقق من عدم وجود العلاقة مسبقاً
                $exists = DB::table('employee_category')
                    ->where('employee_id', $employeeSpecialization->employee_id)
                    ->where('category_id', $categoryId)
                    ->exists();
                
                if (!$exists) {
                    DB::table('employee_category')->insert([
                        'employee_id' => $employeeSpecialization->employee_id,
                        'category_id' => $categoryId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لا يمكن استرجاع البيانات بدقة، لذا نترك الجدول فارغاً
        DB::table('employee_category')->truncate();
    }
};

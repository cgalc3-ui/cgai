<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدم أدمن
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'المشرف الرئيسي',
                'phone' => '0501234567',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_ADMIN,
                'phone_verified_at' => now(),
            ]
        );

        if ($admin->wasRecentlyCreated) {
            $this->command->info('✅ تم إنشاء مستخدم الأدمن بنجاح');
            $this->command->table(
                ['المجال', 'القيمة'],
                [
                    ['الاسم', $admin->name],
                    ['البريد الإلكتروني', $admin->email],
                    ['رقم الهاتف', $admin->phone],
                    ['النوع', $admin->role],
                    ['كلمة المرور', 'password123'],
                ]
            );
        } else {
            $this->command->warn('⚠️  مستخدم الأدمن موجود بالفعل');
        }

        // إنشاء مستخدم موظف
        $staff = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'الموظف الأول',
                'phone' => '0507654321',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_STAFF,
                'phone_verified_at' => now(),
            ]
        );

        if ($staff->wasRecentlyCreated) {
            $this->command->info('✅ تم إنشاء مستخدم الموظف بنجاح');
            $this->command->table(
                ['المجال', 'القيمة'],
                [
                    ['الاسم', $staff->name],
                    ['البريد الإلكتروني', $staff->email],
                    ['رقم الهاتف', $staff->phone],
                    ['النوع', $staff->role],
                    ['كلمة المرور', 'password123'],
                ]
            );
        } else {
            $this->command->warn('⚠️  مستخدم الموظف موجود بالفعل');
        }

        // إنشاء مستخدم عميل للاختبار (API)
        $customer = User::firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'العميل الأول',
                'phone' => '0501111111',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_CUSTOMER,
                'phone_verified_at' => now(),
            ]
        );

        if ($customer->wasRecentlyCreated) {
            $this->command->info('✅ تم إنشاء مستخدم العميل بنجاح');
        } else {
            $this->command->warn('⚠️  مستخدم العميل موجود بالفعل');
        }

        $this->command->newLine();
        $this->command->info('🎉 تم إنشاء جميع المستخدمين بنجاح!');
        $this->command->newLine();
        $this->command->comment('📝 بيانات تسجيل الدخول:');
        $this->command->table(
            ['النوع', 'البريد الإلكتروني', 'كلمة المرور'],
            [
                ['أدمن', 'admin@example.com', 'password123'],
                ['موظف', 'staff@example.com', 'password123'],
                ['عميل', 'customer@example.com', 'password123'],
            ]
        );
    }
}

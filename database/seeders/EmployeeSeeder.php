<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create staff users and link them as employees
        $employees = [
            [
                'user' => [
                    'name' => 'أحمد البرمجي',
                    'email' => 'ahmed@employee.com',
                    'phone' => '0501111112',
                    'password' => Hash::make('password123'),
                    'role' => User::ROLE_STAFF,
                    'phone_verified_at' => now(),
                ],
                'employee' => [
                    'specialization' => 'backend',
                    'bio' => 'مطور باك اند متخصص في Laravel و Node.js',
                    'hourly_rate' => 500.00,
                    'is_available' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'سارة المطورة',
                    'email' => 'sara@employee.com',
                    'phone' => '0502222223',
                    'password' => Hash::make('password123'),
                    'role' => User::ROLE_STAFF,
                    'phone_verified_at' => now(),
                ],
                'employee' => [
                    'specialization' => 'frontend',
                    'bio' => 'مطورة فرونت اند متخصصة في React و Vue.js',
                    'hourly_rate' => 450.00,
                    'is_available' => true,
                ],
            ],
            [
                'user' => [
                    'name' => 'محمد الموبايل',
                    'email' => 'mohammed@employee.com',
                    'phone' => '0503333334',
                    'password' => Hash::make('password123'),
                    'role' => User::ROLE_STAFF,
                    'phone_verified_at' => now(),
                ],
                'employee' => [
                    'specialization' => 'mobile',
                    'bio' => 'مطور موبايل متخصص في Flutter و React Native',
                    'hourly_rate' => 550.00,
                    'is_available' => true,
                ],
            ],
        ];

        foreach ($employees as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['user']['email']],
                $data['user']
            );

            if ($user->wasRecentlyCreated || !$user->employee) {
                Employee::updateOrCreate(
                    ['user_id' => $user->id],
                    $data['employee']
                );
            }
        }

        $this->command->info('✅ تم إنشاء الموظفين بنجاح');
    }
}

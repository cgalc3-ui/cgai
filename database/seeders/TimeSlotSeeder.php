<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();

        if ($employees->isEmpty()) {
            $this->command->warn('⚠️  لا يوجد موظفين. يرجى تشغيل EmployeeSeeder أولاً.');
            return;
        }

        // Generate time slots for the next 30 days
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(30);

        foreach ($employees as $employee) {
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                // Skip weekends (Friday and Saturday)
                if ($currentDate->dayOfWeek == Carbon::FRIDAY || $currentDate->dayOfWeek == Carbon::SATURDAY) {
                    $currentDate->addDay();
                    continue;
                }

                // Generate time slots from 9 AM to 5 PM (every hour)
                $startTime = Carbon::parse($currentDate->format('Y-m-d') . ' 09:00:00');
                $endTime = Carbon::parse($currentDate->format('Y-m-d') . ' 17:00:00');

                while ($startTime->lt($endTime)) {
                    $slotEndTime = $startTime->copy()->addHour();

                    TimeSlot::create([
                        'employee_id' => $employee->id,
                        'date' => $currentDate->format('Y-m-d'),
                        'start_time' => $startTime->format('H:i:s'),
                        'end_time' => $slotEndTime->format('H:i:s'),
                        'is_available' => true,
                    ]);

                    $startTime->addHour();
                }

                $currentDate->addDay();
            }
        }

        $this->command->info('✅ تم إنشاء الأوقات المتاحة بنجاح');
    }
}

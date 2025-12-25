<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin {--email=admin@example.com} {--password=password123} {--name=Admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->error('User with email ' . $email . ' already exists!');
            if ($this->confirm('Do you want to update the existing user to admin?', true)) {
                $existingUser->update([
                    'role' => User::ROLE_ADMIN,
                    'password' => Hash::make($password),
                ]);
                $this->info('User updated successfully!');
                return 0;
            }
            return 1;
        }

        // Create admin user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => '0500000000',
            'password' => Hash::make($password),
            'role' => User::ROLE_ADMIN,
            'phone_verified_at' => now(),
        ]);

        $this->info('Admin user created successfully!');
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Phone', $user->phone],
                ['Role', $user->role],
                ['Password', $password],
            ]
        );

        return 0;
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default teachers
        $teachers = [
            ['teacher1', 'Teacher One', 'teacher1@example.com', '1234567890'],
            ['teacher2', 'Teacher Two', 'teacher2@example.com', '0987654321']
        ];

        foreach ($teachers as $teacher) {
            User::firstOrCreate(
                ['username' => $teacher[0]],
                [
                    'name' => $teacher[1], // Add the name field
                    'fullname' => $teacher[1],
                    'email' => $teacher[2],
                    'phone' => $teacher[3],
                    'password' => Hash::make('123456a@A'),
                    'role' => 'teacher',
                ]
            );
        }

        // Create default students
        $students = [
            ['student1', 'Student One', 'student1@example.com', '1122334455'],
            ['student2', 'Student Two', 'student2@example.com', '5544332211']
        ];

        foreach ($students as $student) {
            User::firstOrCreate(
                ['username' => $student[0]],
                [
                    'name' => $student[1], // Add the name field
                    'fullname' => $student[1],
                    'email' => $student[2],
                    'phone' => $student[3],
                    'password' => Hash::make('123456a@A'),
                    'role' => 'student',
                ]
            );
        }
    }
}

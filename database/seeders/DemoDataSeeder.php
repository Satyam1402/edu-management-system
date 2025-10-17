<?php
// database/seeders/DemoDataSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Franchise;
use App\Models\Student;
use App\Models\Course;
use App\Models\Certificate;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample franchises
        $franchises = [
            [
                'name' => 'Tech Academy Mumbai',
                'code' => 'TECH001',
                'email' => 'mumbai@techacademy.com',
                'phone' => '+91-9876543210',
                'address' => 'Andheri West, Mumbai, Maharashtra',
                'status' => 'active'
            ],
            [
                'name' => 'Skill Center Delhi',
                'code' => 'SKILL002',
                'email' => 'delhi@skillcenter.com',
                'phone' => '+91-9876543211',
                'address' => 'Connaught Place, New Delhi',
                'status' => 'active'
            ],
            [
                'name' => 'Learning Hub Bangalore',
                'code' => 'LEARN003',
                'email' => 'bangalore@learninghub.com',
                'phone' => '+91-9876543212',
                'address' => 'Koramangala, Bangalore, Karnataka',
                'status' => 'inactive'
            ]
        ];

        foreach ($franchises as $franchiseData) {
            $franchise = Franchise::create($franchiseData);

            // Create 10-15 students per franchise
            $studentCount = rand(10, 15);
            for ($i = 1; $i <= $studentCount; $i++) {
                Student::create([
                    'franchise_id' => $franchise->id,
                    'name' => 'Student ' . $i . ' - ' . $franchise->code,
                    'email' => strtolower(str_replace(' ', '', 'student' . $i . $franchise->code)) . '@example.com',
                    'phone' => '+91-98765432' . sprintf('%02d', $i),
                    'status' => ['enquiry', 'admission', 'active'][rand(0, 2)]
                ]);
            }
        }

        // Create sample courses
        $courses = [
            [
                'name' => 'Web Development Bootcamp',
                'code' => 'WEB001',
                'description' => 'Complete full-stack web development course',
                'fee' => 25000.00,
                'duration_months' => 6,
                'status' => 'active'
            ],
            [
                'name' => 'Digital Marketing Mastery',
                'code' => 'DM002',
                'description' => 'Comprehensive digital marketing course',
                'fee' => 15000.00,
                'duration_months' => 3,
                'status' => 'active'
            ],
            [
                'name' => 'Data Science Fundamentals',
                'code' => 'DS003',
                'description' => 'Introduction to data science and analytics',
                'fee' => 30000.00,
                'duration_months' => 8,
                'status' => 'active'
            ]
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }

        // Create some sample certificates
        $students = Student::take(10)->get();
        $courses = Course::all();

        foreach ($students as $student) {
            Certificate::create([
                'student_id' => $student->id,
                'course_id' => $courses->random()->id,
                'number' => 'CERT-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'status' => ['requested', 'approved', 'issued'][rand(0, 2)],
                'issued_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null
            ]);
        }
    }
}

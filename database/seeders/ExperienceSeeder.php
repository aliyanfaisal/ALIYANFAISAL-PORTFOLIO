<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $experiences = [
            [
                'title' => 'Full Stack Web Developer',
                'company' => 'Freelance (Upwork & Fiverr)',
                'description' => 'Working as an independent full-stack web developer delivering custom web-based solutions for international clients. Designing, developing, and maintaining applications using PHP (Laravel) and WordPress, including custom themes, plugins, and back-end architectures. Implementing RESTful APIs, database-driven systems, and server-side business logic, with a focus on performance, security, and maintainability.',
                'employment_type' => 'Freelance',
                'start_date' => '2020-12-09',
                'end_date' => null,
                'current' => true,
            ],
            [
                'title' => 'Web Developer & Data Scientist',
                'company' => 'Planning & Development Department, Gilgit-Baltistan',
                'description' => 'Collaborated with the Planning & Development Department on the Data Gathering and Analytics Dashboard initiative. Led the digitization of paper-based workflows, streamlining the collection, management, and analysis of project data across Gilgit-Baltistan.',
                'employment_type' => 'Freelance',
                'start_date' => '2025-02-01',
                'end_date' => '2025-07-31',
                'current' => false,
            ],
            [
                'title' => 'IT and Web Expert',
                'company' => 'Miami Castle Beach',
                'description' => 'Developed and managed WordPress and Flask websites for a Miami Beach organization managing castle rentals, including server configuration and security.',
                'employment_type' => 'Part-time',
                'start_date' => '2023-08-02',
                'end_date' => '2024-11-09',
                'current' => false,
            ],
            [
                'title' => 'Full Stack Web Developer (Team Lead)',
                'company' => 'GreyMatter Ventures',
                'description' => 'Worked for over 2 years on custom full-stack web application development using Custom WordPress and the Laravel framework, later leading the web development team.',
                'employment_type' => 'Part-time',
                'start_date' => '2021-01-01',
                'end_date' => '2023-01-01',
                'current' => false,
            ],
        ];

        foreach ($experiences as $i => $experience) {
            $experience['sort_order'] = $i;
            Experience::updateOrCreate(
                ['title' => $experience['title'], 'company' => $experience['company']],
                $experience
            );
        }
    }
}

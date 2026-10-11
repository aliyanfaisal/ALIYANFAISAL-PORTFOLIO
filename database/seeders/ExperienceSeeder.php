<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    /** Rows whose title/company changed: renamed in place so re-seeding never duplicates them. */
    private const RENAMES = [
        ['from' => ['title' => 'Founder & CEO', 'company' => 'UrbanSofts'], 'to' => ['company' => 'UrbanSofts (remote software development team)']],
        ['from' => ['title' => 'Full Stack Web Developer', 'company' => 'Freelance (Upwork & Fiverr)'], 'to' => ['title' => 'Applied AI & Backend Engineer', 'company' => 'Freelance (Fiverr & Upwork)']],
        ['from' => ['title' => 'Web Developer & Data Scientist', 'company' => 'Planning & Development Department, Gilgit-Baltistan'], 'to' => ['title' => 'Backend Developer & Data Analyst']],
        ['from' => ['title' => 'IT and Web Expert', 'company' => 'Miami Castle Beach'], 'to' => ['title' => 'IT & Web Expert']],
    ];

    public function run(): void
    {
        foreach (self::RENAMES as $rename) {
            Experience::where($rename['from'])->update($rename['to']);
        }

        $experiences = [
            [
                'title' => 'Founder & CEO',
                'company' => 'UrbanSofts (remote software development team)',
                'description' => 'Founded and lead a remote team of 4–10 developers that delivers client software projects together: I take on projects and bring in the right team members for each, such as app developers, and we build them collaboratively. The team has served 50+ clients.',
                'employment_type' => 'Founder',
                'start_date' => '2022-01-01',
                'end_date' => null,
                'current' => true,
            ],
            [
                'title' => 'Backend Developer & Data Analyst',
                'company' => 'Planning & Development Department, Gilgit-Baltistan',
                'description' => 'A project engagement of about 4–5 months: built the Data Gathering and Analytics Dashboard for the Planning & Development Department, Government of Gilgit-Baltistan: centralized dashboards over public-sector records (XLS, CSV, MySQL) using Python (Pandas) and a Laravel backend. Led the digitization of paper-based workflows into structured reporting that supports regional planning decisions.',
                'employment_type' => 'Freelance',
                'start_date' => '2024-01-01',
                'end_date' => '2025-07-31',
                'current' => false,
            ],
            [
                'title' => 'IT & Web Expert',
                'company' => 'Miami Castle Beach',
                'description' => 'Developed and managed WordPress and Flask websites for a Miami Beach organization managing castle rentals, including server configuration and security.',
                'employment_type' => 'Part-time · Remote',
                'start_date' => '2023-08-02',
                'end_date' => '2024-11-09',
                'current' => false,
            ],
            [
                'title' => 'Full Stack Web Developer (Team Lead)',
                'company' => 'GreyMatter Ventures',
                'description' => 'Custom full-stack web application development with Custom WordPress and the Laravel framework, designing backend architectures and relational schemas. Later led the web development team, mentoring junior developers and remote interns on code optimization, system design and deployment.',
                'employment_type' => 'Full-time',
                'start_date' => '2020-09-09',
                'end_date' => '2022-02-14',
                'current' => false,
            ],
            [
                'title' => 'Applied AI & Backend Engineer',
                'company' => 'Freelance (Fiverr & Upwork)',
                'description' => 'Working as an independent full-stack web developer delivering custom web-based solutions for international clients. Designing, developing, and maintaining applications using PHP (Laravel) and WordPress, including custom themes, plugins, and back-end architectures. Implementing RESTful APIs, database-driven systems, and server-side business logic, with a focus on performance, security, and maintainability.',
                'employment_type' => 'Freelance',
                'start_date' => '2020-12-09',
                'end_date' => null,
                'current' => true,
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

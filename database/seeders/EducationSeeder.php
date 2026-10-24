<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            [
                'degree' => 'BSc, Information Technology',
                'school' => 'Karakoram International University',
                'country' => 'Gilgit-Baltistan, Pakistan',
                'from_year' => null,
                'to_year' => 2024,
                'cgpa' => '3.80 / 4.00',
            ],
        ];

        foreach ($records as $i => $record) {
            $record['sort_order'] = $i;
            Education::updateOrCreate(
                ['degree' => $record['degree'], 'school' => $record['school']],
                $record
            );
        }
    }
}

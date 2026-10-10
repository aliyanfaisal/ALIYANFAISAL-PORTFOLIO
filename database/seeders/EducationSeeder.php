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
                'from_year' => 2019,
                'to_year' => 2023,
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

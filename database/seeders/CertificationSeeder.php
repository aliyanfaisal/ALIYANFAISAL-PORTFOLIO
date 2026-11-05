<?php

namespace Database\Seeders;

use App\Models\CertificationRecord;
use Illuminate\Database\Seeder;

class CertificationSeeder extends Seeder
{
    public function run(): void
    {
        $certifications = [
            ['name' => 'Advanced Web Development', 'issuer' => 'Coursera', 'year' => 2020],
            ['name' => 'PHP', 'issuer' => 'Sololearn / Udemy', 'year' => 2018],
            ['name' => 'E-Commerce', 'issuer' => 'Udemy', 'year' => 2020],
            ['name' => 'PHP Laravel', 'issuer' => 'Udemy', 'year' => 2019],
            ['name' => 'Custom WordPress Development', 'issuer' => 'GreyMatter Ventures', 'year' => 2021],
        ];

        foreach ($certifications as $i => $certification) {
            $certification['sort_order'] = $i;
            CertificationRecord::updateOrCreate(
                ['name' => $certification['name'], 'issuer' => $certification['issuer']],
                $certification
            );
        }
    }
}

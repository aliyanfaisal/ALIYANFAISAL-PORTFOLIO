<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(['id' => 1], [
            'site_name' => 'Aliyan Faisal',
            'site_description' => 'Aliyan Faisal is a full-stack developer and AI/LLM systems engineer building LLM integrations, RAG chatbots, automations and production web apps for clients worldwide. 5+ years experience.',
            'contact_email' => 'aliyanfaisal15@gmail.com',
            'github_url' => 'https://github.com/aliyanfaisal',
            'linkedin_url' => 'https://www.linkedin.com/in/aliyan-faisal-5162261b7/',
            'fiverr_url' => 'https://www.fiverr.com/aliyanfaisal',
            'upwork_url' => 'https://www.upwork.com/freelancers/~01f763ee3322eda908',
        ]);
    }
}

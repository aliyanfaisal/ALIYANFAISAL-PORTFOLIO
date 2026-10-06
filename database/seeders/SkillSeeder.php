<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['name' => 'PHP / Laravel', 'category' => 'Backend', 'proficiency' => 95],
            ['name' => 'AI Integration (OpenAI, Claude, Gemini, Groq)', 'category' => 'AI & Automation', 'proficiency' => 90],
            ['name' => 'WordPress', 'category' => 'CMS', 'proficiency' => 90],
            ['name' => 'JavaScript / jQuery / React / Node.js / Next.js', 'category' => 'Frontend', 'proficiency' => 75],
            ['name' => 'Server Configuration & Linux', 'category' => 'DevOps', 'proficiency' => 80],

            ['name' => 'REST & GraphQL APIs', 'category' => 'Backend', 'proficiency' => 90],
            ['name' => 'AI Workflow Automation', 'category' => 'AI & Automation', 'proficiency' => 85],
            ['name' => 'WooCommerce', 'category' => 'CMS', 'proficiency' => 88],
            ['name' => 'Tailwind CSS / Bootstrap', 'category' => 'Frontend', 'proficiency' => 85],
            ['name' => 'Git & Version Control', 'category' => 'DevOps', 'proficiency' => 90],

            ['name' => 'Debugging', 'category' => 'Backend', 'proficiency' => 92],
            ['name' => 'AI Chatbot Development', 'category' => 'AI & Automation', 'proficiency' => 88],
            ['name' => 'WordPress Plugin Development', 'category' => 'CMS', 'proficiency' => 90],
            ['name' => 'CI/CD & Deployment', 'category' => 'DevOps', 'proficiency' => 80],

            ['name' => 'MySQL', 'category' => 'Backend', 'proficiency' => 88],
            ['name' => 'Prompt Engineering', 'category' => 'AI & Automation', 'proficiency' => 85],
            ['name' => 'Elementor Pro', 'category' => 'CMS', 'proficiency' => 90],
            ['name' => 'Alpine.js', 'category' => 'Frontend', 'proficiency' => 85],
            ['name' => 'Nginx / Apache', 'category' => 'DevOps', 'proficiency' => 82],

            ['name' => 'RAG Systems (Embeddings, Vector Search)', 'category' => 'AI & Automation', 'proficiency' => 85],
            ['name' => 'LangChain', 'category' => 'AI & Automation', 'proficiency' => 80],
            ['name' => 'PostgreSQL', 'category' => 'Backend', 'proficiency' => 80],
            ['name' => 'MongoDB', 'category' => 'Backend', 'proficiency' => 75],
            ['name' => 'Redis', 'category' => 'Backend', 'proficiency' => 75],
            ['name' => 'NumPy & Pandas', 'category' => 'AI & Automation', 'proficiency' => 75],
            ['name' => 'Docker', 'category' => 'DevOps', 'proficiency' => 78],

            ['name' => 'Website Security Hardening', 'category' => 'Security & Performance', 'proficiency' => 88],
            ['name' => 'Performance & Speed Optimization', 'category' => 'Security & Performance', 'proficiency' => 90],
            ['name' => 'Caching & Core Web Vitals', 'category' => 'Security & Performance', 'proficiency' => 85],
            ['name' => 'SSL & Backup Management', 'category' => 'Security & Performance', 'proficiency' => 82],
        ];

        Skill::query()->delete();

        foreach ($skills as $i => $skill) {
            $skill['sort_order'] = $i;
            Skill::create($skill);
        }
    }
}

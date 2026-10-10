<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Testimonial;
use App\Services\GithubService;

class HomeController extends Controller
{
    public function index(GithubService $github)
    {
        return view('home', [
            'featuredProjects' => Project::where('featured', true)->inRandomOrder()->take(3)->get(),
            'featuredServices' => Service::where('featured', true)->orderBy('sort_order')->take(3)->get(),
            'aiService' => Service::where('category', 'AI Development')->first(),
            'skills' => Skill::orderBy('sort_order')->take(12)->get(),
            'allSkills' => Skill::orderBy('sort_order')->get(),
            'testimonials' => Testimonial::inRandomOrder()->get(),
            'githubProfile' => $github->profile(),
        ]);
    }
}

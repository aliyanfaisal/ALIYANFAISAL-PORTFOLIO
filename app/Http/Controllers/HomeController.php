<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Skill;
use App\Services\GithubService;

class HomeController extends Controller
{
    public function index(GithubService $github)
    {
        return view('home', [
            // Same limit as the Projects page: the GitHub service caches the list regardless of $limit.
            'featuredRepos' => array_slice($github->repositories(9), 0, 6),
            'latestPosts' => BlogPost::published()->with('categories')->orderByDesc('published_at')->take(3)->get(),
            'skills' => Skill::orderBy('sort_order')->take(12)->get(),
            'allSkills' => Skill::orderBy('sort_order')->get(),
            'githubProfile' => $github->profile(),
        ]);
    }
}

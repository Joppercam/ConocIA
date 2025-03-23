<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;

class AuthorController extends Controller
{
    public function show($name)
    {
        $articles = News::where('author', $name)->paginate(10);
        return view('authors.show', compact('articles', 'name'));
    }
}
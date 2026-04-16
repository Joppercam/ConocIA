<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function show(string $author)
    {
        $articles = News::with('category')
            ->where('author', $author)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(12);

        // Intentar encontrar el usuario por nombre para mostrar su perfil
        $authorUser = User::where('name', $author)
            ->orWhere('username', $author)
            ->first();

        $authorName = $authorUser?->name ?? $author;

        return view('authors.show', compact('articles', 'authorName', 'authorUser'));
    }
}

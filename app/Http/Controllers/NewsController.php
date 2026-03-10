<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\HeroSlide;

class NewsController extends Controller
{
    public function index()
    {
        $newsItems = News::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->get();

        $news = $newsItems->map(function ($item) {
            return [
                'slug'     => $item->slug,
                'title'    => $item->title,
                'excerpt'  => $item->excerpt ?? \Str::limit(strip_tags($item->content), 160),
                'date'     => $item->published_at?->translatedFormat('d M Y') ?? '-',
                'category' => $item->category ?? 'umum',
                'icon'     => '📰',
                'images'   => $item->images ?? [],
            ];
        })->toArray();

        // ===== HERO SLIDES dari DB =====
        $heroSlides = HeroSlide::getSlides('news');

        return view('news.index', compact('news', 'heroSlides'));
    }

    public function show($slug)
    {
        $item = News::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('news.show', compact('item'));
    }
}
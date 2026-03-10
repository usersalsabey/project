<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\SiteProfile;
use App\Models\HeroSlide;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil profil & kontak dari DB
        $profileData = SiteProfile::first();

        $vision  = $profileData?->visi  ?? 'Terwujudnya Polri yang Presisi';
        $mission = $profileData?->misi  ?? [
            'Menjaga keamanan dan ketertiban masyarakat',
            'Menegakkan hukum secara profesional dan proporsional',
            'Memberikan perlindungan, pengayoman, dan pelayanan kepada masyarakat',
            'Membina ketentraman masyarakat dengan memperhatikan norma dan kearifan lokal',
        ];
        if (is_string($mission)) {
            $mission = json_decode($mission, true) ?? [$mission];
        }

        // Berita terbaru (3 artikel)
        $newsItems = News::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        $news = $newsItems->map(function ($item) {
            return [
                'slug'     => $item->slug,
                'title'    => $item->title,
                'content'  => $item->content,
                'excerpt'  => $item->excerpt ?? \Str::limit(strip_tags($item->content), 130),
                'date'     => $item->published_at?->translatedFormat('d M Y') ?? '-',
                'category' => $item->category ?? 'umum',
                'icon'     => '📰',
                'images'   => $item->images ?? [],
            ];
        })->toArray();

        // Kontak
        $contact = [
            'address' => $profileData?->address ?? 'Jln. MGR Sugiyopranoto No.15, Wonosari',
            'city'    => $profileData?->city    ?? 'Kabupaten Gunungkidul, D.I. Yogyakarta 55813',
            'email'   => $profileData?->email   ?? 'ppidgunungkidul@gmail.com',
            'phone'   => $profileData?->phone   ?? '0851-3375-0875',
            'hotline' => $profileData?->hotline ?? '110 (Darurat)',
            'hours'   => $profileData?->hours   ?? '24 Jam',
        ];

        // Sosial media
        $socialMedia = [];

        // Navigasi footer
        $aboutLinks = [
            ['name' => 'Beranda',             'url' => route('home')],
            ['name' => 'Profil',              'url' => route('profile')],
            ['name' => 'Tribratanews',        'url' => route('news')],
            ['name' => 'Informasi Pelayanan', 'url' => route('information')],
        ];

        // ===== HERO SLIDES dari DB =====
        $heroSlides = HeroSlide::getSlides('beranda');

        return view('home', compact(
            'vision',
            'mission',
            'news',
            'contact',
            'socialMedia',
            'aboutLinks',
            'heroSlides'
        ));
    }
}
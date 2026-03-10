<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InformasiPelayanan;
use App\Models\Profile;
use App\Models\HeroSlide;

class InformasiPelayananController extends Controller
{
    public function index()
    {
        $profileData = Profile::first();

        // ===== DATA PELAYANAN DARI DB =====
        $pelayananItems = InformasiPelayanan::where('is_active', true)
            ->orderBy('urutan')
            ->get();

        // Kontak
        $contact = [
            'address' => $profileData?->alamat        ?? 'Jln. MGR Sugiyopranoto No.15, Wonosari',
            'city'    => 'Kabupaten Gunungkidul, D.I. Yogyakarta 55813',
            'email'   => $profileData?->email         ?? 'ppidgunungkidul@gmail.com',
            'phone'   => $profileData?->telepon       ?? '0851-3375-0875',
            'hotline' => '110 (Darurat)',
            'hours'   => $profileData?->jam_pelayanan ?? '24 Jam',
        ];

        // Navigasi footer
        $aboutLinks = [
            ['name' => 'Beranda',             'url' => route('home')],
            ['name' => 'Profil',              'url' => route('profile')],
            ['name' => 'Tribratanews',        'url' => route('news')],
            ['name' => 'Informasi Pelayanan', 'url' => route('information')],
        ];

        // Hero slides dari DB
        $heroSlides = HeroSlide::getSlides('informasi-pelayanan');

        return view('informasi-pelayanan.index', compact(
            'pelayananItems',
            'contact',
            'aboutLinks',
            'heroSlides'
        ));
    }

    public function sim()
    {
        return view('informasi-pelayanan.sim');
    }

    public function perpusdata()
    {
        return view('informasi-pelayanan.perpusdata');
    }
}
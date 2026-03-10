<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\HeroSlide;

class ProfileController extends Controller
{
    public function index()
    {
        $profileData = Profile::first();

        $misiRaw = $profileData?->misi ?? [];
        // Filament Repeater menyimpan misi sebagai [['isi' => '...'], ...]
        $misiArray = collect($misiRaw)->pluck('isi')->filter()->values()->toArray();

        $profile = [
            'nama_instansi'       => $profileData?->nama_instansi      ?? 'Polres Gunungkidul',
            'kapolres'            => $profileData?->kapolres            ?? 'AKBP ...',
            'foto_kapolres'       => $profileData?->foto_kapolres       ?? null,
            'sambutan'            => $profileData?->sambutan            ?? null,
            'visi'                => $profileData?->visi                ?? 'Terwujudnya Polri yang Presisi',
            'misi'                => $misiArray,
            'sejarah'             => $profileData?->sejarah             ?? null,
            'struktur_organisasi' => $profileData?->struktur_organisasi ?? null,
        ];

        // Kontak — field di tabel profiles: telepon, alamat, jam_pelayanan
        $contact = [
            'address' => $profileData?->alamat         ?? 'Jln. MGR Sugiyopranoto No.15, Wonosari',
            'city'    => 'Kabupaten Gunungkidul, D.I. Yogyakarta 55813',
            'email'   => $profileData?->email          ?? 'ppidgunungkidul@gmail.com',
            'phone'   => $profileData?->telepon        ?? '0851-3375-0875',
            'hotline' => '110 (Darurat)',
            'hours'   => $profileData?->jam_pelayanan  ?? '24 Jam',
        ];

        // Navigasi footer
        $aboutLinks = [
            ['name' => 'Beranda',             'url' => route('home')],
            ['name' => 'Profil',              'url' => route('profile')],
            ['name' => 'Tribratanews',        'url' => route('news')],
            ['name' => 'Informasi Pelayanan', 'url' => route('information')],
        ];

        // Hero slides dari DB
        $heroSlides = HeroSlide::getSlides('profil');

        return view('profile.index', compact(
            'profile',
            'contact',
            'aboutLinks',
            'heroSlides'
        ));
    }
}
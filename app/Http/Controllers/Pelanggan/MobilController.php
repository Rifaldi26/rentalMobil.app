<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Mobil;

class MobilController extends Controller
{
    public function show(Mobil $mobil)
    {
        $isFav = auth()->check()
            ? auth()->user()->hasFavorited($mobil->id)
            : false;

        return view('users.mobil.show', compact('mobil', 'isFav'));
    }
}
<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('users.dashboard');
    }
}
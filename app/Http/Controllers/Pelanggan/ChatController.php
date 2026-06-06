<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function index()
    {
        return view('users.chat');
    }
}
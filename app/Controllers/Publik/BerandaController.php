<?php

namespace App\Controllers\Publik;

use App\Controllers\BaseController;

class BerandaController extends BaseController
{
    public function index()
    {
        return view('publik/beranda');
    }
    
    public function desa($slug)
    {
        return view('publik/beranda');
    }
}

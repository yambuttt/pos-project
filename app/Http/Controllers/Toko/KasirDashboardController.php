<?php

namespace App\Http\Controllers\Toko;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KasirDashboardController extends Controller
{
    public function index()
    {
        return view('toko.kasir.dashboard');
    }
}

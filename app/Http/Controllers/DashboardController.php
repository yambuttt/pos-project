<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('dashboard.admin');
    }

    public function kasir()
    {
        return view('dashboard.kasir');
    }
}

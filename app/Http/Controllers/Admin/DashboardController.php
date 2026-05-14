<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Nantinya di sini kita mengambil data total menu, kriteria, dll dari database
        return view('admin.dashboard');
    }
}
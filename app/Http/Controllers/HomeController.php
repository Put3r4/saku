<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Criterion;
use App\Models\Menu;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function tentangSaw()
    {
        $criteria = Criterion::all();
        return view('pages.tentang-saw', compact('criteria'));
    }

    public function daftarMenu(Request $request)
    {
        $search = $request->get('search');
        
        $query = Menu::where('is_available', true);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('menu_name', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $menus = $query->paginate(12)->withQueryString();
        
        return view('pages.daftar-menu', compact('menus', 'search'));
    }
}
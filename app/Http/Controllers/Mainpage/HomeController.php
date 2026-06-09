<?php
namespace App\Http\Controllers\Mainpage;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function home()
    {
        return view('mainpage.home');
    }
}


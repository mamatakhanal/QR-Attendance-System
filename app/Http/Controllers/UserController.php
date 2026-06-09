<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    function getUserName($name){
        return $name.", Mamata Khanal";
    }
    function homePage(){
        return view('welcome');
    }
    function adminLogin(){
        return view('admin.login');
    }
}

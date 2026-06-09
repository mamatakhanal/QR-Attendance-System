<?php

namespace App\Models\Admin;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Login extends Authenticatable
{
    protected $table = 'admin';
    protected $fillable = ['name','email','password'];
    protected $hidden = ['password'];
}
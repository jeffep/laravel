<?php

//********************
// Aparently I never set this up - 7/30/2025
//

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }
}

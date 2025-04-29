<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class HomeController extends Controller
{
    public function index()
    {
        $planes = Plan::all();
        return view('home', ['planes' => $planes]);
    }
    public function plandetail($id)
    {
        $plan = Plan::where('id', $id)->first();
        return view('plandetail', ['plan' => $plan]);
    }
}

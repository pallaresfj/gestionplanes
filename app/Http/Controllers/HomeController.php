<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Plan::orderBy('updated_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 3);
        $planes = $query->paginate($perPage)->withQueryString();

        return view('home', ['planes' => $planes]);
    }
    public function plandetail($id)
    {
        $plan = Plan::where('id', $id)->first();
        return view('plandetail', ['plan' => $plan]);
    }
}

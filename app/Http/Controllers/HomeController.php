<?php

namespace App\Http\Controllers;

use App\Models\Center;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Subject;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        return view('home');
    }
    public function planes(Request $request)
    {
        $query = Plan::orderBy('updated_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $planes = $query->paginate(3)->withQueryString();

        return view('planes', ['planes' => $planes]);
    }
    public function centers(Request $request)
    {
        $query = Center::orderBy('updated_at', 'desc');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $centers = $query->paginate(3)->withQueryString();

        return view('centers', ['centers' => $centers]);
    }
    public function centerdetail($id)
    {
        $center = Center::where('id', $id)->first();
        return view('centerdetail', ['center' => $center]);
    }
    public function plandetail($id)
    {
        $plan = Plan::where('id', $id)->first();
        return view('plandetail', ['plan' => $plan]);
    }
    public function subjectdetail($id)
    {
        $subject = Subject::where('id', $id)->first();
        return view('subjectdetail', ['subject' => $subject]);
    }
}

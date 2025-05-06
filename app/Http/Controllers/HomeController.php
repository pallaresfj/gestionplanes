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

        $perPage = $request->input('per_page', 3);
        $planes = $query->paginate($perPage)->withQueryString();

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
    /* public function centerdetail($id)
    {
        $center = Center::where('id', $id)->first();
        return view('centerdetail', ['center' => $center]);
    } */
    public function centerdetail($id)
    {
        // Traer solo el center sin cargar todas las relaciones grandes
        $center = Center::findOrFail($id);
    
        // Paginar las actividades
        $activities = $center->activities()->paginate(10, ['*'], 'activities_page');
    
        // Paginar los estudiantes
        $students = $center->students()->paginate(10, ['*'], 'students_page');
    
        // Paginar los presupuestos
        $budgets = $center->budgets()->paginate(10, ['*'], 'budgets_page');
    
        return view('centerdetail', [
            'center' => $center,
            'activities' => $activities,
            'students' => $students,
            'budgets' => $budgets,
        ]);
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

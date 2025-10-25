<?php

namespace App\Http\Controllers\Satisfaccion;

use Illuminate\Support\Facades\Auth;
use App\Models\Survey;

class DashboardController extends Controller
{
    public function admin()
    {
        $surveys = Survey::all();
        return view('satisfaccion.dashboard.admin', compact('surveys'));
    }

    public function student()
    {
        $surveys = Survey::where('state', 'Activa')->get();
        return view('satisfaccion.dashboard.student', compact('surveys'));
    }
}

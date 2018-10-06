<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use Illuminate\Http\Request;

class ProfessionController extends Controller
{
    public function index()
    {
        return view('professions.index',[
            'professions' => Profession::orderBy('title')->get()
        ]);
    }
}

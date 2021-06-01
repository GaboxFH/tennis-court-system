<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rules;

class RulesController extends Controller
{
    //index
    public function index()
    {
        return Rules::get();
    }
}

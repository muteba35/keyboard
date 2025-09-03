<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiTestController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'Connexion API Laravel OK',
            'date' => now()
        ]);
    }
}

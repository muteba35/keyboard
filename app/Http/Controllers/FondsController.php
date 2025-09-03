<?php

namespace App\Http\Controllers;
use Illuminate\View\View;


class FondsController extends Controller
{
 
    public function create(): View
    {
        return view('onglets.fond-blanc');
    }

}


<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;

class TesteController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'API está funcionando!']);
    }
}

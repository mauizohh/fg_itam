<?php

namespace App\Http\Controllers;

class AssetController extends Controller
{
    public function index()
    {
        return redirect()->route('assets.index');
    }
}

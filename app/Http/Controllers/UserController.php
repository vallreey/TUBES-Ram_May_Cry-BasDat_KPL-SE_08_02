<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.dashboard'); // sementara arahkan ke dashboard dulu
    }

    public function create()
    {
        return view('admin.dashboard');
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return view('admin.dashboard');
    }

    public function edit($id)
    {
        return view('admin.dashboard');
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}

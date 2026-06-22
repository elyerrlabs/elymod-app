<?php

namespace ElymodApp\App\Http\Controllers;

use App\Http\Controllers\WebController;

final class UserController extends WebController
{
    public function index()
    {
        return view('ElymodApp::user.index');
    }
}

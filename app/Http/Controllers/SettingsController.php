<?php

namespace ElymodApp\App\Http\Controllers;

use App\Http\Controllers\Web\Admin\Setting\SettingController as Controller;

final class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('userCanAny:settings:elymod-app:full,settings:elymod-app:view')->only('index');
        $this->middleware('userCanAny:settings:elymod-app:full,settings:elymod-app:update')->only('update');
    }

    /**
     * Index
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view("ElymodApp::settings.parts.general");
    }

}

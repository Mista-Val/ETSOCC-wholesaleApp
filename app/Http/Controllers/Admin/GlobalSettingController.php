<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GlobalSetting;
use Illuminate\Foundation\Validation\ValidatesRequests;

class GlobalSettingController extends Controller
{

    public function index(){

    	return view('admin.global-setting.index');
    }

    //show global setting edit form
    public function config()
    {
        
        return view('admin.global-setting.index');

    }
}

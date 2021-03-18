<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DebugController extends Controller
{
    //
	function migrate(Request $request){
		dump(Artisan::call("migrate"));
		dump(Artisan::output());
	}
}

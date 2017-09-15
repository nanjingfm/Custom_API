<?php

namespace App\Http\Controllers;

use App\CustomAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    //
    
    public function home()
    {
        $username = Session::get('username');
        $apis = [];
        if ($username) {
            $apis = CustomAPI::where('username', $username)->get();
        }
        return view('home', [
            'username' => $username,
            'apis' => $apis
        ]);
    }
    
    public function apiLogin(Request $request)
    {
        $username = $request->input('username');
        if ($username) {
            Session::put('username', $username);
        }
        return Redirect::route('home');
    }
    
    
    public function apiSave(Request $request)
    {
        $api_obj = new CustomAPI();
        if ($api_obj->store($request->all())) {
            return response()->json([
               'status' => 'success' 
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'msg' => $api_obj->getError()
            ]);
        }
    }
}

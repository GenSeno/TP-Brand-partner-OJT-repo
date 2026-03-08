<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Validator;

class WelcomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response('invalid', 400);
        }

        $email = $request->input('email');

        Subscriber::firstOrCreate([
            'email' => $email,
        ]);

        return response('success');
    }
}

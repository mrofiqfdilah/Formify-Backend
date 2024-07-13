<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Forms;
use App\Models\Allowed_domain;
use App\Models\Questions;

class FormController extends Controller
{
    public function create_form(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'name' => 'required',
       'slug' => 'required|unique:forms,slug|regex:/^[a-zA-Z0-9.-]+$/',
        'allowed_domains' => 'array'
        ]);

        if($validator->fails())
        {
            return response()->json([
            'message' => 'Invalid field',
            'errors' => $validator->errors()
            ], 422);
        }

        $form = Forms::create([
        'name' => $request->name,
        'slug' => $request->slug,
        'description' => $request->description,
        'limit_one_response' => $request->limit_one_response,
        'creator_id' => Auth::id()
        ]);

        foreach($request->allowed_domains as $dom){
            $allowed = new Allowed_domain;
            $allowed->form_id = $form->id;
            $allowed->domain = $dom;
            $allowed->save();
         }

        return response()->json([
        'message' => 'Create form success',
        'form' => [
            'name' => $form->name,
            'slug' => $form->slug,
            'description' => $form->description,
            'limit_one_response' => $form->limit_one_response,
            'creator_id' => $form->creator_id,
            'id' => $form->id
        ],
        ], 200);
    }

    public function all_form(Request $request)
    {
        $user = Auth::id();

        $getform = Forms::where('creator_id', $user)->get();

        return response()->json([
        'message' => 'Get all forms success',
        'forms' => $getform
        ], 200);
    }

    public function detail_form(Request $request, $slug)
    {
        $form = Forms::where('slug', $slug)->first();

       if(!$form){
        return response()->json([
            'message' => 'Form not found'
            ], 404);
       }

        $getdomain = Allowed_domain::where('form_id', $form->id)->pluck('domain');

        $getquestion = Questions::where('form_id', $form->id)->get();

        $detail = [
            'id' => $form->id,
            'name' => $form->name,
            'slug' => $form->slug,
            'description' => $form->description,
            'limit_one_response' => $form->limit_one_response,
            'creator_id' => $form->creator_id,
            'allowed_domains' => $getdomain,
            'questions' => $getquestion
        ];

        return response()->json([
        'message' => 'Get form success',
        'form' => $detail
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\allowed_domain;
use Illuminate\Http\Request;
use App\Models\Answers;
use App\Models\Responses;
use App\Models\Forms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ResponseController extends Controller
{
    public function submit_response(Request $request,$slug)
    {
        $form = Forms::where('slug', $slug)->first();

        if(!$form){
         return response()->json([
             'message' => 'Form not found'
             ], 404);
        }

        $validator = Validator::make($request->all(),[
        'answers' => 'array',
        ]);

        if($validator->fails()){
            return response()->json([
            'message' => 'Invalid field',
            'errors' => $validator->errors()
            ], 422);
        }

        $checkallowed = Responses::where('form_id', $form->id)->where('user_id',Auth::id())->first();

        if($form->limit_one_response)
        {
           if($checkallowed){
            return response()->json([
            'message' => 'You can not submit form twice'
            ], 422);
           }
        }
           
        $domain = Allowed_domain::where('form_id', $form->id)->pluck('domain')->toArray();
        $emailParts = explode('@', Auth::user()->email); 
        $dumb = $emailParts[1]; 
        
        if (!in_array($dumb, $domain)) {
            return response()->json([
                'message' => 'Forbidden Access'
            ], 403);
        }
       
        $response = new Responses;
        $response->form_id = $form->id;
        $response->user_id = Auth::id();
        $response->date = now();
        $response->save();

        foreach($request->answers as $value){
            $answers = new Answers;
            $answers->response_id = $response->id;
            $answers->question_id = $value['question_id'];
            $answers->value = $value['value'];
            $answers->save();
        }

        return response()->json([
        'message' => 'Submit response success'
        ], 200);
    }

    public function all_response(Request $request, $slug)
    {
        $form = Forms::where('slug', $slug)->first();

        if(!$form){
         return response()->json([
             'message' => 'Form not found'
             ], 404);
        }

        if($form->creator_id != Auth::id()){
            return response()->json([
            'message' => 'Forbidden access'
            ], 403);
        } 

        $response = Responses::with('user','answers.questions')->get();

        $data = $response->map(function ($data){

        foreach($data->answers as $answers)
        {
            $format[$answers->questions->name] = $answers->value;
        }

        return [
            'date' => $data->date,
            'user' => [
                'id' => $data->user->id,
                'name' => $data->user->name,
                'email' => $data->user->email,
                'email_verified_at' => $data->user->email_verified_at,
            ],
            'answers' => $format
        ];
        });

        return response()->json([
        'message' => 'Get responses success',
        'responses' => $data
        ], 200);
    }


}

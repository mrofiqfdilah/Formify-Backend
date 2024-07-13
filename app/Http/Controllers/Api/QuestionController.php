<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Forms;
use App\Models\Questions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function add_question(Request $request, $slug)
    {
        $form = Forms::where('slug', $slug)->first();

        if(!$form){
         return response()->json([
             'message' => 'Form not found'
             ], 404);
        }

        $validator = Validator::make($request->all(),[
        'name' => 'required',
        'choice_type' => 'required|in:short answer,paragraph,date,multiple choice,dropdown,checkboxes',
        'choices' => 'required_if:choice_type,in,multiple choice,dropdown,checkboxes'
        ]);

        if($validator->fails())
        {
            return response()->json([
            'message' => 'Invalid field',
            'errors' => $validator->errors()
            ], 422);
        }

        if($form->creator_id != Auth::id()){
            return response()->json([
            'message' => 'Forbidden access'
            ], 403);
        } 
        

        $choices = null;

        if($request->choice_type === 'multiple choice' || $request->choice_type === 'dropdown' || $request->choice_type === 'checkboxes'){
            $choices = implode(',', $request->choices);
        }

        $questions = Questions::create([
        'form_id' => $form->id,
        'name' => $request->name,
        'choice_type' => $request->choice_type,
        'is_required' => $request->is_required,
        'choices' => $choices
     ]);

     return response()->json([
    'message' => 'Add question success',
    'question' => [
    'name' => $questions->name,
    'choice_type' => $questions->choice_type,
    'is_required' => $questions->is_required,
    'choices' => $questions->choices,
    'form_id' => $questions->form_id,
    'id' => $questions->id
    ],
     ], 200);
    }

    public function remove_question(Request $request,$slug, $id)
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
        
        $questions = Questions::where('id', $id)->first();

        if(!$questions){
            return response()->json([
            'message' => 'Question not found'
            ], 404);
     }

     $questions->delete();

     return response()->json([
    'message' => 'Remove question success'
     ], 200);

    }
}

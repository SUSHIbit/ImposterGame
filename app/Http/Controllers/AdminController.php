<?php

// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionSet;
use App\Models\Question;
use App\Http\Requests\CreateQuestionSetRequest;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    
    public function questionSets()
    {
        $questionSets = QuestionSet::with('questions')->latest()->get();
        return view('admin.question-sets', compact('questionSets'));
    }
    
    public function createQuestionSet(CreateQuestionSetRequest $request)
    {
        $validated = $request->validated();
        
        // Create question set
        $setNumber = QuestionSet::max('set_number') + 1;
        $questionSet = QuestionSet::create(['set_number' => $setNumber]);
        
        // Create normal question
        Question::create([
            'question_set_id' => $questionSet->id,
            'content' => $validated['normal_question'],
            'is_imposter_question' => false,
        ]);
        
        // Create imposter question
        Question::create([
            'question_set_id' => $questionSet->id,
            'content' => $validated['imposter_question'],
            'is_imposter_question' => true,
        ]);
        
        return redirect()->route('admin.question-sets')->with('success', 'Question set created successfully.');
    }
    
    public function deleteQuestionSet($id)
    {
        $questionSet = QuestionSet::findOrFail($id);
        $questionSet->delete();
        
        return redirect()->route('admin.question-sets')->with('success', 'Question set deleted successfully.');
    }
}
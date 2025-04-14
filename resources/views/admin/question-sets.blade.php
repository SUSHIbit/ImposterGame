<!-- resources/views/admin/question-sets.blade.php -->
@extends('layouts.admin')

@section('admin-title', 'Question Sets')

@section('admin-content')
<div class="bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Create New Question Set</h3>
    
    <form action="{{ route('admin.question-sets.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="normal_question" class="block text-gray-700 font-medium mb-2">
                Normal Question (For non-imposters)
            </label>
            <textarea id="normal_question" name="normal_question" rows="3" 
                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      required>{{ old('normal_question') }}</textarea>
            @error('normal_question')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label for="imposter_question" class="block text-gray-700 font-medium mb-2">
                Imposter Question (For the imposter)
            </label>
            <textarea id="imposter_question" name="imposter_question" rows="3" 
                      class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      required>{{ old('imposter_question') }}</textarea>
            @error('imposter_question')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 px-4 rounded">
                Create Question Set
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Existing Question Sets</h3>
    </div>
    
    <div class="divide-y">
        @forelse($questionSets as $questionSet)
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h4 class="text-md font-semibold text-gray-800">Set #{{ $questionSet->set_number }}</h4>
                    <form action="{{ route('admin.question-sets.delete', $questionSet->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm" 
                                onclick="return confirm('Are you sure you want to delete this question set?')">
                            Delete
                        </button>
                    </form>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($questionSet->questions as $question)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h5 class="font-medium text-gray-700 mb-2">
                                {{ $question->is_imposter_question ? 'Imposter Question' : 'Normal Question' }}
                            </h5>
                            <p class="text-gray-600">{{ $question->content }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500">
                No question sets found. Create your first set above.
            </div>
        @endforelse
    </div>
</div>
@endsection
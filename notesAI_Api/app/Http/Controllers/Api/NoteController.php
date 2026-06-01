<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
// use App\Services\SearchService;
use Illuminate\Support\Facades\Log;



class NoteController extends Controller
{
    
    /**
     * Get Notes List
     */
    public function index(Request $request)
    {
        $limit = $request->limit ?? 5;

        $notes = Note::paginate($limit);

        return response()->json([
    'success' => true,
    'message' => 'Notes fetched successfully',
    'data' => [
        'list' => $notes->items(),
        'current_page' => $notes->currentPage(),
        'last_page' => $notes->lastPage(),
        'per_page' => $notes->perPage(),
        'total' => $notes->total(),
    ]
], 200);
    }

    /**
     * Create Note
     */
    public function store(Request $request)
    {
        // Log::info("Before vector");
        //  $vector = $search->vectorize($request->title . ' ' . $request->content);
        //  Log::info("STORE API HIT");
        //  Log::info("Vector created", $vector);
        //  Log::info("STORE API HIT2");
        $data = $request->validate([
            'title'   => 'required|max:255',
            'content' => 'required'
        ]);

        $note =  Note::create([
        'title' => $request->title,
        'content' => $request->content,
        // 'vector' => json_encode($vector)
    ]);

        return response()->json([
            'success' => true,
            'message' => 'Note created successfully',
            
        ], 201);
    }

    /**
     * Get Single Note
     */
    public function show(Note $note)
    {
        return response()->json([
            'success' => true,
            'message' => 'Note fetched successfully',
            'data'    => $note
        ], 200);
    }

    /**
     * Update Note
     */
    public function update(Request $request, Note $note)
    {
        //  $vector = $search->vectorize($request->title . ' ' . $request->content);
        $data = $request->validate([
            'title'   => 'required|max:255',
            'content' => 'required'
        ]);

         $note->update([
        'title' => $request->title,
        'content' => $request->content,
        // 'vector' => json_encode($vector)
    ]);

        return response()->json([
            'success' => true,
            'message' => 'Note updated successfully',
           
        ], 200);
    }

    /**
     * Delete Note
     */
    public function destroy(Note $note)
    {
        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Note deleted successfully'
        ], 200);
    }

    /**
     * AI Summary Endpoint
     */
   public function summary($id)
{
    $note = Note::find($id);

    if (!$note) {
        return response()->json([
            'success' => false,
            'message' => 'Note not found'
        ], 404);
    }

    $summary = $this->generateSummary($note->content);

    return response()->json([
        'success' => true,
        'note_id' => $note->id,
        'summary' => $summary
    ]);
}
private function generateSummary($text)
{
    $sentences = explode('.', $text);

    $sentences = array_filter($sentences);

    // take first 2-3 sentences as summary
    $summary = array_slice($sentences, 0, 2);

    return implode('. ', $summary);
}
    /**
     * Search Notes
     */
    // public function search(Request $request)
    // {
    //     $query = $request->q;

    //     $notes = Note::where('title', 'like', "%{$query}%")
    //         ->orWhere('content', 'like', "%{$query}%")
    //         ->paginate(10);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Search completed successfully',
    //         'data'    => $notes
    //     ], 200);
    // }

    
  
}
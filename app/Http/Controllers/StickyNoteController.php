<?php

namespace App\Http\Controllers;

use App\Models\StickyNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class StickyNoteController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $notes = StickyNote::where('user_id', Auth::id())
            ->orderBy('order')
            ->get()
            ->map(function ($note) {
                return [
                    'id' => $note->id,
                    'title' => $note->title,
                    'content' => $note->content,
                    'color' => $note->color,
                    'order' => $note->order,
                    'shared_url' => $note->shared_url,
                    'updated_at' => $note->updated_at,
                ];
            });

        return Inertia::render('StickyNotes/Index', [
            'notes' => $notes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'color' => 'required|string',
            'order' => 'required|integer',
        ]);

        $note = Auth::user()->stickyNotes()->create($validated);
        
        return response()->json($note);
    }

    public function update(Request $request, StickyNote $stickyNote)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'color' => 'required|string',
            'order' => 'required|integer',
            'shared_url' => 'nullable|string',
        ]);

        $stickyNote->update($validated);
        
        return response()->json($stickyNote);
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'notes' => 'required|array',
            'notes.*.id' => 'required|exists:sticky_notes,id',
            'notes.*.order' => 'required|integer',
        ]);

        foreach ($validated['notes'] as $noteData) {
            StickyNote::where('id', $noteData['id'])
                ->where('user_id', Auth::id())
                ->update(['order' => $noteData['order']]);
        }

        return response()->json(['message' => 'Order updated successfully']);
    }

    public function destroy(StickyNote $stickyNote)
    {
        $stickyNote->delete();
        return response()->json(['message' => 'Note deleted successfully']);
    }
}

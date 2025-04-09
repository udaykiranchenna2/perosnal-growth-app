<?php

namespace App\Http\Controllers;

use App\Models\SharedContent;
use App\Models\StickyNote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SharedContentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sticky_note_id' => 'required|exists:sticky_notes,id',
        ]);

        // Check if the sticky note belongs to the authenticated user
        $stickyNote = StickyNote::where('id', $validated['sticky_note_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Check if the note is already shared
        $existingShare = SharedContent::where('sticky_note_id', $stickyNote->id)->first();
        if ($existingShare) {
            return response()->json([
                'url' => route('shared-content.show', $existingShare->slug)
            ]);
        }

        $content = SharedContent::create([
            'sticky_note_id' => $stickyNote->id
        ]);

        return response()->json([
            'url' => route('shared-content.show', $content->slug)
        ]);
    }

    public function show($slug)
    {
        try {
            $content = SharedContent::where('slug', $slug)
                ->with('stickyNote')
                ->firstOrFail();

            // Check if the sticky note exists
            if (!$content->stickyNote) {
                // Delete the shared content since the note no longer exists
                $content->delete();
                return Inertia::render('SharedContent/Show', [
                    'error' => 'The note you are trying to view has been deleted.',
                    'content' => null
                ]);
            }

            return Inertia::render('SharedContent/Show', [
                'content' => [
                    'title' => $content->stickyNote->title,
                    'content' => $content->stickyNote->content,
                    'color' => $content->stickyNote->color,
                    'updated_at' => $content->stickyNote->updated_at,
                    'slug' => $content->slug
                ],
                'error' => null
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Inertia::render('SharedContent/Show', [
                'error' => 'The shared note could not be found.',
                'content' => null
            ]);
        }
    }

    public function destroy($slug)
    {
        try {
            $content = SharedContent::where('slug', $slug)
                ->with('stickyNote')
                ->firstOrFail();

            // Check if the sticky note exists and belongs to the authenticated user
            if (!$content->stickyNote || auth()->id() !== $content->stickyNote->user_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $content->delete();

            return response()->json(['message' => 'Content deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Content not found'], 404);
        }
    }
} 
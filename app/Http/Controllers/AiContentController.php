<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiContentService;

class AiContentController extends Controller
{
    public function showForm()
    {
        return view('my-ai-form');
    }

    public function generate(Request $request, AiContentService $ai)
    {
        $validated = $request->validate([
            'title' => 'required|string|min:5|max:500',
        ]);

        try {
            $output = $ai->generateDraft(
                $validated['title'],
            );

            return view('my-ai-form', [
                'output' => $output,
                'title'  => $validated['title'],
            ]);
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'AI request failed: ' . $e->getMessage()
                ]);
        }
    }
}

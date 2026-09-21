<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\ImpactStat;
use App\Models\ImpactStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ImpactController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Impact', [
            'stats' => ImpactStat::ordered()->get(),
            'stories' => ImpactStory::orderByDesc('published_at')->get(),
        ]);
    }

    public function storeStat(Request $request)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:100'],
            'order' => ['nullable', 'integer'],
        ]);

        ImpactStat::create($validated);

        return back()->with('success', 'Impact stat added.');
    }

    public function updateStat(Request $request, ImpactStat $stat)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:100'],
            'order' => ['nullable', 'integer'],
        ]);

        $stat->update($validated);

        return back()->with('success', 'Impact stat updated.');
    }

    public function destroyStat(ImpactStat $stat)
    {
        $stat->delete();

        return back()->with('success', 'Impact stat removed.');
    }

    public function storeStory(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:litigation_outcome,policy_influence,community_impact,story_of_change'],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('impact-stories', 'public');
        }

        ImpactStory::create($validated);

        ActivityLogger::log('created', 'impact', 'Created impact story: ' . $validated['title']);

        return back()->with('success', 'Impact story created.');
    }

    public function updateStory(Request $request, ImpactStory $story)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:litigation_outcome,policy_influence,community_impact,story_of_change'],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('image')) {
            if ($story->image) {
                Storage::disk('public')->delete($story->image);
            }
            $validated['image'] = $request->file('image')->store('impact-stories', 'public');
        } else {
            unset($validated['image']);
        }

        $story->update($validated);

        ActivityLogger::log('updated', 'impact', 'Updated impact story: ' . $story->title);

        return back()->with('success', 'Impact story updated.');
    }

    public function toggleStoryActive(ImpactStory $story)
    {
        $story->update(['is_active' => !$story->is_active]);

        return back()->with('success', 'Impact story status updated.');
    }

    public function destroyStory(ImpactStory $story)
    {
        $story->delete();

        ActivityLogger::log('deleted', 'impact', 'Deleted impact story: ' . $story->title);

        return back()->with('success', 'Impact story removed.');
    }
}

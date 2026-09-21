<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\WorkArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WorkAreaController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/WorkAreas', [
            'workAreas' => WorkArea::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'order' => ['nullable', 'integer'],
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('work-areas', 'public');
        }

        WorkArea::create($validated);

        ActivityLogger::log('created', 'work_areas', 'Created program: ' . $validated['title']);

        return back()->with('success', 'Program created.');
    }

    public function update(Request $request, WorkArea $workArea)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:100'],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'order' => ['nullable', 'integer'],
        ]);

        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('image')) {
            if ($workArea->image) {
                Storage::disk('public')->delete($workArea->image);
            }
            $validated['image'] = $request->file('image')->store('work-areas', 'public');
        } else {
            unset($validated['image']);
        }

        $workArea->update($validated);

        ActivityLogger::log('updated', 'work_areas', 'Updated program: ' . $workArea->title);

        return back()->with('success', 'Program updated.');
    }

    public function toggleActive(WorkArea $workArea)
    {
        $workArea->update(['is_active' => !$workArea->is_active]);

        return back()->with('success', 'Program status updated.');
    }

    public function destroy(WorkArea $workArea)
    {
        $workArea->delete();

        ActivityLogger::log('deleted', 'work_areas', 'Deleted program: ' . $workArea->title);

        return back()->with('success', 'Program deleted.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Campaigns', [
            'campaigns' => Campaign::orderByDesc('published_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'in:active,past'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('campaigns', 'public');
        }

        Campaign::create($validated);

        ActivityLogger::log('created', 'campaigns', 'Created campaign: ' . $validated['title']);

        return back()->with('success', 'Campaign created.');
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'in:active,past'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('banner')) {
            if ($campaign->banner) {
                Storage::disk('public')->delete($campaign->banner);
            }
            $validated['banner'] = $request->file('banner')->store('campaigns', 'public');
        } else {
            unset($validated['banner']);
        }

        $campaign->update($validated);

        ActivityLogger::log('updated', 'campaigns', 'Updated campaign: ' . $campaign->title);

        return back()->with('success', 'Campaign updated.');
    }

    public function toggleActive(Campaign $campaign)
    {
        $campaign->update(['is_active' => !$campaign->is_active]);

        return back()->with('success', 'Campaign status updated.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        ActivityLogger::log('deleted', 'campaigns', 'Deleted campaign: ' . $campaign->title);

        return back()->with('success', 'Campaign removed.');
    }
}

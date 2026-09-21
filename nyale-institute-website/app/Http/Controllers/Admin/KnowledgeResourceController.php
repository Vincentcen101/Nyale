<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\KnowledgeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class KnowledgeResourceController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/KnowledgeHub', [
            'resources' => KnowledgeResource::orderByDesc('published_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:research_report,policy_brief,advocacy_manual,legal_resource,issues_paper,programme_report,publication'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data = collect($validated)->except(['file', 'cover_image'])->toArray();
        $data['created_by'] = auth()->id();

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('knowledge-hub', 'public');
        }
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('knowledge-hub/covers', 'public');
        }

        KnowledgeResource::create($data);

        ActivityLogger::log('created', 'knowledge_hub', 'Uploaded knowledge resource: ' . $validated['title']);

        return back()->with('success', 'Resource added to Knowledge Hub.');
    }

    public function update(Request $request, KnowledgeResource $resource)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:research_report,policy_brief,advocacy_manual,legal_resource,issues_paper,programme_report,publication'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data = collect($validated)->except(['file', 'cover_image'])->toArray();
        $data['updated_by'] = auth()->id();

        if ($request->hasFile('file')) {
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            $data['file_path'] = $request->file('file')->store('knowledge-hub', 'public');
        }
        if ($request->hasFile('cover_image')) {
            if ($resource->cover_image) {
                Storage::disk('public')->delete($resource->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('knowledge-hub/covers', 'public');
        }

        $resource->update($data);

        ActivityLogger::log('updated', 'knowledge_hub', 'Updated knowledge resource: ' . $resource->title);

        return back()->with('success', 'Resource updated.');
    }

    public function toggleActive(KnowledgeResource $resource)
    {
        $resource->update(['is_active' => !$resource->is_active]);

        return back()->with('success', 'Resource status updated.');
    }

    public function destroy(KnowledgeResource $resource)
    {
        $resource->delete();

        ActivityLogger::log('deleted', 'knowledge_hub', 'Deleted knowledge resource: ' . $resource->title);

        return back()->with('success', 'Resource removed.');
    }
}

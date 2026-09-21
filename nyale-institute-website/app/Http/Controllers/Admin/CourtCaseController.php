<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\CourtCase;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CourtCaseController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'case_number' => ['nullable', 'string', 'max:100'],
            'court' => ['nullable', 'string', 'max:255'],
            'lawyer' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:ongoing,concluded,on_appeal'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'case_date' => ['nullable', 'date'],
        ];
    }

    public function index()
    {
        return Inertia::render('Dashboard/Cases', [
            'cases' => CourtCase::orderByDesc('case_date')->orderByDesc('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $validated['body'] = RichText::clean($validated['body'] ?? null);
        $validated['slug'] = Str::slug(Str::limit($validated['title'], 80, '')) . '-' . Str::random(5);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('cases', 'public');
        }

        CourtCase::create($validated);

        ActivityLogger::log('created', 'cases', 'Added case: ' . $validated['title']);

        return back()->with('success', 'Case added.');
    }

    public function update(Request $request, CourtCase $case)
    {
        $validated = $request->validate($this->rules());
        $validated['body'] = RichText::clean($validated['body'] ?? null);
        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('image')) {
            if ($case->image) {
                Storage::disk('public')->delete($case->image);
            }
            $validated['image'] = $request->file('image')->store('cases', 'public');
        } else {
            unset($validated['image']);
        }

        $case->update($validated);

        ActivityLogger::log('updated', 'cases', 'Updated case: ' . $case->title);

        return back()->with('success', 'Case updated.');
    }

    public function toggleActive(CourtCase $case)
    {
        $case->update(['is_active' => !$case->is_active]);

        return back()->with('success', 'Case status updated.');
    }

    public function destroy(CourtCase $case)
    {
        $case->delete();

        ActivityLogger::log('deleted', 'cases', 'Deleted case: ' . $case->title);

        return back()->with('success', 'Case deleted.');
    }
}

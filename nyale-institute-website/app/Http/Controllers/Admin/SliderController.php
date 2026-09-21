<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SliderController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'description' => [
                'nullable', 'string', 'max:4000',
                function ($attribute, $value, $fail) {
                    if (mb_strlen(RichText::plain($value)) > 600) {
                        $fail('The description may not be longer than 600 characters.');
                    }
                },
            ],
            'image' => ['nullable', 'image', 'max:4096'],
            'button_label' => ['nullable', 'string', 'max:60'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
        ];
    }

    public function index()
    {
        return Inertia::render('Dashboard/Sliders', [
            'sliders' => Slider::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $validated['description'] = RichText::clean($validated['description'] ?? null);
        $validated['order'] = $validated['order'] ?? 0;
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        Slider::create($validated);

        ActivityLogger::log('created', 'sliders', 'Created slide: ' . $validated['title']);

        return back()->with('success', 'Slide created.');
    }

    public function update(Request $request, Slider $slider)
    {
        $validated = $request->validate($this->rules());
        $validated['description'] = RichText::clean($validated['description'] ?? null);
        $validated['order'] = $validated['order'] ?? 0;
        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('image')) {
            if ($slider->image) {
                Storage::disk('public')->delete($slider->image);
            }
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        } else {
            unset($validated['image']);
        }

        $slider->update($validated);

        ActivityLogger::log('updated', 'sliders', 'Updated slide: ' . $slider->title);

        return back()->with('success', 'Slide updated.');
    }

    public function toggleActive(Slider $slider)
    {
        $slider->update(['is_active' => !$slider->is_active]);

        return back()->with('success', 'Slide status updated.');
    }

    public function destroy(Slider $slider)
    {
        $slider->delete();

        ActivityLogger::log('deleted', 'sliders', 'Deleted slide: ' . $slider->title);

        return back()->with('success', 'Slide deleted.');
    }
}

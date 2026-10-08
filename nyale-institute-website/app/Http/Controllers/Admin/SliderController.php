<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
            'button_url' => ['nullable', Rule::in(array_keys(Slider::BUTTON_OPTIONS))],
            'order' => ['nullable', 'integer'],
        ];
    }

    private function withButtonLabel(array $validated): array
    {
        $validated['button_url'] = $validated['button_url'] ?? null;
        $validated['button_label'] = Slider::BUTTON_OPTIONS[$validated['button_url']] ?? null;

        return $validated;
    }

    public function index()
    {
        return Inertia::render('Dashboard/Sliders', [
            'sliders' => Slider::ordered()->get(),
            'buttonOptions' => collect(Slider::BUTTON_OPTIONS)
                ->map(fn ($label, $url) => ['url' => $url, 'label' => $label])
                ->values(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->withButtonLabel($request->validate($this->rules()));
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
        $validated = $this->withButtonLabel($request->validate($this->rules()));
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

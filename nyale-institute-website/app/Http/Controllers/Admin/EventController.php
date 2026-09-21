<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class EventController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:1000'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'location' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'registration_url' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function index()
    {
        return Inertia::render('Dashboard/Events', [
            'events' => Event::orderByDesc('starts_at')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        Event::create($validated);

        ActivityLogger::log('created', 'events', 'Created event: ' . $validated['title']);

        return back()->with('success', 'Event created.');
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate($this->rules());
        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validated['image'] = $request->file('image')->store('events', 'public');
        } else {
            unset($validated['image']);
        }

        $event->update($validated);

        ActivityLogger::log('updated', 'events', 'Updated event: ' . $event->title);

        return back()->with('success', 'Event updated.');
    }

    public function toggleActive(Event $event)
    {
        $event->update(['is_active' => !$event->is_active]);

        return back()->with('success', 'Event status updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        ActivityLogger::log('deleted', 'events', 'Deleted event: ' . $event->title);

        return back()->with('success', 'Event deleted.');
    }
}

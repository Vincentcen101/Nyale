<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Team', [
            'members' => TeamMember::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'type' => ['required', 'in:board,staff'],
            'email' => ['nullable', 'email', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
        ]);

        $validated['created_by'] = auth()->id();

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('team', 'public');
        }

        TeamMember::create($validated);

        ActivityLogger::log('created', 'team', 'Added team member: ' . $validated['name']);

        return back()->with('success', 'Team member added.');
    }

    public function update(Request $request, TeamMember $member)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'type' => ['required', 'in:board,staff'],
            'email' => ['nullable', 'email', 'max:255'],
            'linkedin' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
        ]);

        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            $validated['photo'] = $request->file('photo')->store('team', 'public');
        } else {
            unset($validated['photo']);
        }

        $member->update($validated);

        ActivityLogger::log('updated', 'team', 'Updated team member: ' . $member->name);

        return back()->with('success', 'Team member updated.');
    }

    public function toggleActive(TeamMember $member)
    {
        $member->update(['is_active' => !$member->is_active]);

        return back()->with('success', 'Team member status updated.');
    }

    public function destroy(TeamMember $member)
    {
        $member->delete();

        ActivityLogger::log('deleted', 'team', 'Removed team member: ' . $member->name);

        return back()->with('success', 'Team member removed.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GetInvolvedSubmission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GetInvolvedController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/GetInvolved', [
            'submissions' => GetInvolvedSubmission::latest()->get(),
        ]);
    }

    public function updateStatus(Request $request, GetInvolvedSubmission $submission)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,in_progress,resolved'],
        ]);

        $submission->update($validated);

        return back()->with('success', 'Submission status updated.');
    }

    public function destroy(GetInvolvedSubmission $submission)
    {
        $submission->delete();

        return back()->with('success', 'Submission deleted.');
    }
}

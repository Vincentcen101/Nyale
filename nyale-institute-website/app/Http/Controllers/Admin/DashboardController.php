<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\GetInvolvedSubmission;
use App\Models\ImpactStory;
use App\Models\KnowledgeResource;
use App\Models\Post;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\WorkArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Overview', [
            'counts' => [
                'workAreas' => WorkArea::count(),
                'impactStories' => ImpactStory::count(),
                'knowledgeResources' => KnowledgeResource::count(),
                'campaigns' => Campaign::count(),
                'posts' => Post::count(),
                'cases' => \App\Models\CourtCase::count(),
                'events' => \App\Models\Event::count(),
                'newSubmissions' => GetInvolvedSubmission::where('status', 'new')->count(),
                'users' => User::count(),
            ],
            'recentSubmissions' => GetInvolvedSubmission::latest()->limit(5)->get(),
            'recentPosts' => Post::latest()->limit(5)->get(),
            // Activity is for administrators only; editors never receive it.
            'recentActivity' => auth()->user()?->isAdmin()
                ? UserActivityLog::with('user')->latest()->limit(8)->get()
                : [],
        ]);
    }

    public function users()
    {
        return Inertia::render('Dashboard/Users', [
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,editor'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        ActivityLogger::log('created', 'user', 'Created user: ' . $validated['email']);

        return back()->with('success', 'User created.');
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,editor'],
        ]);

        $user->update($validated);

        ActivityLogger::log('updated', 'user', 'Updated user: ' . $user->email);

        return back()->with('success', 'User updated.');
    }

    // Accounts are never deleted (their name stays attached to everything they created);
    // they are deactivated instead, which blocks sign-in but keeps all data intact.
    public function suspendUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        if ($user->isAdmin() && ! User::where('role', 'admin')->where('status', 'active')->where('id', '!=', $user->id)->exists()) {
            return back()->with('error', 'You cannot deactivate the last active administrator.');
        }

        $user->update(['status' => 'suspended', 'suspended_at' => now()]);

        ActivityLogger::log('deactivated', 'user', 'Deactivated user: ' . $user->email);

        return back()->with('success', 'User account deactivated. Their data has been kept.');
    }

    public function activateUser(User $user)
    {
        $user->update(['status' => 'active', 'suspended_at' => null]);

        ActivityLogger::log('reactivated', 'user', 'Reactivated user: ' . $user->email);

        return back()->with('success', 'User account reactivated.');
    }

    public function activityLogs(Request $request)
    {
        $filters = $request->only(['search', 'user', 'action', 'module', 'from', 'to']);

        $logs = UserActivityLog::with('user')
            ->when($filters['search'] ?? null, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('description', 'like', "%{$term}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($filters['user'] ?? null, fn ($query, $id) => $query->where('user_id', $id))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($filters['module'] ?? null, fn ($query, $module) => $query->where('module', $module))
            ->when($filters['from'] ?? null, fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'] ?? null, fn ($query, $to) => $query->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Dashboard/ActivityLog', [
            'logs' => $logs,
            'filters' => (object) $filters,
            'users' => User::whereIn('id', UserActivityLog::select('user_id')->distinct())->orderBy('name')->get(['id', 'name']),
            'actions' => UserActivityLog::select('action')->distinct()->orderBy('action')->pluck('action'),
            'modules' => UserActivityLog::select('module')->distinct()->orderBy('module')->pluck('module'),
        ]);
    }
}

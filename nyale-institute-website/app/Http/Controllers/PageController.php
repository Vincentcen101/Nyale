<?php

namespace App\Http\Controllers;

use App\Models\AboutSetting;
use App\Models\Campaign;
use App\Models\CourtCase;
use App\Models\Event;
use App\Models\GetInvolvedSubmission;
use App\Models\ImpactStat;
use App\Models\ImpactStory;
use App\Models\KnowledgeResource;
use App\Models\Post;
use App\Models\Slider;
use App\Models\TeamMember;
use App\Models\WorkArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PageController extends Controller
{
    public function home()
    {
        return Inertia::render('Home', [
            'stats' => ImpactStat::active()->ordered()->get(),
            'workAreas' => WorkArea::active()->ordered()->limit(4)->get(),
            'campaigns' => Campaign::active()->status('active')->latest()->limit(8)->get(),
            'latestPosts' => Post::active()->latest()->limit(3)->get(),
            'featuredStory' => ImpactStory::active()->latest()->first(),
            'sliders' => Slider::active()->ordered()->get(),
        ]);
    }

    public function about()
    {
        return Inertia::render('About', [
            'aboutSections' => AboutSetting::active()->ordered()->get()
                ->groupBy('section')
                ->map(fn ($items) => $items->first()),
            'board' => TeamMember::active()->board()->ordered()->get(),
            'team' => TeamMember::active()->staff()->ordered()->get(),
        ]);
    }

    public function ourWork()
    {
        return Inertia::render('OurWork/Index', [
            'workAreas' => WorkArea::active()->ordered()->get(),
        ]);
    }

    public function ourWorkShow($slug)
    {
        $workArea = WorkArea::active()->where('slug', $slug)->firstOrFail();
        $related = WorkArea::active()->where('id', '!=', $workArea->id)->ordered()->limit(3)->get();

        return Inertia::render('OurWork/Show', [
            'workArea' => $workArea,
            'related' => $related,
        ]);
    }

    public function impact()
    {
        return Inertia::render('Impact', [
            'stats' => ImpactStat::active()->ordered()->get(),
            'stories' => ImpactStory::active()->latest()->get(),
        ]);
    }

    public function impactShow($slug)
    {
        $story = ImpactStory::active()->where('slug', $slug)->firstOrFail();
        $related = ImpactStory::active()->where('id', '!=', $story->id)->latest()->limit(3)->get();

        return Inertia::render('Impact/Show', [
            'story' => $story,
            'related' => $related,
        ]);
    }

    public function knowledgeHub(Request $request)
    {
        $query = KnowledgeResource::active()->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        return Inertia::render('KnowledgeHub/Index', [
            'resources' => $query->get(),
            'filterType' => $request->string('type')->toString(),
        ]);
    }

    public function knowledgeHubDownload(KnowledgeResource $resource)
    {
        abort_unless($resource->is_active && $resource->file_path, 404);

        return Storage::disk('public')->download($resource->file_path, $resource->title . '.pdf');
    }

    public function campaigns(Request $request)
    {
        $query = Campaign::active()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return Inertia::render('Campaigns/Index', [
            'campaigns' => $query->get(),
        ]);
    }

    public function campaignShow($slug)
    {
        $campaign = Campaign::active()->where('slug', $slug)->firstOrFail();

        return Inertia::render('Campaigns/Show', [
            'campaign' => $campaign,
        ]);
    }

    public function news(Request $request)
    {
        $query = Post::active()->with('program:id,title,slug')->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        // Blog posts belong to a program; filtering by program only applies to the Blog category.
        $program = $request->string('category')->toString() === 'blog' ? $request->string('program')->toString() : '';
        if ($program !== '') {
            $query->whereHas('program', fn ($q) => $q->where('slug', $program));
        }

        return Inertia::render('News/Index', [
            'posts' => $query->paginate(9)->withQueryString(),
            'filterCategory' => $request->string('category')->toString(),
            'filterProgram' => $program,
        ]);
    }

    public function newsShow($slug)
    {
        $post = Post::active()->with('program:id,title,slug')->where('slug', $slug)->firstOrFail();
        // Related stories stay within the same section (a news item never suggests blogs, and vice versa).
        $related = Post::active()->where('id', '!=', $post->id)->where('category', $post->category)->latest()->limit(3)->get();

        return Inertia::render('News/Show', [
            'post' => $post,
            'related' => $related,
            'comments' => $post->comments()->approved()->get(),
            'liked' => in_array($post->id, session('liked_posts', [])),
        ]);
    }

    public function toggleLike(Post $post)
    {
        $liked = session('liked_posts', []);

        if (in_array($post->id, $liked)) {
            $liked = array_values(array_diff($liked, [$post->id]));
            $post->decrement('likes_count');
        } else {
            $liked[] = $post->id;
            $post->increment('likes_count');
        }

        session(['liked_posts' => $liked]);

        return back(303);
    }

    public function storeComment(Request $request, Post $post)
    {
        // Commenting is a Blog feature; news and other updates are not open for comments.
        abort_unless($post->category === 'blog', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $post->comments()->create($validated + ['is_approved' => false]);

        return back()->with('success', 'Thank you! Your comment has been submitted and will appear once it has been approved.');
    }

    public function caseTracker()
    {
        return Inertia::render('CaseTracker/Index', [
            'cases' => CourtCase::active()->withCount(['comments' => fn ($q) => $q->where('is_approved', true)])->orderByDesc('case_date')->orderByDesc('id')->get(),
        ]);
    }

    public function caseShow($slug)
    {
        $case = CourtCase::active()->where('slug', $slug)->firstOrFail();
        $related = CourtCase::active()->where('id', '!=', $case->id)->orderByDesc('case_date')->limit(3)->get();

        return Inertia::render('CaseTracker/Show', [
            'courtCase' => $case,
            'related' => $related,
            'comments' => $case->comments()->approved()->get(),
            'liked' => in_array($case->id, session('liked_cases', [])),
        ]);
    }

    public function toggleCaseLike(CourtCase $case)
    {
        $liked = session('liked_cases', []);

        if (in_array($case->id, $liked)) {
            $liked = array_values(array_diff($liked, [$case->id]));
            $case->decrement('likes_count');
        } else {
            $liked[] = $case->id;
            $case->increment('likes_count');
        }

        session(['liked_cases' => $liked]);

        return back(303);
    }

    public function storeCaseComment(Request $request, CourtCase $case)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $case->comments()->create($validated + ['is_approved' => false]);

        return back()->with('success', 'Thank you! Your comment has been submitted and will appear once it has been approved.');
    }

    public function events()
    {
        $events = Event::active();

        return Inertia::render('Events/Index', [
            'upcoming' => (clone $events)->where('starts_at', '>=', now())->orderBy('starts_at')->get(),
            'past' => (clone $events)->where('starts_at', '<', now())->orderByDesc('starts_at')->get(),
        ]);
    }

    public function eventShow($slug)
    {
        $event = Event::active()->where('slug', $slug)->firstOrFail();
        $related = Event::active()->where('id', '!=', $event->id)->where('starts_at', '>=', now())->orderBy('starts_at')->limit(3)->get();

        return Inertia::render('Events/Show', [
            'event' => $event,
            'related' => $related,
        ]);
    }

    public function getInvolved()
    {
        return Inertia::render('GetInvolved');
    }

    public function submitGetInvolved(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'organisation' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'in:partner,research_collaboration,programme_collaboration,volunteer,support,general'],
            'message' => ['required', 'string'],
        ]);

        GetInvolvedSubmission::create($validated);

        return back()->with('success', 'Thank you — your message has been received. We will be in touch soon.');
    }
}

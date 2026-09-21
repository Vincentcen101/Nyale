<?php

namespace App\Http\Middleware;

use App\Models\CaseComment;
use App\Models\ContactSetting;
use App\Models\PostComment;
use App\Models\PartnerLogo;
use App\Models\SocialMediaSetting;
use App\Models\WorkArea;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'pendingComments' => fn () => $request->user()
                ? PostComment::where('is_approved', false)->whereHas('post', fn ($q) => $q->where('category', 'blog'))->count()
                    + CaseComment::where('is_approved', false)->count()
                : 0,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'site' => [
                'contact' => fn () => ContactSetting::query()->active()->ordered()->get()
                    ->mapWithKeys(fn ($c) => [$c->type => $c->value]),
                'social' => fn () => SocialMediaSetting::query()->active()->get()
                    ->mapWithKeys(fn ($s) => [$s->platform => $s->url]),
                'partners' => fn () => PartnerLogo::query()->active()->ordered()->get(),
                'programs' => fn () => WorkArea::query()->active()->ordered()->get(['id', 'title', 'slug']),
            ],
        ];
    }
}

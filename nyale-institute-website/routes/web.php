<?php

use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\CourtCaseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GetInvolvedController;
use App\Http\Controllers\Admin\ImpactController;
use App\Http\Controllers\Admin\KnowledgeResourceController;
use App\Http\Controllers\Admin\PostCommentController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\WorkAreaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/programs', [PageController::class, 'ourWork'])->name('programs.index');
Route::get('/programs/{slug}', [PageController::class, 'ourWorkShow'])->name('programs.show');
// Old address kept working so existing links and bookmarks still arrive.
Route::redirect('/our-work', '/programs', 301);
Route::get('/our-work/{slug}', fn (string $slug) => redirect('/programs/' . $slug, 301));
Route::get('/our-impact', [PageController::class, 'impact'])->name('impact');
Route::get('/our-impact/{slug}', [PageController::class, 'impactShow'])->name('impact.show');
Route::get('/knowledge-hub', [PageController::class, 'knowledgeHub'])->name('knowledge-hub.index');
Route::get('/knowledge-hub/{resource}/download', [PageController::class, 'knowledgeHubDownload'])->name('knowledge-hub.download');
Route::get('/campaigns', [PageController::class, 'campaigns'])->name('campaigns.index');
Route::get('/campaigns/{slug}', [PageController::class, 'campaignShow'])->name('campaigns.show');
Route::get('/news', [PageController::class, 'news'])->name('news.index');
Route::get('/news/{slug}', [PageController::class, 'newsShow'])->name('news.show');
Route::post('/news/{post}/like', [PageController::class, 'toggleLike'])->name('news.like');
Route::post('/news/{post}/comments', [PageController::class, 'storeComment'])->name('news.comments.store');
Route::get('/case-tracker', [PageController::class, 'caseTracker'])->name('case-tracker.index');
Route::get('/case-tracker/{slug}', [PageController::class, 'caseShow'])->name('case-tracker.show');
Route::post('/case-tracker/{case}/like', [PageController::class, 'toggleCaseLike'])->name('case-tracker.like');
Route::post('/case-tracker/{case}/comments', [PageController::class, 'storeCaseComment'])->name('case-tracker.comments.store');
Route::get('/events', [PageController::class, 'events'])->name('events.index');
Route::get('/events/{slug}', [PageController::class, 'eventShow'])->name('events.show');
Route::get('/get-involved', [PageController::class, 'getInvolved'])->name('get-involved');
Route::post('/get-involved', [PageController::class, 'submitGetInvolved'])->name('get-involved.submit');

/*
|--------------------------------------------------------------------------
| Admin Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Home page sliders
    Route::get('/sliders', [SliderController::class, 'index'])->name('sliders');
    Route::post('/sliders', [SliderController::class, 'store'])->name('sliders.store');
    Route::put('/sliders/{slider}', [SliderController::class, 'update'])->name('sliders.update');
    Route::patch('/sliders/{slider}/toggle-active', [SliderController::class, 'toggleActive'])->name('sliders.toggle-active');
    Route::delete('/sliders/{slider}', [SliderController::class, 'destroy'])->name('sliders.destroy');

    // Work Areas
    Route::get('/work-areas', [WorkAreaController::class, 'index'])->name('work-areas');
    Route::post('/work-areas', [WorkAreaController::class, 'store'])->name('work-areas.store');
    Route::put('/work-areas/{workArea}', [WorkAreaController::class, 'update'])->name('work-areas.update');
    Route::patch('/work-areas/{workArea}/toggle-active', [WorkAreaController::class, 'toggleActive'])->name('work-areas.toggle-active');
    Route::delete('/work-areas/{workArea}', [WorkAreaController::class, 'destroy'])->name('work-areas.destroy');

    // Impact (stats + stories)
    Route::get('/impact', [ImpactController::class, 'index'])->name('impact');
    Route::post('/impact/stats', [ImpactController::class, 'storeStat'])->name('impact.stats.store');
    Route::put('/impact/stats/{stat}', [ImpactController::class, 'updateStat'])->name('impact.stats.update');
    Route::delete('/impact/stats/{stat}', [ImpactController::class, 'destroyStat'])->name('impact.stats.destroy');
    Route::post('/impact/stories', [ImpactController::class, 'storeStory'])->name('impact.stories.store');
    Route::put('/impact/stories/{story}', [ImpactController::class, 'updateStory'])->name('impact.stories.update');
    Route::patch('/impact/stories/{story}/toggle-active', [ImpactController::class, 'toggleStoryActive'])->name('impact.stories.toggle-active');
    Route::delete('/impact/stories/{story}', [ImpactController::class, 'destroyStory'])->name('impact.stories.destroy');

    // Knowledge Hub
    Route::get('/knowledge-hub', [KnowledgeResourceController::class, 'index'])->name('knowledge-hub');
    Route::post('/knowledge-hub', [KnowledgeResourceController::class, 'store'])->name('knowledge-hub.store');
    Route::put('/knowledge-hub/{resource}', [KnowledgeResourceController::class, 'update'])->name('knowledge-hub.update');
    Route::patch('/knowledge-hub/{resource}/toggle-active', [KnowledgeResourceController::class, 'toggleActive'])->name('knowledge-hub.toggle-active');
    Route::delete('/knowledge-hub/{resource}', [KnowledgeResourceController::class, 'destroy'])->name('knowledge-hub.destroy');

    // Campaigns
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::patch('/campaigns/{campaign}/toggle-active', [CampaignController::class, 'toggleActive'])->name('campaigns.toggle-active');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');

    // News & Blogs
    Route::get('/news', [PostController::class, 'index'])->name('news');
    Route::post('/news', [PostController::class, 'store'])->name('news.store');
    Route::put('/news/{post}', [PostController::class, 'update'])->name('news.update');
    Route::patch('/news/{post}/toggle-active', [PostController::class, 'toggleActive'])->name('news.toggle-active');
    Route::delete('/news/{post}', [PostController::class, 'destroy'])->name('news.destroy');

    // Case Tracker
    Route::get('/cases', [CourtCaseController::class, 'index'])->name('cases');
    Route::post('/cases', [CourtCaseController::class, 'store'])->name('cases.store');
    Route::put('/cases/{case}', [CourtCaseController::class, 'update'])->name('cases.update');
    Route::patch('/cases/{case}/toggle-active', [CourtCaseController::class, 'toggleActive'])->name('cases.toggle-active');
    Route::delete('/cases/{case}', [CourtCaseController::class, 'destroy'])->name('cases.destroy');

    // Events
    Route::get('/events', [EventController::class, 'index'])->name('events');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::patch('/events/{event}/toggle-active', [EventController::class, 'toggleActive'])->name('events.toggle-active');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // Comments (moderation)
    Route::get('/comments', [PostCommentController::class, 'index'])->name('comments');
    Route::patch('/comments/{comment}/approval', [PostCommentController::class, 'toggleApproval'])->name('comments.approval');
    Route::patch('/case-comments/{comment}/approval', [PostCommentController::class, 'toggleCaseApproval'])->name('case-comments.approval');
    Route::delete('/comments/{comment}', [PostCommentController::class, 'destroy'])->name('comments.destroy');
    Route::delete('/case-comments/{comment}', [PostCommentController::class, 'destroyCaseComment'])->name('case-comments.destroy');

    // Team & Board Members (administrators only)
    Route::middleware('admin')->group(function () {
        Route::get('/team', [TeamController::class, 'index'])->name('team');
        Route::post('/team', [TeamController::class, 'store'])->name('team.store');
        Route::put('/team/{member}', [TeamController::class, 'update'])->name('team.update');
        Route::patch('/team/{member}/toggle-active', [TeamController::class, 'toggleActive'])->name('team.toggle-active');
        Route::delete('/team/{member}', [TeamController::class, 'destroy'])->name('team.destroy');
    });

    // Get Involved submissions
    Route::get('/get-involved', [GetInvolvedController::class, 'index'])->name('get-involved');
    Route::patch('/get-involved/{submission}/status', [GetInvolvedController::class, 'updateStatus'])->name('get-involved.status');
    Route::delete('/get-involved/{submission}', [GetInvolvedController::class, 'destroy'])->name('get-involved.destroy');

    // Users (administrators only)
    Route::middleware('admin')->group(function () {
        Route::get('/users', [DashboardController::class, 'users'])->name('users');
        Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');
        Route::put('/users/{user}', [DashboardController::class, 'updateUser'])->name('users.update');
        Route::post('/users/{user}/suspend', [DashboardController::class, 'suspendUser'])->name('users.suspend');
        Route::post('/users/{user}/activate', [DashboardController::class, 'activateUser'])->name('users.activate');
    });

    // Activity Log (administrators only)
    Route::get('/activity-logs', [DashboardController::class, 'activityLogs'])->name('activity-logs')->middleware('admin');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/about', [SettingsController::class, 'updateAbout'])->name('settings.about.update');
    Route::post('/settings/contact', [SettingsController::class, 'updateContact'])->name('settings.contact.update');
    Route::post('/settings/social', [SettingsController::class, 'updateSocial'])->name('settings.social.update');
    Route::post('/settings/seo', [SettingsController::class, 'updateSeo'])->name('settings.seo.update');
    Route::post('/settings/partners', [SettingsController::class, 'storePartner'])->name('settings.partners.store');
    Route::delete('/settings/partners/{partner}', [SettingsController::class, 'destroyPartner'])->name('settings.partners.destroy');
});

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\AboutSetting;
use App\Models\ContactSetting;
use App\Models\PartnerLogo;
use App\Models\SEOSetting;
use App\Models\SocialMediaSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Settings', [
            'aboutSettings' => AboutSetting::orderBy('order')->get(),
            'contactSettings' => ContactSetting::orderBy('order')->get(),
            'socialSettings' => SocialMediaSetting::orderBy('order')->get(),
            'seoSettings' => SEOSetting::orderBy('page')->get(),
            'partners' => PartnerLogo::orderBy('order')->get(),
        ]);
    }

    public function updateAbout(Request $request)
    {
        $validated = $request->validate([
            'section' => ['required', 'string', 'max:100'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $setting = AboutSetting::firstOrNew(['section' => $validated['section']]);
        $setting->title = $validated['title'] ?? null;
        $setting->content = $validated['content'] ?? null;

        if ($request->hasFile('image')) {
            if ($setting->image) {
                Storage::disk('public')->delete($setting->image);
            }
            $setting->image = $request->file('image')->store('about', 'public');
        }

        $setting->is_active = true;
        $setting->save();

        ActivityLogger::log('updated', 'settings', 'Updated About section: ' . $validated['section']);

        return back()->with('success', 'About section updated.');
    }

    public function updateContact(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'label' => ['nullable', 'string', 'max:255'],
            'value' => ['nullable', 'string'],
        ]);

        $setting = ContactSetting::firstOrNew(['type' => $validated['type']]);
        $setting->label = $validated['label'] ?? null;
        $setting->value = $validated['value'] ?? null;
        $setting->is_active = true;
        $setting->save();

        return back()->with('success', 'Contact details updated.');
    }

    public function updateSocial(Request $request)
    {
        $validated = $request->validate([
            'platform' => ['required', 'string', 'max:100'],
            'url' => ['nullable', 'string', 'max:255'],
        ]);

        $setting = SocialMediaSetting::firstOrNew(['platform' => $validated['platform']]);
        $setting->url = $validated['url'] ?? null;
        $setting->is_active = true;
        $setting->save();

        return back()->with('success', 'Social link updated.');
    }

    public function updateSeo(Request $request)
    {
        $validated = $request->validate([
            'page' => ['required', 'string', 'max:100'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $setting = SEOSetting::firstOrNew(['page' => $validated['page']]);
        $setting->meta_title = $validated['meta_title'] ?? null;
        $setting->meta_description = $validated['meta_description'] ?? null;
        $setting->save();

        return back()->with('success', 'SEO settings updated.');
    }

    public function storePartner(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'url' => ['nullable', 'string', 'max:255'],
            'order' => ['nullable', 'integer'],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('partners', 'public');
        }

        PartnerLogo::create($validated);

        return back()->with('success', 'Partner logo added.');
    }

    public function destroyPartner(PartnerLogo $partner)
    {
        if ($partner->logo) {
            Storage::disk('public')->delete($partner->logo);
        }
        $partner->delete();

        return back()->with('success', 'Partner logo removed.');
    }
}

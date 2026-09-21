<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\WorkArea;
use App\Support\RichText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PostController extends Controller
{
    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:news,blog'],
            'work_area_id' => ['nullable', 'required_if:category,blog', 'exists:work_areas,id'],
            'body' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function index()
    {
        return Inertia::render('Dashboard/News', [
            'posts' => Post::with('program:id,title')->orderByDesc('published_at')->get(),
            'programs' => WorkArea::ordered()->get(['id', 'title']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $validated['body'] = RichText::clean($validated['body'] ?? null);

        if ($validated['category'] !== 'blog') {
            $validated['work_area_id'] = null;
        }

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $request->file('cover_image')->store('posts', 'public');
        }

        Post::create($validated);

        ActivityLogger::log('created', 'news', 'Published post: ' . $validated['title']);

        return back()->with('success', 'Post published.');
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate($this->rules());
        $validated['body'] = RichText::clean($validated['body'] ?? null);

        if ($validated['category'] !== 'blog') {
            $validated['work_area_id'] = null;
        }

        $validated['updated_by'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            if ($post->cover_image) {
                Storage::disk('public')->delete($post->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('posts', 'public');
        } else {
            unset($validated['cover_image']);
        }

        $post->update($validated);

        ActivityLogger::log('updated', 'news', 'Updated post: ' . $post->title);

        return back()->with('success', 'Post updated.');
    }

    public function toggleActive(Post $post)
    {
        $post->update(['is_active' => !$post->is_active]);

        return back()->with('success', 'Post status updated.');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        ActivityLogger::log('deleted', 'news', 'Deleted post: ' . $post->title);

        return back()->with('success', 'Post removed.');
    }
}

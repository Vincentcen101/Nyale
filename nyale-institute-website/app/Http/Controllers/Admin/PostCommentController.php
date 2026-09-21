<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\CaseComment;
use App\Models\PostComment;
use App\Models\WorkArea;
use Inertia\Inertia;

class PostCommentController extends Controller
{
    public function index()
    {
        // Pending comments first, so nothing waiting for review gets buried.
        return Inertia::render('Dashboard/Comments', [
            // Only Blog posts accept comments.
            'comments' => PostComment::with(['post:id,title,slug,work_area_id', 'post.program:id,title', 'approver:id,name'])
                ->whereHas('post', fn ($q) => $q->where('category', 'blog'))
                ->orderBy('is_approved')->latest()->get(),
            'caseComments' => CaseComment::with(['courtCase:id,title,slug,status', 'approver:id,name'])
                ->orderBy('is_approved')->latest()->get(),
            'programs' => WorkArea::ordered()->get(['id', 'title']),
        ]);
    }

    public function toggleApproval(PostComment $comment)
    {
        return $this->toggle($comment, 'blog/news comment');
    }

    public function toggleCaseApproval(CaseComment $comment)
    {
        return $this->toggle($comment, 'case comment');
    }

    public function destroy(PostComment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }

    public function destroyCaseComment(CaseComment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }

    private function toggle($comment, string $label)
    {
        $approve = !$comment->is_approved;

        $comment->update([
            'is_approved' => $approve,
            'approved_by' => $approve ? auth()->id() : null,
            'approved_at' => $approve ? now() : null,
        ]);

        ActivityLogger::log($approve ? 'approved' : 'unapproved', 'comments', ($approve ? 'Approved' : 'Unapproved') . " {$label} by {$comment->name}");

        return back()->with('success', $approve ? 'Comment approved and now visible on the site.' : 'Comment hidden from the site.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * List all forum topics for a course.
     */
    public function index(Course $course)
    {
        $topics = $course->topics()
            ->with(['user', 'replies'])
            ->withCount('replies')
            ->orderByDesc('created_at')
            ->get();

        return view('forums.index', compact('course', 'topics'));
    }

    /**
     * Show a forum topic thread with replies.
     */
    public function show(Course $course, ForumTopic $topic)
    {
        if ($topic->course_id !== $course->id) {
            abort(404);
        }

        // Load replies with their user, including nested replies
        // To support nesting, we can fetch all replies for the topic,
        // and structure them in a tree format inside the view or here.
        $replies = $topic->replies()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'replies.replies.user']) // up to 3 levels of nesting preloaded
            ->orderBy('created_at')
            ->get();

        return view('forums.show', compact('course', 'topic', 'replies'));
    }

    /**
     * Create a new forum topic.
     */
    public function storeTopic(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        ForumTopic::create([
            'course_id' => $course->id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Topic posted successfully!');
    }

    /**
     * Post a reply.
     */
    public function storeReply(Request $request, Course $course, ForumTopic $topic)
    {
        if ($topic->course_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:forum_replies,id',
        ]);

        ForumReply::create([
            'forum_topic_id' => $topic->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Reply posted successfully!');
    }
}

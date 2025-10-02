<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CounselingCategory;
use Illuminate\Http\Request;
use App\Models\Counselor;

class AdminCounselingCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = CounselingCategory::with('counselor.user');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('counselor.user', function($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $categories = $query->latest()->paginate(10)->appends($request->all());
        return view('admin.counseling-categories.index', compact('categories'));
    }

    public function show(CounselingCategory $counseling_category)
    {
        $counseling_category->load('counselor.user');
        return view('admin.counseling-categories.show', [
            'category' => $counseling_category
        ]);
    }

    public function create()
    {
        $counselors = Counselor::with('user')->get();
        return view('admin.counseling-categories.create', compact('counselors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:counseling_categories',
            'description' => 'required|string|max:1000',
            'counselor_id' => 'required|exists:counselors,id',
            'status' => 'required|in:active,inactive',
        ]);

        CounselingCategory::create($validated);

        return redirect()->route('admin.counseling-categories.index')
            ->with('success', 'Counseling category created successfully.');
    }

    public function edit(CounselingCategory $counseling_category)
    {
        $counselors = Counselor::with('user')->get();
        return view('admin.counseling-categories.edit', [
            'category' => $counseling_category,
            'counselors' => $counselors
        ]);
    }

    public function update(Request $request, CounselingCategory $counseling_category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:counseling_categories,name,' . $counseling_category->id,
            'description' => 'required|string|max:1000',
            'counselor_id' => 'required|exists:counselors,id',
            'status' => 'required|in:active,inactive',
            'admin_feedback' => 'nullable|string|max:500',
        ]);

        $counseling_category->update($validated);

        return redirect()->route('admin.counseling-categories.index')
            ->with('success', 'Counseling category updated successfully.');
    }

    public function destroy(CounselingCategory $counseling_category)
    {
        $counseling_category->delete();
        return redirect()->route('admin.counseling-categories.index')
            ->with('success', 'Counseling category deleted successfully.');
    }

    public function approve(CounselingCategory $counseling_category)
    {
        $counseling_category->update([
            'status' => 'active',
            'admin_feedback' => 'Approved by admin'
        ]);

        return redirect()->back()->with('success', 'Category approved successfully.');
    }

    public function reject(Request $request, CounselingCategory $counseling_category)
    {
        $validated = $request->validate([
            'admin_feedback' => 'required|string|max:500'
        ]);

        $counseling_category->update([
            'status' => 'inactive',
            'admin_feedback' => $validated['admin_feedback']
        ]);

        return redirect()->back()->with('success', 'Category rejected with feedback.');
    }
}
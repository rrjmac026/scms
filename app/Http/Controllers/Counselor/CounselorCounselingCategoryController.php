<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\CounselingCategory;
use Illuminate\Http\Request;

class CounselorCounselingCategoryController extends Controller
{
    public function index()
    {
        $categories = CounselingCategory::where('counselor_id', auth()->user()->counselor->id)
            ->latest()
            ->paginate(10);
            
        return view('counselors.counseling-categories.index', compact('categories'));
    }

    public function show(CounselingCategory $counseling_category)
    {
        // Ensure the counselor can only view their own categories
        if ($counseling_category->counselor_id !== auth()->user()->counselor->id) {
            abort(403, 'Unauthorized access to this category.');
        }

        return view('counselors.counseling-categories.show', [
            'category' => $counseling_category
        ]);
    }

    public function create()
    {
        return view('counselors.counseling-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:counseling_categories',
            'description' => 'required|string|max:1000',
        ]);

        // Add counselor_id and set initial status
        $validated['counselor_id'] = auth()->user()->counselor->id;
        $validated['status'] = 'inactive'; // Requires admin approval

        CounselingCategory::create($validated);

        return redirect()->route('counselors.counseling-categories.index')
            ->with('success', 'Category submitted for approval.');
    }

    public function edit(CounselingCategory $counseling_category)
    {
        // Ensure the counselor can only edit their own categories
        if ($counseling_category->counselor_id !== auth()->user()->counselor->id) {
            abort(403, 'Unauthorized access to this category.');
        }

        // Check if category is already approved - counselors shouldn't edit approved categories
        if ($counseling_category->status === 'active') {
            return redirect()->route('counselors.counseling-categories.show', $counseling_category)
                ->with('warning', 'Cannot edit an approved category. Please contact admin if changes are needed.');
        }

        return view('counselors.counseling-categories.edit', [
            'category' => $counseling_category
        ]);
    }

    public function update(Request $request, CounselingCategory $counseling_category)
    {
        // Ensure the counselor can only update their own categories
        if ($counseling_category->counselor_id !== auth()->user()->counselor->id) {
            abort(403, 'Unauthorized access to this category.');
        }

        // Check if category is already approved
        if ($counseling_category->status === 'active') {
            return redirect()->route('counselors.counseling-categories.show', $counseling_category)
                ->with('error', 'Cannot update an approved category. Please contact admin if changes are needed.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:counseling_categories,name,' . $counseling_category->id,
            'description' => 'required|string|max:1000',
        ]);

        // Reset status to inactive when updated (resubmit for approval)
        $validated['status'] = 'inactive';
        $validated['admin_feedback'] = null; // Clear previous feedback

        $counseling_category->update($validated);

        return redirect()->route('counselors.counseling-categories.index')
            ->with('success', 'Category updated and resubmitted for approval.');
    }

    public function destroy(CounselingCategory $counseling_category)
    {
        // Ensure the counselor can only delete their own categories
        if ($counseling_category->counselor_id !== auth()->user()->counselor->id) {
            abort(403, 'Unauthorized access to this category.');
        }

        // Don't allow deletion of active categories
        if ($counseling_category->status === 'active') {
            return redirect()->route('counselors.counseling-categories.index')
                ->with('error', 'Cannot delete an approved category. Please contact admin.');
        }

        $counseling_category->delete();
        
        return redirect()->route('counselors.counseling-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
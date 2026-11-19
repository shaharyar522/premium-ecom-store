<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $q = Review::with('product.images')->latest();
        if ($request->query('status') === 'pending') $q->where('is_approved', false);
        if ($request->query('status') === 'approved') $q->where('is_approved', true);

        return view('admin.reviews.index', [
            'reviews' => $q->paginate(20)->withQueryString(),
            'pending' => Review::where('is_approved', false)->count(),
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $review->update(['is_approved' => $request->boolean('is_approved')]);

        return back()->with('ok', $review->is_approved ? 'Review published.' : 'Review hidden.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('ok', 'Review deleted.');
    }
}

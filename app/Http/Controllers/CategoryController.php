<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    /**
     * @return Factory|View
     */
    public function index(): Factory|View
    {
        // $categories = Category::withCount('listings')
        //   ->orderBy('name')
        //   ->get();

        return view('pages.categories', [
            // 'categories' => $categories,
        ]);
    }

    /**
     * @param $slug
     * @return Factory|View
     */
    public function show($slug): Factory|View
    {
        // $category = Category::where('slug', $slug)->firstOrFail();
        // $listings = Listing::where('category_id', $category->id)
        //   ->where('status', 'approved')
        //   ->latest()
        //   ->paginate(20);

        return view('pages.category-listings', [
            // 'category' => $category,
            // 'listings' => $listings,
        ]);
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', ['categories' => Category::withCount('products')->orderBy('sort')->orderBy('name')->get()]);
    }

    public function create()
    {
        return view('admin.categories.form', ['category' => new Category(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        Category::create($this->data($request));

        return redirect()->route('admin.categories.index')->with('ok', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.form', ['category' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->data($request, $category));

        return redirect()->route('admin.categories.index')->with('ok', 'Category saved.');
    }

    public function destroy(Category $category)
    {
        Shop::deleteUpload($category->image);
        $category->delete(); // products keep existing, just without a category

        return back()->with('ok', 'Category deleted.');
    }

    private function data(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'slug' => ['nullable', 'alpha_dash', 'max:100', Rule::unique('categories', 'slug')->ignore($category?->id)],
            'tagline' => 'nullable|string|max:160',
            'sort' => 'nullable|integer|min:0',
            'image_file' => 'nullable|image|max:5120',
            'image_url' => 'nullable|url|max:500',
        ]);
        $out = [
            'name' => $data['name'],
            'slug' => $data['slug'] ?: Str::slug($data['name']),
            'tagline' => $data['tagline'] ?? null,
            'sort' => (int) ($data['sort'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ];
        if ($request->hasFile('image_file')) {
            Shop::deleteUpload($category?->image);
            $out['image'] = Shop::upload($request->file('image_file'), 'categories');
        } elseif (! empty($data['image_url'])) {
            $out['image'] = $data['image_url'];
        }

        return $out;
    }
}

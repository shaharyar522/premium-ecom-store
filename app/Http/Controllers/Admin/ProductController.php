<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::with('images', 'category');
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
        }
        if ($c = $request->query('category')) $q->where('category_id', $c);
        match ($request->query('stock')) {
            'low' => $q->where('stock', '>', 0)->where('stock', '<=', (int) Shop::get('low_stock', 5)),
            'out' => $q->where('stock', 0),
            default => null,
        };
        if ($request->query('status') === 'hidden') $q->where('is_active', false);
        if ($request->query('status') === 'featured') $q->where('is_featured', true);

        return view('admin.products.index', [
            'products' => $q->latest()->paginate(20)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.products.form', ['product' => new Product(['is_active' => true, 'stock' => 10]), 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $product = Product::create($this->data($request));
        $this->saveImages($request, $product);

        return redirect()->route('admin.products.edit', $product)->with('ok', 'Product created.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', ['product' => $product->load('images'), 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->data($request, $product));
        $this->saveImages($request, $product);

        return back()->with('ok', 'Product saved.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $img) Shop::deleteUpload($img->path);
        $product->delete();

        return redirect()->route('admin.products.index')->with('ok', 'Product deleted.');
    }

    public function duplicate(Product $product)
    {
        $copy = $product->replicate(['sold']);
        $copy->name = $product->name.' (copy)';
        $copy->slug = $this->uniqueSlug($copy->name);
        $copy->is_active = false;
        $copy->save();
        foreach ($product->images as $img) {
            // The copy points at the same files; deleteImage() only removes a file once no product uses it.
            $copy->images()->create(['path' => $img->path, 'sort' => $img->sort]);
        }

        return redirect()->route('admin.products.edit', $copy)->with('ok', 'Copy created (hidden until you publish it).');
    }

    public function deleteImage(ProductImage $image)
    {
        $shared = ProductImage::where('path', $image->path)->where('id', '!=', $image->id)->exists();
        if (! $shared) Shop::deleteUpload($image->path);
        $image->delete();

        return back()->with('ok', 'Image removed.');
    }

    private function data(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:160',
            'slug' => ['nullable', 'string', 'max:180', 'alpha_dash', Rule::unique('products', 'slug')->ignore($product?->id)],
            'sku' => 'nullable|string|max:60',
            'category_id' => 'nullable|exists:categories,id',
            'short_description' => 'nullable|string|max:300',
            'description' => 'nullable|string|max:10000',
            'price' => 'required|integer|min:0',
            'compare_price' => 'nullable|integer|min:0',
            'stock' => 'required|integer|min:0',
            'sizes' => 'nullable|string|max:200',
            'colors' => 'nullable|string|max:200',
            'badge' => 'nullable|string|max:30',
            'images.*' => 'image|max:5120',
            'image_urls' => 'nullable|string|max:5000',
        ]);

        $list = fn ($s) => ($v = array_values(array_filter(array_map('trim', explode(',', (string) $s))))) ? $v : null;
        $data['sizes'] = $list($data['sizes'] ?? '');
        $data['colors'] = $list($data['colors'] ?? '');
        $data['slug'] = $data['slug'] ?: ($product?->slug ?? $this->uniqueSlug($data['name']));
        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        unset($data['images'], $data['image_urls']);

        return $data;
    }

    private function saveImages(Request $request, Product $product): void
    {
        $sort = (int) $product->images()->max('sort');
        foreach ((array) $request->file('images', []) as $file) {
            $product->images()->create(['path' => Shop::upload($file, 'products'), 'sort' => ++$sort]);
        }
        foreach (preg_split('/\s+/', (string) $request->input('image_urls')) as $url) {
            if (filter_var($url, FILTER_VALIDATE_URL) && str_starts_with($url, 'https://')) {
                $product->images()->create(['path' => $url, 'sort' => ++$sort]);
            }
        }
        // Cover image: chosen image goes first.
        if ($cover = $request->input('cover')) {
            $product->images()->whereKey($cover)->update(['sort' => -1]);
            $product->images()->get()->values()->each(fn ($img, $i) => $img->update(['sort' => $i]));
        }
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'product';
        $slug = $base;
        for ($i = 2; Product::where('slug', $slug)->exists(); $i++) $slug = "{$base}-{$i}";

        return $slug;
    }
}

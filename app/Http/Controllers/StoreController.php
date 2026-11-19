<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function home()
    {
        $base = Product::active()->with('images', 'category');

        return view('store.home', [
            'categories' => Category::active()->withCount(['products' => fn ($q) => $q->active()])->get(),
            'featured' => (clone $base)->where('is_featured', true)->latest()->take(8)->get(),
            'newArrivals' => (clone $base)->latest()->take(8)->get(),
            'bestSellers' => (clone $base)->orderByDesc('sold')->take(8)->get(),
            'reviews' => Review::with('product')->where('is_approved', true)->where('rating', '>=', 4)->latest()->take(6)->get(),
        ]);
    }

    public function shop(Request $request, ?Category $category = null)
    {
        abort_if($category && ! $category->is_active, 404);

        $q = Product::active()->with('images', 'category');
        if ($category) $q->where('category_id', $category->id);
        if ($s = trim((string) $request->query('q'))) {
            $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('short_description', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
        }
        if ($request->filled('min')) $q->where('price', '>=', (int) $request->query('min'));
        if ($request->filled('max')) $q->where('price', '<=', (int) $request->query('max'));
        if ($request->boolean('sale')) $q->whereColumn('compare_price', '>', 'price');
        if ($request->boolean('instock')) $q->where('stock', '>', 0);
        if ($size = $request->query('size')) $q->where('sizes', 'like', '%"'.str_replace(['"', '%'], '', $size).'"%');

        match ($request->query('sort')) {
            'price_asc' => $q->orderBy('price'),
            'price_desc' => $q->orderByDesc('price'),
            'popular' => $q->orderByDesc('sold'),
            default => $q->latest(),
        };

        // Only sizes that exist in this collection, letters in S→XL order, then numbers ascending.
        $order = ['XXS', 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'];
        $sizes = Product::active()->whereNotNull('sizes')->when($category, fn ($w) => $w->where('category_id', $category->id))
            ->pluck('sizes')->flatten()->unique()
            ->sortBy(fn ($s) => is_numeric($s) ? 1000 + (float) $s : (($i = array_search($s, $order, true)) === false ? 999 : $i))
            ->values();

        return view('store.shop', [
            'category' => $category,
            'categories' => Category::active()->get(),
            'products' => $q->paginate(12)->withQueryString(),
            'sizes' => $sizes,
            'priceMax' => (int) Product::active()->max('price'),
        ]);
    }

    public function product(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load('images', 'category', 'approvedReviews');

        $related = Product::active()->with('images')
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->inRandomOrder()->take(4)->get();

        return view('store.product', [
            'product' => $product,
            'related' => $related,
            'rating' => round((float) $product->approvedReviews->avg('rating'), 1),
        ]);
    }

    public function review(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'rating' => 'required|integer|between:1,5',
            'body' => 'required|string|max:1500',
        ]);
        $product->reviews()->create($data + ['is_approved' => false]);

        return back()->with('ok', 'Thank you! Your review will appear once it is approved.');
    }

    public function wishlist(Request $request)
    {
        $ids = array_filter(array_map('intval', explode(',', (string) $request->query('ids'))));

        return view('store.wishlist', [
            'products' => $ids ? Product::active()->with('images')->whereIn('id', $ids)->get() : collect(),
            'loaded' => $request->has('ids'),
        ]);
    }

    public function track(Request $request)
    {
        $order = null;
        if ($request->filled('number')) {
            $digits = fn ($p) => substr(preg_replace('/\D/', '', (string) $p), -10);
            $order = Order::with('items', 'events')->where('number', strtoupper(trim($request->query('number'))))->first();
            if ($order && $digits($order->phone) !== $digits($request->query('phone'))) $order = null;
        }

        return view('store.track', ['order' => $order, 'searched' => $request->filled('number')]);
    }

    public function contact()
    {
        return view('store.contact');
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:80',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:30',
            'body' => 'required|string|max:3000',
        ]);
        Message::create($data);

        return back()->with('ok', 'Message sent — we usually reply within a few hours.');
    }

    public function subscribe(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:150']);
        Subscriber::firstOrCreate(['email' => strtolower($data['email'])]);

        return back()->with('ok', 'You are on the list. Welcome!');
    }

    public function page(string $page)
    {
        return view('pages.'.$page);
    }
}

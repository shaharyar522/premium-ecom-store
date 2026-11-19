<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Demo store: categories, products with free Pexels photos, reviews, coupons and ~60 days of orders.
 * For a real client: php artisan migrate:fresh --seed --seeder=CleanStoreSeeder (admin account only).
 */
class DatabaseSeeder extends Seeder
{
    private static function px(int $id, int $w = 1200): string
    {
        return "https://images.pexels.com/photos/{$id}/pexels-photo-{$id}.jpeg?auto=compress&cs=tinysrgb&w={$w}";
    }

    public function run(): void
    {
        mt_srand(42);
        $this->call(CleanStoreSeeder::class);

        $cats = [
            'women' => ['Women', 'Effortless silhouettes', 1858488],
            'men' => ['Men', 'Sharp, modern tailoring', 1043474],
            'bags' => ['Bags', 'Carry it beautifully', 1152077],
            'footwear' => ['Footwear', 'Every step, considered', 2529148],
            'accessories' => ['Watches & Accessories', 'The finishing touch', 190819],
            'beauty' => ['Beauty', 'Glow, naturally', 1502219],
        ];
        $cat = [];
        $i = 0;
        foreach ($cats as $slug => [$name, $tagline, $img]) {
            $cat[$slug] = Category::create(['name' => $name, 'slug' => $slug, 'tagline' => $tagline, 'image' => self::px($img), 'sort' => $i++]);
        }

        $apparel = ['XS', 'S', 'M', 'L', 'XL'];
        $shoes = ['39', '40', '41', '42', '43', '44'];
        // [category, name, price, compare, stock, sizes, colors, [pexels ids], badge, featured, short description]
        $products = [
            ['women', 'Ochre Wool Overcoat', 14500, 18000, 12, ['S', 'M', 'L', 'XL'], ['Ochre'], [1858488], 'New', true, 'A relaxed, double-faced wool blend coat with dropped shoulders and deep patch pockets.'],
            ['women', 'Blush Keyhole Blouse', 4200, null, 24, $apparel, ['Blush', 'Ivory'], [1036623], null, false, 'Soft crepe blouse with a delicate keyhole neckline and short sleeves.'],
            ['women', 'Striped Linen Shirt Dress', 6800, 8500, 18, $apparel, ['Blue Stripe'], [1462637], 'Sale', true, 'Breathable pure linen, a self-tie belt and an easy midi length for warm days.'],
            ['women', 'Bandeau Crop & Skirt Set', 5900, null, 9, ['XS', 'S', 'M', 'L'], ['Black / Camel'], [1152994], null, false, 'A two-piece set: fitted bandeau top and a high-waisted camel midi skirt.'],
            ['women', 'Everyday Black Jacket', 11900, null, 15, $apparel, ['Black'], [1689731], null, true, 'A clean-lined cotton-twill jacket that layers over everything you own.'],
            ['women', 'Printed Maxi Dress', 7500, 9200, 4, $apparel, ['Paisley'], [1078958], 'Sale', false, 'Flowing viscose maxi with an all-over heritage paisley print.'],
            ['men', 'Washed Denim Jacket', 8900, 10500, 20, ['S', 'M', 'L', 'XL'], ['Mid Blue'], [1040945], null, true, 'A lived-in trucker jacket in rigid cotton denim with a soft enzyme wash.'],
            ['men', 'Mustard Pullover Hoodie', 5200, null, 30, ['S', 'M', 'L', 'XL'], ['Mustard', 'Black'], [1183266], 'Bestseller', true, 'Heavyweight brushed-back fleece hoodie with a roomy, relaxed fit.'],
            ['men', 'Oxford Slim Shirt', 4800, null, 26, ['S', 'M', 'L', 'XL'], ['Sky Blue', 'White'], [1043474], null, false, 'Crisp cotton oxford with a button-down collar, cut slim through the body.'],
            ['men', 'Biker Leather Jacket', 24000, null, 6, ['M', 'L', 'XL'], ['Black'], [1124468], 'Limited', true, 'Full-grain lambskin with an asymmetric zip, quilted shoulders and silver hardware.'],
            ['men', 'Burgundy Two-Piece Suit', 32000, 38000, 5, ['48', '50', '52', '54'], ['Burgundy'], [1300550], 'Sale', false, 'Half-canvassed wool suit with peak lapels and a slim tapered trouser.'],
            ['men', 'Gingham Dress Shirt', 4500, null, 22, ['S', 'M', 'L', 'XL'], ['Grey Check'], [297933, 996329], null, false, 'Fine two-ply cotton in a micro gingham check, with a contrast inner collar.'],
            ['men', 'Straight Leg Denim', 5900, null, 40, ['30', '32', '34', '36'], ['Light Wash', 'Indigo'], [4210866], null, false, 'A timeless straight leg in 12oz comfort-stretch denim.'],
            ['men', 'Relaxed Cotton Tee', 2400, null, 60, $apparel, ['White', 'Sand', 'Black'], [1549200, 3735641], 'Bestseller', false, 'Our everyday tee: 220gsm combed cotton, boxy fit and a ribbed crew neck.'],
            ['bags', 'Heritage Leather Messenger', 16500, null, 10, null, ['Tan'], [1152077], null, true, 'Vegetable-tanned leather messenger with brass buckles that age beautifully.'],
            ['bags', 'Porcelain Chain Bag', 9800, 12000, 8, null, ['White / Blue'], [904350], 'Sale', false, 'Structured mini bag with a porcelain-inspired print and a chain shoulder strap.'],
            ['bags', 'Voyager Canvas Backpack', 7900, null, 18, null, ['Cobalt'], [2905238], null, false, 'Waxed canvas backpack with leather trims and a padded laptop sleeve.'],
            ['bags', 'Essential Canvas Tote', 2900, null, 45, null, ['Black', 'Natural'], [1214212], null, false, 'Heavy cotton canvas tote with an inside pocket — for groceries, books and everything else.'],
            ['bags', 'Rose Patent Frame Bag', 8500, null, 3, null, ['Rose'], [1381553], null, false, 'Glossy patent frame bag with a kiss-lock closure and top handle.'],
            ['bags', 'Noir Structured Handbag', 13500, null, 7, null, ['Black / Red'], [1374910], 'New', true, 'A sculpted top-handle bag with a contrast lining and gold-tone ring detail.'],
            ['footwear', 'Aero Knit Runners', 9500, 11000, 16, $shoes, ['Grey / Yellow'], [2529148], null, true, 'Featherlight knit runners with a responsive foam sole for all-day comfort.'],
            ['footwear', 'Coral Street Sneakers', 7200, null, 14, $shoes, ['Coral'], [1306248], 'New', false, 'Bold suede-mix sneakers with a cushioned insole and grippy rubber outsole.'],
            ['footwear', 'Classic Slip-On Canvas', 4900, null, 25, $shoes, ['Slate'], [1639729], null, false, 'Easy slip-ons in washed canvas with elastic side gores.'],
            ['accessories', 'Chronograph Steel Watch', 28000, 32000, 6, null, ['Steel'], [190819], 'Sale', true, '42mm stainless steel chronograph with sapphire-coated crystal and 5 ATM water resistance.'],
            ['accessories', 'Midnight Mesh Watch', 12500, null, 11, null, ['Navy'], [277390], null, false, 'Slim navy dial on a quick-release mesh strap. Japanese quartz movement.'],
            ['accessories', 'Minimal White Dial Watch', 15800, null, 9, null, ['Black Leather'], [1697214], null, true, 'An ultra-clean white dial with a soft leather strap — dress it up or down.'],
            ['accessories', 'Sunset Aviator Sunglasses', 3800, null, 35, null, ['Gold / Amber'], [46710], null, false, 'Classic metal aviators with UV400 gradient amber lenses.'],
            ['accessories', 'Layered Charm Bracelets', 2600, null, 2, null, ['Gold'], [1191531], null, false, 'A set of three dainty bracelets with charms, plated in 18k gold.'],
            ['accessories', 'Solitaire Ring Gift Box', 19500, null, 4, ['5', '6', '7', '8'], ['Silver'], [1927259], 'Gift', false, 'Sterling silver solitaire in a velvet presentation box — ready to give.'],
            ['beauty', 'Nude Eyeshadow Palette', 3400, null, 28, null, null, [2113855], null, false, 'Twelve buttery mattes and shimmers in wearable warm nudes.'],
            ['beauty', 'Glow Skincare Ritual Set', 6900, 8200, 13, null, null, [1502219], 'Bestseller', true, 'Cleanser, vitamin C serum and moisturiser — your complete morning routine.'],
            ['beauty', 'Velvet Matte Lipstick', 1800, null, 50, null, ['Cobalt Case'], [3373736], null, false, 'Long-wear, non-drying matte colour in a refillable case.'],
            ['women', 'Fine Knit Lounge Set', 6400, null, 0, $apparel, ['Oat'], [1937336], null, false, 'Soft fine-gauge knit top and wide trousers — currently sold out, back soon.'],
        ];

        $created = [];
        foreach ($products as $n => [$c, $name, $price, $compare, $stock, $sizes, $colors, $imgs, $badge, $featured, $short]) {
            $p = Product::create([
                'category_id' => $cat[$c]->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'sku' => strtoupper(substr($c, 0, 3)).'-'.str_pad((string) ($n + 101), 4, '0', STR_PAD_LEFT),
                'short_description' => $short,
                'description' => $short."\n\nWhy you'll love it:\n• Premium materials, checked by hand before dispatch\n• True to size — see the size guide for exact measurements\n• Delivered in our signature gift-ready packaging\n\nCare: follow the label inside. Store away from direct sunlight.",
                'price' => $price,
                'compare_price' => $compare,
                'stock' => $stock,
                'sizes' => $sizes,
                'colors' => $colors,
                'badge' => $badge,
                'is_featured' => $featured,
                'sold' => mt_rand(3, 120),
                'created_at' => now()->subDays(count($products) - $n),
            ]);
            foreach ($imgs as $k => $id) $p->images()->create(['path' => self::px($id), 'sort' => $k]);
            $created[] = $p;
        }

        $names = ['Ayesha K.', 'Hamza R.', 'Sara M.', 'Bilal A.', 'Zara T.', 'Usman F.', 'Mahnoor S.', 'Ali H.', 'Fatima N.', 'Omar Q.'];
        $bodies = [
            [5, 'Exactly like the pictures. The quality is far better than I expected for the price.'],
            [5, 'Beautiful packaging and super fast delivery. Will definitely order again!'],
            [4, 'Lovely piece, fits true to size. Delivery took a day longer than expected.'],
            [5, 'Premium feel — the stitching and material are excellent. Highly recommended.'],
            [4, 'Really happy with it. Colour is slightly warmer in person but still gorgeous.'],
            [5, 'Customer service helped me pick the right size on WhatsApp. Perfect fit.'],
        ];
        foreach ($created as $p) {
            foreach (range(1, mt_rand(1, 4)) as $_) {
                [$r, $b] = $bodies[array_rand($bodies)];
                $p->reviews()->create(['name' => $names[array_rand($names)], 'rating' => $r, 'body' => $b, 'is_approved' => true, 'created_at' => now()->subDays(mt_rand(1, 60))]);
            }
        }
        Review::create(['product_id' => $created[0]->id, 'name' => 'Hina J.', 'rating' => 5, 'body' => 'Is this available in a darker colour too? Love the cut.', 'is_approved' => false]);

        Coupon::create(['code' => 'WELCOME10', 'type' => 'percent', 'value' => 10, 'is_active' => true]);
        Coupon::create(['code' => 'FLAT500', 'type' => 'fixed', 'value' => 500, 'min_subtotal' => 4000, 'max_uses' => 200, 'is_active' => true]);
        Coupon::create(['code' => 'SUMMER25', 'type' => 'percent', 'value' => 25, 'expires_at' => now()->subMonth(), 'is_active' => true]);

        // ~60 days of demo orders so the dashboard has real-looking numbers.
        $people = [['Ayesha Khan', '03001234567', 'Lahore'], ['Hamza Raza', '03111234567', 'Karachi'], ['Sara Malik', '03211234567', 'Islamabad'],
            ['Bilal Ahmed', '03331234567', 'Rawalpindi'], ['Zara Tariq', '03451234567', 'Faisalabad'], ['Usman Farooq', '03021234567', 'Multan'],
            ['Mahnoor Shah', '03121234567', 'Peshawar'], ['Ali Hassan', '03221234567', 'Lahore'], ['Fatima Noor', '03341234567', 'Karachi'],
            ['Omar Qureshi', '03461234567', 'Islamabad'], ['Hina Javed', '03031234567', 'Sialkot'], ['Daniyal Iqbal', '03131234567', 'Lahore']];
        $flow = ['delivered', 'delivered', 'delivered', 'delivered', 'shipped', 'processing', 'confirmed', 'pending', 'cancelled', 'returned'];
        for ($o = 0; $o < 70; $o++) {
            [$name, $phone, $city] = $people[array_rand($people)];
            $at = now()->subDays(mt_rand(0, 59))->setTime(mt_rand(9, 23), mt_rand(0, 59));
            $age = now()->diffInDays($at, true);
            $status = $age < 2 ? ['pending', 'confirmed'][mt_rand(0, 1)] : ($age < 5 ? ['processing', 'shipped', 'confirmed'][mt_rand(0, 2)] : $flow[array_rand($flow)]);
            $items = collect($created)->where('stock', '>', 0)->random(mt_rand(1, 3));
            $sub = 0;
            $lines = [];
            foreach ($items as $p) {
                $qty = mt_rand(1, 2);
                $sub += $p->price * $qty;
                $lines[] = ['product_id' => $p->id, 'name' => $p->name, 'image' => $p->images()->value('path'), 'options' => $p->sizes ? $p->sizes[array_rand($p->sizes)] : null, 'price' => $p->price, 'qty' => $qty];
            }
            $ship = $sub >= 5000 ? 0 : 250;
            $order = Order::create([
                'number' => 'ORD-'.(500000 + $o * 137),
                'name' => $name, 'phone' => $phone, 'email' => Str::slug($name, '.').'@example.com',
                'address' => 'House '.mt_rand(1, 400).', Street '.mt_rand(1, 40).', Block '.chr(mt_rand(65, 72)), 'city' => $city,
                'subtotal' => $sub, 'discount' => 0, 'shipping' => $ship, 'total' => $sub + $ship,
                'status' => $status, 'payment_method' => mt_rand(0, 4) ? 'cod' : 'bank',
                'payment_status' => $status === 'delivered' ? 'paid' : ($status === 'returned' ? 'refunded' : 'unpaid'),
                'tracking_number' => in_array($status, ['shipped', 'delivered'], true) ? 'TCS'.mt_rand(10000000, 99999999) : null,
                'created_at' => $at, 'updated_at' => $at,
            ]);
            foreach ($lines as $l) $order->items()->create($l);
            $order->events()->create(['status' => 'pending', 'note' => 'Order placed', 'created_at' => $at]);
            $steps = array_slice(Order::FLOW, 1, max(0, array_search($status, Order::FLOW, true) ?: 0));
            foreach ($steps as $k => $s) $order->events()->create(['status' => $s, 'created_at' => (clone $at)->addHours(($k + 1) * 14)]);
            if (in_array($status, ['cancelled', 'returned'], true)) $order->events()->create(['status' => $status, 'created_at' => (clone $at)->addDay()]);
        }

        User::create(['name' => 'Ayesha Khan', 'email' => 'customer@example.com', 'phone' => '03001234567', 'password' => 'password', 'role' => 'customer', 'city' => 'Lahore']);
        Order::where('phone', '03001234567')->update(['user_id' => User::where('email', 'customer@example.com')->value('id')]);

        Message::create(['name' => 'Sana Iqbal', 'email' => 'sana@example.com', 'phone' => '03009876543', 'body' => 'Hi! Do you deliver to Gilgit, and how long does it usually take?']);
        Message::create(['name' => 'Ahmed Ali', 'email' => 'ahmed@example.com', 'body' => 'I would like to place a bulk order of 20 canvas totes for a corporate event. Is there a discount?', 'is_read' => true]);
        foreach (['hello@example.com', 'style@example.com', 'news@example.com'] as $e) Subscriber::create(['email' => $e]);
    }
}

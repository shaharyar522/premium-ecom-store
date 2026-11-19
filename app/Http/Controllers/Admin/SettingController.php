<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Shop;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /** Settings form, grouped into sections: key => [label, type, help]. */
    public const GROUPS = [
        'Brand' => [
            'store_name' => ['Store name', 'text'],
            'tagline' => ['Tagline', 'text'],
            'logo' => ['Logo', 'image', 'PNG/SVG with transparent background. Leave empty to use the store name as a text logo.'],
            'favicon' => ['Browser icon (favicon)', 'image'],
            'accent' => ['Brand colour', 'color', 'Buttons, links and highlights.'],
            'dark' => ['Dark colour', 'color', 'Text and dark sections.'],
            'meta_description' => ['Search engine description', 'textarea'],
        ],
        'Hero slider' => [
            'hero_image' => ['Slide 1 — image', 'image', 'Portrait photo (taller than wide), at least 1200 px tall. The last word of each title is shown in outline style.'],
            'hero_side_image' => ['Small side photo', 'image', 'The small photo beside the arch, e.g. a product close-up.'],
            'hero_eyebrow' => ['Slide 1 — small heading', 'text'],
            'hero_title' => ['Slide 1 — title', 'text'],
            'hero_subtitle' => ['Slide 1 — text', 'textarea'],
            'hero_cta' => ['Button text (all slides)', 'text'],
            'hero_image_2' => ['Slide 2 — image', 'image', 'Remove the title to skip this slide.'],
            'hero2_eyebrow' => ['Slide 2 — small heading', 'text'],
            'hero2_title' => ['Slide 2 — title', 'text'],
            'hero2_subtitle' => ['Slide 2 — text', 'textarea'],
            'hero_image_3' => ['Slide 3 — image', 'image', 'Remove the title to skip this slide.'],
            'hero3_eyebrow' => ['Slide 3 — small heading', 'text'],
            'hero3_title' => ['Slide 3 — title', 'text'],
            'hero3_subtitle' => ['Slide 3 — text', 'textarea'],
        ],
        'Homepage' => [
            'announcement' => ['Announcement bar', 'text', 'Thin bar at the very top. Leave empty to hide it.'],
            'promo_title' => ['Promo banner title', 'text'],
            'promo_text' => ['Promo banner text', 'textarea'],
            'promo_image' => ['Promo banner image', 'image'],
            'about' => ['About text', 'textarea', 'Used on the homepage story block and the About page.'],
        ],
        'Contact & social' => [
            'phone' => ['Phone', 'text'],
            'whatsapp' => ['WhatsApp number', 'text', 'International format, digits only, e.g. 923001234567.'],
            'email' => ['Email', 'text'],
            'address' => ['Address', 'text'],
            'instagram' => ['Instagram URL', 'text'],
            'facebook' => ['Facebook URL', 'text'],
            'tiktok' => ['TikTok URL', 'text'],
        ],
        'Checkout & shipping' => [
            'currency' => ['Currency symbol', 'text'],
            'currency_after' => ['Show currency after the amount', 'toggle'],
            'shipping_fee' => ['Delivery charge', 'number'],
            'free_shipping_over' => ['Free delivery over', 'number', '0 = never free.'],
            'cod_enabled' => ['Cash on delivery', 'toggle'],
            'bank_enabled' => ['Bank transfer', 'toggle'],
            'bank_details' => ['Bank details (shown at checkout)', 'textarea'],
            'low_stock' => ['Low-stock alert at', 'number', 'Dashboard warns when stock falls to this number.'],
        ],
    ];

    public function edit()
    {
        return view('admin.settings', ['groups' => self::GROUPS, 's' => Shop::settings()]);
    }

    public function update(Request $request)
    {
        $rules = [];
        foreach (self::GROUPS as $fields) {
            foreach ($fields as $key => [$label, $type]) {
                $rules[$key] = match ($type) {
                    'image' => 'nullable|image|max:5120',
                    'color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
                    'number' => 'nullable|integer|min:0',
                    'toggle' => 'nullable',
                    default => 'nullable|string|max:2000',
                };
            }
        }
        $request->validate($rules);

        $current = Shop::settings();
        foreach (self::GROUPS as $fields) {
            foreach ($fields as $key => [$label, $type]) {
                if ($type === 'image') {
                    if ($request->boolean("remove_{$key}")) {
                        // Back to the default image from config/shop.php.
                        Shop::deleteUpload($current[$key] ?? null);
                        Setting::whereKey($key)->delete();
                        continue;
                    } elseif ($request->hasFile($key)) {
                        Shop::deleteUpload($current[$key] ?? null);
                        $value = Shop::upload($request->file($key), 'brand');
                    } else {
                        continue;
                    }
                } elseif ($type === 'toggle') {
                    $value = $request->boolean($key) ? '1' : '0';
                } else {
                    $value = (string) $request->input($key, '');
                }
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }
        Shop::forgetSettings();

        return back()->with('ok', 'Settings saved.');
    }
}

<?php

/*
|--------------------------------------------------------------------------
| Store defaults
|--------------------------------------------------------------------------
| Everything here can be changed from Admin → Settings (saved in the `settings`
| table, which overrides these values). For a new client you normally only
| change the settings page and upload their products — no code changes.
*/

return [
    'store_name' => 'Maison',
    'tagline' => 'Modern essentials, thoughtfully made',
    'logo' => null,            // uploads/... or URL; null = text logo
    'favicon' => null,
    'accent' => '#9A6B47',     // brand colour (buttons, links, highlights)
    'dark' => '#1B1714',       // ink / dark sections

    'currency' => 'Rs',
    'currency_after' => false, // true → "1,200 Rs"

    'phone' => '+92 300 0000000',
    'whatsapp' => '923000000000', // digits only, used for wa.me links
    'email' => 'hello@maison.store',
    'address' => 'Studio 4, Main Boulevard, Lahore',
    'instagram' => 'https://instagram.com/',
    'facebook' => 'https://facebook.com/',
    'tiktok' => 'https://tiktok.com/',

    'announcement' => 'Free delivery on orders over Rs 5,000 · Easy 7-day exchanges',

    // Hero slider — up to 3 slides. Portrait photos (taller than wide) look best in the arched frame.
    // The last word of each title is drawn in outline style.
    'hero_eyebrow' => 'New Season 2026',
    'hero_title' => 'Style that speaks first',
    'hero_subtitle' => 'Premium pieces in honest materials — designed to be worn, loved and kept for years.',
    'hero_image' => 'https://images.pexels.com/photos/1689731/pexels-photo-1689731.jpeg',
    'hero_cta' => 'Shop the collection',
    'hero2_eyebrow' => 'The Autumn Edit',
    'hero2_title' => 'The colour of confidence',
    'hero2_subtitle' => 'Bold coats and warm tones for cooler days. Up to 30% off for a limited time.',
    'hero_image_2' => 'https://images.pexels.com/photos/1858488/pexels-photo-1858488.jpeg',
    'hero3_eyebrow' => 'Menswear',
    'hero3_title' => 'Leather, simply redefined',
    'hero3_subtitle' => 'Full-grain jackets, sharp shirts and tailoring cut for everyday confidence.',
    'hero_image_3' => 'https://images.pexels.com/photos/1124468/pexels-photo-1124468.jpeg',
    'hero_side_image' => 'https://images.pexels.com/photos/1152077/pexels-photo-1152077.jpeg',

    'promo_title' => 'Up to 30% off selected styles',
    'promo_text' => 'Our seasonal sale is on — refresh your wardrobe with pieces you\'ll wear for years.',
    'promo_image' => 'https://images.pexels.com/photos/1036623/pexels-photo-1036623.jpeg',

    'about' => 'We started with a simple idea: fewer, better things. Every piece is chosen for its material, its fit and how long it will last — then priced fairly and delivered to your door.',

    'shipping_fee' => 250,
    'free_shipping_over' => 5000,
    'cod_enabled' => '1',
    'bank_enabled' => '1',
    'bank_details' => "Bank: Meezan Bank\nTitle: Maison Store\nIBAN: PK00 MEZN 0000 0000 0000 0000",
    'low_stock' => 5,

    'meta_description' => 'Premium clothing, bags, shoes and accessories — delivered nationwide with cash on delivery.',
];

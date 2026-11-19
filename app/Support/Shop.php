<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class Shop
{
    /** Store setting: admin value from the DB, else the default in config/shop.php. */
    public static function get(string $key, $default = null)
    {
        $all = self::settings();

        return array_key_exists($key, $all) && $all[$key] !== null && $all[$key] !== '' ? $all[$key] : $default;
    }

    public static function settings(): array
    {
        static $memo = null;
        if ($memo !== null) return $memo;

        $db = [];
        try {
            $db = Cache::rememberForever('shop.settings', fn () => Setting::pluck('value', 'key')->all());
        } catch (Throwable) {
            // Fresh install before migrations: use defaults.
        }

        // A saved empty value means "hide / none" (e.g. no announcement bar), so it overrides the default.
        return $memo = array_merge(config('shop'), $db);
    }

    public static function forgetSettings(): void
    {
        Cache::forget('shop.settings');
    }

    public static function money(int|float|null $amount): string
    {
        $n = number_format((float) $amount);
        $c = self::get('currency', 'Rs');

        return self::get('currency_after') ? "{$n} {$c}" : "{$c} {$n}";
    }

    /**
     * Public URL for an image stored either as a full URL (stock photos) or a path under public/.
     * Pexels / Unsplash URLs are resized on their CDN so cards never download full-size photos.
     */
    public static function img(?string $src, int $w = 800): string
    {
        if (! $src) return asset('img/placeholder.svg');
        if (str_starts_with($src, 'http')) {
            if (str_contains($src, 'images.pexels.com')) {
                $base = strtok($src, '?');

                return "{$base}?auto=compress&cs=tinysrgb&w={$w}";
            }
            if (str_contains($src, 'images.unsplash.com')) {
                return strtok($src, '?')."?auto=format&fit=crop&q=80&w={$w}";
            }

            return $src;
        }

        return asset($src);
    }

    /** "#9A6B47" → "154 107 71" for rgb(var(--x) / a) in CSS. */
    public static function rgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) $hex = preg_replace('/(.)/', '$1$1', $hex);
        $n = hexdec(substr($hex.'000000', 0, 6));

        return (($n >> 16) & 255).' '.(($n >> 8) & 255).' '.($n & 255);
    }

    public static function whatsappUrl(?string $text = null): string
    {
        return 'https://wa.me/'.preg_replace('/\D/', '', (string) self::get('whatsapp')).($text ? '?text='.rawurlencode($text) : '');
    }

    /** Save an uploaded image under public/uploads/{folder} and return its relative path. */
    public static function upload(\Illuminate\Http\UploadedFile $file, string $folder): string
    {
        $dir = public_path("uploads/{$folder}");
        if (! is_dir($dir)) @mkdir($dir, 0775, true);
        $name = bin2hex(random_bytes(10)).'.'.strtolower($file->guessExtension() ?: 'jpg');
        $file->move($dir, $name);

        return "uploads/{$folder}/{$name}";
    }

    /** Delete a previously uploaded local file (stock-photo URLs are left alone). */
    public static function deleteUpload(?string $path): void
    {
        if ($path && str_starts_with($path, 'uploads/')) @unlink(public_path($path));
    }

    public static function hasTables(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (Throwable) {
            return false;
        }
    }
}

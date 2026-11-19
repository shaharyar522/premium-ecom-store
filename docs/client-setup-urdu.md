# Premium Store — reusable online store template

Ek mukammal online store (storefront + admin dashboard) jo har naye client ke liye
sirf **settings badal kar aur products daal kar** live kiya ja sakta hai. Code mein koi tabdeeli nahi chahiye.

- Laravel 12 · Blade · plain CSS/JS (koi `npm build` nahi), is liye shared hosting (Hostinger waghera) pe asaani se chalta hai
- Database: SQLite (default, koi setup nahi) ya MySQL
- Har screen mobile, tablet aur desktop pe responsive hai

---

## Local pe kholna (XAMPP)

| | URL |
|---|---|
| Store | http://localhost/theorignals/laravel-app/premium-store/public/ |
| Admin | http://localhost/theorignals/laravel-app/premium-store/public/admin |

**Demo logins**

| Kaun | Email | Password |
|---|---|---|
| Owner (admin) | `admin@store.test` | `admin12345` |
| Customer | `customer@example.com` | `password` |

Demo coupons: `WELCOME10` (10%), `FLAT500` (Rs 500 off, Rs 4,000 se upar), `SUMMER25` (expired, test ke liye).

Demo data dobara banana ho to:
```
php artisan migrate:fresh --seed
```

---

## Naye client ke liye store banana (step by step)

1. **Folder copy karein**: `premium-store` ko naye naam se copy karein (e.g. `client-ayesha-store`).
2. **Khaali store banayein** (sirf owner account, koi demo product nahi):
   ```
   php artisan migrate:fresh --seeder=CleanStoreSeeder
   ```
   `.env` mein `ADMIN_EMAIL=` aur `ADMIN_PASSWORD=` likh dein to woh login ban jayega.
3. **Admin → Settings** mein jayein aur bharein:
   - Store ka naam, tagline, **logo**, favicon
   - **Brand colour** (poori website ka rang isi se badalta hai)
   - Homepage: hero title, text, hero images, promo banner
   - Phone, **WhatsApp number**, email, address, Instagram/Facebook/TikTok
   - Delivery charge, free delivery limit, COD / bank transfer, bank details
4. **Categories** banayein (photo ke saath).
5. **Products** daalein: photos upload karein (ya Pexels/Unsplash ka link paste karein), price, "was" price (sale badge ke liye), stock, sizes, colours.
6. Client ke liye **Staff** mein unka owner account bana dein aur demo admin hata dein.

---

## Admin dashboard mein kya hai

| Page | Kaam |
|---|---|
| **Dashboard** | Revenue, orders, average order, naye customers (7/30/90 din), sales chart, status breakdown, top products, low-stock alert |
| **Orders** | Status tabs, search, date/payment filters, bulk "mark shipped/delivered", CSV export, order detail, timeline, courier tracking #, printable invoice, ek click WhatsApp confirmation |
| **Products** | Add/edit/duplicate/delete, kai photos, cover photo, sizes, colours, stock, featured, badge, hide/show |
| **Categories** | Photo, tagline, order, hide/show |
| **Customers** | Har khareedar (guest COD wale bhi, phone se), kitna kharch kiya, order history |
| **Coupons** | % ya fixed, minimum order, usage limit, expiry |
| **Reviews** | Customer reviews approve/hide/delete (approve ke baad hi store pe dikhte hain) |
| **Messages** | Contact form ke messages, email/WhatsApp se jawab |
| **Subscribers** | Newsletter list + CSV export |
| **Settings** | Upar wali saari branding / checkout settings (sirf owner) |
| **Staff** | Team members: *Owner* (sab kuch) ya *Staff* (orders/products/customers) |

Order cancel ya return karne pe stock **khud wapas** aa jata hai. COD order "Delivered" karne pe payment khud "Paid" ho jati hai.

## Storefront mein kya hai

Home (hero, categories, featured/new tabs, promo banner, bestsellers, story, reviews), shop (category, size, price, sale, stock filters + sorting + search), product page (gallery + zoom, size/colour, stock alert, Buy now, **Order on WhatsApp**, reviews, related products, mobile sticky "Add to bag"), slide-out cart, coupon, checkout (COD / bank transfer), thank-you page, **order tracking** (order # + phone), customer account, wishlist, contact, FAQ, delivery, returns, privacy, terms. WhatsApp floating button har page pe.

---

## Live karna (Hostinger / cPanel)

1. Poora folder upload karein, domain ka *document root* `public/` folder pe set karein.
2. Server pe `.env` banayein: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://clientdomain.com`.
3. MySQL use karna ho to `.env` mein `DB_CONNECTION=mysql` aur `DB_HOST/DB_DATABASE/DB_USERNAME/DB_PASSWORD` bharein.
4. Chalayein:
   ```
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --force --seeder=CleanStoreSeeder --seed
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```
5. `public/uploads` folder writable hona chahiye (product photos yahin save hoti hain).

## Files jo aap badal sakte hain

- `config/shop.php`: settings ki default values (naya client admin se badal leta hai)
- `public/css/store.css`: storefront design (sab rang CSS variables mein hain)
- `public/css/admin.css`: dashboard design
- `database/seeders/DatabaseSeeder.php`: demo products (Pexels ki free photos)

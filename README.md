# Smart Delivery Verification System

A multi-role (Customer / Rider / Admin) e-commerce site with **OTP + photo-proof delivery verification**, built with HTML, CSS, JavaScript, PHP, and MySQL — matching your project proposal for CSE 3210.

---

## 1. Requirements

- **XAMPP** (or WAMP/MAMP) — gives you PHP + MySQL + Apache in one install.
  Download: https://www.apachefriends.org/
- **VS Code** with the **PHP Intelephense** extension (optional but helpful) and **PHP Server** or just use XAMPP's Apache directly.

---

## 2. Setup Steps (15 minutes)

1. **Install XAMPP.** Start the **Apache** and **MySQL** modules from the XAMPP Control Panel.

2. **Copy this whole `smart-delivery` folder** into XAMPP's `htdocs` directory:
   - Windows: `C:\xampp\htdocs\smart-delivery` (your install path may differ — e.g. `C:\xampp_new\htdocs\` — check your XAMPP Control Panel title bar or install location if `C:\xampp\` doesn't exist on your PC)
   - Mac: `/Applications/XAMPP/htdocs/smart-delivery`
   - Linux: `/opt/lampp/htdocs/smart-delivery`

3. **Create the database:**
   - Open `http://localhost/phpmyadmin` in your browser.
   - Click **New** (left sidebar) → name it `smart_delivery` → Create.
   - Click the **Import** tab → choose the file `sql/database.sql` from this project → click **Go**.
   - This creates all 4 tables (`users`, `products`, `orders`, `order_items`) and 6 sample products.

4. **Seed demo accounts** (Admin, Rider, Customer logins):
   - Visit `http://localhost/smart-delivery/seed.php` in your browser **once**.
   - It will print out 3 accounts it created. You'll see:
     - Admin: `admin@delivery.com` / `admin123`
     - Rider: `rider@delivery.com` / `rider123`
     - Customer: `customer@delivery.com` / `cust123`
   - **Delete `seed.php`** afterward (or just don't visit it again) — it's only meant to run once.

5. **Open the project in VS Code** (`File > Open Folder` → select `smart-delivery`) so you can read/edit the code.

6. **Visit the site:** `http://localhost/smart-delivery/`

That's it — no `npm install`, no build step. It's plain PHP, so Apache just runs it directly.

---

## 3. Folder Structure

```
smart-delivery/
├── index.php                  Landing page
├── login.php                  Login (all roles)
├── register.php                Registration (all roles)
├── logout.php
├── seed.php                    One-time demo account creator (delete after use)
├── css/style.css               All styling (responsive)
├── js/
│   ├── validate.js             Registration form validation
│   ├── cart.js                 Shopping cart logic (localStorage)
│   └── otp.js                  OTP input boxes + photo preview
├── includes/
│   ├── db_connect.php          MySQL connection
│   ├── auth.php                Session/role guard helpers
│   ├── header.php               Shared navbar
│   └── footer.php
├── customer/
│   ├── dashboard.php           Browse products
│   ├── cart.php                View/edit cart
│   ├── checkout.php            Places the order (server validates stock/price)
│   ├── orders.php              Track orders + see OTP when assigned
│   ├── track_order.php         Live map view of the rider (NEW)
│   └── get_location.php        API: returns rider's latest location (NEW)
├── rider/
│   ├── dashboard.php           List of assigned deliveries
│   ├── verify_delivery.php     Enter OTP + upload proof photo
│   ├── share_location.php      Broadcasts rider's live GPS (NEW)
│   └── update_location.php     API: saves rider's latest location (NEW)
├── admin/
│   ├── dashboard.php           Stats overview
│   ├── manage_products.php     Product list
│   ├── add_product.php
│   ├── edit_product.php
│   ├── delete_product.php
│   ├── manage_orders.php       Assign riders to pending orders
│   └── assign_rider.php        Generates OTP + assigns rider
├── uploads/
│   ├── products/               Product images
│   └── proofs/                 Delivery proof photos
└── sql/
    ├── database.sql            Database schema + sample products
    └── migration_live_tracking.sql   Adds location columns (run if upgrading)
```

---

## 4. How the Core Flow Works

1. **Customer** registers/logs in → browses products → adds to cart → checks out with a delivery address. Order is created with status `Pending`.
2. **Admin** logs in → goes to Manage Orders → assigns a Rider to the Pending order. This **generates a random 6-digit OTP** and sets status to `Assigned`.
3. **Customer** sees the OTP on their Orders page once a rider is assigned (in real life this would be SMS — here it's shown directly to keep the project scope realistic for a course lab).
4. **Rider** logs in → sees the assigned order → goes to the physical delivery → asks the customer for the OTP → enters it + uploads a photo of the delivered package.
5. If the OTP matches, the order is marked `Delivered`, with the proof photo and timestamp saved. Both the Admin and Customer can now see the proof photo.

---

## 5. Suggested 2-Day Build/Test Plan

**Day 1 — Get it running + understand it**
- Morning: Do the setup steps above. Confirm you can log in as all 3 roles using the seed accounts.
- Afternoon: Walk through the full flow once manually: place an order as Customer → assign rider as Admin → verify delivery as Rider. Take screenshots as you go — you'll want these for your report/demo.
- Evening: Read through `checkout.php` and `verify_delivery.php` closely — these two files are the "brains" of the project and the most likely thing a supervisor will ask you to explain.

**Day 2 — Polish + prepare for submission**
- Add a couple of your own products as Admin so the catalog isn't just the 6 samples.
- Test edge cases: wrong OTP, empty cart checkout, registering with a duplicate email — confirm the validation messages show up.
- Take final screenshots / record a short demo video of the 3-role flow.
- Write your project report referencing this same flow (Sections 4-6 of your proposal map directly onto what's built).
- Optional polish if time allows: add your university logo to the navbar, tweak colors in `css/style.css`.

---

## 7. NEW FEATURE: Live Rider Location Tracking

The customer can now see the rider's live position on a map while their order is `Assigned`. This uses **Leaflet.js + OpenStreetMap** — completely free, no API key, no billing, no signup required. The map library loads from a public CDN automatically.

### 7.1 Run the database migration

If you already imported `database.sql` before this feature was added:

1. Open `http://localhost/phpmyadmin`
2. Select the `smart_delivery` database
3. Click the **SQL** tab
4. Open `sql/migration_live_tracking.sql` from this project, copy its contents, paste into the SQL box, click **Go**

(If you're setting up fresh, the updated `database.sql` already includes these columns — just import it normally, skip this step.)

### 7.2 How it works

- **Rider side** (`rider/share_location.php`): once an order is `Assigned`, the rider opens "Share Live Location" from their dashboard. The page uses the browser's GPS (`navigator.geolocation.watchPosition`) and sends coordinates to `rider/update_location.php` every time the GPS updates (effectively every few seconds while moving).
- **Customer side** (`customer/track_order.php`): the customer opens "Track Rider on Map" from My Orders. The page polls `customer/get_location.php` every 5 seconds and moves a marker on a Leaflet map to the rider's latest position.
- Tracking automatically stops being available once the order is marked `Delivered`.
- No setup step is needed for the map itself — it just works the moment you open the page, since Leaflet and OpenStreetMap tiles are free and keyless.

### 7.3 Testing it on one PC (important)

Browser GPS needs a real location signal, which is awkward to test alone on a desktop. Two practical options:

- **Easiest for a demo:** Open Chrome DevTools (F12) on the rider's tab → click the 3-dot menu → **More tools → Sensors** → set a custom Location (any lat/lng, e.g. Dhaka: `23.8103, 90.4125`). This fakes your GPS so `share_location.php` sends that fixed point — good enough to prove the pipeline works end-to-end for your demo/recording. Change the coordinates slightly every few seconds to simulate movement on the customer's map.
- **More realistic:** Open `share_location.php` on your phone's browser (same WiFi network, use your PC's local IP instead of `localhost`, e.g. `http://192.168.1.X/smart-delivery/rider/share_location.php`) while viewing `track_order.php` on your PC as the customer.

### 7.4 Live tracking troubleshooting

| Problem | Fix |
|---|---|
| Map doesn't load / blank grey box | Check your internet connection — Leaflet and the map tiles load from a CDN, so they need internet access even though there's no API key |
| Rider page says "permission denied" | Browser blocked location access — click the lock icon in the address bar and allow Location |
| Customer map never shows a marker | Confirm the rider's `share_location.php` page is open and actively running (closing the tab stops updates) |
| Map tiles look broken/missing in a small area | OpenStreetMap is community-maintained — coverage is excellent in most areas but can occasionally be sparse; this isn't an error on your end |

---

## 8. Common Issues (General)

- **"Database connection failed"** → Check `includes/db_connect.php`. Default XAMPP MySQL user is `root` with an empty password. If you set a MySQL password, update it there.
- **Images not showing** → Make sure `uploads/products/` and `uploads/proofs/` folders exist and are writable. They're already created in this project.
- **OTP not showing for customer** → It only appears once an Admin has assigned a rider (status changes from Pending → Assigned).
- **File upload fails** → Check your PHP's `upload_max_filesize` and `post_max_size` in `php.ini` if you're uploading large images (default XAMPP settings allow several MB, which is enough here).


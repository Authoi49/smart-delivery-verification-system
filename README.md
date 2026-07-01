# Smart Delivery Verification System

A multi-role (Customer / Rider / Admin) e-commerce site with **OTP + photo-proof delivery verification**, built with HTML, CSS, JavaScript, PHP, and MySQL .

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


---

## 5. NEW FEATURE: Live Rider Location Tracking

The customer can now see the rider's live position on a map while their order is `Assigned`. This uses **Leaflet.js + OpenStreetMap** — completely free, no API key, no billing, no signup required. The map library loads from a public CDN automatically.





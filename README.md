# Smart Delivery Verification System

A multi-role e-commerce web application that solves delivery disputes through OTP-based verification and photo proof — built as an undergraduate project for CSE 3210 at Varendra University.

---

## 🔗 Live Demo

**[smart-delivery.free.nf](http://smart-delivery.free.nf)**

---

## 📌 About the Project

In local e-commerce, riders often mark orders as delivered without completing the delivery, and customers sometimes falsely deny receiving packages. This project solves that problem with a simple but effective verification system — the rider must enter a customer-provided OTP and upload a delivery photo before an order can be marked as delivered. Customers can also track the rider's live location on a map while their order is on the way.

---

## 👥 User Roles

| Role | What they can do |
|------|-----------------|
| **Customer** | Browse products, manage cart, place orders, view OTP, track rider live on map |
| **Rider** | View assigned deliveries, share live location, enter OTP, upload proof photo |
| **Admin** | Manage products, assign riders to orders, monitor all deliveries |

---

## ✨ Key Features

- 🛒 Product browsing, cart management, and checkout
- 📦 Order status tracking (Pending → Assigned → Delivered)
- 🔐 6-digit OTP delivery verification
- 📸 Delivery proof photo upload
- 📍 Live rider location tracking (Leaflet.js + OpenStreetMap — no API key needed)
- 🛠️ Admin dashboard with full product and order management
- 📱 Fully responsive design

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Structure | HTML |
| Styling | CSS |
| Frontend Logic | JavaScript |
| Backend | PHP |
| Database | MySQL |
| Maps | Leaflet.js + OpenStreetMap |

---

## 📁 Project Structure

```
smart-delivery/
├── index.php              # Landing page
├── login.php              # Login (all roles)
├── register.php           # Registration
├── customer/              # Customer pages
├── rider/                 # Rider pages
├── admin/                 # Admin pages
├── includes/              # DB connection, auth, shared header/footer
├── css/style.css          # Global stylesheet
├── js/                    # Cart, OTP, and validation scripts
├── uploads/               # Product images and delivery proof photos
└── sql/                   # Database schema
```

---

## 👨‍💻 Team

| Name | Student ID |
|------|-----------|
| Al Souhardo Authoi | 232311297 |
| MD. Akib Jabed | 232311139 |
| Anus Ahmed Toha | 232311141 |

---

## 🏫 Course Information

- **University:** Varendra University
- **Department:** Computer Science and Engineering
- **Course:** CSE 3210 — E-Commerce and Web Programming Lab
- **Semester:** 6th, Section D
- **Supervisor:** Barisha Chowdhury (Lecturer, Dept. of CSE)

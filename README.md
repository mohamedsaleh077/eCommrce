# 🛒 E-Commerce REST API

A Laravel REST API for a small online store. It supports user signup/login with email verification, product & category management, product image uploads, and order placement. Admins manage the catalog and orders; regular users browse products and place orders.

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Database Schema](#database-schema)
- [Getting Started](#getting-started)
- [Authentication & Roles](#authentication--roles)
- [API Reference](#api-reference)
  - [Endpoint Summary](#endpoint-summary)
  - [Auth](#auth)
  - [Categories](#categories)
  - [Products](#products)
  - [Uploads (Images)](#uploads-images)
  - [Orders](#orders)
- [Error Format](#error-format)

---

## Features

- Role-based access (`admin` / `user`)
- Signup, login, and email verification
- Category and product CRUD (admin)
- Public product browsing (only in-stock products are listed publicly)
- Product image upload, stored privately and served through an API route
- Orders with multiple items per order (cart → order + receipts)

## Tech Stack

- PHP 8.2+
- Laravel 11+
- MySQL / MariaDB / SQLite (any Laravel-supported database)

---

## Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php          # Abstract base controller
│   │   │   ├── Auth/                   # Signup, Login, VerifyMail
│   │   │   ├── Admin/
│   │   │   │   └── Uploads.php         # Image upload CRUD + image serving
│   │   │   ├── Categories.php          # Category CRUD
│   │   │   ├── Products.php            # Product CRUD
│   │   │   ├── OrderController.php     # Place / list / update / delete orders
│   │   │   └── Profile.php             # Empty stub (not routed yet)
│   │   └── Middleware/
│   │       ├── UserAuth.php            # Requires a logged-in user
│   │       ├── IsAdmin.php             # Requires the admin role
│   │       └── isVerified.php          # Email verification gate
│   └── Models/                         # User, Role, Categorie, Product, Upload, Order, Receipt
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php   # roles, users, password resets, sessions
│   │   ├── 2026_09_26_143554_products.php             # categories, products
│   │   ├── 2026_09_26_144908_uploads.php              # uploads
│   │   └── 2026_09_26_145343_orders.php               # orders, receipts
│   └── seeders/
│       └── DatabaseSeeder.php          # Roles, admin account, default categories
├── routes/
│   └── api.php                         # All API routes
└── storage/app/private/product_images/ # Uploaded images (local disk)
```

| Part | What it does |
|---|---|
| **Controllers** | Handle validation, database work, and JSON responses. |
| **Middleware** | `UserAuth` identifies the user, `IsAdmin` restricts admin routes, `isVerified` guards the email-verification routes. |
| **Models** | Eloquent models for each table. |
| **Migrations** | Define the database tables. |
| **Seeder** | Creates the roles, a default admin, and starter categories. |

---

## Database Schema

| Table | Key columns | Notes |
|---|---|---|
| `roles` | `id`, `title` | `admin` and `user` |
| `users` | `id`, `role_id`, `name`, `email` (unique), `email_verified_at`, `password`, `address` | Belongs to a role |
| `categories` | `id`, `category` | |
| `products` | `id`, `category_id`, `name`, `description`, `price`, `discount`, `discount_end`, `stock`, `sold` | `sold` defaults to `0` |
| `uploads` | `id`, `product_id`, `filename`, `description` | Image metadata; file lives on disk |
| `orders` | `id`, `user_id`, `estimated_time`, `arrived_at` | One order per checkout |
| `receipts` | `order_id`, `product_id`, `amount` | Line items of an order |

**Relationships**

```
roles 1 ── * users 1 ── * orders 1 ── * receipts * ── 1 products * ── 1 categories
                                                         products 1 ── * uploads
```

Deleting a parent row cascades to its children (e.g. deleting a category removes its products).

---

## Getting Started

```bash
# 1. Install dependencies
composer install

# 2. Configure environment
cp .env.example .env
php artisan key:generate
# edit .env: database credentials + mail settings (needed for email verification)

# 3. Create tables and seed default data
php artisan migrate --seed

# 4. Run the server
php artisan serve
```

The API is available at `http://localhost:8000/api`.

**Seeded data**

| Item | Value |
|---|---|
| Admin email | `admin@admin.com` |
| Admin password | `admin password` |
| Categories | laptops, clothes, phones, accessories, watches |

> ⚠️ Change the default admin password before deploying.

---

## Authentication & Roles

| Access level | Meaning |
|---|---|
| **Public** | No login required |
| **User** | Logged in (`UserAuth` middleware) |
| **Admin** | Logged in and has the `admin` role (`UserAuth` + `IsAdmin`) |

Send the credentials returned by `/login` with each protected request (typically `Authorization: Bearer <token>`, depending on how `UserAuth` is implemented). All routes below are prefixed with `/api`.

---

## API Reference

### Endpoint Summary

| Method | Endpoint | Access | Description |
|---|---|---|---|
| POST | `/signup` | Public | Register a new account |
| POST | `/login` | Public | Log in |
| POST | `/email/code` | `isVerified` | Request a new verification code |
| POST | `/email/verify` | `isVerified` | Verify email with a code |
| GET | `/category` | Public | List categories |
| GET | `/category/{id}` | Public | Get one category |
| POST | `/category` | Admin | Create category |
| PUT | `/category/{id}` | Admin | Update category |
| DELETE | `/category/{id}` | Admin | Delete category |
| GET | `/product` | Public | List in-stock products |
| GET | `/product/{id}` | Public | Get one product |
| GET | `/product/image/{id}` | Public | Download an image file (by upload id) |
| GET | `/product/all` | Admin | List all products (any stock) |
| POST | `/product` | Admin | Create product |
| PUT | `/product/{id}` | Admin | Update product |
| DELETE | `/product/{id}` | Admin | Delete product |
| GET | `/upload` | Admin | List uploads |
| GET | `/upload/{id}` | Admin | Get upload info |
| POST | `/upload` | Admin | Upload an image |
| PUT | `/upload/{id}` | Admin | Replace an upload |
| DELETE | `/upload/{id}` | Admin | Delete an upload |
| POST | `/order` | User | Place an order |
| GET | `/order` | User | List own orders (admin: all orders) |
| PUT | `/order/{id}` | Admin | Update an order |
| DELETE | `/order/{id}` | Admin | Delete an order |

---

### Auth

> The auth controllers (`Signup`, `Login`, `VerifyMail`) are single-action classes. Fields below follow the `users` table, so check them against your controllers.

#### `POST /signup`
Creates a new user (default role: `user`).

| Field | Type | Required |
|---|---|---|
| `name` | string | yes |
| `email` | string (unique) | yes |
| `password` | string | yes |
| `address` | string | no |

#### `POST /login`

| Field | Type | Required |
|---|---|---|
| `email` | string | yes |
| `password` | string | yes |

**Response:** the auth credentials to send on protected requests.

#### `POST /email/code`
Sends a fresh verification code to the user's email.

#### `POST /email/verify`

| Field | Type | Required |
|---|---|---|
| `code` | string | yes |

---

### Categories

#### `GET /category`
**200**
```json
{ "categories": [ { "id": 1, "category": "laptops", "created_at": "...", "updated_at": "..." } ] }
```
**404** `{ "message": "no categories have been found, create one in admin dashboard" }`

#### `GET /category/{id}`
**200** `{ "categories": { "id": 1, "category": "laptops", ... } }`
**404** `{ "message": "Category not found" }`

#### `POST /category` (Admin)

| Field | Rules |
|---|---|
| `category` | required, string, 1–255 chars, unique |

**201** `{ "message": "category phones Added Successfully!" }`

#### `PUT /category/{id}` (Admin)
Same body as create.
**200** `{ "message": "update category success" }`
If the id doesn't exist: `{ "message": "no category with {id} id" }`

#### `DELETE /category/{id}` (Admin)
**200** `{ "message": "Category laptops is deleted successfully!" }`
If the id doesn't exist: `{ "message": "no category with {id} id" }`

---

### Products

#### `GET /product`
Returns only products with `stock > 0`, including their `category` and `upload`.

**200**
```json
{
  "products": [
    {
      "id": 1, "category_id": 1, "name": "Laptop X", "description": "...",
      "price": 999.99, "discount": 10, "discount_end": "2026-12-31 00:00:00",
      "stock": 5, "sold": 0,
      "category": { "id": 1, "category": "laptops" },
      "upload": { "id": 3, "filename": "laptop-x-1700000000.jpg" }
    }
  ]
}
```
**404** `{ "message": "no Products have been found, create one in admin dashboard" }`

#### `GET /product/{id}`
**200** `{ "product": { ..., "category": { ... } } }`
**404** `{ "message": "Product not found" }`

#### `GET /product/image/{id}`
`{id}` is the **upload id**. Returns the raw image file (not JSON).
**404** `{ "message": "Image not found" }`

#### `GET /product/all` (Admin)
Same response as `GET /product`, but includes out-of-stock products.

#### `POST /product` (Admin)

| Field | Rules |
|---|---|
| `category_id` | required, integer, must exist in `categories` |
| `name` | required, string, 1–255 chars |
| `description` | optional, string, max 1024 |
| `image_id` | optional, integer |
| `price` | required, numeric, ≥ 0 |
| `discount` | optional, numeric, 0–100 (percent) |
| `discount_end` | optional, date |
| `stock` | required, integer, ≥ 0 |

**201** `{ "message": "Product Laptop X Added Successfully!" }`

#### `PUT /product/{id}` (Admin)
Same fields as create, but **all optional**, plus:

| Field | Rules |
|---|---|
| `sold` | optional, integer, ≥ 0 |

**200** `{ "message": "update product Laptop X success" }`
If the id doesn't exist: `{ "message": "no product with {id} id" }`

#### `DELETE /product/{id}` (Admin)
**200** `{ "message": "Category Laptop X is deleted successfully!" }`
If the id doesn't exist: `{ "message": "no product with {id} id" }`

---

### Uploads (Images)

Images are saved on the private `local` disk under `product_images/` and served through `GET /product/image/{id}`. File names are slugified and get a timestamp suffix. Requests with files must use `multipart/form-data`.

#### `GET /upload` (Admin)
**200** `{ "uploads": [ { "id": 1, "product_id": 1, "filename": "...", "description": "..." } ] }`
**404** `{ "message": "no Uploads have been found, upload one in admin dashboard" }`

#### `GET /upload/{id}` (Admin)
**200**
```json
{ "id": 1, "product_id": 1, "filename": "laptop-x-1700000000.jpg", "description": "Front view", "size": 20480 }
```
**404** `{ "message": "Image not found" }` or `{ "message": "File not found" }`

#### `POST /upload` (Admin)

| Field | Rules |
|---|---|
| `product_id` | required, integer, must exist in `products` |
| `image` | required, file: jpeg, png, jpg, gif, svg, webp, max 4 MB |
| `filename` | required, string, 3–255 chars, unique |
| `description` | optional, max 255 |

**201**
```json
{
  "message": "Uploaded image laptop-x-1700000000.jpg added successfully!",
  "data": { "image_name": "laptop-x-1700000000.jpg", "image_url": "...", "mime": "image/jpeg" }
}
```

#### `PUT /upload/{id}` (Admin)
Same fields and rules as create. Replaces the stored file and metadata. When sending files, send a `POST` with `_method=PUT` (PHP doesn't parse multipart bodies on real `PUT` requests).

**200**
```json
{
  "message": "update upload success",
  "product_id": 1, "filename": "...", "description": "...",
  "image_url": "...", "size": 20480
}
```
**404** `{ "message": "Image not found" }`

#### `DELETE /upload/{id}` (Admin)
**200** `{ "message": "upload {filename} is deleted successfully!" }`
**404** `{ "message": "Image not found" }`

---

### Orders

An order is a header (`orders`) plus one line per product (`receipts`).

#### `POST /order` (User)
Places an order for the logged-in user.

| Field | Rules |
|---|---|
| `cart` | required, array, at least 1 item |
| `cart.*.product_id` | required, integer, unique within the cart, must exist in `products` |
| `cart.*.amount` | required, integer, ≥ 1 |

```json
{ "cart": [ { "product_id": 1, "amount": 2 }, { "product_id": 4, "amount": 1 } ] }
```

**200**
```json
{
  "message": "you have placed the order successfully",
  "receipt": [
    { "order_id": 7, "product_id": 1, "amount": 2, "product": { "id": 1, "name": "Laptop X", ... } }
  ]
}
```

#### `GET /order` (User)
Regular users get their own orders; admins get all orders. Each order includes `user` and `receipts` (with `product`).

**200** `[ { "id": 7, "user_id": 2, "estimated_time": null, "arrived_at": null, "user": { ... }, "receipts": [ { ..., "product": { ... } } ] } ]`
**404** `{ "message": "no orders yet" }`

#### `PUT /order/{id}` (Admin)
Updates the order and **replaces all its receipts** with the new cart.

| Field | Rules |
|---|---|
| `order_user_id` | required, integer, must exist in `users` |
| `cart` | required, array, at least 1 item |
| `cart.*.product_id` | required, integer, unique within the cart, must exist in `products` |
| `cart.*.amount` | required, integer, ≥ 1 |
| `estimated_time` | optional, date |
| `arrived_at` | optional, date |

**200** `{ "message": "You have updated the order successfully", "data": { ...order, "user": {...}, "receipts": [...] } }`
**404** if the order doesn't exist (Laravel `findOrFail`).

#### `DELETE /order/{id}` (Admin)
Deletes the order and its receipts.
**200** `{ "message": "You have deleted the order successfully" }`
**404** if the order doesn't exist.

---

## Error Format

Validation failures return **422** in Laravel's standard format:

```json
{
  "message": "The category field is required.",
  "errors": { "category": ["The category field is required."] }
}
```

| Status | Meaning |
|---|---|
| `200` / `201` | Success / created |
| `401` / `403` | Not logged in / not allowed (from middleware) |
| `404` | Resource not found |
| `422` | Validation failed |
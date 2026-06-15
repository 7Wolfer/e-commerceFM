# Frutería Madrid 🥑

![PHP](https://img.shields.io/badge/PHP-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black)
![Firebase](https://img.shields.io/badge/Firebase_Auth-FFCA28?logo=firebase&logoColor=black)
![Stripe](https://img.shields.io/badge/Stripe-635BFF?logo=stripe&logoColor=white)

A full-stack e-commerce web app for an online grocery / fruit store. Customers can
browse a product catalog by department, filter items, add them to a cart, sign in with
Firebase Authentication, and check out — either by **picking up and paying in store** or
by **paying online with Stripe** for home delivery.

> Prices are in **MXN** and the store is based in Hermosillo, Sonora (Mexico).
> 🔗 **Live demo:** runs locally for now — see the **Roadmap** for deployment options.

---

## 📸 Screenshots

> 📷 _Coming soon._ To add them, drop these PNGs into `docs/screenshots/` and uncomment the
> gallery below: `01-hero.png`, `02-ofertas.png`, `03-catalogo.png`, `04-mobile.png`.

<!-- Una vez que existan las imágenes, descomenta este bloque:
<p align="center">
  <img src="docs/screenshots/01-hero.png" alt="Home — hero, live search and department menu" width="100%">
</p>

| Weekly offers | Catalog & filters | Mobile |
| --- | --- | --- |
| ![Weekly offers](docs/screenshots/02-ofertas.png) | ![Catalog and filters](docs/screenshots/03-catalogo.png) | ![Mobile view](docs/screenshots/04-mobile.png) |
-->

---

## ✨ Features

- **Product catalog** served from a MySQL database, grouped into 12 departments with
  sub-categories.
- **Filtering & sorting** — by category, brand, "new", "on sale", and lowest price.
- **Shopping cart** persisted in `localStorage` (survives page reloads), with a slide-in
  drawer, quantity controls, and live total.
- **Authentication** via Firebase (Google or Email/Password). The Firebase ID token is
  verified server-side with the Firebase Admin SDK before a PHP session is created.
- **User profile** — save name, phone, and email.
- **Two checkout flows:**
  - **Pickup** → order is created directly in the database (pay in store).
  - **Delivery** → redirects to **Stripe Checkout**; on success the paid order is
    persisted in the database.
- **Live search** in the header — filter products by name as you type.
- **Weekly offers** section with the original (strike-through) price and an automatic
  discount badge, plus a live countdown.
- **Geolocation** helper to autofill the delivery address.
- **Modern, responsive UI** built with vanilla HTML/CSS/JS (no framework) — a refined
  "fresh market" design using the *Fraunces* + *Hanken Grotesk* typefaces.

---

## 🧰 Tech Stack

| Layer        | Technology                                            |
| ------------ | ----------------------------------------------------- |
| Front end    | HTML, CSS, vanilla JavaScript                         |
| Back end     | PHP (no framework), MySQLi                             |
| Database     | MySQL / MariaDB                                        |
| Auth         | Firebase Authentication + `kreait/firebase-php` (Admin SDK) |
| Payments     | Stripe Checkout (`stripe/stripe-php`)                 |
| Server       | Apache (XAMPP)                                         |
| Dependencies | Composer                                              |

---

## 📁 Project Structure

```
fruteria-madrid/
├── index.php                 # Main page (catalog, cart, auth modal, footer)
├── app.js                    # Front-end logic (catalog, cart, checkout, auth UI)
├── styles.css                # Styles
├── db.sql                    # Database schema + seed data
├── composer.json             # PHP dependencies
├── assets/                   # Logo, banners, product images
└── api/
    ├── config.php                  # MySQL connection
    ├── config_firebase.php         # Firebase Admin SDK bootstrap
    ├── stripe_config.php           # Stripe keys + base domain
    ├── products.php                # GET catalog (with filters)
    ├── meta.php                    # GET categories + brands
    ├── login_verify.php            # POST verify Firebase ID token → PHP session
    ├── me.php                      # GET current session user
    ├── logout.php                  # POST destroy session
    ├── profile.php                 # GET/POST user profile
    ├── create_order.php            # POST create order (pickup / pay in store)
    ├── create_stripe_checkout.php  # POST create Stripe Checkout session (delivery)
    ├── success.php                 # Stripe success callback → persists paid order
    └── cancel.php                  # Stripe cancel page
```

---

## 🚀 Getting Started

### Prerequisites

- **XAMPP** (or any Apache + PHP + MySQL stack), PHP 7.4+
- **Composer** — https://getcomposer.org
- A **Firebase** project (for login)
- A **Stripe** account (for online payments)

### 1. Place the project

Copy this folder into your XAMPP web root:

- **macOS:** `/Applications/XAMPP/htdocs/fruteria-madrid`
- **Windows:** `C:\xampp\htdocs\fruteria-madrid`

Start **Apache** and **MySQL** from the XAMPP control panel.

### 2. Install PHP dependencies

From the project root:

```bash
composer install
```

### 3. Create the database

Import the schema and seed data (via phpMyAdmin or the CLI):

```bash
mysql -u root < db.sql
```

This creates the `fruteria_madrid` database with `categorias`, `marcas`, `productos`,
`usuarios`, `direcciones`, `pedidos`, and `pedido_items` tables, plus sample products.

### 4. Configure database access

Edit [`api/config.php`](api/config.php) if your MySQL credentials differ from the XAMPP
defaults (`root` / empty password).

### 5. Configure Firebase

1. In the Firebase Console, create a project and a **Web App**.
2. Enable the sign-in methods you want under **Authentication → Sign-in method**
   (Google, Email/Password).
3. Under **Authentication → Settings → Authorized domains**, add `localhost`.
4. Paste your web `firebaseConfig` into the marked block in
   [`index.php`](index.php) (near the bottom).
5. Download a service-account key
   (**Project settings → Service accounts → Generate new private key**) and save it as
   `api/firebase-service-account.json`.

### 6. Configure Stripe

Copy the example config and fill in your **test** keys:

```bash
cp api/stripe_config.example.php api/stripe_config.php
```

Then edit [`api/stripe_config.php`](api/stripe_config.php) with your `sk_test_…` and
`pk_test_…` keys, and adjust `$STRIPE_DOMAIN` if you change the folder name or host.
`stripe_config.php` is git-ignored, so your secret key is never committed.

### 7. Run it

Open:

```
http://localhost/fruteria-madrid/index.php
```

---

## ☁️ Deploy (free, no credit card)

The app is container-ready (`Dockerfile`) and reads all secrets from **environment
variables**, so it can run on any host without committing credentials. A fully-free,
durable setup:

- **App:** [Render](https://render.com) — free Docker web service.
- **Database:** [TiDB Cloud Serverless](https://tidbcloud.com) (MySQL-compatible, free,
  TLS) or [Aiven for MySQL](https://aiven.io) free plan.

### Steps (overview)

1. **Database** — create a free MySQL cluster, then import `db.sql` plus the
   `migration*.sql` files. Note its host, port, user, password, and database name.
2. **Render** — *New → Web Service*, connect this GitHub repo, runtime **Docker**, plan
   **Free**.
3. **Environment variables** (see [`.env.example`](.env.example)):
   `DB_HOST`, `DB_PORT`, `DB_USER`, `DB_PASS`, `DB_NAME`, `DB_SSL=1`,
   `DB_SSL_CA=/etc/ssl/certs/ca-certificates.crt`, `STRIPE_SECRET`,
   `STRIPE_PUBLISHABLE`, `STRIPE_DOMAIN` (your Render URL), and
   `FIREBASE_SERVICE_ACCOUNT` (the full service-account JSON on one line).
4. **Firebase Console → Authentication → Settings → Authorized domains** — add your
   Render domain.
5. Deploy. Render rebuilds automatically on every `git push`.

> The free Render service sleeps after ~15 min of inactivity; the first request after
> that takes ~30 s to wake up.

---

## 🔌 API Endpoints

| Method   | Endpoint                          | Description                                              |
| -------- | --------------------------------- | -------------------------------------------------------- |
| `GET`    | `/api/products.php`               | List products. Filters: `categoria_id`, `marca_id`, `nuevo`, `oferta`, `organico`, `sort` (`price_asc` / `price_desc`). |
| `GET`    | `/api/meta.php`                   | List categories and brands.                              |
| `POST`   | `/api/login_verify.php`           | Verify a Firebase ID token and create a PHP session. Body: `{ "idToken": "…" }`. |
| `GET`    | `/api/me.php`                     | Return the current session user (or `204`).              |
| `POST`   | `/api/logout.php`                 | Destroy the session.                                     |
| `GET/POST` | `/api/profile.php`              | Get or update the user profile.                          |
| `POST`   | `/api/create_order.php`           | Create a pickup order (pay in store). Body: `{ metodo, direccion, items }`. |
| `POST`   | `/api/create_stripe_checkout.php` | Create a Stripe Checkout session for delivery orders.    |
| `GET`    | `/api/success.php`                | Stripe success redirect — validates the session and persists the paid order. |
| `GET`    | `/api/cancel.php`                 | Stripe cancel page.                                      |

---

## 🔐 How authentication works

Login uses Firebase on the client but is **verified on the server** — the browser never
decides who is logged in:

1. The user signs in with Google or Email/Password via the Firebase JS SDK (in `index.php`).
2. The client gets a Firebase **ID token** and POSTs it to `api/login_verify.php`.
3. The server verifies the token with the Firebase Admin SDK (`kreait/firebase-php`) and,
   only if it's valid, creates the PHP session and upserts the user into the `usuarios` table.
4. `api/me.php` and `api/logout.php` read and destroy that session.

A forged request therefore cannot create a session without a valid Firebase token.

---

## 🗄️ Database Schema (overview)

- **`categorias`** / **`marcas`** — product categories and brands.
- **`productos`** — products (SKU, name, price, unit, image, flags: `nuevo`, `oferta`,
  `organico`).
- **`usuarios`** — customers (linked to Firebase `uid` / email).
- **`direcciones`** — delivery addresses.
- **`pedidos`** — orders (total, method `pickup`/`delivery`, status).
- **`pedido_items`** — line items per order.

See [`db.sql`](db.sql) for full definitions and seed data.

---

## 🔒 Security Note

Secret files are kept out of version control via [`.gitignore`](.gitignore):

- `api/firebase-service-account.json` (Firebase **admin private key**)
- `api/stripe_config.php` (Stripe secret key) — create it from `api/stripe_config.example.php`
- `/vendor/` (reinstall with `composer install`)

> **Important:** these files were committed in earlier history, so the keys they
> contained must be treated as **exposed** and rotated:
> - **Firebase** — *Project settings → Service accounts → generate a new private key*,
>   then revoke the old one.
> - **Stripe** — roll the secret key in the Stripe Dashboard.
>
> Adding them to `.gitignore` only stops *future* tracking; to purge them from past
> commits you must rewrite history (e.g. `git filter-repo`) and force-push.

The Firebase Web `apiKey` in `index.php` is meant to be public, but the **service
account** and **Stripe secret key** grant privileged access and must be kept private.

---

## 🗺️ Roadmap

Known limitations and planned improvements:

- [ ] **Deploy the live demo** — the app is already containerized (`Dockerfile`, env-based config); it just needs a host (Render + a free MySQL DB) and a link here.
- [ ] **Stripe webhook** (`checkout.session.completed`) so orders persist even if the user closes the tab, with idempotency to avoid duplicates.
- [ ] **Admin panel** to manage products, categories, and orders.
- [ ] **Order history** for logged-in users.
- [ ] **Server-side validation & CSRF protection** on POST endpoints.
- [ ] **Automated tests** (PHPUnit + a few front-end tests).

**Recently shipped:** ✅ professional UI redesign (hero, weekly offers, live search) · ✅ touch-friendly mega-menu · ✅ relative base paths · ✅ local product images · ✅ mega-menu catalog filtering · ✅ server-side price validation · ✅ secrets removed from version control.

---

## 📄 License

Released under the [MIT License](LICENSE) — free to use, modify, and learn from.

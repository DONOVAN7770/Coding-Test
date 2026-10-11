# E-commerce Admin

A Laravel product and order management app with two web roles:

- **Admin:** manage products, import product files, view orders, and export orders.
- **User:** browse available products and place orders.

## 1. System requirements

Install the following before setting up the project:

- PHP **8.3 or later**, with the PDO MySQL extension (`pdo_mysql`) enabled
- Composer
- Node.js and npm
- Git
- MySQL Server

The app also uses Laravel's standard PHP extensions; if Composer reports a missing extension, install or enable it for your PHP version and rerun `composer install`.

## 2. Get the project and install dependencies

```bash
git clone <Project Repo Link>
cd ecommerce-admin
composer install
npm install
```

Replace the example GitHub URL with the repository URL you pushed to GitHub or GitLab. The final `ecommerce-admin` argument names the local folder, so the `cd` command works regardless of the repository's actual name. If you already have the repository locally, skip `git clone` and enter its project folder.

## 3. Configure the environment

Copy the example environment file and generate an application key.

**Windows PowerShell:**

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

**macOS / Linux:**

```bash
cp .env.example .env
php artisan key:generate
```

Create a MySQL database for the app. For example, sign in to MySQL and run:

```sql
CREATE DATABASE ecommerce_admin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

The `.env.example` uses these local MySQL settings by default:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_admin
DB_USERNAME=root
DB_PASSWORD=
```

Update `DB_USERNAME` and `DB_PASSWORD` to match your MySQL account. If you copied `.env.example` before changing it, update the same `DB_*` values in your `.env`. Keep database credentials local; do not commit `.env`.

## 4. Run migrations and seed sample data

```bash
php artisan migrate --seed
```

This creates the users, products, orders, order items, cache, and role tables in MySQL, then adds sample products and a local admin account:

| Login | Email | Password |
|---|---|---|
| Admin | `admin@gmail.com` | `admin123` |

These are development credentials from the seeder. Change the password before using a deployed environment. New registrations receive the `user` role.

To rebuild a **local development database from scratch** and reseed it, run `php artisan migrate:fresh --seed`. This deletes existing database data.

## 5. Build the frontend and start the app

Build the CSS and JavaScript assets:

```bash
npm run build
```

Start the Laravel server:

```bash
php artisan serve
```

Open <http://127.0.0.1:8000>. For frontend development with automatic asset updates, run `npm run dev` in a second terminal while the Laravel server is running.

## 6. Import products from CSV or Excel

Sign in as an admin, open **Products**, and use **Import file**. The first row containing all required headers is treated as the header row. Header names are case-insensitive and spaces are ignored.

Include these four columns in any order. The importer looks for their header row within the first 10 rows of the file:

| Header | Required value |
|---|---|
| `name` | Product name, up to 255 characters |
| `sku` | Product SKU |
| `price` | Number greater than or equal to `100` |
| `stock` | Whole number greater than or equal to `0` |

Example CSV:

```csv
name,sku,price,stock
Wireless Mouse,PROD-101,15000,20
USB-C Cable,PROD-102,2500,50
```

You can use the same columns in an `.xlsx` workbook. Do not add other non-empty headers or values in extra columns. If any row is invalid or an extra column contains a value, the import reports the errors and saves **none** of the file's rows. A matching SKU updates the existing product's price and stock; a new SKU creates a product.

## 7. API examples

The JSON API is versioned under `/api/v1`. It currently exposes product reads and order submission.

### List products

```bash
curl "http://127.0.0.1:8000/api/v1/products?search=mouse&page=1" \
  -H "Accept: application/json"
```

The response contains a `data` array, pagination `meta`, and pagination `links`.

### Get one product

```bash
curl "http://127.0.0.1:8000/api/v1/products/1" \
  -H "Accept: application/json"
```

Replace `1` with a product ID. The response is wrapped in a `data` object.

### Submit an order

```bash
curl -X POST "http://127.0.0.1:8000/api/v1/orders" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_name": "Example Customer",
    "customer_email": "customer@example.com",
    "items": [
      {"product_id": 1, "quantity": 2}
    ]
  }'
```

Use product IDs that exist and quantities that are available in stock. Prices and totals are calculated from the database. A successful request returns HTTP `201` with the created order; invalid data or insufficient stock returns a validation error. The endpoint is limited to 30 requests per minute.

These requests can also be entered in Postman with the same URLs, headers, and JSON body.

## 8. Roles and main pages

- `/register` creates a regular user account.
- `/login` signs in either role and redirects to the matching home page.
- `/shop` is the customer product shop and recent orders page.
- `/dashboard` and `/admin/products` are admin-only pages.
- `/admin/orders` lists orders; admins can also export them.

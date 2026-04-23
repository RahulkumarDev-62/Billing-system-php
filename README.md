# PHP Supermarket MVC

This folder now contains a role-based supermarket system with:

- three login pages: admin, shop staff, and branch
- three panels: admin, shop staff, and branch
- admin-created staff and branch accounts
- branch management
- category CRUD
- product CRUD with auto-generated barcode numbers
- barcode label page for products
- barcode-driven sales checkout
- sales summary and stock control

## Run locally

1. Import [database-schema.sql](database-schema.sql) into MySQL.
2. Create the first admin account directly in the database, then use the admin panel to create staff and branch accounts.
3. Update [config/database.php](config/database.php) with your database credentials.
4. Start the app:

```bash
php -S localhost:8000 -t public
```

5. Open `http://localhost:8000`.

## Notes

Use `/admin/login`, `/staff/login`, and `/branch/login` for the three role-specific login pages. Products auto-generate a unique barcode and also expose a printable barcode page.

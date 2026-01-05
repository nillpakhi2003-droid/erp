# Laravel ERP System

A complete Enterprise Resource Planning (ERP) starter application built with Laravel 11, featuring role-based access control, inventory management, sales tracking, and comprehensive reporting.

## Features

### 4-Level User Hierarchy
- **Super Admin**: Manages owners, views all system data and reports
- **Owner**: Manages managers, products, stock, and has full reporting access
- **Manager**: Manages salesmen, products, stock entries, and generates reports
- **Salesman**: Creates sales and views own sales history

### Core Functionality
- ✅ Role-based authentication using Spatie Laravel Permission
- ✅ User management with hierarchical permissions
- ✅ Product management (CRUD operations)
- ✅ Inventory/Stock management with entry tracking
- ✅ Sales system with automatic profit calculation
- ✅ Comprehensive reporting (today's sales, custom date ranges)
- ✅ Real-time stock updates
- ✅ Dashboard analytics for each role

## Technology Stack

- **Framework**: Laravel 11
- **Authentication**: Laravel Breeze (Blade)
- **Permissions**: Spatie Laravel Permission
- **Frontend**: Blade Templates + Tailwind CSS (CDN)
- **Database**: MySQL (configurable)

## Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Node.js & NPM (optional, for asset compilation)

### Step 1: Install Dependencies

```bash
composer install
```

### Step 2: Environment Configuration

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 3: Configure Database

Edit `.env` file and set your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp_system
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 4: Run Migrations & Seeders

```bash
# Run migrations to create database tables
php artisan migrate

# Seed the database with roles, permissions, and demo users
php artisan db:seed
```

### Step 5: Publish Spatie Permission Config (if needed)

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

### Step 6: Start the Development Server

```bash
php artisan serve
```

Visit: `http://localhost:8000`

## Default Login Credentials

After seeding, you can login with these demo accounts:

| Role | Phone Number | Password |
|------|--------------|----------|
| Super Admin | 1234567890 | password |
| Owner | 1234567891 | password |
| Manager | 1234567892 | password |
| Salesman | 1234567893 | password |

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── AuthenticatedSessionController.php
│   │   ├── SuperAdmin/
│   │   │   ├── SuperAdminController.php
│   │   │   └── OwnerController.php
│   │   ├── Owner/
│   │   │   ├── OwnerController.php
│   │   │   └── ManagerController.php
│   │   ├── Manager/
│   │   │   ├── ManagerController.php
│   │   │   ├── SalesmanController.php
│   │   │   ├── ProductController.php
│   │   │   └── StockController.php
│   │   ├── Salesman/
│   │   │   ├── SalesmanController.php
│   │   │   └── SaleController.php
│   │   └── ReportController.php
│   └── Requests/
│       └── Auth/
│           └── LoginRequest.php
├── Models/
│   ├── User.php
│   ├── Product.php
│   ├── Sale.php
│   └── StockEntry.php
└── Providers/
    ├── AppServiceProvider.php
    └── AuthServiceProvider.php

database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 2024_01_01_000001_create_products_table.php
│   ├── 2024_01_01_000002_create_stock_entries_table.php
│   └── 2024_01_01_000003_create_sales_table.php
└── seeders/
    ├── DatabaseSeeder.php
    └── RolePermissionSeeder.php

resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php
    │   └── guest.blade.php
    ├── auth/
    │   └── login.blade.php
    ├── superadmin/
    │   ├── dashboard.blade.php
    │   ├── reports.blade.php
    │   └── owners/
    ├── owner/
    │   ├── dashboard.blade.php
    │   ├── reports.blade.php
    │   └── managers/
    ├── manager/
    │   ├── dashboard.blade.php
    │   ├── reports.blade.php
    │   ├── salesmen/
    │   ├── products/
    │   └── stock/
    └── salesman/
        ├── dashboard.blade.php
        └── sales/

routes/
├── web.php
└── console.php
```

## Database Schema

### Users Table
- `id`: Primary key
- `name`: User full name
- `phone`: Unique phone number (10-15 digits)
- `password`: Hashed password
- `created_by`: Foreign key to users (creator)
- `timestamps`

### Products Table
- `id`: Primary key
- `name`: Product name
- `sku`: Stock Keeping Unit (unique)
- `purchase_price`: Cost price
- `sell_price`: Selling price
- `current_stock`: Available quantity
- `timestamps`

### Stock Entries Table
- `id`: Primary key
- `product_id`: Foreign key to products
- `quantity`: Amount added
- `purchase_price`: Price at time of entry
- `added_by`: Foreign key to users
- `timestamps`

### Sales Table
- `id`: Primary key
- `product_id`: Foreign key to products
- `user_id`: Foreign key to users (salesman)
- `quantity`: Units sold
- `sell_price`: Price per unit
- `total_amount`: Total sale value
- `profit`: Calculated profit
- `timestamps`

## Roles & Permissions

### Super Admin
- `manage-owners`: Create, edit, delete owners
- `view-all-users`: See all system users
- `manage-products`: Full product control
- `view-all-sales`: See all sales
- `view-all-reports`: Access all reports

### Owner
- `manage-managers`: Create, edit, delete managers
- `view-all-users`: See all users
- `manage-products`: Full product control
- `add-stock`: Add inventory
- `view-all-sales`: See all sales
- `view-all-reports`: Access all reports

### Manager
- `manage-salesmen`: Create, edit, delete salesmen
- `manage-products`: Full product control
- `add-stock`: Add inventory
- `view-all-sales`: See all sales
- `view-reports`: Access reports

### Salesman
- `view-products`: View product list
- `create-sales`: Make new sales
- `view-own-sales`: See personal sales only

## Key Features Explained

### Automatic Calculations
- **Sale Profit**: Automatically calculated as `(sell_price - purchase_price) × quantity`
- **Total Amount**: Calculated as `quantity × sell_price`
- **Stock Updates**: Inventory automatically decrements on sale creation

### Role-Based Dashboards
Each user role sees a customized dashboard with:
- Relevant statistics and KPIs
- Quick action buttons
- Recent activity tables
- Role-appropriate navigation

### Reporting System
- Filter by date range (from/to)
- View total sales, profit, quantity
- See current stock value
- Detailed transaction history

### User Hierarchy
- Super Admins create Owners
- Owners create Managers
- Managers create Salesmen
- Each user tracks who created them (`created_by` field)

## Security Features

- Password hashing using bcrypt
- CSRF protection on all forms
- Route protection with role middleware
- Session-based authentication
- Rate limiting on login attempts

## Customization

### Adding New Roles
Edit `database/seeders/RolePermissionSeeder.php` to add custom roles and permissions.

### Modifying Permissions
Update the permissions array in the seeder and re-run:
```bash
php artisan migrate:fresh --seed
```

### Changing UI Theme
The project uses Tailwind CSS via CDN. Modify Blade templates in `resources/views/` to customize the design.

## Troubleshooting

### Permission Denied Errors
```bash
chmod -R 775 storage bootstrap/cache
```

### Database Connection Issues
- Verify MySQL is running
- Check `.env` database credentials
- Ensure database exists: `CREATE DATABASE erp_system;`

### Spatie Permission Issues
```bash
php artisan cache:clear
php artisan config:clear
php artisan permission:cache-reset
```

## API Endpoints (Routes)

### Authentication
- `GET /login` - Login form
- `POST /login` - Authenticate user
- `POST /logout` - Logout user

### Super Admin
- `/superadmin/dashboard` - Dashboard
- `/superadmin/owners` - Manage owners (CRUD)
- `/superadmin/reports` - View reports

### Owner
- `/owner/dashboard` - Dashboard
- `/owner/managers` - Manage managers (CRUD)
- `/owner/products` - Manage products (CRUD)
- `/owner/stock` - Stock management
- `/owner/reports` - View reports

### Manager
- `/manager/dashboard` - Dashboard
- `/manager/salesmen` - Manage salesmen (CRUD)
- `/manager/products` - Manage products (CRUD)
- `/manager/stock` - Stock management
- `/manager/reports` - View reports

### Salesman
- `/salesman/dashboard` - Dashboard
- `/salesman/sales` - View sales history
- `/salesman/sales/create` - Create new sale

## Contributing

Feel free to fork this project and customize it for your needs. This is a starter template designed to be extended.

## License

This project is open-sourced software licensed under the MIT license.

## Support

For issues and questions:
- Review the Laravel documentation: https://laravel.com/docs
- Check Spatie Permission docs: https://spatie.be/docs/laravel-permission

## Future Enhancements

Potential features to add:
- Multi-currency support
- Advanced analytics and charts
- Export reports to PDF/Excel
- Email notifications
- Invoice generation
- Barcode scanning
- Multi-warehouse support
- API for mobile apps
- Real-time notifications
- Audit logging

---

**Built with Laravel 11 + Spatie Permission + Tailwind CSS**
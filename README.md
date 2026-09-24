# MetoHub (formerly MetoHub)

MetoHub is a multi-tenant SaaS e-commerce platform designed specifically for grocers and niche retailers. It enables merchants to instantly launch beautiful, high-conversion online stores with dynamic themes, integrated inventory, and a comprehensive admin panel.

## Key Features

- **Multi-Tenant Architecture**: A single codebase serves multiple distinct stores (tenants), separated logically in the database via `tenant_id`.
- **Dynamic Theming Engine**: Storefronts can switch between distinct UI templates (e.g., Template 1, Template 2, Template 3) seamlessly.
- **Unified Authentication System**: Dual-guard authentication separating Super Admins, Store Admins, and Storefront Customers, with cross-guard login modals for a seamless user experience.
- **Cart & Checkout**: Advanced cart management with session-to-database syncing upon login.
- **Third-Party Integrations**: Built-in support for Zoho and DartPOS sync.

## Tech Stack

- **Backend**: Laravel 12.x, PHP 8.2+
- **Frontend**: Blade Templating, Vanilla CSS, jQuery, AJAX
- **Database**: MySQL 

## Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL Database

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/nerajlal/MetoHub.git
   cd MetoHub
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup:**
   Copy the example environment file and configure your database settings:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Migration & Seeding:**
   Run the migrations to set up the multi-tenant architecture:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Serve the Application:**
   ```bash
   php artisan serve
   ```

## Architecture Overview

### Routing & Middleware
- `IdentifyTenant`: Resolves the active tenant for Admin backend routes.
- `IdentifyStorefrontTenant`: Resolves the active tenant for frontend shopping routes based on domain/session.

### Authentication
- Admins and customers are unified in the `users` table, distinguished by the `type` column (`super_admin`, `admin`, `user`) and scoped by `tenant_id`.

## License
© 2026 MetoHub. All rights reserved.

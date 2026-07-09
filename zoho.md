# Inventory Integration Guide
## For integrating with Zoho Inventory / Zoho Books or Tally ERP

---

## Table of Contents
1. [Zoho Integration Guide](#zoho-integration-guide)
2. [Tally Integration Guide](#tally-integration-guide)
3. [Current System Inventory Structure](#current-system-inventory-structure)
4. [Tenant Zoho Connection Flow](#tenant-zoho-connection-flow)

---

## Zoho Integration Guide
### Step 1: Set Up Your Zoho Developer Account (App Owner Only - Do This Once!)
1. Go to the [Zoho API Console](https://api-console.zoho.com/)
2. Sign up or log in with **your own Zoho account** (this is for your master app registration)
3. Click **Add Client** → Choose **Server-based Applications**
4. Fill in the details:
   - **Client Name**: Your Grocery App Name
   - **Homepage URL**: Your app's main URL (e.g., `https://yourgroceryapp.com`)
   - **Authorized Redirect URIs**: Your app's OAuth callback URL (e.g., `https://yourgroceryapp.com/admin/integrations/zoho/callback`)
5. Click **Create** to get your `Client ID` and `Client Secret` (keep these secret!)

### Step 2: Configure Your App
Add these variables to your `.env` file:
```env
ZOHO_CLIENT_ID=your_zoho_client_id
ZOHO_CLIENT_SECRET=your_zoho_client_secret
ZOHO_REDIRECT_URI=https://yourgroceryapp.com/admin/integrations/zoho/callback
ZOHO_API_DOMAIN=https://inventory.zoho.com
```

### Step 3: Tenant-Specific Connection - How Store Owners Connect *Their Own Zoho Accounts*
Each store owner (tenant) uses **their own existing Zoho Inventory/Books account** (not yours!):

#### Tenant Connection Flow
1. Tenant logs in to your app's admin panel
2. They navigate to **Integrations > Zoho**
3. They click **Connect Zoho Account**
4. They're redirected to Zoho's OAuth login page (using *your app's Client ID*)
5. They log in with **their own Zoho credentials** (their personal/business Zoho account)
6. They select which of *their Zoho organizations* to connect (if they have multiple)
7. They grant permission to your app to access their Zoho data
8. Zoho redirects them back to your app with an OAuth code
9. Your app exchanges the code for an `access_token` and `refresh_token`
10. You **securely store these tokens in your database** (encrypted!), linked to *their tenant ID*

**Important**:
- You only need *one* master Client ID/Secret (from your Zoho developer account)
- Each tenant connects *their own Zoho account* and has *their own unique tokens*
- Never share tokens between tenants!

### Step 4: Sync Features
#### Initial Sync: Import from Zoho → Your App (When Tenant First Connects!)
When a tenant connects their Zoho account for the first time, we can automatically pull all their existing data:
- **Import Zoho Items → Your Products & Variants**
  - Maps Zoho Item fields:
    - `name` → `Product.title`
    - `sku` → `ProductVariant.sku`
    - `rate` → `ProductVariant.price`
    - `available_stock` → `ProductVariant.stock`
    - `image_url` → `ProductImage.path`
  - Creates missing collections/vendors in your system
- **Import Zoho Contacts → Your Users/Customers**
- **Import Zoho Sales Orders → Your Orders (Optional, to backfill past orders)**

#### One-Way Sync (Your App → Zoho)
- Push new products/variants to Zoho Items
- Push stock updates to Zoho
- Push placed orders to Zoho Sales Orders
- Push customer data to Zoho Contacts

#### One-Way Sync (Zoho → Your App)
- Import new Zoho Items (if tenant adds products directly in Zoho)
- Pull stock/price updates from Zoho
- Pull order status updates from Zoho

#### Two-Way Sync
- Listen for Zoho webhooks to receive real-time updates for:
  - Item created/updated/deleted
  - Stock level changed
  - Sales Order status changed

---

## Tenant Zoho Connection Flow
```mermaid
graph TD
    A[Tenant Logs In to Your App] --> B[Goes to Integrations > Zoho]
    B --> C[Clicks 'Connect Zoho Account']
    C --> D[Redirected to Zoho OAuth]
    D --> E[Logs In With *Their Own Zoho Credentials*]
    E --> F[Selects *Their Zoho Organization*]
    F --> G[Grants Permission to Your App]
    G --> H[Redirected Back to Your App]
    H --> I[Your App Exchanges Code for Tokens]
    I --> J[Stores *Their Tokens* in DB (Encrypted)]
```

---

## Tally Integration Guide
### Option A: File-Based Sync (Easiest to Start)
1. **Export from Your App**:
   - Go to **Admin > Integrations > Tally**
   - Export:
     - Products/Stock as Tally XML
     - Orders as Tally Sales Vouchers XML
2. **Import into Tally**:
   - Open Tally ERP
   - Go to **Gateway of Tally > Import of Data > Masters** (for products)
   - Or **Import of Data > Vouchers** (for orders)
   - Select the XML file you exported

### Option B: Real-Time Sync via ODBC
1. Enable ODBC in Tally:
   - Open Tally → **F12: Configure > Advanced Configuration**
   - Set **Enable ODBC Server** to Yes
   - Note the Port (default: 9000)
2. Set up cron jobs on your server to sync data periodically

---

## Current System Inventory Structure
### Models
#### Product
Located at `app/Models/Product.php`
- Fields: `id`, `title`, `slug`, `description`, `status`, `type`, `vendor`, `collection_id`, `gender`, `olfactory_family`, `intensity`, `oil_concentration`, `notes_top`, `notes_heart`, `notes_base`, `min_order_qty`, `max_order_qty`, `tenant_id`
- Relations: `variants`, `images`, `collection`, `discounts`, `bundles`

#### ProductVariant
Located at `app/Models/ProductVariant.php`
- Fields: `id`, `product_id`, `size`, `price`, `compare_at_price`, `stock`, `sku`
- Relations: `product`

#### Order
Located at `app/Models/Order.php`
- Fields: `id`, `user_id`, `order_number`, `status`, `payment_method`, `payment_status`, `subtotal`, `shipping_cost`, `discount_amount`, `total_amount`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `billing_address`, `notes`, `placed_at`, `tracking_number`, `delivery_partner_id`, `tenant_id`, `tax_name`, `tax_rate`, `tax_amount`
- Relations: `items`, `user`, `deliveryPartner`

#### OrderItem
Located at `app/Models/OrderItem.php`
- Fields: `id`, `order_id`, `product_id`, `bundle_id`, `name`, `sku`, `quantity`, `price`, `total`, `size`, `type`, `options`
- Relations: `order`, `product`, `bundle`

### Database Migrations
- Create Products Tables: `database/migrations/2025_01_04_120000_create_products_tables.php`
- Add Order Limits to Products: `database/migrations/2026_06_29_170057_add_order_limits_to_products_table.php`
- Create Orders Table: `database/migrations/2026_01_06_035606_create_orders_table.php`
- Add Tracking Number to Orders: `database/migrations/2026_01_06_043937_add_tracking_number_to_orders_table.php`
- Add Delivery Partner to Orders: `database/migrations/2026_01_07_051422_add_delivery_partner_id_to_orders_table.php`
- Add Tax to Orders: `database/migrations/2026_07_01_065543_add_tax_to_orders_table.php`

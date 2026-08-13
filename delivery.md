# Delivery Management Module Specification

This document outlines the architecture, features, and implementation plan for the new Delivery Management Module for GoSlot Store. This module allows individual store owners to manage their own local delivery fleets and gives delivery personnel a dedicated interface to manage their assigned orders.

## 1. Overview
The Delivery Module will introduce a new user role (`delivery_boy`) scoped to individual stores (tenants). Store admins can assign incoming orders to these delivery personnel. Delivery personnel will have their own secure login to view their assigned tasks, update order statuses, and collect payments (e.g., Cash on Delivery). 

---

## 2. User Roles & Permissions

### Store Admin (`admin` or `store_owner`)
- **Manage Delivery Staff**: Ability to add, edit, or remove delivery boy accounts for their specific store.
- **Order Assignment**: Ability to assign (or re-assign) an order to a specific delivery boy from the existing `/admin/orders` interface.
- **Tracking**: View real-time status updates provided by the delivery boy.

### Delivery Boy (`delivery_boy`)
- **Authentication**: Can log into the store's domain using their own email/phone and password.
- **Delivery Dashboard**: A simplified, mobile-friendly view listing only the orders assigned to them.
- **Order Details**: View customer address, phone number, order items, and payment status (especially COD amounts to be collected).
- **Status Updates**: Ability to update the order status (e.g., `Out for Delivery`, `Delivered`, `Failed/Returned`).

---

## 3. Database Modifications

We will utilize the existing `users` and `orders` tables with minimal schema changes:

1. **User Type**: The `users.type` column currently supports roles like `admin`, `customer`. We will introduce a new value: `delivery_boy`.
2. **Order Assignment**: The existing `orders.delivery_partner_id` column will be used as a foreign key pointing to `users.id` (where type = `delivery_boy`).

---

## 4. Admin Panel Integration

The existing Order Management page (`/admin/orders`) will be enhanced:
- **Assignment Dropdown**: A new dropdown in the order details view (and optionally on the list view) populated with active delivery boys for the current tenant.
- **Delivery Staff Management**: A new section under the admin settings/users to create and manage `delivery_boy` credentials.

---

## 5. Delivery Boy Interface

A new, mobile-optimized route group (`/delivery/*`) will be created for the delivery personnel.

- **Login**: Uses the standard login but redirects to the Delivery Dashboard if `type == 'delivery_boy'`.
- **Active Orders View**: Shows orders marked as `Processing` or `Out for Delivery`.
- **Completed Orders View**: History of deliveries completed today.
- **Update Workflow**:
    - Tap order -> View Map/Address & Call Customer.
    - Tap "Start Delivery" -> Status changes to `Out for Delivery`.
    - Tap "Mark as Delivered" -> Prompts for payment collection if COD, changes status to `Delivered`.

---

## Open Questions

1. **Authentication Flow**: Should delivery boys log in through the same URL as the Store Admin (`/admin/login`), or should we create a dedicated login page just for them (e.g., `/delivery/login`)?
2. **Order Status Flow**: Currently, an order goes from `Processing` -> `Completed`. Do we need to add intermediate statuses like `Out for Delivery` and `Failed Attempt` to the core system?

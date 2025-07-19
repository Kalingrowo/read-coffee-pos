☕ Coffee Shop POS API
=====================================

📌 Project Summary
------------------

A backend API to manage a coffee shop's daily sales operations, including:

-   Product management

-   Order processing

-   Inventory control

-   User roles

-   Reporting

* * * * *

🎯 Key Features & Modules
-------------------------

### 1\. Authentication & Authorization

-   User registration and login via **Laravel Sanctum**

-   **Role-based access control**:

    -   **Owner**: Full access to all modules

    -   **Cashier**: Limited access (e.g. transactions, product view only)

* * * * *

### 2\. User & Role Management

-   Assign multiple roles to users

-   List users with their roles

-   Change user roles (assign/remove roles)

* * * * *

### 3\. Product & Category Management

#### Categories

-   CRUD operations for product categories (e.g. Coffee, Tea, Snacks)

#### Products

-   CRUD operations for products

-   Fields:

    -   `name`

    -   `category`

    -   `price`

    -   `stock`

    -   `description` (optional)

-   **Stock update upon sale**

* * * * *

### 4\. Transaction & Order Management

#### Orders

-   Create new orders (checkout flow)

-   Add products to order (cart functionality)

-   Deduct stock upon order completion

-   Record:

    -   Order total

    -   Payment method

    -   Change

#### Order Items

-   Relational items per order

* * * * *

### 5\. Payment Handling

-   Record payments:

    -   Cash

    -   QRIS/Transfer (**mock for now**)

-   Store payment details per order

* * * * *

### 6\. Reporting

#### Daily Sales Report

-   Total sales per day

-   Filterable by date range

#### Top-Selling Products

-   Endpoint to retrieve most sold items within a date range

#### Inventory Alert

-   List products with low stock (below defined threshold)

* * * * *

### 7\. Export & Notifications

-   Generate **PDF or Excel reports** (daily sales, top products)

-   *(Optional)* Send daily sales summary via **email or WhatsApp**

* * * * *

### 8\. Miscellaneous

-   **Dockerized setup** for local development

-   **PostgreSQL** as primary database

-   **Redis** for queues (future: jobs, notifications)

-   API documentation via **Postman or Swagger**

* * * * *

📌 Nice-to-Have (Future Expansion)
----------------------------------

-   Multi-outlet support (for multiple branches)

-   POS integration for QR code-based table ordering

-   Expense tracking (profit/loss calculations)

-   Integration with **WhatsApp API** for order notifications
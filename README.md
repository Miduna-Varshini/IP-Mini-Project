# VOGUEX - E-Commerce Management System

A dynamic fashion e-commerce website built for the **CS2307 - Internet Programming Lab** mini project. Customers can browse products, manage a cart and place orders. Admins can manage products, orders and customers.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?logo=javascript&logoColor=black)

---

## Tech Stack

| Layer | Technology |
|---|---|
| Structure | HTML5 |
| Styling | CSS3 (responsive layout) |
| Client-side | JavaScript |
| Server-side | PHP |
| Database | MySQL (via phpMyAdmin) |
| Server | XAMPP (Apache + MySQL) |

---

## Features

### Customer
- Register, login and logout with hashed passwords
- Browse products with **search**, **category filter** and **sorting**
- Sale and New Arrivals pages
- Product details page with related products
- Shopping cart: add, update quantity, remove and clear
- Checkout with Cash on Delivery
- Order history and cancellation of pending orders

### Admin
- Dashboard with customer, product, order and revenue statistics
- Low-stock overview
- Add, edit and delete products, including image upload
- Update order status (Pending, Shipped, Delivered, Cancelled)
- View all registered customers

### Security
- Passwords hashed with `password_hash()`
- SQL injection protection with prepared statements
- Output escaping against XSS
- Admin pages restricted by role

---

## Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) installed

### Installation

1. **Start the servers**  
   Open the XAMPP Control Panel and start **Apache** and **MySQL**.

2. **Copy the project**  
   Place the `voguex` folder inside the XAMPP `htdocs` directory, for example `C:\xampp\htdocs\voguex`.

3. **Import the database**  
   - Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
   - Go to the **Import** tab
   - Choose `database.sql` and click **Import**

4. **Open the website**  
   [http://localhost/voguex/](http://localhost/voguex/)

### Default Login

| Role | Email | Password |
|---|---|---|
| Admin | `admin@voguex.com` | `admin123` |
| Customer | Register a new account | - |

---

## Database

The database `voguex_db` contains 5 tables:

| Table | Purpose |
|---|---|
| `users` | Customers and admin accounts |
| `categories` | Product categories |
| `products` | Product details, price and stock |
| `orders` | Customer orders |
| `order_items` | Products inside each order |

---

## Project Structure

```
voguex/
├── admin/                 Admin panel
│   ├── index.php          Dashboard
│   ├── products.php       Product list
│   ├── product_form.php   Add / edit product
│   ├── orders.php         Manage orders
│   └── users.php          Customer list
├── css/
│   └── style.css          Styling
├── images/                Product images
├── includes/
│   ├── header.php         Shared header
│   └── footer.php         Shared footer
├── js/
│   └── main.js            Client-side scripts
├── cart.php               Shopping cart
├── checkout.php           Place an order
├── config.php             Database connection and helpers
├── database.sql           Tables and sample data
├── index.php              Home page
├── login.php              Login
├── logout.php             Logout
├── orders.php             Customer order history
├── product.php            Product details
├── register.php           Registration
└── shop.php               Product listing
```

---

## Author

**Miduna Varshini**  
GitHub: [@Miduna-Varshini](https://github.com/Miduna-Varshini)

---
# Visitors Book System

## Project Overview

A web-based Visitors Book developed using PHP, MySQLi, MySQL and Tailwind CSS

The application allows authorized staff to record visitor arrivals and departures, view the day's visitors and search historical visits.

## Features

- Secure staff login and logout
- Visitor check-in with server-side validation
- Automatic check-in and checkout timestamps
- Today's visitor list and inside count
- Visitor checkout with double-checkout prevention
- Search by visitor name, phone number and date range
- Visit-duration display
- Responsive Tailwind CSS interface

## Technologies

- PHP 8.2 or newer
- MySQL or MariaDB
- MySQLi prepared statements
- HTML and Tailwind CSS
- XAMPP for local development
- Git and GitHub

## Local Installation

1. Install XAMPP and start Apache and MySQL.
2. Clone this GitHub repository into `C:\xampp\htdocs\visitors-book`.
3. Open `http://localhost/phpmyadmin`.
4. Import `database/schema.sql` to create the `visitors_book` database and its `users` and `visits` tables.
5. Copy `config/database.example.php` to `config/database.php`.
6. Set the correct local database host, name, username and password in `config/database.php`.
7. Ensure PHP has the MySQLi and mbstring extensions available.

Do not commit `config/database.php`.

## Create the Initial Staff Account

Run `database/seed_user.php` from the command line after setting the `VISITOR_ADMIN_PASSWORD` environment variable to a strong initial password.

On XAMPP, use `C:\xampp\php\php.exe` to run the script.

The sample administrator email is `admin@example.com`. The password is chosen during setup, not published in this README.

Run the seed script only once for each administrator account.

## Start the Application

Open:

`http://localhost/visitors-book/login.php`

Log in with the initial staff account, then use the navigation to check in visitors, view today's visits, check out visitors and search visit history.

## Security

- Passwords are hashed using `password_hash()`.
- Authentication is maintained using PHP sessions.
- Form submissions use CSRF protection.
- Database input is handled through MySQLi prepared statements.
- Visitor details are escaped before HTML output.
- Database connection credentials remain outside version control.

## Testing

Test login, logout, visitor check-in, validation, refresh behavior, today's list, checkout, double-checkout prevention, historical search, duration calculations and mobile responsiveness.

Use fictional records for testing.

## Development

The project uses milestone branches and pull requests for mentor review.

Phase 1 implements the system using MySQLi. Phase 2 will rebuild the database layer using PDO after the MySQLi version is reviewed and accepted.

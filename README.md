Banking System Project
A multi-role Banking System built with PHP, MySQL, HTML/CSS/JavaScript, and Tailwind CSS for a modern, user-friendly interface. This system provides distinct dashboards and functionality for Admins, Staff, and Customers, ensuring role-based access and efficient banking operations.

Overview
This project simulates a banking environment where different users (admin, staff, customer) can access distinct features. The system handles user registration, account management, transactions, ATM services, and complaint handling. It leverages a MySQL database for data persistence and Tailwind CSS for a sleek, responsive UI.


Key Features
Role-Based Access: Only authorized users can log in to view or modify data relevant to their role.
Customer Portal:
Dashboard displaying balances, transaction histories, and daily limits.
ATM Services (deposit, withdraw with daily limit checks, card management).
Complaints (file a new complaint, track status).
Staff Portal:
Deposit/Withdraw cash for customers in the same branch.
View & Manage Complaints for their branch.
Staff Action History to audit deposit/withdraw operations.
Admin Portal:
Manage Users (update profile info, block/unblock accounts).
Manage Cards (block/unblock cards, set daily limits, change PIN).
Balance Updates for special cases.
Full Control over staff actions and system data.
Complaints Management:
Comprehensive complaints table, with Pending or Resolved statuses.
Staff can filter by customers in their branch, assist with complaint resolution, or escalate.

Roles & Dashboards
Admin
Oversees all user actions, accounts, and cards.
Access to advanced modifications (balances, user status, card settings).
Staff
Manages day-to-day branch tasks (depositing, withdrawing, viewing customer complaints).
Each staff belongs to a specific branch and can only handle that branch’s customers.
Customer
Sees personal balances, cards, transaction histories.
Files or views complaints, uses ATM operations, and checks daily limits.

Technology Stack
Backend: PHP (procedural or with minimal OOP usage) + MySQL for database interactions.
Frontend: HTML, JavaScript, Tailwind CSS for responsive layouts, forms, tables, and consistent UI/UX.
Server: Runs on Apache or similar (XAMPP, WAMP, LAMP, etc.).
Version Control: Git & GitHub for source code management.

Installation & Setup
Clone or Download this repository to your local machine.
Place it inside your web server’s document root (e.g., htdocs for XAMPP).
Import the SQL schema (if provided) into your MySQL database (e.g., create a new DB banking_system, then import banking_system.sql).
Edit db_connect.php with your MySQL credentials:
php
Copy code
$conn = mysqli_connect("localhost", "root", "", "banking_system");
Start Apache & MySQL (via XAMPP or otherwise).
Access the project at http://localhost/banking_system in your browser.
Database Schema & Configuration
users table for storing user credentials (username, password, role, etc.).
customer_accounts for storing account numbers, balances, branch info.
cards for storing card info (card_number, expiry, daily_limit, is_blocked, pin, attempts).
transactions for logging deposit/withdraw/transfer details.
complaints for capturing complaint data (customer username, title, description, status).
staff_actions for deposit/withdraw logs performed by staff.
(Adjust table names/fields as per your actual schema.)

TO GET FULLY WORKING DATABASE FILE SEND ME EMAIL - Umeshrockz57@gmail.com

Usage
Open the login page (e.g., index.html or login.php).
Choose a role (customer, staff, admin) and enter valid credentials.
Navigate to your dashboard to see role-appropriate features:
Customers: Perform ATM operations, file/view complaints, see transaction history.
Staff: Deposit/withdraw for customers, view staff action logs, handle complaints for your branch.
Admins: Update user info, manage accounts and cards, handle high-level tasks.

Directory Structure

![image](https://github.com/user-attachments/assets/2a7fcbbb-e935-4d46-9d97-0c40997c02c5)


Contributing
Fork this repo and clone it locally.
Create a new branch for your feature or fix: git checkout -b feature/my-feature.
Commit and push changes to your fork.
Create a Pull Request describing your changes.
Ensure your code passes all checks and reviews before merging.

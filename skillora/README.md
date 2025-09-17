# Skillora - Learn. Upgrade. Earn.

Skillora is a comprehensive online video course website built from the ground up using plain HTML, CSS, JavaScript, PHP, and MySQL. It is designed to be a production-ready, responsive platform deployable on standard hosting environments like XAMPP or WAMP.

The core feature of Skillora is its unique payment and user registration flow. Users select a membership tier and submit proof of payment (e.g., a QR payment screenshot). An administrator then reviews this payment. Upon approval, the user's account is automatically created, membership is assigned, and they gain access to the courses permitted for their tier.

## Tech Stack

*   **Frontend**: Plain HTML, CSS, JavaScript (no frameworks)
*   **Backend**: Plain PHP (no frameworks)
*   **Database**: MySQL
*   **Server Environment**: Designed for XAMPP/WAMP

## Key Features

*   **Manual Payment Flow**: Users pay via QR code and upload a receipt for manual verification.
*   **Admin Approval System**: Admins review pending payments and approve or reject them.
*   **Automatic User Creation**: User profiles are automatically created only *after* a payment is approved.
*   **Tier-Based Course Access**: A normalized database schema links membership tiers to courses, ensuring users can only access content they are permitted to view.
*   **Full Course Management**: Admins have complete CRUD (Create, Read, Update, Delete) control over courses and their associated lessons from the admin panel.
*   **Wallet & Withdrawal System**: Users earn from referrals and can request to withdraw their wallet balance. Admins can approve or reject these requests.
*   **Referral & Earn System**: Users get a unique referral code after activation and earn wallet bonuses from successful referrals.
*   **Secure Admin & User Panels**: Separate, secure login and dashboard areas for both administrators and regular users.
*   **Secure Coding Practices**: The application is built with security in mind, utilizing prepared statements to prevent SQL injection and output sanitization to prevent XSS attacks.

## Setup Instructions for XAMPP/WAMP

Follow these steps to get the Skillora project running on your local machine.

1.  **Install XAMPP/WAMP**: If you don't have it already, download and install [XAMPP](https://www.apachefriends.org/index.html) or a similar local server environment.

2.  **Place Project Files**:
    *   Clone or download this repository.
    *   Place the entire `skillora` project folder inside the `htdocs` directory (for XAMPP) or `www` (for WAMP). Your file path should look something like `C:/xampp/htdocs/skillora`.

3.  **Start Services**: Open the XAMPP/WAMP control panel and start the **Apache** and **MySQL** services.

4.  **Create the Database**:
    *   Open your web browser and go to `http://localhost/phpmyadmin`.
    *   Click on the "New" button on the left sidebar.
    *   Enter `skillora` as the database name and click "Create".

5.  **Import the Database Schema**:
    *   Select the newly created `skillora` database from the left sidebar.
    *   Click on the **Import** tab at the top.
    *   Click "Choose File" and select the `database.sql` file located inside the `skillora` project folder.
    *   Scroll down and click "Go" to start the import. This will create all the necessary tables and populate them with sample data.

6.  **Run the Application**:
    *   You're all set! Open your browser and navigate to:
    *   `http://localhost/skillora/`

## Default Login Credentials

You can use the following sample accounts to test the application:

*   **Admin Account**:
    *   **Email**: `admin@skillora.com`
    *   **Password**: `adminpass123`

*   **Regular User Account** (with Gold Membership):
    *   **Email**: `user@example.com`
    *   **Password**: `password123`

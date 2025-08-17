# CashMitra - Earning Platform Website

CashMitra is a modern, responsive, and secure earning platform website built with PHP, MySQL, HTML, CSS, and JavaScript. It allows users to earn money by completing offers, watching ads, and referring friends. The project includes a user-facing website and a comprehensive admin panel for management.

**Developer:** Dhiraj SAH

## Features

### User-Facing Features
- **Modern UI/UX:** A futuristic, responsive design built with Bootstrap 5.
- **User Authentication:** Secure registration, login, and password reset functionality.
- **Dashboard:** A user-friendly dashboard showing earnings, recent activity, and referral stats.
- **Offerwalls:** A dedicated page to browse and complete offers from various providers.
- **Ad Viewing:** Users can watch ads to earn points.
- **Referral System:** Users get a unique referral link to invite friends and earn commissions.
- **Withdrawal System:** Users can request withdrawals of their earnings.
- **Notifications:** Toast notifications for a better user experience.
- **Security:** CSRF protection, password hashing, and rate limiting are implemented.
- **Performance:** Minified CSS/JS and lazy-loaded images for a fast experience.

### Admin Panel Features
- **Secure Login:** A separate, secure login for administrators.
- **Dashboard:** An overview of site statistics.
- **User Management:** View, edit, and delete users.
- **Offer Management:** Add, edit, and delete offers.
- **Ad Management:** Add, edit, and delete ads.
- **Withdrawal Management:** Approve or reject withdrawal requests.
- **Fraud Prevention:** Basic IP tracking to identify potential duplicate accounts.

## How to Set Up on XAMPP/WAMP

1.  **Download and Install XAMPP/WAMP:**
    *   Download and install [XAMPP](https://www.apachefriends.org/index.html) or [WAMP](https://www.wampserver.com/en/).

2.  **Clone the Repository:**
    *   Clone this repository into the `htdocs` folder of your XAMPP installation (e.g., `C:\xampp\htdocs\cashmitra`) or the `www` folder for WAMP.

3.  **Start Apache and MySQL:**
    *   Open the XAMPP/WAMP control panel and start the Apache and MySQL services.

4.  **Create the Database:**
    *   Open your web browser and go to `http://localhost/phpmyadmin`.
    *   Create a new database named `cashmitra`.
    *   Select the `cashmitra` database, go to the "Import" tab, and choose the `sql/database.sql` file from the project directory to import the database schema and sample data.

5.  **Configure the Application:**
    *   Open the `php/core/config.php` file.
    *   Make sure the `DB_USER`, `DB_PASS`, and `DB_NAME` constants match your database configuration. By default, the username is `root` and the password is an empty string for XAMPP.
    *   Update the `BASE_URL` constant to match your project's URL (e.g., `http://localhost/cashmitra`).

6.  **Run the Application:**
    *   You can now access the website by navigating to `http://localhost/cashmitra` in your web browser.

## Admin Panel

-   **URL:** `http://localhost/cashmitra/admin`
-   **Default Username:** `admin`
-   **Default Password:** The default password is 'admin'. The `database.sql` file contains the hashed version of this password.

## Notes

-   **Email Verification:** Email verification is simulated by logging the verification links to a file named `verification_links.log` in the `php/controllers` directory. In a production environment, you would integrate a proper email sending library like PHPMailer.
-   **Image Assets:** The offerwall provider logos are referenced in the code but are not included in the repository. You will need to add the images to the `public/images/offerwalls` directory for them to display correctly.

---
Developed by Dhiraj SAH.

# CashMitra Mobile App

This is the mobile application for the CashMitra Earning Platform, built with React Native and Expo.

## How to Run

This project was bootstrapped with Expo.

### Prerequisites

-   Node.js and npm/yarn installed on your machine.
-   Expo Go app installed on your physical Android or iOS device.

### 1. Install Dependencies

Navigate to the `mobile-app` directory in your terminal and run:

```bash
npm install
```
or
```bash
yarn install
```

### 2. Start the Development Server

Once the dependencies are installed, you can start the Expo development server:

```bash
npm start
```
or
```bash
expo start
```

### 3. Run on Your Device

-   The command will output a QR code in your terminal.
-   Open the Expo Go app on your phone.
-   Scan the QR code to open the CashMitra app on your device.

**Note:** Your computer and your mobile device must be on the same Wi-Fi network for this to work.

## Backend Connection

This mobile app is designed to connect to the PHP backend of the main CashMitra web project. Ensure that the web server (e.g., XAMPP/WAMP) is running and that the API endpoints in the app's code (e.g., in `screens/LoginScreen.js`) are pointing to the correct local server address (e.g., `http://your-local-ip/cashmitra/php/api/login.php`). You cannot use `localhost` from the mobile device; you must use your computer's local network IP address.

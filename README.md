# Student Attendance System with QR Code

This is a simple PHP application for managing student attendance using QR codes. It features a responsive design and an admin panel for managing students.

## Features

-   Student management (CRUD)
-   QR code generation for each student
-   QR code scanner for recording attendance
-   Admin panel for managing students and viewing attendance
-   Responsive design using Bootstrap
-   Easy setup wizard

## Installation

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    ```

2.  **Install dependencies:**
    This project uses Composer to manage PHP dependencies. Make sure you have Composer installed, and then run the following command in the project root:
    ```bash
    composer install
    ```

3.  **Set up the application:**
    Open the project in your web browser. You will be redirected to a setup wizard that will guide you through the process of configuring the database and setting up the application.

4.  **Follow the setup wizard:**
    Enter your database credentials and follow the instructions to complete the setup.

5.  **Secure your installation:**
    After the setup is complete, it is important to delete the `setup.php` file from the project root for security reasons.

## Usage

-   **Admin Panel:** Navigate to `/admin` to manage students.
-   **QR Code Scanner:** Navigate to `/qrcode/scan` to record attendance.

### Default Admin Credentials

-   **Username:** `admin`
-   **Password:** `admin`
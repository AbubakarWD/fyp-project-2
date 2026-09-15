# BloodLife — Social Blood Donation & Emergency Assistance Platform

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-777BB4?style=flat-square&logo=php)](https://www.php.net)
[![Database](https://img.shields.io/badge/MySQL%2FMariaDB-PDO%20Prepared-4479A1?style=flat-square&logo=mysql)](https://www.mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.3-7952B3?style=flat-square&logo=bootstrap)](https://getbootstrap.com)
[![Security](https://img.shields.io/badge/Security-BCRYPT%20%7C%20CSRF%20%7C%20XSS-green?style=flat-square)](#security--architecture)

**BloodLife** is a full-stack social blood donation platform built using **Core PHP 8+**, **MySQL (PDO)**, **HTML5**, **CSS3 (Custom Unified Design System)**, **Vanilla JavaScript**, and **Bootstrap 5.3**. It bridges voluntary blood donors with emergency recipients through ABO compatibility matching, real-time donor availability toggles, response tracking, verified donation logs, and database-backed in-app notifications.

---

## 🌟 Key Features & Capabilities

### 1. Unified BloodLife Design System
- Custom CSS design system (`public/assets/css/design-system.css`) utilizing HSL color variables, modern typography (Inter), glassmorphism navbar, crimson gradients, and crisp card shadows.
- Consistent visual language across all public pages, authentication views, and authenticated dashboards.

### 2. Multi-Section Public Homepage & Discovery Engine
- Hero CTA banner, quick blood search widget, ABO blood groups compatibility grid, platform stats counters, "How It Works" workflow, testimonials, and emergency CTA.
- Advanced Donor Discovery page (`/donors.php`) with real-time filtering by blood group, city, availability, and donor name search.
- Privacy-conscious phone masking for unauthenticated guests.

### 3. Real Authentication & Authorization System
- Role-based account creation (`donor` vs `recipient/requester`).
- Secure BCRYPT password hashing and strict PDO prepared statements.
- Role-based dashboard routing with authentication guards (`AuthMiddleware.php`).

### 4. Donor Dashboard & Real-Time Availability
- Interactive Availability Toggle switch (`/api/donor-availability.php`) allowing donors to turn discovery ON/OFF instantly.
- Live emergency request feed matched to donor's ABO compatibility.
- One-click response mechanism with custom notes and unit offering.

### 5. Recipient Dashboard & Request Lifecycle Management
- Comprehensive request creation form (`/request-create.php`) with patient details, urgency level, required date, hospital address, and contact information.
- Automated ABO matching engine (`MatchingService.php`) that identifies compatible available donors and dispatches in-app notification alerts automatically.
- Request lifecycle management: `active` ➔ `partially_fulfilled` ➔ `fulfilled` / `cancelled`.

### 6. Donor Response & Connection Hub
- Dedicated connection manager (`/responses.php`) for acceptors and donors to view contact details, update response statuses (`pending` ➔ `accepted` ➔ `completed` / `cancelled`).

### 7. Profile & Verified Donation Log System
- Profile management (`/profile.php`) with avatar uploads via secure `FileUploadService` (MIME/extension validation, size limit 3MB).
- Verified donation history timeline (`/donation_history.php`) logging facility name, city, date, and units donated.
- Automatic transaction-backed increment of donor's total donation count and last donated timestamp.

### 8. In-App Notification Engine
- Real-time notification center (`/notifications.php`) with unread counters, notification categories (`donor_match`, `new_response`, `request_update`), time-ago formatting, and AJAX bell dropdown feed.

### 9. Production-Grade Security Hardening
- 100% PDO prepared statements preventing SQL injection.
- CSRF token generation and validation on all state-changing POST forms.
- Context-aware XSS escaping (`SecurityHelper::e()`).
- Security HTTP response headers (`X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Referrer-Policy`).

---

## 📁 Directory Architecture

```
fyp project 2/
├── app/
│   ├── config/
│   │   ├── config.php            # Central app constants & security headers
│   │   └── database.php          # PDO Singleton instance & MySQL connection
│   ├── controllers/
│   │   ├── AuthController.php        # Login, Signup, Logout, Password Reset
│   │   ├── DashboardController.php   # Donor & Recipient Dashboards
│   │   ├── DonorController.php       # Donor Discovery & Search Engine
│   │   ├── RequestController.php     # Emergency Request Posting & Lifecycle
│   │   ├── ResponseController.php    # Donor Responses & Connection Hub
│   │   ├── ProfileController.php     # Profile Management & Avatar Uploads
│   │   ├── DonationController.php    # Verified Donation History Tracking
│   │   └── NotificationController.php# Notification Center & Alerts
│   ├── helpers/
│   │   ├── SecurityHelper.php        # XSS, CSRF, Password Hashing & Sanitization
│   │   ├── SessionHelper.php         # Session Guarding & Flash Messages
│   │   └── FormatHelper.php          # Date, Phone, Urgency Badge Formatters
│   ├── middleware/
│   │   └── AuthMiddleware.php        # Route Authentication & Role Guards
│   ├── models/
│   │   ├── BloodGroup.php            # ABO Groups & Compatibility Matrix
│   │   ├── UserProfile.php           # User Profiles & Search Queries
│   │   ├── BloodRequest.php          # Emergency Requests CRUD
│   │   ├── RequestResponse.php       # Donor Responses & Status Transitions
│   │   ├── DonationRecord.php        # Verified Donation Logs
│   │   └── Notification.php          # Database Notification Alerts
│   └── services/
│       ├── FileUploadService.php     # Secure Image & Avatar Upload Handler
│       └── MatchingService.php       # ABO Donor Matching Engine
├── database/
│   ├── schema.sql                # Complete Database Tables & Indexes
│   ├── seed.sql                  # Initial Blood Groups & Test Accounts
│   └── init_db.php               # Database Auto-Initialization Script
├── public/
│   ├── api/
│   │   ├── donor-availability.php    # AJAX Availability Toggle API
│   │   ├── respond.php               # AJAX Donor Response API
│   │   ├── request-status.php        # AJAX Request Status API
│   │   ├── notifications-unread.php  # AJAX Unread Notifications Counter
│   │   └── donor-cancel-response.php # AJAX Response Cancellation API
│   ├── assets/
│   │   ├── css/
│   │   │   ├── design-system.css     # Centralized Unified Tokens & Component Styles
│   │   │   └── landing.css           # Public Homepage Specific Styles
│   │   └── js/
│   │       ├── app.js                # Core Application Client Logic
│   │       └── validation.js         # Frontend Form Validation
│   ├── uploads/
│   │   └── avatars/                  # Uploaded Profile Pictures
│   ├── index.php                 # Public Landing Page
│   ├── login.php                 # Login View Route
│   ├── signup.php                # Sign Up View Route
│   ├── dashboard.php             # Authenticated Dashboard Route
│   ├── donors.php                # Donor Discovery Search Route
│   ├── requests.php              # Emergency Requests Feed Route
│   ├── responses.php             # Response Connections Route
│   ├── profile.php               # Profile Settings Route
│   ├── donation_history.php      # Donation History Timeline Route
│   └── notifications.php         # Notification Center Route
├── views/
│   ├── auth/                     # Login & Signup Views
│   ├── dashboard/                # Donor & Recipient Dashboard Views
│   ├── donors/                   # Donor Search View & Profile Modal
│   ├── requests/                 # Request Listing, Detail & Create Views
│   ├── responses/                # Connection Management View
│   ├── profile/                  # Profile Edit & Avatar Upload View
│   ├── donations/                # Donation History Timeline View
│   ├── notifications/            # Notification Center Feed View
│   └── includes/                 # Reusable Layout Headers, Sidebar & Toast
└── tests/                        # Comprehensive Automated Test Suites
    ├── test_auth_system.php
    ├── test_backend_foundation.php
    ├── test_donor_dashboard.php
    ├── test_recipient_dashboard.php
    ├── test_donor_discovery.php
    ├── test_request_management.php
    ├── test_response_connection.php
    ├── test_profile_donation.php
    ├── test_notifications.php
    ├── test_security_audit.php
    └── test_full_system_integration.php
```

---

## 🔑 Demo Credentials

To test the application out of the box, use the pre-configured demo user accounts:

| User Type | Email | Password | Role |
| :--- | :--- | :--- | :--- |
| **Voluntary Donor** | `donor1@bloodlife.org` | `Password123!` | Donor (`A+`) |
| **Emergency Requester** | `requester1@bloodlife.org` | `Password123!` | Requester (`O-`) |

---

## ⚙️ Local Installation & Database Setup

1. **Clone/Ensure Workspace Path**:
   `/var/www/html/projects/full-stack-sites/fyp project 2/`

2. **Initialize Database & Seed Data**:
   Ensure MySQL/MariaDB service is running, then execute:
   ```bash
   php database/init_db.php
   ```

3. **Start Local Development Server**:
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```
   Access the web app at `http://127.0.0.1:8000`.

4. **Run All Automated Test Suites**:
   ```bash
   php tests/test_auth_system.php
   php tests/test_backend_foundation.php
   php tests/test_donor_dashboard.php
   php tests/test_recipient_dashboard.php
   php tests/test_donor_discovery.php
   php tests/test_request_management.php
   php tests/test_response_connection.php
   php tests/test_profile_donation.php
   php tests/test_notifications.php
   php tests/test_security_audit.php
   php tests/test_full_system_integration.php
   ```

---

## 📜 License & Acknowledgments

Built for life-saving voluntary social blood donation and emergency assistance. All rights reserved.

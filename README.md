# P&D Manager - Full Stack Application

A complete product and discount management system built with Laravel and Angular, featuring JWT authentication, advanced sorting, comprehensive validation, and 95+ unit tests.

---

## 📋 Table of Contents

1. [System Requirements](#system-requirements)
2. [Technology Stack](#technology-stack)
3. [Installation Guide](#installation-guide)
4. [Configuration](#configuration)
5. [Running the Application](#running-the-application)
6. [Testing](#testing)
7. [Project Structure](#project-structure)
8. [Key Features](#key-features)
9. [Troubleshooting](#troubleshooting)

---

## 🔧 System Requirements

### Minimum Requirements
- **PHP**: 8.1 or higher
- **Node.js**: 18 or higher
- **MySQL**: 8.0 or higher
- **Composer**: Latest version
- **npm**: 10.0 or higher (comes with Node.js)

### Optional
- **Git**: For cloning the repository
- **Postman/Insomnia**: For API testing

---

## 🛠️ Technology Stack

### Backend
| Technology | Version | Purpose |
|-----------|---------|---------|
| PHP | 8.1+ | Server-side language |
| Laravel | 10.10+ | Web framework |
| MySQL | 8.0+ | Database |
| Composer | Latest | Package manager |
| JWT Auth | 2.2+ | Authentication (tymon/jwt-auth) |
| PHPUnit | 10.1+ | Testing framework |

### Frontend
| Technology | Version | Purpose |
|-----------|---------|---------|
| Angular | 19.2.0 | Frontend framework |
| TypeScript | Latest | Programming language |
| Angular Material | 19.2.19 | UI components |
| RxJS | 7.8.0 | Reactive programming |
| npm | 10.0+ | Package manager |

### Database
| Component | Version | Notes |
|-----------|---------|-------|
| MySQL | 8.0+ | Primary database |
| Migrations | Laravel 10 | Schema management |

---

## 📥 Installation Guide

### Prerequisites Setup

#### Windows
1. Download and install PHP 8.1+ from [php.net](https://www.php.net/downloads)
2. Download and install MySQL 8.0+ from [mysql.com](https://www.mysql.com/downloads/)
3. Download and install Node.js 18+ from [nodejs.org](https://nodejs.org/)
4. Download and install Composer from [getcomposer.org](https://getcomposer.org/)

#### macOS
```bash
# Using Homebrew
brew install php@8.1
brew install mysql
brew install node
brew install composer
```

#### Linux (Ubuntu/Debian)
```bash
# PHP 8.1
sudo apt-get install php8.1 php8.1-cli php8.1-mysql php8.1-curl php8.1-xml

# MySQL 8.0
sudo apt-get install mysql-server

# Node.js 18+
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install nodejs

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Backend Installation

```bash
# Navigate to backend directory
cd backend

# Install PHP dependencies
composer install

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Create database
mysql -u root -p -e "CREATE DATABASE p_d_manager;"

# Run migrations and seeders
php artisan migrate:refresh --seed
```

**⚠️ Important**: The `migrate:refresh --seed` command will:
- Drop all existing tables
- Re-run all migrations
- Seed the database with initial data (admin user, sample products, discounts)

### Frontend Installation

```bash
# Navigate to frontend directory
cd frontend

# Install Node.js dependencies
npm install

# Verify installation
npm --version  # Should be 10.0+
node --version # Should be 18+
```

---

## ⚙️ Configuration

### Backend Configuration (`.env`)

Create or update the `.env` file in the backend directory:

```env
# Application Configuration
APP_NAME=PDManager
APP_ENV=local
APP_KEY=base64:your_key_generated_by_php_artisan_key_generate
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=p_d_manager
DB_USERNAME=root
DB_PASSWORD=password

# JWT Configuration
JWT_SECRET=your_secret_generated_by_php_artisan_jwt_secret
JWT_ALGORITHM=HS256
JWT_TTL=60

# CORS Configuration
CORS_ALLOWED_ORIGINS=http://localhost:4200,http://localhost:3000
```

### Frontend Configuration

The frontend API URL is configured in:
- `src/environments/environment.ts` (Development)
- `src/environments/environment.prod.ts` (Production)

Default API URL: `http://localhost:8000/api`

---

## 🚀 Running the Application

### Terminal 1: Start Backend Server

```bash
cd backend

# Start Laravel development server
php artisan serve
```

**Backend URL**: http://localhost:8000
**API Base URL**: http://localhost:8000/api

### Terminal 2: Start Frontend Server

```bash
cd frontend

# Start Angular development server
npm start
```

**Frontend URL**: http://localhost:4200

### Test Login Credentials

After running `php artisan migrate:refresh --seed`:

```
Email: admin@example.com
Password: password
```

---

## 🧪 Testing

### Run All Tests

```bash
cd backend

# Run entire test suite
php artisan test

# Run with environment flag
php artisan test --env=testing
```

### Run Specific Test Files

```bash
php artisan test tests/Unit/ProductControllerTest.php
php artisan test tests/Unit/DiscountControllerTest.php
php artisan test tests/Unit/AuthControllerTest.php
php artisan test tests/Unit/ProductModelTest.php
php artisan test tests/Unit/DiscountModelTest.php
```

### Test Statistics

- **Total Test Cases**: 95+
- **Code Coverage**: 70%+
- **Test Duration**: ~6 seconds
- **All tests passing**: ✓

### Test Files Location

```
backend/tests/Unit/
├── ProductControllerTest.php     (27 tests - CRUD & Sorting)
├── DiscountControllerTest.php    (25 tests - Discount Management)
├── ProductModelTest.php           (15 tests - Product Model)
├── DiscountModelTest.php          (18 tests - Discount Model)
└── AuthControllerTest.php         (20 tests - Authentication)
```

### Test Database Setup

Tests use a separate database (`p_d_manager_test`). Configuration is in `.env.testing`:

```bash
# Migrations are automatically run for test database
php artisan migrate --env=testing
```

---

## 📁 Project Structure

```
P-D-Manger/
├── backend/                          # Laravel Backend (PHP 8.1+)
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── ProductController.php      (Advanced sorting, search, pagination)
│   │   │   │   ├── DiscountController.php     (Discount CRUD with validation)
│   │   │   │   └── AuthController.php         (JWT authentication)
│   │   │   ├── Middleware/
│   │   │   └── Kernel.php
│   │   ├── Models/
│   │   │   ├── Product.php          (With soft deletes & relationships)
│   │   │   ├── Discount.php         (With soft deletes & relationships)
│   │   │   └── User.php             (JWT subject implementation)
│   │   └── Providers/
│   ├── database/
│   │   ├── migrations/              (7 migrations for schema)
│   │   ├── factories/               (ProductFactory, DiscountFactory)
│   │   └── seeders/                 (Database seeders)
│   ├── routes/
│   │   └── api.php                  (Protected API routes)
│   ├── tests/
│   │   └── Unit/                    (95+ comprehensive tests)
│   ├── config/
│   │   ├── auth.php                 (JWT guard configuration)
│   │   ├── database.php
│   │   └── cors.php
│   ├── .env.example
│   ├── .env.testing
│   ├── composer.json                (Laravel 10.10, JWT 2.2, PHPUnit 10.1)
│   └── artisan
│
├── frontend/                         # Angular Frontend (Angular 19.2.0)
│   ├── src/
│   │   ├── app/
│   │   │   ├── products/            (Product management components)
│   │   │   ├── discounts/           (Discount management components)
│   │   │   ├── auth/                (Login & authentication)
│   │   │   └── services/            (API & auth services)
│   │   ├── environments/            (Configuration files)
│   │   ├── styles.css
│   │   └── main.ts
│   ├── angular.json
│   ├── tsconfig.json
│   ├── package.json                 (Angular 19.2.0, Material 19.2.19, RxJS 7.8.0)
│   └── README.md
│
└── README.md                         # This file
```

---

## ✨ Key Features

### Authentication & Security
- ✅ JWT-based authentication using Tymon/JWT-Auth (v2.2+)
- ✅ Secure token generation and refresh
- ✅ Protected API endpoints with middleware
- ✅ Bcrypt password hashing
- ✅ Token expiration management (60 minutes)

### Product Management
- ✅ Create, read, update, delete products
- ✅ Advanced search by name and description
- ✅ Sorting by multiple fields:
  - Database columns: name, price, created_at
  - Calculated fields: final_price, savings
- ✅ Pagination support (configurable per_page)
- ✅ Soft delete functionality
- ✅ Discount associations

### Discount Management
- ✅ Percentage and fixed-amount discounts
- ✅ Validation:
  - Percentage discounts cannot exceed 100%
  - All values have maximum limit of 99,999.99
- ✅ Active/inactive status management
- ✅ Many-to-many relationship with products
- ✅ Soft delete functionality

### Advanced Sorting
- ✅ Database-level sorting for standard fields
- ✅ Calculated field sorting (final_price, savings)
- ✅ Bidirectional sorting (ascending/descending)
- ✅ Smart sorting combining database and in-memory operations
- ✅ Default sorting when invalid parameters provided

### Frontend Features
- ✅ Real-time form validation
- ✅ Dynamic discount type-specific limits
- ✅ User-friendly error messages
- ✅ Material Design UI (Angular Material 19.2.19)
- ✅ Responsive design
- ✅ Toast notifications

### Backend Features
- ✅ Request validation with detailed error responses
- ✅ Business logic validation
- ✅ Consistent error formatting
- ✅ HTTP status code compliance
- ✅ CORS support for frontend communication
- ✅ Database transactions for data integrity

### Testing & Quality
- ✅ 95+ comprehensive unit tests
- ✅ 70%+ code coverage
- ✅ Factory classes for test data generation
- ✅ Separate test database configuration
- ✅ All tests passing ✓

---

## 🐛 Troubleshooting

### Backend Issues

#### 1. JWT Secret Not Set
```bash
php artisan jwt:secret
# Check .env for JWT_SECRET value
```

#### 2. Database Connection Error
```bash
# Verify MySQL is running
mysql -u root -p -e "SHOW DATABASES;"

# Update .env with correct credentials
# Then run migrations
php artisan migrate:refresh --seed
```

#### 3. Port 8000 Already in Use
```bash
php artisan serve --port=8001
```

#### 4. Permission Denied Errors (Linux/macOS)
```bash
chmod -R 775 storage bootstrap/cache
```

#### 5. Composer Dependencies Issue
```bash
composer update
composer install
```

### Frontend Issues

#### 1. npm install fails
```bash
# Clear npm cache
npm cache clean --force

# Delete node_modules and package-lock.json
rm -rf node_modules package-lock.json

# Reinstall
npm install
```

#### 2. Port 4200 Already in Use
```bash
ng serve --port 4201
```

#### 3. CORS Errors
Ensure:
- Backend is running on http://localhost:8000
- Check `config/cors.php` in Laravel
- Verify frontend API URL configuration
- Check browser console for detailed error

### Testing Issues

#### 1. Tests Not Running
```bash
# Ensure test database exists and is configured
# Check .env.testing file

# Run with environment flag
php artisan test --env=testing
```

#### 2. Database Errors in Tests
```bash
# Recreate test database
mysql -u root -p -e "DROP DATABASE IF EXISTS p_d_manager_test; CREATE DATABASE p_d_manager_test;"

# Run migrations for test environment
php artisan migrate --env=testing
```

#### 3. JWT Token Issues in Tests
```bash
# Verify JWT_SECRET is set in .env.testing
php artisan jwt:secret --env=testing
```

---

## 📚 API Documentation

### Authentication Endpoints

**Login**
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password"
}

Response: 200 OK
{
  "access_token": "eyJ0eXAi...",
  "token_type": "bearer",
  "user": { "id": 1, "email": "admin@example.com" }
}
```

**Get Authenticated User**
```http
GET /api/auth/user
Authorization: Bearer {token}

Response: 200 OK
{
  "id": 1,
  "name": "Admin",
  "email": "admin@example.com",
  ...
}
```

**Logout**
```http
POST /api/auth/logout
Authorization: Bearer {token}

Response: 200 OK
{
  "message": "User successfully signed out"
}
```

**Refresh Token**
```http
POST /api/auth/refresh
Authorization: Bearer {token}

Response: 200 OK
{
  "access_token": "eyJ0eXAi...",
  "token_type": "bearer",
  "user": { ... }
}
```

### Product Endpoints

**List Products**
```http
GET /api/products?page=1&per_page=15&sort_by=name&sort_order=asc&search=laptop
Authorization: Bearer {token}

Query Parameters:
- page: Page number (default: 1)
- per_page: Items per page (default: 15)
- sort_by: name, price, final_price, savings (default: name)
- sort_order: asc or desc (default: asc)
- search: Search term for name/description
```

**Get Single Product**
```http
GET /api/products/{id}
Authorization: Bearer {token}
```

**Create Product**
```http
POST /api/products
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Laptop Pro",
  "description": "High-end laptop",
  "price": 1500.00
}
```

**Update Product**
```http
PUT /api/products/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Name",
  "price": 1200.00
}
```

**Delete Product**
```http
DELETE /api/products/{id}
Authorization: Bearer {token}
```

### Discount Endpoints

**List Discounts**
```http
GET /api/discounts?page=1&per_page=15
Authorization: Bearer {token}
```

**Get Single Discount**
```http
GET /api/discounts/{id}
Authorization: Bearer {token}
```

**Create Discount**
```http
POST /api/discounts
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Summer Sale 20%",
  "type": "percentage",
  "value": 20,
  "is_active": true
}
```

Validation:
- Percentage type: value must be 0-100
- Fixed type: value max 99,999.99
- All types: value must be >= 0

**Update Discount**
```http
PUT /api/discounts/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Updated Title",
  "value": 25
}
```

**Delete Discount**
```http
DELETE /api/discounts/{id}
Authorization: Bearer {token}
```

---

## 📝 Important Notes

### Installation Reminder
Always run migrations with seeding after backend installation:
```bash
cd backend
php artisan migrate:refresh --seed
```

This populates your database with:
- 1 admin user (admin@example.com / password)
- Sample products
- Sample discounts

### API Authentication
All API requests (except login) require JWT token in header:
```
Authorization: Bearer {your_jwt_token}
```

### Database Backup
Before running `migrate:refresh`, backup important data:
```bash
mysqldump -u root -p p_d_manager > backup.sql
```

### Production Deployment
Before going to production:
- [ ] Set `APP_ENV=production` in .env
- [ ] Set `APP_DEBUG=false` in .env
- [ ] Configure proper database credentials
- [ ] Set up HTTPS
- [ ] Configure CORS for production domain
- [ ] Run tests: `php artisan test`
- [ ] Check security headers

---

## ✅ Quick Reference

### Common Commands

```bash
# Backend Commands
cd backend
composer install                      # Install dependencies
php artisan serve                    # Start dev server
php artisan migrate:refresh --seed   # Reset & seed database
php artisan test                     # Run all tests
php artisan test --env=testing       # Run tests explicitly
php artisan jwt:secret               # Generate JWT secret

# Frontend Commands
cd frontend
npm install                          # Install dependencies
npm start                            # Start dev server
npm run build                        # Build for production
npm test                             # Run tests
```

### Database Commands

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE p_d_manager;"

# Drop database
mysql -u root -p -e "DROP DATABASE p_d_manager;"

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Reset migrations
php artisan migrate:reset

# Fresh migrations with seed
php artisan migrate:fresh --seed
```

---

## 🎯 Versions Summary

### Required Versions
- **PHP**: 8.1 or higher ✓
- **Node.js**: 18 or higher ✓
- **MySQL**: 8.0 or higher ✓
- **Composer**: Latest ✓
- **npm**: 10.0+ ✓

### Framework Versions
- **Laravel**: 10.10+ ✓
- **Angular**: 19.2.0 ✓
- **Laravel JWT Auth**: 2.2+ ✓
- **PHPUnit**: 10.1+ ✓

---

## 📞 Support

For issues:
1. Check the Troubleshooting section above
2. Review test files for usage examples
3. Check logs: `storage/logs/laravel.log`
4. Verify all services are running
5. Check environment configuration

---

## 📄 License

This project is provided as-is for development and educational purposes.

---

**Last Updated**: January 18, 2026
**Version**: 1.0.0
**Status**: ✅ Fully Functional & Tested

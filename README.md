# Classroom Management System - Laravel Edition

## Overview

The Classroom Management System is a robust web application built with Laravel that facilitates interaction between teachers and students in an educational environment. It provides a secure platform for assignment management, submission handling, and educational challenges, while enabling effective communication between users.

## Features

### User Management
- **Role-Based Access Control**: Separate interfaces and permissions for teachers and students
- **Authentication**: Secure login and session management
- **Profile Management**: Users can update their personal information

### Assignments
- **Teacher Features**:
  - Create assignments with detailed descriptions
  - Upload assignment files (documents, archives)
  - Review student submissions
  - Filter assignments by teacher
- **Student Features**:
  - View available assignments
  - Download assignment files
  - Submit completed work
  - Update existing submissions

### Challenges
- **Teacher Features**:
  - Create programming challenges with hints
  - Define expected solutions
  - Upload challenge files
- **Student Features**:
  - View and attempt programming challenges
  - Submit solutions
  - Track solved challenges

### Communication
- Direct messaging between teachers and students
- System notifications for important events

### File Management
- Secure file upload and download functionality
- Support for various document and archive formats
- File validation and sanitization

## Technical Architecture

### Technology Stack
- **Backend**: Laravel 12.x
- **Frontend**: Bootstrap 5, Vue.js components
- **Database**: MySQL (configurable)
- **Authentication**: Laravel's built-in authentication system
- **File Storage**: Laravel's filesystem abstraction

### System Architecture
- **MVC Pattern**: Follows Laravel's Model-View-Controller architecture
- **Eloquent ORM**: Database operations through Eloquent models
- **Blade Templating**: Server-side rendering with Blade templates
- **Vue Components**: Selected interactive UI elements with Vue.js

## Database Structure

### Core Tables
- **users**: User accounts with roles (teacher/student)
- **assignments**: Assignments created by teachers
- **submissions**: Student submissions for assignments
- **challenges**: Programming challenges created by teachers
- **messages**: Direct messages between users

### System Tables
- **sessions**: User session management
- **cache**, **cache_locks**: Application cache storage
- **jobs**, **job_batches**, **failed_jobs**: Background job processing

## Security Features

- **Password Security**: Bcrypt hashing for password storage
- **CSRF Protection**: Cross-Site Request Forgery prevention on all forms
- **Input Validation**: Thorough validation of all user inputs
- **XSS Prevention**: Content sanitization to prevent cross-site scripting
- **File Security**:
  - File type validation
  - File size restrictions
  - Filename sanitization
  - Secure storage paths
- **Authorization**: Role-based access control for all features

## Installation

### Requirements
- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or PostgreSQL 9.6+
- Node.js and npm (for frontend assets)

### Installation Steps
1. Clone the repository
   ```bash
   git clone https://github.com/LoGGGG2402/Prog6.git 
   cd Prog6
   ```

2. Install PHP dependencies
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. Install JavaScript dependencies
   ```bash
   npm install
   npm run build
   ```

4. Configure environment variables
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Update the `.env` file with your database credentials
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=classroom_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. Run database migrations and seed initial data
   ```bash
   php artisan migrate --seed
   ```

7. Create storage symbolic link
   ```bash
   php artisan storage:link
   ```
   ```

8. Start the development server
   ```bash
   php artisan serve
   ```

10. Access the application at http://localhost:8000

## Default Users

After installation, you can log in with these default credentials:

### Teachers
- Username: `teacher1`, Password: `123456a@A`
- Username: `teacher2`, Password: `123456a@A`

### Students
- Username: `student1`, Password: `123456a@A`
- Username: `student2`, Password: `123456a@A`

## Directory Structure

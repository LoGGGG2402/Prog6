# Classroom Management System - Laravel Edition

This is a Laravel-based version of the Classroom Management System, migrated from the original PHP application.

## Overview

The Classroom Management System is a web application that provides an interactive platform for teachers and students. It enables teachers to create assignments, manage challenges, monitor student submissions, and communicate with students. Students can access assignments, submit their work, solve challenges, and communicate with teachers.

This system has been migrated from a traditional PHP application to Laravel, taking advantage of Laravel's modern features, security practices, and MVC architecture.

## Features

- **User Management**: Authentication, profile management, and role-based access control
- **Assignments**: Create, list, and download assignments
- **Submissions**: Submit and manage assignment submissions
- **Challenges**: Create educational challenges with hints and solutions
- **Messaging**: Direct messaging between teachers and students
- **File Management**: Secure file upload and download functionality

## Technical Details

- Built with Laravel 10.x
- Uses MySQL database
- Implements Laravel's authentication system
- MVC architecture
- Eloquent ORM for database interactions
- Blade templating for views
- Bootstrap 5 for frontend styling

## Installation

1. Clone the repository
   ```bash
   git clone https://github.com/username/classroom-management.git /var/www/html/classroom
   ```

2. Install dependencies
   ```bash
   cd /var/www/html/classroom
   composer install
   ```

3. Copy the environment file and update the settings
   ```bash
   cp .env.example .env
   ```

4. Generate application key
   ```bash
   php artisan key:generate
   ```

5. Create a MySQL database and update the .env file with your database credentials
   ```
   DB_DATABASE=classroom_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. Run the migrations and seed the database
   ```bash
   php artisan migrate --seed
   ```

7. Create symbolic link for storage
   ```bash
   php artisan storage:link
   ```

8. Set proper permissions
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

9. Start the development server
   ```bash
   php artisan serve
   ```

10. Access the application at http://localhost:8000

## Default Users

- **Teacher**: Username: `teacher1` Password: `123456a@A`
- **Student**: Username: `student1` Password: `123456a@A`

## Project Structure

The application follows Laravel's standard directory structure:

- `app/` - Contains the core code of the application
  - `Http/Controllers/` - Contains the controllers
  - `Models/` - Contains the Eloquent models
  - `Providers/` - Contains service providers
- `config/` - Contains all the application's configuration files
- `database/` - Contains migrations and seeders
- `public/` - Contains the entry point and assets
- `resources/` - Contains views, raw assets, and language files
- `routes/` - Contains route definitions
- `storage/` - Contains application storage (uploads, logs, etc.)

## Usage

### Teacher Features
- Create and manage assignments
- Create challenges for students
- View student submissions
- Communicate with students
- Manage student profiles

### Student Features
- View and download assignments
- Submit completed assignments
- Attempt to solve challenges
- Track submission history
- Communicate with teachers and other students

## Security Features

- Password hashing using Laravel's authentication system
- CSRF protection for all forms
- Input validation and sanitization
- File upload security checks
- Role-based access control
- Secure routing and middleware protection

# Programming Assignment System

## Database Structure

This application uses Laravel's standard database tables plus custom tables for the assignment management system.

### Migration Structure

The database migrations are set up as follows:

1. Laravel's default migrations create the standard tables:
   - `users`
   - `password_reset_tokens` 
   - `sessions`
   - `failed_jobs`
   - `job_batches`
   - `cache` and `cache_locks`

2. Our custom migration (`2023_01_01_000000_create_initial_tables.php`):
   - Adds additional columns to the `users` table (username, fullname, phone, role, avatar)
   - Creates our custom tables:
     - `messages`: For communication between teachers and students
     - `assignments`: For assignments created by teachers
     - `submissions`: For students' assignment submissions
     - `challenges`: For programming challenges created by teachers

### Database Diagram

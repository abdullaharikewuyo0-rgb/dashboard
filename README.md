# Student Dashboard

A PHP-based student dashboard with tools for tracking assignments, grades, courses, schedules, and personal tasks.

## Features

- **Authentication** — secure login and registration for students
- **Assignments** — view and manage assignment details and deadlines
- **Grades** — check results and grade history
- **Courses** — browse enrolled courses
- **Schedule** — view class timetable
- **Todo List** — personal task manager
- **Calculator** — built-in utility calculator
- **Clock / Counter** — small utility tools
- **Student Records** — manage student profile information

## Tech Stack

- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Server:** Apache (XAMPP)

## Setup Instructions

### Prerequisites
- XAMPP (or any PHP + MySQL + Apache stack)
- Git

### Steps

1. **Clone the repository**

2. **Move it into your web server's root folder**

For XAMPP on Windows, place the folder inside `C:\xampp\htdocs\`.

3. **Create the database**

- Start Apache and MySQL from the XAMPP Control Panel.
- Open http://localhost/phpmyadmin.
- Create a new database (e.g. `student_dashboard`).
- Import the SQL schema if one is provided.

4. **Configure database credentials**

The `db.php` file holds the MySQL connection details and is not tracked by Git. Copy `db.example.php` to `db.php` and fill in your own credentials.

5. **Run the app**

Open http://localhost/dashboard in your browser.

## Project Structure

## Author

**Abdullah Ariwkuyo**
- GitHub: [@abdullaharikewuyo0-rgb](https://github.com/abdullaharikewuyo0-rgb)

## License

This project is for educational purposes.
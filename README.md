# Student Parliament Management System

## Overview
The Student Parliament Management System is a web-based application designed to streamline the management of student parliament activities. It facilitates attendance tracking, fine calculation, announcement posting, and event management for students and administrators. The system uses QR code technology for efficient attendance scanning and provides separate dashboards for students and admins.

## Features

### User Management
- User registration and login
- Role-based access (Student and Admin)
- Department-based user organization

### Attendance Tracking
- QR code generation for each student
- Real-time attendance scanning via QR codes
- Support for multiple scan times (AM In, AM Out, PM In, PM Out)
- Event-specific attendance records

### Fine Management
- Automatic fine calculation based on missing attendance scans
- Configurable fine amounts per missing scan
- Separate views for students and admins to monitor fines

### Announcements
- Admin can post announcements
- Students can view active announcements
- Announcement status management (active/inactive)

### Institutional Events
- Create and manage institutional events
- Department-specific or general events
- Event status tracking

### Dashboards
- Admin Dashboard: Overview of attendance, fines, and system stats
- Student Dashboard: Personal attendance, fines, and announcements

### Additional Features
- Responsive design with CSS styling
- JavaScript for QR scanning and search functionality
- API endpoints for attendance and fine data
- Secure database connections using PDO

## Tech Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL 10.4+
- **Frontend**: HTML5, CSS3, JavaScript
- **Libraries**: phpqrcode for QR code generation
- **Server**: Apache (via XAMPP)

## Installation

### Prerequisites
- XAMPP (or similar Apache/MySQL/PHP stack)
- PHP 7.4 or higher
- MySQL 10.4 or higher
- Web browser

### Steps
1. **Clone or Download the Project**:
   - Place the project files in `c:/xampp/htdocs/student_parliament`

2. **Database Setup**:
   - Start XAMPP and ensure Apache and MySQL are running
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database named `student_parliament_db`
   - Import the `student_parliament_db.sql` file to set up tables and initial data

3. **Configuration**:
   - Update database credentials in `dbconnection.php` if necessary (default: host=127.0.0.1, user=root, pass='', db=student_parliament_db)

4. **Run the Application**:
   - Access the application at `http://localhost/student_parliament`
   - Default admin credentials: Check the database dump or create via signup

## Usage

### For Students
- **Login/Signup**: Register or log in with your credentials
- **Dashboard**: View personal attendance, fines, and announcements
- **Attendance**: Scan QR code at events for attendance
- **Fines**: Check outstanding fines and payment status
- **Announcements**: Read latest announcements from admins

### For Admins
- **Login**: Use admin credentials
- **Dashboard**: Monitor overall system stats
- **Manage Users**: View and manage student accounts
- **Attendance**: Scan QR codes for attendance, view reports
- **Fines**: Calculate and view fines for students
- **Announcements**: Post and manage announcements
- **Events**: Create and manage institutional events

### API Endpoints
- `/api/attendance.php`: Handle attendance scans
- `/api/get_attendance_today.php`: Fetch today's attendance
- `/api/student_fines_admin.php`: Admin fine management

## Project Structure
```
student_parliament/
├── api/                    # API endpoints
├── css/                    # Stylesheets
├── images/                 # Static images
├── js/                     # JavaScript files
├── phpqrcode/              # QR code library
├── qrcodes/                # Generated QR codes
├── *.php                   # Main PHP files
├── dbconnection.php        # Database connection
├── student_parliament_db.sql # Database schema
└── README.md               # This file
```

## Contributing
Contributions are welcome! Please fork the repository and submit a pull request for any improvements or bug fixes.

## License
This project is licensed under the MIT License - see the LICENSE file for details.

## Support
For issues or questions, please contact the development team or create an issue in the repository.

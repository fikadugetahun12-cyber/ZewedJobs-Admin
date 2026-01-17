# ZewedJobs-Admin
# ZewedJobs Admin Panel

A complete admin panel for managing job listings, companies, and users for the ZewedJobs platform.

## Features

- **Dashboard**: Overview with statistics and charts
- **Job Management**: CRUD operations for job listings
- **Company Management**: Manage registered companies
- **User Management**: Admin and user account management
- **Responsive Design**: Works on all devices
- **JSON Database**: No SQL required, uses JSON files for storage
- **Secure Authentication**: Password hashing and session management

## Installation

1. **Clone or download** the project files to your server
2. **Set up web server** (Apache/Nginx) to point to the project root
3. **Configure permissions**:
   ```bash
   chmod 755 /path/to/zewedjobs-admin/
   chmod 777 /path/to/zewedjobs-admin/database/
   chmod 777 /path/to/zewedjobs-admin/logs/

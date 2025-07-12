# 🚀 AXIOM - Full-Stack Web Platform

A comprehensive digital platform built with PHP and MySQL that delivers a modern web experience with robust backend functionality. AXIOM represents the future of web applications, showcasing Sri Lankan tech innovation.

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Usage](#usage)
- [Database Schema](#database-schema)
- [Security Features](#security-features)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### User Management
- **Complete Registration & Login System** - Secure user authentication with session management
- **Dynamic Profile Management** - Users can update profiles with photo upload functionality
- **Secure Password Management** - Encrypted password storage and recovery

### Core Functionality
- **Real-time Activity Dashboard** - Track user activities, projects, and points system
- **Interactive FAQ System** - Expandable content sections for better user experience
- **Professional Team Showcase** - Dynamic team member profiles and information
- **Contact Form Integration** - Backend processing with database storage
- **User Activity Logging** - Comprehensive tracking of user interactions

### Design & UX
- **Responsive Mobile-First Design** - Optimized for all devices
- **Modern UI/UX** - Clean, intuitive interface
- **Dynamic Content Generation** - Server-side rendering for optimal performance

## 🛠️ Tech Stack

### Backend
- **PHP** - Server-side development and business logic
- **MySQL** - Relational database management
- **Session Management** - Secure user authentication

### Frontend
- **HTML5** - Modern markup structure
- **CSS3** - Styling and responsive design
- **JavaScript** - Interactive user interface elements

### Security
- **Encrypted Authentication** - Secure login system
- **Form Validation** - Server-side and client-side validation
- **SQL Injection Protection** - Prepared statements and sanitization

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependencies)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/axiom.git
   cd axiom
   ```

2. **Database Configuration**
   ```bash
   # Create database
   mysql -u root -p
   CREATE DATABASE axiom_db;
   
   # Import database schema
   mysql -u root -p axiom_db < database/schema.sql
   ```

3. **Configure Environment**
   ```php
   // config/database.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'axiom_db');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

4. **Set Permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 config/
   ```

5. **Start Development Server**
   ```bash
   php -S localhost:8000
   ```

## 📊 Database Schema

### Key Tables

#### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### User Activities
```sql
CREATE TABLE user_activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    activity_type VARCHAR(50),
    points INT DEFAULT 0,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

#### Contact Submissions
```sql
CREATE TABLE contact_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🔐 Security Features

- **Password Encryption** - BCrypt hashing for secure password storage
- **Session Management** - Secure session handling with timeout
- **SQL Injection Prevention** - Prepared statements and input sanitization
- **XSS Protection** - Output escaping and input validation
- **CSRF Protection** - Token-based form protection
- **File Upload Security** - Type validation and secure file handling

## 🎯 Usage

### User Registration
```php
// Register new user
POST /register.php
{
    "username": "john_doe",
    "email": "john@example.com",
    "password": "secure_password"
}
```

### User Authentication
```php
// Login user
POST /login.php
{
    "username": "john_doe",
    "password": "secure_password"
}
```

### Profile Management
```php
// Update profile with image
POST /profile/update.php
{
    "profile_data": "...",
    "profile_image": "file_upload"
}
```

## 📱 Key Components

### Authentication System
- Secure login/logout functionality
- Session management and validation
- Password reset capabilities

### Dashboard Features
- Real-time activity tracking
- Points system implementation
- Project management tools

### Content Management
- Dynamic FAQ system
- Team member profiles
- Contact form processing

## 🌟 Highlights

- **Scalable Architecture** - Modular PHP structure for easy maintenance
- **Database Optimization** - Efficient MySQL queries and indexing
- **Mobile Responsive** - Works seamlessly across all devices
- **Security First** - Comprehensive security measures implemented
- **User-Friendly** - Intuitive interface with smooth user experience

## 📈 Future Enhancements

- [ ] REST API implementation
- [ ] Real-time notifications
- [ ] Advanced admin panel
- [ ] Multi-language support
- [ ] Integration with external APIs

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Sonal Shaminda Fernando**
- Portfolio: [sonal-shaminda.netlify.app](https://sonal-shaminda.netlify.app/)
- LinkedIn: [Sonal Fernando](https://www.linkedin.com/in/sonal-fernando1/)
- Email: sonalshaminda01@gmail.com

## 🙏 Acknowledgments

- Sri Lankan tech community for inspiration
- Open source contributors
- PHP and MySQL documentation teams

---

**⭐ If you found this project helpful, please consider giving it a star!**

*Built with ❤️ in Sri Lanka*

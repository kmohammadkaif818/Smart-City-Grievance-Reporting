CREATE DATABASE IF NOT EXISTS smart_city_grievance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smart_city_grievance;

CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NULL,
    category VARCHAR(40) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    photo VARCHAR(255) NULL,
    status ENUM('Submitted','Assigned','In Progress','Resolved','Rejected') NOT NULL DEFAULT 'Submitted',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

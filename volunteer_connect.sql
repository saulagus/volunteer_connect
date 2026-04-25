CREATE DATABASE IF NOT EXISTS volunteerConnect;

USE volunteerConnect;

-- schema

-- users: stores all accounts regardless of role
CREATE TABLE IF NOT EXISTS users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    -- decides permissions and access
    role ENUM('admin', 'organiser', 'attendee')
     NOT NULL DEFAULT 'attendee',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories: event category
CREATE TABLE IF NOT EXISTS categories (
    categoryId INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- events: volunteer events created by organisers
CREATE TABLE IF NOT EXISTS events (
    eventId INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    location VARCHAR(200),
    eventDate DATETIME NOT NULL,
    capacity INT UNSIGNED NOT NULL,
    organiserId INT NOT NULL,
    categoryId INT,
    status ENUM('draft', 'published', 'full', 'inProgress', 'completed', 'cancelled') DEFAULT 'draft',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (organiserId) REFERENCES users(userId),
    FOREIGN KEY (categoryId) REFERENCES categories(categoryId)
) ;

-- bookings: tracks which attendee signed up for which event
CREATE TABLE IF NOT EXISTS bookings (
    bookingId INT AUTO_INCREMENT PRIMARY KEY,
    userId   INT NOT NULL,
    eventId  INT NOT NULL,
    bookingDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('booked', 'cancelled', 'attended') DEFAULT 'booked',
    UNIQUE KEY uniqueBooking (userId, eventId),
    FOREIGN KEY (userId)  REFERENCES users(userId),
    FOREIGN KEY (eventId) REFERENCES events(eventId) ON DELETE RESTRICT
);

-- Insert default data
INSERT IGNORE INTO categories (name, description) VALUES 
('Environmental', 'Events focused on nature and conservation.'),
('Education', 'Tutoring and school-related support.'),
('Animal Welfare', 'Helping at shelters and wildlife.'),
('Community Outreach', 'Food drives and local support.'),
('Health & Wellness', 'Hospitals and awareness.'),
('Disaster Relief', 'Emergency response.'),
('Other', 'Miscellaneous events.');
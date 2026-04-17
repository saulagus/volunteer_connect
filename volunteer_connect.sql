CREATE DATABASE IF NOT EXISTS volunteer_connect;

USE volunteer_connect;

-- schema

-- users: stores all accounts regardless of role
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
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
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- events: volunteer events created by organisers
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    location VARCHAR(200),
    eventDate DATETIME NOT NULL,
    capacity INT UNSIGNED NOT NULL,
    organiser_id INT NOT NULL,
    category_id INT,
    status ENUM('active', 'cancelled') DEFAULT 'active',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (organiser_id) REFERENCES users(user_id),
    FOREIGN KEY (category_id) REFERENCES categories(category_id)
) ;

-- bookings: tracks which attendee signed up for which event
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id   INT NOT NULL,
    event_id  INT NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('booked', 'cancelled', 'attended') DEFAULT 'booked',
    UNIQUE KEY uniqueBooking (user_id, event_id),
    FOREIGN KEY (user_id)  REFERENCES users(user_id),
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
);

-- TODO: Add seed
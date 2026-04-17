CREATE DATABASE IF NOT EXISTS volunteer_connect;

USE volunteer_connect;

-- schema

-- users: stores all accounts regardless of role
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    -- decides permissions and access 
    role ENUM('admin', 'organiser', 'attendee')
     NOT NULL DEFAULT 'attendee',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- events: volunteer events created by organisers
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    location VARCHAR(200),
    eventDate DATETIME NOT NULL,
    capacity INT UNSIGNED NOT NULL,
    organiserId INT NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organiserId) REFERENCES users(id) ON DELETE CASCADE
) ;

-- bookings: tracks which attendee signed up for which event
CREATE TABLE IF NOT EXISTS bookings (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    userId   INT NOT NULL,
    eventId  INT NOT NULL,
    bookedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniqueBooking (userId, eventId),
    FOREIGN KEY (userId)  REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (eventId) REFERENCES events(id) ON DELETE CASCADE
);

-- TODO: Add seed
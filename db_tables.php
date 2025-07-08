<?php
include 'db_connect.php';

$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

function createUsersTable($conn) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
}

function createDepartmentsTable($conn) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS departments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_by INT NOT NULL
        )
    ");
}

function createRolesTable($conn) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS roles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_by INT NOT NULL
        )
    ");
}

function createCountriesTable($conn) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS countries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL
        )
    ");
}

function createCitiesTable($conn) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS cities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            country_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE
        )
    ");
}

function createEmployeesTable($conn) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS employees (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            department_id INT,
            role_id INT,
            country_id INT,
            city_id INT,
            email VARCHAR(100),
            phone VARCHAR(20),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_by INT,
            FOREIGN KEY (department_id) REFERENCES departments(id),
            FOREIGN KEY (role_id) REFERENCES roles(id),
            FOREIGN KEY (country_id) REFERENCES countries(id),
            FOREIGN KEY (city_id) REFERENCES cities(id)
        )
    ");
}

function insertSampleCountries($conn) {
    $conn->query("
        INSERT INTO countries (name)
        SELECT * FROM (SELECT 'India') AS tmp
        WHERE NOT EXISTS (SELECT name FROM countries WHERE name = 'India')
        UNION
        SELECT * FROM (SELECT 'USA') AS tmp
        WHERE NOT EXISTS (SELECT name FROM countries WHERE name = 'USA')
        UNION
        SELECT * FROM (SELECT 'Canada') AS tmp
        WHERE NOT EXISTS (SELECT name FROM countries WHERE name = 'Canada')
    ");
}

function insertSampleCities($conn) {
    $existing = $conn->query("SELECT COUNT(*) as total FROM cities");
    $row = $existing->fetch_assoc();
    if ($row['total'] == 0) {
        $conn->query("
            INSERT INTO cities (country_id, name) VALUES 
            (1, 'Delhi'), (1, 'Mumbai'), (1, 'Bangalore'),
            (2, 'New York'), (2, 'Los Angeles'), (2, 'Chicago'),
            (3, 'Toronto'), (3, 'Vancouver'), (3, 'Montreal')
        ");
    }
}

createUsersTable($conn);
createDepartmentsTable($conn);
createRolesTable($conn);
createCountriesTable($conn);
createCitiesTable($conn);
createEmployeesTable($conn);
insertSampleCountries($conn);
insertSampleCities($conn);

echo "✅ Database and tables created successfully.";

$conn->close();
?>

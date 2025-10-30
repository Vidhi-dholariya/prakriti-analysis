<?php
require_once 'db.php';
// Users table
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    gender TEXT,
    dob TEXT,
    phone TEXT
);");
// Health info per user
$db->exec("CREATE TABLE IF NOT EXISTS user_health (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    height TEXT,
    weight TEXT,
    blood_group TEXT,
    allergies TEXT,
    diseases TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);");
// Prakriti results
$db->exec("CREATE TABLE IF NOT EXISTS prakriti_results (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    answers TEXT NOT NULL,
    dominant_dosha TEXT NOT NULL,
    reflection TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id)
);");
echo 'DB Initialized.';

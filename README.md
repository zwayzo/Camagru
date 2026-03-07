# Camagru

Camagru is a small web application that allows users to take pictures with their webcam, apply image overlays (stickers), and share them with other users.
The project focuses on building a complete web application using **PHP**, **MySQL**, **Apache**, and **Docker**.

It is part of the **42 Network curriculum** and aims to introduce web development fundamentals, database management, and containerization.

---

## Features

* User registration and authentication
* Email verification
* Webcam image capture
* Image upload from local files
* Image overlays (stickers)
* Public image gallery
* Like and comment system
* Email notifications for comments
* Secure user authentication
* Image storage on the server

---

## Technologies Used

* **PHP 8**
* **MySQL / MariaDB**
* **Apache**
* **Docker & Docker Compose**
* **JavaScript**
* **HTML / CSS**

---

## Project Structure

```
Camagru/
│
├── config/                 # Configuration files
│   └── setup.php           # Database setup script
│
├── public/                 # Public web root
│   ├── assets/
│       └── img/            # Stored images
│       └── css/  
│       └── js/  
│       └── stickers/       # List of used stickers
│
├── Dockerfile              # Docker related files
│   
│
├── src/                    # SRC files of projects
│  
│
├── docker-entrypoint.sh    # Container startup script
├── docker-compose.yml
└── README.md
```

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/zwayzo/camagru.git
cd camagru
```

### 2. Build and start the containers

```bash
docker compose up --build
```

### 3. Access the application

Open your browser:

```
http://localhost:8081
```

phpMyAdmin:

```
http://localhost:8082
```

---

## Docker Services

The project uses multiple containers:

| Service    | Description                   |
| ---------- | ----------------------------- |
| web        | PHP + Apache server           |
| db         | MySQL database                |
| phpmyadmin | Database management interface |

---

## Database Initialization

When the container starts:

1. The application waits for the database.
2. `config/setup.php` runs automatically.
3. The database and tables are created if they do not exist.

This ensures the application works **without manual database setup**.

---

## Security

The application implements several security practices:

* Password hashing
* Prepared SQL statements
* Input validation
* Session protection
* Email verification
* File upload validation

---

## Image Storage

Captured images are stored in:

```
public/assets/img/
```

Proper permissions are set automatically during container startup.

---

## How It Works

1. Users register an account.
2. They verify their email.
3. They can capture a photo or upload one.
4. Stickers can be added to images.
5. Images appear in the public gallery.
6. Other users can like or comment on images.

---

## Development

To stop containers:

```bash
docker compose down
```

To rebuild containers:

```bash
docker compose build --no-cache
```

---

## Author

Project developed as part of the **42 School curriculum**.

---

# 📸 KUET Photography Society — Club Portfolio Website

A full-stack club portfolio website built for the **KUET Photography Society (KUETS)** as part of a Web Programming lab project. The site showcases the club's gallery, events, team members, and allows visitors to contact the club or register for events — all managed through a dynamic admin panel.

---

## 🌐 Live Features

- **Hero Section** — Club intro with animated stats (members, events, photos)
- **About Section** — Club overview with feature highlights
- **Photo Gallery** — Dynamic gallery loaded from database, with category filters and a photo review/rating system
- **Events** — Upcoming events with seat tracking and registration system (KUET roll number validated)
- **Team** — Team members display loaded from database
- **Contact Form** — Visitor messages stored in the database
- **Admin Panel** — Secured admin dashboard to manage photos, events, team, and messages
- **Responsive Design** — Mobile-friendly layout with hamburger navigation

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP (procedural + PDO) |
| Database | MySQL |
| Frontend | HTML5, CSS3 (custom dark theme), vanilla JavaScript |
| Fonts | Google Fonts — Cormorant Garamond, DM Sans |
| Server | XAMPP (Apache + MySQL) |

---

## 📁 Project Structure

```
Project_Web_ClubWebsite/
├── index.php           # Main public-facing page (hero, gallery, events, team, contact)
├── gallery.php         # Gallery page
├── applications.html   # Club membership application form
├── admin.php           # Admin dashboard (protected)
├── adminlogin.php      # Admin login page
├── adminlogout.php     # Admin logout handler
├── db.php              # Database connection (PDO)
├── images/             # Uploaded/stored images
└── Mainlogo.png        # Club logo
```

---

## 🗄️ Database Tables

| Table | Purpose |
|---|---|
| `photos` | Gallery photos with categories |
| `photo_reviews` | Visitor ratings & comments on photos |
| `events` | Upcoming events with seat counts |
| `event_registrations` | Tracks who registered for each event |
| `team` | Team members with roles and sort order |
| `messages` | Contact form submissions + event registration notifications |

---

## ⚙️ Setup Instructions

### Prerequisites
- XAMPP (or any Apache + PHP + MySQL stack)
- PHP 7.4 or higher

### Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/imtiaz2207015/Project_Web_ClubWebsite.git
   ```

2. **Move to your web server directory**
   ```
   C:/xampp/htdocs/Project_Web_ClubWebsite/
   ```

3. **Create the database**
   - Open **phpMyAdmin** → create a database (e.g., `kuet_club`)
   - Import the SQL schema (create tables: `photos`, `photo_reviews`, `events`, `event_registrations`, `team`, `messages`)

4. **Configure the database connection**
   Edit `db.php`:
   ```php
   $host = 'localhost';
   $db   = 'kuet_club';
   $user = 'root';
   $pass = '';
   $pdo  = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
   ```

5. **Run the project**
   - Start Apache and MySQL in XAMPP
   - Visit: `http://localhost/Project_Web_ClubWebsite/`

---

## 🔐 Admin Panel

- URL: `http://localhost/Project_Web_ClubWebsite/adminlogin.php`
- Log in with admin credentials to manage gallery photos, events, team members, and view submitted messages/registrations.

---

## ✅ Key Functionalities

- **Event Registration** — Validates KUET student roll numbers (7 digits), checks for duplicate registrations, and decrements available seats in real time
- **Photo Reviews** — Visitors can rate and comment on gallery photos (1–5 stars)
- **Contact Form** — Input-validated, stored in DB, visible in admin panel
- **Session-based Admin Auth** — Admin routes protected with PHP sessions
- **Responsive Navigation** — Hamburger menu for mobile screens

---

## 🎨 Design

- **Theme:** Dark purple aesthetic (`#07050f` background, `#7c3aed` purple accents, `#e879f9` fuchsia highlights)
- **Typography:** Cormorant Garamond (display) + DM Sans (body)
- **Layout:** CSS Grid and Flexbox throughout
- **Animations:** Fade-in/fade-up keyframes on hero section

---

## 👤 Author

**Tahani** — Student ID: 2207015
Khulna University of Engineering & Technology (KUET)
Web Programming Lab Project

---

## 📄 License

This project is for academic purposes only.

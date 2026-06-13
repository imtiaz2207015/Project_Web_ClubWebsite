This is a club portfolio website I built for the KUET Photography Society as a Web Programming lab project.


The site has a public-facing side and an admin panel. Visitors can browse the photo gallery, check upcoming events and register for them, see the team, and send a message through a contact form. The admin can log in to manage all of that — adding photos, creating events, updating team members, and reading submitted messages.


Built with PHP (procedural style with PDO) on the backend, MySQL for the database, and plain HTML, CSS, and JavaScript on the frontend. Runs on XAMPP locally.


The main page is index.php, which pulls together the hero section, gallery, events, team, and contact form. admin.php is the dashboard, protected by session-based login. db.php holds the database connection, and uploaded images go into the images/ folder.

Database : Six tables: photos, photo_reviews, events, event_registrations, team, and messages. Pretty self-explanatory from the names.

Clone the repo and drop it into htdocs
Create a database in phpMyAdmin (e.g. kuet_club) and import the schema
Update db.php with your database name and credentials
Start Apache and MySQL in XAMPP, then open http://localhost/Project_Web_ClubWebsite/

Admin login is at /adminlogin.php.
A few things worth noting
Event registration validates KUET roll numbers (must be 7 digits), prevents duplicate sign-ups, and updates available seats live. The photo gallery has a 1–5 star rating and comment system. The whole admin area is session-protected.
Design
Dark theme — near-black background with purple and fuchsia accents. Fonts are Cormorant Garamond for headings and DM Sans for body text. Layout uses CSS Grid and Flexbox, and it's mobile-responsive with a hamburger menu.

Tahani · Student ID: 2207015 · KUET · Web Programming Lab

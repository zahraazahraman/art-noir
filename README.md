# Art Noir

Art Noir — an elegant online gallery experience combining famous artworks and community submissions in a clean, museum-like interface.

Project vision

Art Noir displays classic masterpieces (Van Gogh, Monet, Frida Kahlo, etc.) alongside community art. Registered users can submit art and vote; advanced artists may promote their works via a premium plan. The experience prioritizes a classical, ethereal aesthetic using a monochrome palette and elegant typography. (Source: Project brief). fileciteturn0file0

Core features

Main Gallery (public): curated famous artworks + featured advanced community art.

User registration & authentication (login/signup single-page with animated form switch).

User Dashboard: manage submissions, view votes, profile management.

Voting system: community voting with weighted points by artist level (Beginner=1, Intermediate=2, Advanced=3).

Promotion pipeline: artworks move through tiers (Beginner → Intermediate → Advanced → Main Gallery) based on votes and thresholds.

Admin Dashboard: manage users, artworks, plans, and messages.

Contact page for visitors and users.

Target users

Casual visitors (browse main gallery)

Community artists (submit & engage)

Curators & admin (manage site)

Tech stack

Frontend: HTML, CSS, JavaScript, jQuery, Bootstrap

Backend: PHP

Database: MySQL

Optional: RESTful API endpoints for frontend/backend separation

Design

Color palette: black, white, grey with subtle shading

Font suggestions: Playfair Display, Cinzel, Cormorant Garamond

Grid-style artwork display, smooth transitions, minimal footer aligned left

Database (planned structure)

users (id, name, email, password_hash, role, plan, level, created_at)

artworks (id, title, image_path, artist_id, status, votes, created_at)

votes (id, voter_id, artwork_id, points, created_at)

plans (id, name, monthly_limit, price)

messages (id, sender_id, content, timestamp)

Project structure (suggested)
/ (root)
  /public
    index.html
    gallery.html
    login.html
    contact.html
  /src
    /css
    /js
    /php
  /assets
    /images
    /fonts
  /docs
    README.md
    WBS.md
    CHANGELOG.md
Setup (local dev)

Install PHP, MySQL, and a local server (XAMPP, MAMP, or LAMP).

Clone repository.

Import the provided SQL schema to create the database art_noir.

Configure config.php with DB credentials.

Place artwork images in /assets/images and ensure proper read permissions.

Run the app at http://localhost/art-noir.

Contributing

We follow Agile with small sprints and PR-based workflow. Please:

Fork the repo

Create features on topic branches (feat/*) or fixes (fix/*)

Open a PR with description and link to related WBS task

Maintain semantic commits

License

Add a LICENSE file (recommended MIT for open collaboration) or specify a different license.

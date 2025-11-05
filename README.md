# Art Noir

**Art Noir** — an elegant online gallery experience combining famous artworks and community submissions in a clean, museum-like interface.

---

## Project Vision

Art Noir displays **classic masterpieces** (Van Gogh, Monet, Frida Kahlo, etc.) alongside **community art**.  
Registered users can **submit** artworks and **vote**; advanced artists may **promote their works** via a premium plan.  

The experience prioritizes a **classical, ethereal aesthetic** using a **monochrome palette** and **elegant typography**.  
*(Source: Project brief)*

---

## Core Features

- **Main Gallery (public):** Curated famous artworks + featured advanced community art  
- **User Authentication:** Login/signup single-page with animated form switch  
- **User Dashboard:** Manage submissions, view votes, profile management  
- **Voting System:** Community voting with weighted points by artist level  
  - Beginner = 1  
  - Intermediate = 2  
  - Advanced = 3  
- **Promotion Pipeline:** Artworks move through tiers *(Beginner → Intermediate → Advanced → Main Gallery)* based on votes  
- **Admin Dashboard:** Manage users, artworks, plans, and messages  
- **Contact Page:** For visitors and users to reach the team

---

## Target Users

| Type | Description |
|------|--------------|
| **Casual visitors** | Browse the main gallery |
| **Community artists** | Submit and engage with art |
| **Curators & admins** | Manage users, artworks, and site operations |

---

## Tech Stack

**Frontend:**  
HTML • CSS • JavaScript • jQuery • Bootstrap  

**Backend:**  
PHP  

**Database:**  
MySQL  

**Optional:**  
RESTful API endpoints for frontend/backend separation

---

## Design Guidelines

- **Color Palette:** Black, white, grey with subtle shading  
- **Font Suggestions:** Playfair Display, Cinzel, Cormorant Garamond  
- **Layout:** Grid-style artwork display, smooth transitions, minimal left-aligned footer  

---

## Database (Planned Structure)

| Table | Fields |
|--------|---------|
| **users** | id, name, email, password_hash, role, plan, level, created_at |
| **artworks** | id, title, image_path, artist_id, status, votes, created_at |
| **votes** | id, voter_id, artwork_id, points, created_at |
| **plans** | id, name, monthly_limit, price |
| **messages** | id, sender_id, content, timestamp |

---

## Project Structure (Suggested)

/ (root)
├── /public
│ ├── index.html
│ ├── gallery.html
│ ├── login.html
│ └── contact.html
├── /src
│ ├── /css
│ ├── /js
│ └── /php
├── /assets
│ ├── /images
│ └── /fonts
├── /docs
│ ├── README.md
│ ├── WBS.md
│ └── CHANGELOG.md


---

## Setup (Local Development)

1. Install **PHP**, **MySQL**, and a local server (e.g., XAMPP, MAMP, or LAMP)  
2. Clone the repository  
3. Import the provided SQL schema to create the database `art_noir`  
4. Configure `config.php` with your database credentials  
5. Place artwork images in `/assets/images` and ensure read permissions  
6. Run the app at: http://localhost/art-noir


---

## Contributing

We follow **Agile** with **short sprints** and a **PR-based workflow**.  

Please:
- Fork the repository  
- Create feature branches (`feat/*`) or fix branches (`fix/*`)  
- Open a Pull Request with:
- A clear description  
- A link to the related WBS task  
- Follow **semantic commit** conventions  

---

© 2025 *Art Noir Project Team*  
Crafted with love for art and community.

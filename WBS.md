# Work Breakdown Structure — Art Noir

High-level breakdown aligned to Agile sprints.  
Each major item is broken into **epics → user stories → tasks**.

---

## Epic 0 — Project Setup & Documentation

### Story 0.1: Create documentation
- [x] **Task 0.1.1:** `README.md`
- [x] **Task 0.1.2:** `WBS.md`
- [x] **Task 0.1.3:** `CHANGELOG.md`

### Story 0.2: Repository & CI
- [ ] **Task 0.2.1:** Initialize git repo, `.gitignore`
- [ ] **Task 0.2.2:** Setup basic CI (linting, PHP checks)

---

## Epic 1 — Database & Backend Foundations

### Story 1.1: Database schema design
- [ ] **Task 1.1.1:** Define ERD and SQL schema  
- [ ] **Task 1.1.2:** Implement migrations / SQL scripts

### Story 1.2: Authentication & Authorization
- [ ] **Task 1.2.1:** Signup & Login endpoints (with secure password hashing)  
- [ ] **Task 1.2.2:** Role-based access (user/admin)

### Story 1.3: Artworks CRUD
- [ ] **Task 1.3.1:** Upload handling & storage  
- [ ] **Task 1.3.2:** Create / Read / Update / Delete endpoints

### Story 1.4: Voting system
- [ ] **Task 1.4.1:** Vote recording and points calculation  
- [ ] **Task 1.4.2:** Promotion pipeline implementation (tier thresholds)

---

## Epic 2 — Frontend: Core Pages

### Story 2.1: Main Gallery Page
- [ ] **Task 2.1.1:** Responsive grid layout  
- [ ] **Task 2.1.2:** Artwork cards with metadata  
- [ ] **Task 2.1.3:** Pagination / lazy loading

### Story 2.2: Login / Signup Single Page
- [ ] **Task 2.2.1:** Animated overlay form switch  
- [ ] **Task 2.2.2:** Client-side validation

### Story 2.3: User Dashboard
- [ ] **Task 2.3.1:** My artworks list  
- [ ] **Task 2.3.2:** Vote summary & profile editing

### Story 2.4: Voting Page
- [ ] **Task 2.4.1:** Show voting-eligible artworks  
- [ ] **Task 2.4.2:** Voting actions & rate-limits

### Story 2.5: Contact Page
- [ ] **Task 2.5.1:** Contact form and email sending

---

## Epic 3 — Admin & Monetization

### Story 3.1: Admin Dashboard
- [ ] **Task 3.1.1:** User management  
- [ ] **Task 3.1.2:** Artwork moderation  
- [ ] **Task 3.1.3:** Plan management (premium features)

### Story 3.2: Payment & Plans (optional)
- [ ] **Task 3.2.1:** Integrate payment gateway  
- [ ] **Task 3.2.2:** Plan enforcement & billing logic

---

## Epic 4 — Styling, Animations & Themes

- [ ] **Story 4.1:** Base theme implementation *(Gloomy/Vintage variants planned)*  
- [ ] **Story 4.2:** Smooth transitions & micro-interactions  
- [ ] **Story 4.3:** Accessibility review & improvements

---

## Epic 5 — Testing, Security & Deployment

- [ ] **Story 5.1:** Unit & integration tests  
- [ ] **Story 5.2:** Security hardening *(input sanitization, CSRF, rate-limiting)*  
- [ ] **Story 5.3:** Deployment pipeline *(staging → production)*

---

## Suggested Sprint Plan (2-Week Sprints)

| Sprint | Focus |
|:-------|:------|
| **0** | Docs, repo, DB design |
| **1** | Auth + Main Gallery (backend + basic frontend) |
| **2** | Uploads, User Dashboard, Voting backend |
| **3** | Voting frontend, promotion logic, admin basics |
| **4** | Styling, payments, QA |
| **5** | Final polish & launch |

---

## Estimates (Rough)

- **Initial MVP (Sprints 0–2):** ~6–8 weeks  
- **Full Feature Set (to payment + admin):** ~12–16 weeks

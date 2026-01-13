# Secure Vote Ph - Project Documentation

![Z Developers Logo](https://raw.githubusercontent.com/itsZekiee/Secure-Vote-Ph/main/public/images/logo.png)

**Transforming Ideas into Digital Success**

---

## 📄 Project Information

*   **Project Name:** Secure Vote Ph
*   **Repository URL:** [https://github.com/itsZekiee/Secure-Vote-Ph-Capstone-Project.git](https://github.com/itsZekiee/Secure-Vote-Ph-Capstone-Project.git)
*   **Version:** Scaffolding backend and frontend structured
*   **Authors:**
    *   **Zekiee** (Lead Developer)
    *   **Raymond Nino Ong** (Co-Developer)

---

## 📖 Overview

**SecureVote PH** is an electronic voting system designed as a capstone project to provide a modern, secure, and verifiable platform for conducting small to medium-scale elections, such as student council elections, association board member polls, or internal organizational votes within the Philippines. The primary goal is to address the inefficiencies, security vulnerabilities, and lack of transparency often associated with manual voting processes.

---

## 🎯 Purpose and Goals

*   **Security:** Implement strong authentication for voters and administrators. Ensure all vote data is encrypted in transit and at rest. Prevent duplicate voting and unauthorized access.
*   **Transparency:** Provide a verifiable, auditable trail of election results *after* the polling period closes.
*   **Usability:** Offer an intuitive and accessible interface for voters (especially mobile users) and a comprehensive dashboard for administrators.
*   **Efficiency:** Automate the tallying process to provide instant, accurate results upon the closure of the election.

---

## 📋 Requirements and Specifications

*   **Voter Authentication:** The system must authenticate voters using unique, pre-registered credentials (e.g., a unique ID and password/PIN or Google OAuth).
*   **Ballot Casting:** Voters must be able to view the slate of candidates and cast one vote for each designated position.
*   **Vote Secrecy:** The system must encrypt and anonymize votes immediately after submission to prevent linking the vote back to the voter ID.
*   **Election Management:** Administrators must be able to create, configure, start, and end multiple elections.
*   **Result Tallying:** The system must automatically and accurately tally votes and display results only after the election officially ends.
*   **Audit Trail:** The system must record a non-tamperable log (using hashing) for every cast ballot to ensure result integrity.

---

## 🏗️ Architecture and Technology Stack

### System Architecture
*   **Type:** Client-Server (Monolithic Laravel backend, Blade frontend)
*   **Layers:**
    *   **Presentation:** Blade templates, Tailwind CSS, Alpine.js
    *   **Application:** Laravel controllers/services
    *   **Data:** Eloquent ORM, SQLite (dev), MySQL/PostgreSQL (prod)

### Technology Stack
*   **PHP 8+ (Laravel Framework):** Chosen for its robust structure, comprehensive built-in security features (CSRF, injection prevention), and rapid API development capabilities.
*   **Blade & Alpine.js:** 
    *   **Blade:** Laravel's powerful templating engine for server-side rendering.
    *   **Alpine.js:** Part of the TALL stack approach, used for adding reactive, component-like behavior with minimal JavaScript overhead.
*   **Tailwind CSS & Remixicon:**
    *   **Tailwind CSS:** Utility-first framework for rapid, responsive design.
    *   **Remixicon:** Open-source icon library used for consistent, high-quality UI elements.
*   **Database:**
    *   **MySQL (Production):** High-performance, reliable relational database for handling sensitive, high-integrity data.
    *   **SQLite (Development):** Used for lightweight local testing and setup.
*   **Authentication:**
    *   **Laravel Auth:** Default secure session-based authentication for core voter/admin access.
    *   **OAuth (Google):** Optional Google Sign-In integration for simplified voter onboarding.
*   **Package Management:**
    *   **Composer (PHP):** Dependency manager for PHP/Laravel packages.
    *   **npm (JS/CSS):** Dependency manager for front-end assets.
    *   **Vite:** Used for compiling front-end assets (Tailwind, Alpine.js).
*   **Testing:**
    *   **PHPUnit:** Laravel's default testing framework for comprehensive unit and feature testing of the core application logic and API endpoints.

---

## 🛠️ Security Implementation

*   **Bcrypt & SHA-256:**
    *   **Bcrypt:** Used by Laravel for secure password hashing.
    *   **SHA-256:** Used for generating tamper-proof vote hashes and audit trails.
*   **Communication & Storage:**
    *   **Laravel Mail:** Used for sending notifications (e.g., initial voter credentials).
    *   **File Storage:** Used for storing election assets (candidate photos, external documentation).
*   **Core Security Features:**
    *   **One-vote policy** enforcement per user per election.
    *   **CSRF protection** and **XSS prevention**.
    *   **Session management** with automatic timeout.

---

## ⚙️ Development Process

### 1. Methodology and Approach
The project followed a modified **Agile/Scrum methodology** with a focus on iterative development across three primary sprints:

*   **Sprint 1: Core Functionality & Data Model:** Focused on setting up the tech stack, database, user authentication (login/registration), and the fundamental CRUD operations for Election setup.
*   **Sprint 2: Voting & Security Implementation:** Focused on the core voting workflow, vote encryption/anonymization, implementing the audit trail, and building the voter interface.
*   **Sprint 3: Administration & Reporting:** Focused on the admin dashboard, result tallying logic, visual reporting, and final security hardening/testing.

### 2. Version Control and Standards
*   **Version Control:** **Git** was used for all source code management, hosted on **GitHub**.
*   **Branching Strategy:** The **Gitflow Workflow** was used:
    *   `main`: Always represents the production-ready, stable code.
    *   `develop`: Integration branch for features merged from feature branches.
    *   `feature/`: Short-lived branches for developing new features.
*   **Coding Standards:**
    *   **PHP/Laravel:** Adherence to **PSR-12** coding standards.
    *   **JavaScript/CSS:** Use of **ESLint** and **Prettier** for consistent front-end formatting.
    *   **Database:** All database changes were managed via migration scripts to ensure consistency across environments.
    *   **Documentation:** Functions, APIs, and key components were documented using **PHPDoc** style comments.

### 3. Development Tools and Environment
*   **IDE:** Visual Studio Code (VS Code) and PHP Storm.
*   **Package Management:** **Composer** for PHP dependencies, and **npm** for front-end assets.
*   **Testing:** **PHPUnit** for comprehensive unit and feature testing.
*   **CI/CD:** While a full CI/CD pipeline was beyond the capstone scope, a manual deployment process was documented and followed, leveraging environment variables for seamless transition from development to production hosting.

---

## 👥 Team and Contributors

| Name | Role | Responsibilities |
|------|------|------------------|
| **Zekiee** | Lead Developer | System Architecture, Backend Development, Security Implementation, Database Design |
| **Raymond Nino Ong** | Co-Developer | Frontend Development, UI/UX Design, Documentation, Feature Testing |

---

## 📅 Timeline and Milestones

| Milestone | Description | Status |
|-----------|-------------|--------|
| **Project Initiation** | Requirement gathering and tech stack selection | ✅ Completed |
| **Sprint 1** | Core Functionality & Data Model | ✅ Completed |
| **Sprint 2** | Voting & Security Implementation | ✅ Completed |
| **Sprint 3** | Administration & Reporting | ✅ Completed |
| **Final Testing** | System-wide UAT and security audits | ✅ Completed |
| **Deployment** | Production environment setup and final launch | ✅ Completed |

---

## 🎨 Design and User Experience

The system follows a clean, modern aesthetic focusing on clarity and ease of use.
*   **Wireframes:** Designed for mobile-first accessibility.
*   **Color Scheme:** Professional and neutral to maintain election neutrality.
*   **Typography:** Clear, sans-serif fonts for high readability across devices.
*   **User Flow:** Minimized steps from authentication to ballot submission to reduce voter fatigue.

---

## 📈 Future Roadmap

*   **Multi-language Support** (Tagalog, English, Cebuano)
*   **SMS Notification Integration** for vote confirmations
*   **Blockchain Verification Layer** for enhanced transparency
*   **Biometric Authentication** support
*   **Mobile Applications** (iOS & Android native apps)

---
*Created by Z Developers - 2026*

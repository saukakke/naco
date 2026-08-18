# NACO — Normal Apprenticeship Company

A complete, responsive public website for **Normal Apprenticeship Company (NACO)**, built around its mission of empowering Nigerian youths with practical manpower skills, moral development, emergency response capabilities and community-development values.

## Website

The site is a lightweight static website suitable for GitHub Pages, Netlify, Vercel or shared hosting.

### Pages

- `index.html` — public home page and organization overview
- `about.html` — mission, vision, identity and principles
- `programs.html` — life-saving, vocational and community-development programs
- `impact.html` — NACO's impact model and success stories
- `contact.html` — enrolment, contact and partnership enquiries

### Shared assets

- `assets/styles.css` — responsive design system and components
- `assets/script.js` — mobile navigation, form feedback and dynamic copyright year
- `badge.jpg` — existing NACO badge supplied in the original repository

## Content foundation

The redesign preserves the core information from the original NACO website:

- NACO stands for **Normal Apprenticeship Company**.
- The organization focuses on manpower/vocational skills, moral training, emergency response and self-reliance.
- Key activities include tailoring, carpentry, welding, electronics, hairdressing, first aid, CPR, basic life support, water and fire rescue, ceremonial drills, communal labour and entrepreneurship mentorship.
- The three main service groups are **Life Saving & Emergency Response**, **Manpower & Vocational Skills**, and **Moral & Community Development**.
- The stated mission is to empower youths to become self-reliant, productive citizens who contribute to community and national development.
- The stated vision is a skilled, disciplined and self-sufficient youth population.

## Design direction

The new interface keeps NACO's established deep-green and gold identity while introducing a cleaner information hierarchy, reusable components, stronger typography, accessible focus states, responsive navigation and distinct page-level layouts.

### Core palette

| Role | Value |
|---|---|
| Deep green | `#0a2e1f` |
| Dark green | `#062418` |
| Gold | `#f5a623` |
| Warm background | `#fbfaf5` |
| Surface | `#ffffff` |

## Technology

- Semantic HTML5
- Modern CSS with Grid, Flexbox, custom properties and responsive breakpoints
- Vanilla JavaScript
- Existing NACO badge asset
- No build step or framework dependency

## Forms

The enrolment/contact forms currently provide frontend confirmation only. They are intentionally structured so a backend endpoint or form provider can be connected later without redesigning the UI.

## Local preview

Open `index.html` in a modern browser, or serve the repository with any static HTTP server.

Example:

```bash
python3 -m http.server 8080
```

Then visit `http://localhost:8080`.

## Deployment

The site can be deployed directly as a static project to GitHub Pages, Netlify, Vercel or conventional shared hosting.

## Contact information currently represented

- Phone: `+234 813 014 4920`
- Email: `hello@naco.org.ng`
- Location: Kaduna, Nigeria

Contact details and social links should be verified with NACO before production launch.

## License

No license was specified in the original repository. Add the appropriate license before redistributing the project.
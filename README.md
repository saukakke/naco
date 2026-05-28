# NACO – Normal Apprenticeship Company Website

A modern, high-converting, fully responsive website for **Normal Apprenticeship Company (NACO)** – a training organization that empowers Nigerian youths with manpower skills, moral training, and emergency response capabilities.

---

## 📌 Live Preview

Open `about.html` in any modern browser to view the website locally.

---

## 🧩 Features

- **Fully Responsive** – Works perfectly on desktop, tablet, and mobile devices.
- **High-Converting Layout** – Clear CTAs (Enroll Now, Learn More, Chat on WhatsApp), trust signals, testimonials, and enrollment form.
- **Professional Design** – Warm color scheme: deep green (`#0a2e1f`) + gold/orange (`#f5a623`).
- **Service Classification** – Three major service groups with icons and descriptions.
- **Interactive Forms** – "Get Started" enrollment form + contact message form.
- **Floating WhatsApp Button** – Instant chat for inquiries.
- **Smooth Scrolling & Hover Effects** – Modern UX with card shadows and transitions.
- **No External Dependencies** – Only Font Awesome icons (CDN) – easy to deploy.

---

## 📁 File Structure
project/
│
├── index.html          # Complete website (HTML/CSS/JS)
└── README.md           # Project documentation

> No additional files, images, or folders required. All icons come from Font Awesome CDN.

---

## 🛠️ Technologies Used

- HTML5
- CSS3 (Flexbox, Grid, custom properties)
- JavaScript (vanilla – form handling)
- Font Awesome 6 (free CDN)

---

## 🚀 How to Use

1. **Download** the `index.html` file.
2. **Open** it in any web browser (Chrome, Firefox, Edge, Safari).
3. **Deploy** to any web hosting service (Netlify, Vercel, GitHub Pages, or any shared hosting).

### Customization Tips

- **Contact Info** – Update phone numbers, email, address in the top bar, contact section, and footer.
- **WhatsApp number** – Replace `2348123456789` in the floating button link (search `wa.me/2348123456789` in the code).
- **Form submission** – Currently displays a success message on the frontend. To add backend email sending, integrate a service like Formspree, Netlify Forms, or PHP.
- **Social media links** – Replace the `#` in footer and top bar with actual profile URLs.

---

## 📄 Content Sections

1. **Hero** – Headline, subheadline, primary CTAs.
2. **About Us** – Organization background + Mission & Vision cards.
3. **What We Do** – Six core activities with icons.
4. **Our Services** – Three major groups (Life Saving, Manpower Skills, Moral & Community Development).
5. **Why Choose NACO** – Six value propositions.
6. **Aims of NACO** – Six core aims.
7. **Testimonials** – Three placeholder success stories (easily editable).
8. **Get Started Form** – Captures name, email, phone, training area.
9. **Contact Section** – Contact details + message form.
10. **Footer** – Quick links, social icons, copyright.
11. **Floating WhatsApp Button** – Persistent chat widget.

---

## 🎨 Color Palette

| Role        | Color Code | Example                     |
|-------------|------------|-----------------------------|
| Primary     | `#0a2e1f`  | Dark green (headers, nav)   |
| Accent      | `#f5a623`  | Gold/orange (buttons, icons)|
| Background  | `#fafaf8`  | Off-white / warm light      |
| Card Bg     | `#ffffff`  | White + subtle shadows      |

---

## 📱 Responsiveness

- Mobile-first CSS with `@media (max-width: 768px)`.
- Flexbox and grid wrap on all sections.
- Touch-friendly buttons and form inputs.

---

## 📧 Form Handling

Currently the forms use **frontend JavaScript** that shows a confirmation message without actually sending an email. To enable real email delivery:

- Replace the form action with a service like **Formspree** (`https://formspree.io/f/your-endpoint`).
- Or integrate **Netlify Forms** (add `data-netlify="true"` to the form).
- Or connect to a backend PHP script.

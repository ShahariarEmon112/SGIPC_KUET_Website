# HTML & CSS Guide for SGIPC Website - Beginner Friendly

## Table of Contents

1. [What is HTML?](#what-is-html)
2. [What is CSS?](#what-is-css)
3. [How HTML & CSS Work Together](#how-html--css-work-together)
4. [Structure of index.html](#structure-of-indexhtml)
5. [Understanding CSS Styling](#understanding-css-styling)
6. [Testing Locally](#testing-locally)
7. [Modifying the Website](#modifying-the-website)
8. [Common HTML & CSS Examples](#common-html--css-examples)

---

## What is HTML?

HTML stands for **HyperText Markup Language** - it's the structure/skeleton of a web page.

### HTML is like a building blueprint:

- It defines WHAT content appears on the page
- It organizes information in a logical structure
- It uses **tags** to mark different parts

### Basic HTML Structure:

```html
<!DOCTYPE html>
<html>
  <head>
    <title>My Website</title>
  </head>
  <body>
    <h1>Hello World</h1>
    <p>This is a paragraph</p>
  </body>
</html>
```

### Common HTML Tags:

| Tag              | Purpose               | Example                                 |
| ---------------- | --------------------- | --------------------------------------- |
| `<h1>` to `<h6>` | Headings (h1=largest) | `<h1>Main Title</h1>`                   |
| `<p>`            | Paragraph text        | `<p>Some text here</p>`                 |
| `<a>`            | Link                  | `<a href="page.html">Click me</a>`      |
| `<button>`       | Clickable button      | `<button>Click</button>`                |
| `<div>`          | Container/section     | `<div>Content here</div>`               |
| `<section>`      | Major section         | `<section id="about">...</section>`     |
| `<form>`         | Data input form       | `<form method="POST">...</form>`        |
| `<input>`        | Text input            | `<input type="text">`                   |
| `<table>`        | Data table            | `<table><tr><td>Cell</td></tr></table>` |
| `<ul>` `<li>`    | Bullet list           | `<ul><li>Item</li></ul>`                |

### In Our index.html:

```html
<!-- Topbar - simple text at top -->
<div class="topbar">
  <strong>SGIPC</strong> | Special Group Interested In Programming Contest
</div>

<!-- Navigation bar - links to different sections -->
<header class="navbar">
  <a class="brand" href="#home">SGIPC</a>
  <ul class="menu">
    <li><a href="#about">About</a></li>
    <li><a href="#form-demo">Join Us</a></li>
  </ul>
</header>

<!-- Main section - with an id so CSS can style it -->
<section id="form-demo">
  <h2>Join Our Community</h2>
  <form action="request_join.php" method="POST">
    <input type="text" name="full_name" required />
    <button type="submit">Submit</button>
  </form>
</section>
```

---

## What is CSS?

CSS stands for **Cascading Style Sheets** - it's the design/makeup of a web page.

### CSS is like paint and decoration:

- It defines HOW things look
- Colors, sizes, spacing, animations
- It uses **selectors** to target HTML elements

### Basic CSS Syntax:

```css
/* Select an element and style it */
h1 {
  color: blue; /* Text color */
  font-size: 32px; /* Size of text */
  text-align: center; /* Alignment */
  margin: 20px; /* Space outside */
}
```

### Common CSS Properties:

| Property           | What it does          | Example                                   |
| ------------------ | --------------------- | ----------------------------------------- |
| `color`            | Text color            | `color: blue;`                            |
| `background`       | Background color      | `background: #fff;`                       |
| `font-size`        | Size of text          | `font-size: 18px;`                        |
| `font-weight`      | Boldness of text      | `font-weight: 700;`                       |
| `padding`          | Space inside element  | `padding: 20px;`                          |
| `margin`           | Space outside element | `margin: 20px;`                           |
| `border`           | Edge around element   | `border: 2px solid blue;`                 |
| `border-radius`    | Rounded corners       | `border-radius: 8px;`                     |
| `width`            | Element width         | `width: 100%;`                            |
| `height`           | Element height        | `height: 400px;`                          |
| `display`          | How to position       | `display: flex;` or `grid`                |
| `background-image` | Background image      | `background-image: url('image.jpg');`     |
| `box-shadow`       | Shadow effect         | `box-shadow: 0 4px 15px rgba(0,0,0,0.1);` |
| `transition`       | Animation effect      | `transition: all 0.3s;`                   |

### CSS Selectors:

```css
/* Select by tag name */
h1 {
  color: blue;
}

/* Select by class name (.) */
.btn {
  padding: 10px;
}

/* Select by id (#) */
#about {
  background: white;
}

/* Select all elements */
* {
  margin: 0;
}

/* Hover effect - when user hovers over element */
.btn:hover {
  color: red;
}

/* Combine selectors */
.menu a {
  color: black;
} /* All <a> inside .menu */
```

---

## How HTML & CSS Work Together

### The Complete Picture:

```
┌─────────────────────────────────────────────────────────────────┐
│ HTML (Structure)                                                │
│ ─────────────────────────────────────────────────────────────   │
│                                                                 │
│ <section class="highlight-card">                              │
│     <h3>Learning</h3>                                          │
│     <p>Access to study materials</p>                           │
│ </section>                                                      │
│                                                                 │
│ This creates a box with a heading and paragraph                │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ CSS (Styling)                                                   │
│ ─────────────────────────────────────────────────────────────   │
│                                                                 │
│ .highlight-card {                                             │
│     background: white;           /* White background */        │
│     padding: 30px;               /* Space inside */            │
│     border-radius: 12px;         /* Rounded corners */         │
│     box-shadow: 0 4px 15px ...; /* Shadow effect */           │
│     border: 2px solid transparent;                            │
│ }                                                               │
│                                                                 │
│ .highlight-card:hover {          /* When mouse hovers over */ │
│     border-color: #667eea;       /* Border becomes purple */  │
│     transform: translateY(-10px); /* Moves up slightly */      │
│ }                                                               │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ RESULT IN BROWSER                                               │
│ ─────────────────────────────────────────────────────────────   │
│                                                                 │
│ ┌──────────────────────────────────┐                           │
│ │  📚                              │  ← Icon from HTML         │
│ │  Learning                        │  ← H3 from HTML           │
│ │  Access to study materials       │  ← P from HTML            │
│ │                                  │                           │
│ │ (White background, rounded,      │  ← All styled by CSS      │
│ │  shadow, moves on hover)         │                           │
│ └──────────────────────────────────┘                           │
└─────────────────────────────────────────────────────────────────┘
```

### Step-by-Step: How a User Sees the Website

```
1. USER OPENS PAGE
   Browser loads: http://localhost/sgipc/index.html

2. BROWSER READS HTML FILE
   ✓ Reads all <head> content (includes CSS)
   ✓ Parses HTML structure
   ✓ Finds all CSS files and styles

3. BROWSER APPLIES CSS
   ✓ Matches HTML elements with CSS selectors
   ✓ Applies colors, sizes, spacing
   ✓ Adds animations and effects

4. BROWSER RENDERS PAGE
   ✓ Draws all HTML elements with CSS styling
   ✓ Applies hover effects when user moves mouse
   ✓ Runs JavaScript for interactions

5. USER SEES BEAUTIFUL WEBSITE
   ✓ All colors, fonts, animations working
   ✓ Responsive (looks good on mobile too)
   ✓ Can click buttons and submit forms
```

---

## Structure of index.html

### The File Format:

```html
<!DOCTYPE html>
<!-- Tells browser this is HTML5 -->
<html lang="en">
  <!-- Main HTML element, English language -->

  <head>
    <!-- Contains meta-info, not visible -->
    <meta charset="UTF-8" />
    <!-- Character encoding -->
    <meta name="viewport" ... />
    <!-- Makes responsive on mobile -->
    <title>SGIPC Website</title>
    <!-- Page title (shown in tab) -->
    <link rel="stylesheet" ... />
    <!-- Links to CSS file -->
    <style>
                    <!-- Internal CSS styles -->
      /* All CSS code goes here */
    </style>
  </head>

  <body>
    <!-- Contains all visible content -->

    <div class="topbar">...</div>
    <!-- Top banner -->
    <header class="navbar">...</header>
    <!-- Navigation bar -->
    <main class="container">
      <!-- Main content -->
      <section id="home">...</section>
      <!-- Hero section -->
      <section id="about">...</section>
      <!-- About section -->
      <section id="form-demo">...</section>
      <!-- Join form -->
    </main>
    <footer>...</footer>
    <!-- Footer -->

    <script>
      <!-- JavaScript code for interactions -->
      // Code to handle menu clicks, animations, etc.
    </script>
  </body>
</html>
```

### Key Sections in Our index.html:

#### 1. Topbar

```html
<div class="topbar">
  <strong>SGIPC</strong> | Special Group Interested In Programming Contest
</div>
```

CSS makes it gray background with white text.

#### 2. Navigation Bar

```html
<header class="navbar">
  <a class="brand" href="#home">SGIPC</a>
  <ul class="menu">
    <li><a href="#about">About</a></li>
    <li><a href="#form-demo">Join Us</a></li>
  </ul>
</header>
```

CSS makes it:

- Sticky (stays at top when scrolling)
- Has white background
- Menu items are properly spaced
- Links change color on hover

#### 3. Hero Section

```html
<section class="reveal">
  <p class="tagline">KUET reaching ICPC World Finals 2024</p>
  <h1>Master Competitive Programming</h1>
  <p>Join SGIPC and become part of a thriving community...</p>
  <div class="cta-buttons">
    <a href="#form-demo" class="btn btn-primary">Join Now</a>
    <a href="contests.php" class="btn btn-secondary">View Contests</a>
  </div>
</section>
```

CSS makes it:

- Have a beautiful purple gradient background
- Large, centered heading
- Two colored buttons
- Responsive on mobile

#### 4. About Section

```html
<section id="about">
  <h2>About SGIPC</h2>
  <div class="about-content">
    <div class="about-text">
      <h3>Special Group...</h3>
      <ul>
        <li>Regular contests</li>
        <li>Experienced mentors</li>
      </ul>
    </div>
    <div class="about-image">🚀</div>
  </div>
</section>
```

CSS makes it:

- Two-column layout (text + emoji)
- List items have checkmark bullets (using CSS ::before)
- Clean spacing

#### 5. Form Section

```html
<section id="form-demo">
  <h2>Join Our Community</h2>
  <div class="form-container">
    <form action="request_join.php" method="POST">
      <div class="form-group">
        <label for="full-name">Full Name *</label>
        <input type="text" id="full-name" name="full_name" required />
      </div>
      <button type="submit" class="form-submit">Submit Request</button>
    </form>
  </div>
</section>
```

CSS makes it:

- Centered container with white background
- Inputs have borders that highlight on focus
- Button has gradient and hover effect
- Responsive for mobile

---

## Understanding CSS Styling

### 1. Colors & Gradients

```css
/* Solid color */
color: #333; /* Dark gray text */
background: white; /* White background */

/* Gradient - smooth transition between colors */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
/* Direction: 135deg (diagonal)
   Start color: #667eea (blue-purple)
   End color: #764ba2 (purple)
   Creates smooth transition from top-left to bottom-right
*/
```

### 2. Spacing - Padding vs Margin

```
     MARGIN (outside space)
     ▲
     │ 20px
     │
┌────────────────────────┐
│   PADDING (inside)     │
│   ┌─────────────────┐  │
│   │                 │  │ 10px
│   │   Content       │  │
│   │                 │  │
│   └─────────────────┘  │
│   ┌─────────────────┐  │
└────────────────────────┘
     │
     │ 20px
     ▼
```

In CSS:

```css
.box {
  padding: 20px; /* Space INSIDE the box */
  margin: 30px; /* Space OUTSIDE the box */
}

/* Can specify individual sides */
padding: 10px 20px 10px 20px; /* top right bottom left */
```

### 3. Display Types

#### Display: Flex (Flexible Layout)

```css
.menu {
  display: flex; /* Makes children arrange in row/column */
  gap: 30px; /* Space between items */
  align-items: center; /* Vertical alignment */
}
```

Result: Items in menu line up horizontally with space between them

#### Display: Grid (Grid Layout)

```css
.highlights-grid {
  display: grid; /* Grid layout */
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px; /* Space between items */
}
```

Result: Cards arrange in 3 columns on desktop, 2 on tablet, 1 on mobile

### 4. Hover Effects

```css
.btn {
  background: white;
  transition: all 0.3s; /* Smooth transition over 0.3 seconds */
}

.btn:hover {
  background: #f0f0f0;
  transform: translateY(-3px); /* Move up 3 pixels */
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}
```

When user hovers over button:

- Background changes
- Button moves up slightly
- Shadow appears (looks like lifting off)
- All happens smoothly over 0.3 seconds

### 5. Responsive Design

```css
/* Default - for desktop */
.highlights-grid {
  grid-template-columns: repeat(3, 1fr); /* 3 columns */
}

/* For tablets and smaller */
@media (max-width: 768px) {
  .highlights-grid {
    grid-template-columns: repeat(2, 1fr); /* 2 columns */
  }
}

/* For mobile phones */
@media (max-width: 480px) {
  .highlights-grid {
    grid-template-columns: 1fr; /* 1 column */
  }
}
```

---

## Testing Locally

### Method 1: Direct File Opening (Simplest)

```bash
# Windows: Just double-click index.html

# macOS/Linux: Open in browser
open ~/Desktop/sgipc\ website/index.html
# OR
firefox ~/Desktop/sgipc\ website/index.html
```

### Method 2: Using XAMPP (Recommended)

1. Copy folder to: `C:\xampp\htdocs\sgipc\`
2. Open: `http://localhost/sgipc/index.html`

### Method 3: Using Python Server

```bash
cd ~/Desktop/sgipc\ website
python -m http.server 8000
# Then open: http://localhost:8000/index.html
```

### What to Test:

✅ All sections visible and colored correctly
✅ Navigation bar sticks to top when scrolling
✅ Buttons change color on hover
✅ Form has proper input styling
✅ Table displays with proper formatting
✅ On mobile, layout changes (menu collapses, single column)
✅ Smooth scrolling when clicking navigation links
✅ Form submits to request_join.php

---

## Modifying the Website

### Example 1: Change Hero Section Color

**Current CSS:**

```css
.reveal {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

**Change to green:**

```css
.reveal {
  background: linear-gradient(135deg, #00d084 0%, #00a372 100%);
}
```

### Example 2: Add New Section

**HTML:**

```html
<section id="news">
  <h2>News & Updates</h2>
  <p>Latest updates here...</p>
</section>
```

**CSS (add at the end):**

```css
#news {
  background: #f5f5f5;
  padding: 60px 20px;
}

#news h2 {
  font-size: 40px;
  margin-bottom: 30px;
  text-align: center;
}
```

### Example 3: Change Button Style

**Current:**

```css
.btn-primary {
  background: white;
  color: #667eea;
}
```

**Make it bigger:**

```css
.btn-primary {
  background: white;
  color: #667eea;
  padding: 16px 40px; /* Changed from 14px 30px */
  font-size: 16px; /* Larger text */
}
```

### Example 4: Change Form Input Styling

**Current:**

```css
.form-group input {
  border: 2px solid #e0e0e0;
  border-radius: 8px;
  padding: 12px;
}
```

**Make borders more prominent:**

```css
.form-group input {
  border: 3px solid #667eea; /* Thicker, colored border */
  border-radius: 12px; /* More rounded */
  padding: 15px; /* More space inside */
  font-size: 15px; /* Slightly larger text */
}
```

---

## Common HTML & CSS Examples

### Example 1: Creating a Card

**HTML:**

```html
<div class="card">
  <img src="image.jpg" alt="Description" />
  <h3>Card Title</h3>
  <p>Card description text here</p>
  <a href="#">Read More</a>
</div>
```

**CSS:**

```css
.card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  padding: 20px;
  transition: all 0.3s;
}

.card:hover {
  transform: translateY(-10px); /* Move up on hover */
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
}

.card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 15px;
}

.card h3 {
  margin-bottom: 10px;
  color: #333;
}

.card a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.card a:hover {
  text-decoration: underline;
}
```

### Example 2: Creating a Responsive Grid

**HTML:**

```html
<div class="grid">
  <div class="grid-item">Item 1</div>
  <div class="grid-item">Item 2</div>
  <div class="grid-item">Item 3</div>
  <div class="grid-item">Item 4</div>
</div>
```

**CSS:**

```css
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  padding: 20px;
}

.grid-item {
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
```

### Example 3: Creating a Navigation Bar

**HTML:**

```html
<nav class="navbar">
  <a href="#" class="logo">Logo</a>
  <ul class="nav-menu">
    <li><a href="#home">Home</a></li>
    <li><a href="#about">About</a></li>
    <li><a href="#contact">Contact</a></li>
  </ul>
</nav>
```

**CSS:**

```css
.navbar {
  background: #333;
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 100;
}

.logo {
  color: white;
  font-weight: 700;
  font-size: 24px;
  text-decoration: none;
}

.nav-menu {
  display: flex;
  list-style: none;
  gap: 30px;
}

.nav-menu a {
  color: white;
  text-decoration: none;
  transition: color 0.3s;
}

.nav-menu a:hover {
  color: #667eea;
}

@media (max-width: 768px) {
  .nav-menu {
    flex-direction: column;
    gap: 10px;
  }
}
```

---

## Quick Reference Cheat Sheet

### Most Used HTML Tags:

```html
<h1>Heading 1</h1>
<h2>Heading 2</h2>
<p>Paragraph text</p>
<a href="url">Link</a>
<button>Click me</button>
<input type="text" />
<img src="image.jpg" />
<div>Container</div>
<section>Section</section>
<form action="file.php">Form</form>
<ul>
  <li>List item</li>
</ul>
```

### Most Used CSS Properties:

```css
color: #333;
background: white;
padding: 20px;
margin: 10px;
font-size: 16px;
font-weight: 700;
text-align: center;
border: 2px solid #333;
border-radius: 8px;
width: 100%;
height: 400px;
display: flex;
gap: 20px;
box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
transition: all 0.3s;
transform: translateY(-10px);
```

### CSS Units:

```css
px       /* Pixels - fixed size */
%        /* Percentage - relative to parent */
em       /* Relative to parent font size */
rem      /* Relative to root font size */
vh       /* Viewport height (mobile responsive) */
vw       /* Viewport width (mobile responsive) */
```

---

## Next Steps

1. ✅ Open `index.html` in browser
2. ✅ Inspect elements (F12) to see HTML & CSS
3. ✅ Try modifying CSS values and refresh page
4. ✅ Change colors, sizes, padding
5. ✅ Add new sections
6. ✅ Test on mobile (F12 → device mode)
7. ✅ Connect form to PHP backend (request_join.php)

---

## Summary

**HTML:** Defines WHAT (structure and content)
**CSS:** Defines HOW (styling and appearance)
**Together:** Create beautiful, interactive websites!

The website displays correctly because:

1. ✅ HTML provides proper semantic structure
2. ✅ CSS styles every element beautifully
3. ✅ Media queries make it responsive
4. ✅ Hover effects make it interactive
5. ✅ Forms connect to PHP backend

Now you can modify, customize, and expand the website as needed!

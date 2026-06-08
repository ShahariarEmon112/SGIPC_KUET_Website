# Practical HTML & CSS Examples - See It In Action

## How to Use This Guide

For each example, I'll show you:

1. **HTML Code** - The structure
2. **CSS Code** - The styling
3. **Visual Result** - How it looks
4. **How to Test** - Try it yourself

---

## Example 1: Simple Button

### HTML Code:

```html
<button class="my-button">Click Me</button>
```

### CSS Code:

```css
.my-button {
  padding: 12px 24px; /* Space inside button */
  background: #667eea; /* Purple background */
  color: white; /* White text */
  border: none; /* No border */
  border-radius: 8px; /* Rounded corners */
  font-size: 16px; /* Text size */
  font-weight: 700; /* Bold text */
  cursor: pointer; /* Show it's clickable */
  transition: all 0.3s; /* Smooth animation */
}

.my-button:hover {
  background: #764ba2; /* Darker on hover */
  transform: translateY(-2px); /* Move up slightly */
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}
```

### Visual Result:

```
Before Hover:          After Hover:
┌──────────────┐      ┌──────────────┐
│  Click Me    │  ──→ │  Click Me    │
└──────────────┘      └──────────────┘
(Purple)             (Darker purple, elevated)
```

### Try It:

1. Open `index.html` in browser
2. Scroll to "Join Us" section
3. Hover over "Submit Request" button
4. You'll see it change color and move up!

---

## Example 2: Navigation Bar

### HTML Code:

```html
<header class="navbar">
  <a class="brand" href="#home">SGIPC</a>
  <ul class="menu">
    <li><a href="#about">About</a></li>
    <li><a href="#form-demo">Join Us</a></li>
    <li><a href="admin_login.php">Admin</a></li>
  </ul>
</header>
```

### CSS Code:

```css
.navbar {
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
  position: sticky; /* Stays at top */
  top: 0; /* Always at top */
  z-index: 100; /* On top of other elements */
}

.brand {
  font-size: 20px;
  font-weight: 700;
  color: #333;
}

.menu {
  display: flex; /* Items in a row */
  gap: 30px; /* 30px space between items */
  list-style: none; /* No bullet points */
}

.menu a {
  text-decoration: none;
  color: #333;
  transition: color 0.3s;
}

.menu a:hover {
  color: #667eea; /* Purple on hover */
}
```

### Visual Result:

```
┌─────────────────────────────────────────────────────┐
│ SGIPC        About    Join Us    Admin            │
│                                                    │
└─────────────────────────────────────────────────────┘
 Brand        (Links change to purple on hover)
```

### Try It:

1. Open `index.html` in browser
2. Scroll down and scroll back up
3. Notice navbar sticks to the top
4. Hover over links - they turn purple!

---

## Example 3: Card with Hover Effect

### HTML Code:

```html
<div class="highlight-card">
  <div class="highlight-icon">🏆</div>
  <h3>Competitions</h3>
  <p>Participate in regular contests</p>
</div>
```

### CSS Code:

```css
.highlight-card {
  background: white;
  padding: 30px; /* Space inside */
  border-radius: 12px; /* Rounded corners */
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Light shadow */
  text-align: center;
  transition: all 0.3s; /* Smooth transition */
  border: 2px solid transparent; /* Invisible border ready */
}

.highlight-card:hover {
  border-color: #667eea; /* Border appears */
  transform: translateY(-10px); /* Moves up 10px */
  box-shadow: 0 12px 30px rgba(102, 126, 234, 0.2); /* Bigger shadow */
}

.highlight-icon {
  font-size: 48px;
  margin-bottom: 15px;
}

.highlight-card h3 {
  font-size: 20px;
  margin-bottom: 10px;
  color: #333;
}

.highlight-card p {
  color: #666;
  font-size: 14px;
}
```

### Visual Result:

```
BEFORE HOVER:           AFTER HOVER (Move mouse over card):

┌──────────────┐       ┌──────────────┐
│              │       │   ▲▲▲       │ (Moves up)
│  🏆          │  →→→→→│  🏆          │
│ Competitions │       │ Competitions │
│ Participate  │       │ Participate  │
└──────────────┘       └──────────────┘
(Subtle shadow)       (Bigger shadow, blue border)
```

### Try It:

1. Open `index.html` in browser
2. Find "Why Join SGIPC?" section
3. Hover over cards (🏆 Competitions, 📚 Learning, etc.)
4. Watch them move up and get a blue border!

---

## Example 4: Form Input

### HTML Code:

```html
<div class="form-group">
  <label for="full-name">Full Name *</label>
  <input type="text" id="full-name" name="full_name" required />
</div>
```

### CSS Code:

```css
.form-group {
  margin-bottom: 25px; /* Space below field */
}

.form-group label {
  display: block; /* Takes full width */
  margin-bottom: 10px;
  font-weight: 600;
  color: #333;
  font-size: 14px;
}

.form-group input {
  width: 100%; /* Full width */
  padding: 12px; /* Space inside input */
  border: 2px solid #e0e0e0; /* Light gray border */
  border-radius: 8px; /* Rounded corners */
  font-size: 14px;
  transition: border-color 0.3s; /* Smooth color change */
}

.form-group input:focus {
  outline: none; /* Remove default outline */
  border-color: #667eea; /* Border turns purple */
}
```

### Visual Result:

```
BEFORE CLICK:          AFTER CLICK (Focus):

Full Name *            Full Name *
┌─────────────────┐   ┌─────────────────┐
│                 │   │ [cursor blinking]│
└─────────────────┘   └─────────────────┘
(Gray border)         (Purple border)
```

### Try It:

1. Open `index.html` in browser
2. Scroll to "Join Our Community" section
3. Click on the "Full Name" input
4. Notice the border turns purple!

---

## Example 5: Grid Layout

### HTML Code:

```html
<div class="highlights-grid">
  <div class="highlight-card">
    <h3>🏆 Competitions</h3>
  </div>
  <div class="highlight-card">
    <h3>📚 Learning</h3>
  </div>
  <div class="highlight-card">
    <h3>👥 Community</h3>
  </div>
  <div class="highlight-card">
    <h3>🎓 Mentorship</h3>
  </div>
  <div class="highlight-card">
    <h3>🌟 Career Growth</h3>
  </div>
  <div class="highlight-card">
    <h3>💻 Skills</h3>
  </div>
</div>
```

### CSS Code:

```css
.highlights-grid {
  display: grid; /* Use grid */
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  /* Creates responsive grid:
       - On desktop (1200px): 3 columns
       - On tablet (768px): 2 columns
       - On mobile (375px): 1 column
    */
  gap: 30px; /* 30px space */
}
```

### Visual Result:

**DESKTOP (3 columns):**

```
┌──────────┐ ┌──────────┐ ┌──────────┐
│ 🏆 Comp  │ │ 📚 Learn │ │ 👥 Comm  │
└──────────┘ └──────────┘ └──────────┘
┌──────────┐ ┌──────────┐ ┌──────────┐
│ 🎓 Mentor│ │ 🌟 Career│ │ 💻 Skills│
└──────────┘ └──────────┘ └──────────┘
```

**TABLET (2 columns):**

```
┌──────────┐ ┌──────────┐
│ 🏆 Comp  │ │ 📚 Learn │
└──────────┘ └──────────┘
┌──────────┐ ┌──────────┐
│ 👥 Comm  │ │ 🎓 Mentor│
└──────────┘ └──────────┘
┌──────────┐ ┌──────────┐
│ 🌟 Career│ │ 💻 Skills│
└──────────┘ └──────────┘
```

**MOBILE (1 column):**

```
┌──────────┐
│ 🏆 Comp  │
└──────────┘
┌──────────┐
│ 📚 Learn │
└──────────┘
┌──────────┐
│ 👥 Comm  │
└──────────┘
(... rest below)
```

### Try It:

1. Open `index.html` in browser
2. Find "Why Join SGIPC?" section
3. Resize your browser window (make it narrow)
4. Watch cards rearrange automatically!

---

## Example 6: Gradient Background

### HTML Code:

```html
<section class="reveal">
  <h1>Master Competitive Programming</h1>
</section>
```

### CSS Code:

```css
.reveal {
  padding: 80px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  /* Explanation:
       135deg = direction (bottom-right)
       #667eea = starting color (blue-purple)
       0% = 0% along gradient (top-left)
       #764ba2 = ending color (darker purple)
       100% = 100% along gradient (bottom-right)
    */
  color: white;
  text-align: center;
}

.reveal h1 {
  font-size: 56px;
  font-weight: 800;
}
```

### Visual Result:

```
┌──────────────────────────────────────┐
│                                      │
│         Master Competitive         │
│          Programming               │
│                                      │
│     (Smooth gradient from          │
│      blue-purple to purple)        │
│                                      │
└──────────────────────────────────────┘

Color visualization:
#667eea ──────────────→ #764ba2
(light purple)          (dark purple)
```

### Try It:

1. Open `index.html` in browser
2. Look at the hero section at the top
3. You see the purple gradient background!

---

## Example 7: Table Styling

### HTML Code:

```html
<table>
  <thead>
    <tr>
      <th>Rank</th>
      <th>Team Name</th>
      <th>Rating</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>1</td>
      <td>KUET_Team1</td>
      <td>2450</td>
    </tr>
    <tr>
      <td>2</td>
      <td>KUET_Team2</td>
      <td>2385</td>
    </tr>
  </tbody>
</table>
```

### CSS Code:

```css
thead {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  /* Makes header row purple with white text */
}

th {
  padding: 18px;
  text-align: left;
  font-weight: 700;
}

td {
  padding: 16px 18px;
  border-bottom: 1px solid #eee;
  /* Light gray line between rows */
}

tbody tr:hover {
  background: #f5f5f5;
  /* Rows highlight when you hover over them */
}

.rank-badge {
  display: inline-flex;
  width: 35px;
  height: 35px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 50%;
  /* Creates circle badge for rank */
}
```

### Visual Result:

```
┌─────┬──────────────┬────────┐
│① Rank│ Team Name   │ Rating │ (Purple header)
├─────┼──────────────┼────────┤
│ ① │ KUET_Team1   │ 2450   │ (Hover: light gray)
├─────┼──────────────┼────────┤
│ ② │ KUET_Team2   │ 2385   │ (Hover: light gray)
├─────┼──────────────┼────────┤
│ ③ │ KUET_Team3   │ 2310   │ (Hover: light gray)
└─────┴──────────────┴────────┘
```

### Try It:

1. Open `index.html` in browser
2. Find "Top Teams Performance" section
3. Hover over table rows
4. Notice they highlight in light gray!

---

## Example 8: Responsive Menu (Mobile)

### HTML Code:

```html
<header class="navbar">
  <a class="brand" href="#home">SGIPC</a>
  <button class="menu-btn" id="menuBtn">☰</button>
  <ul class="menu" id="menu">
    <li><a href="#about">About</a></li>
    <li><a href="#form-demo">Join Us</a></li>
  </ul>
</header>
```

### CSS Code:

```css
.menu {
  display: flex; /* Show on desktop */
  gap: 30px;
}

.menu-btn {
  display: none; /* Hide on desktop */
}

/* Mobile version - screens smaller than 768px */
@media (max-width: 768px) {
  .menu {
    display: none; /* Hide menu by default */
    flex-direction: column; /* Items in column on mobile */
    position: absolute; /* Float above page */
    top: 60px;
    left: 0;
    width: 100%;
    background: white;
    gap: 15px;
    padding: 20px;
    border-top: 1px solid #eee;
  }

  .menu.active {
    display: flex; /* Show when active */
  }

  .menu-btn {
    display: block; /* Show menu button */
  }
}
```

### Visual Result:

**DESKTOP (≥ 768px):**

```
┌─────────────────────────────────────┐
│ SGIPC   About  Join Us  Admin      │
└─────────────────────────────────────┘
(Menu items visible in row)
```

**MOBILE (< 768px):**

```
┌──────────────┐
│ SGIPC      ☰ │  ← Click hamburger (☰)
└──────────────┘
       ↓
┌──────────────┐
│ SGIPC      ☰ │
├──────────────┤
│ About        │  ← Menu appears
│ Join Us      │
│ Admin        │
└──────────────┘
```

### Try It:

1. Open `index.html` in browser
2. Press F12 to open Developer Tools
3. Click the mobile icon (device mode)
4. Watch the menu collapse into hamburger icon!

---

## Example 9: List Items with Custom Bullets

### HTML Code:

```html
<ul class="about-text">
  <li>Regular online and offline contests</li>
  <li>Experienced mentors and guidance</li>
  <li>Resource sharing and learning materials</li>
</ul>
```

### CSS Code:

```css
.about-text ul {
  list-style: none; /* Hide default bullets */
  margin-top: 20px;
}

.about-text li {
  padding: 12px 0;
  padding-left: 30px; /* Space for checkmark */
  position: relative;
  color: #666;
  font-size: 15px;
}

.about-text li:before {
  content: '✓'; /* Add checkmark */
  position: absolute;
  left: 0; /* At the left */
  color: #667eea; /* Purple color */
  font-weight: 700;
  font-size: 18px;
}
```

### Visual Result:

```
✓ Regular online and offline contests
✓ Experienced mentors and guidance
✓ Resource sharing and learning materials
✓ Team-based training for ICPC
✓ Portfolio building through competitions

(Checkmarks are purple, custom colored)
```

### Try It:

1. Open `index.html` in browser
2. Find "About SGIPC" section
3. Look at the bullet points
4. They have purple checkmarks instead of regular bullets!

---

## Example 10: Flexbox Layout

### HTML Code:

```html
<div class="cta-buttons">
  <a href="#form-demo" class="btn btn-primary">Join Now</a>
  <a href="contests.php" class="btn btn-secondary">View Contests</a>
</div>
```

### CSS Code:

```css
.cta-buttons {
  display: flex; /* Flexible layout */
  gap: 15px; /* Space between buttons */
  justify-content: center; /* Center horizontally */
  flex-wrap: wrap; /* Wrap on mobile */
  margin-top: 30px;
}

.btn {
  padding: 14px 30px;
  border: none;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary {
  background: white;
  color: #667eea;
}

.btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.btn-secondary {
  background: transparent;
  color: white;
  border: 2px solid white;
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.1);
}
```

### Visual Result:

**DESKTOP:**

```
              ┌────────────┐  ┌────────────┐
              │ Join Now   │  │View Contests│
              └────────────┘  └────────────┘
              (Centered, side by side)
```

**MOBILE:**

```
              ┌────────────┐
              │ Join Now   │
              └────────────┘
              ┌────────────┐
              │View Contests│
              └────────────┘
              (Stacked vertically)
```

### Try It:

1. Open `index.html` in browser
2. Look at the top hero section
3. See two buttons side by side
4. Resize browser - buttons stack on mobile!

---

## CSS Properties Reference

### Color Properties:

```css
color: #667eea; /* Text color */
background: white; /* Background color */
background: linear-gradient(135deg, #667eea, #764ba2); /* Gradient */
border: 2px solid #667eea; /* Border */
box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Shadow */
```

### Spacing Properties:

```css
padding: 20px; /* Inside space */
margin: 10px; /* Outside space */
gap: 30px; /* Space between flex/grid items */
```

### Text Properties:

```css
font-size: 16px; /* Text size */
font-weight: 700; /* Boldness (400-900) */
text-align: center; /* Alignment */
line-height: 1.6; /* Space between lines */
```

### Layout Properties:

```css
display: flex; /* Flexible layout */
display: grid; /* Grid layout */
display: block; /* Block element */
width: 100%; /* Full width */
height: 400px; /* Fixed height */
```

### Visual Effects:

```css
border-radius: 8px; /* Rounded corners */
transform: translateY(-10px); /* Move element */
transition: all 0.3s; /* Smooth animation */
opacity: 0.8; /* Transparency */
```

---

## Quick Testing Checklist

When you open `index.html`, check these:

- [ ] Page loads without errors
- [ ] Navbar sticks to top when scrolling
- [ ] Buttons change color on hover
- [ ] Form inputs highlight on focus
- [ ] Cards lift up on hover
- [ ] Table rows highlight on hover
- [ ] Mobile menu works (F12 → mobile view)
- [ ] Gradient backgrounds look smooth
- [ ] Checkmarks are purple
- [ ] All text is readable

---

## Summary

Now you understand:

1. ✅ How HTML creates structure
2. ✅ How CSS creates styling
3. ✅ How they work together
4. ✅ Common CSS properties
5. ✅ Responsive design (mobile/tablet/desktop)
6. ✅ Hover effects and animations
7. ✅ Grids and flexbox layouts

**Next Steps:**

1. Modify CSS colors and sizes
2. Try adding new sections
3. Experiment with hover effects
4. Connect forms to PHP backend
5. Create your own custom pages!

The more you experiment, the better you'll understand HTML & CSS! 🎨✨

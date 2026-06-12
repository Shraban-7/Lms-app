# Implementation Plan

## Overview

Convert the Aether LMS project's entire design system from a custom CSS file (`public/css/style.css` with 682 lines of hand-written CSS + inline styles in Blade templates) to Tailwind CSS v4 utility-first classes. Tailwind v4 is already installed and configured in the project but unused by the main application views. The goal is to eliminate the external `public/css/style.css`, define all design tokens via Tailwind v4's `@theme` directive, and convert all 17 Blade templates to use Tailwind utility classes while preserving the exact same visual design, dark/light theme toggle, and glassmorphism aesthetic.

## Types

### Design Tokens (Tailwind v4 `@theme` Configuration)

```css
@theme {
  /* Font */
  --font-sans: 'Instrument Sans', 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;

  /* Brand Colors */
  --color-primary: #6366F1;
  --color-primary-hover: #4F46E5;
  --color-accent: #EC4899;
  --color-accent-hover: #DB2777;
  --color-success: #10B981;
  --color-warning: #F59E0B;
  --color-danger: #EF4444;

  /* Surface Colors (Dark - default) */
  --color-surface: #141A2D;
  --color-surface-solid: #141A2D;

  /* Border */
  --color-border: rgba(255, 255, 255, 0.08);
  
  /* Text */
  --color-text-primary: #F3F4F6;
  --color-text-secondary: #9CA3AF;
  --color-text-muted: #6B7280;

  /* Radius */
  --radius-card: 14px;
  --radius-button: 8px;
  --radius-badge: 50px;

  /* Shadows */
  --shadow-card: 0 10px 30px -10px rgba(0, 0, 0, 0.7);
  --shadow-glow: 0 0 20px rgba(99, 102, 241, 0.15);
}
```

### Dark Mode Variant

In Tailwind v4, configure a custom variant for `data-theme="dark"`:

```css
@custom-variant dark (&:where([data-theme="dark"], [data-theme="dark"] *));
```

This allows using `dark:bg-*`, `dark:text-*`, etc. to apply dark-mode-specific styles when `data-theme="dark"` is set on `<html>`.

### Color Mapping Strategy

| Current CSS Variable | Light Value | Dark Value | Tailwind Approach |
|---|---|---|---|
| `--bg-main` | `#F9FAFB` | `#0B0F19` | `bg-gray-50 dark:bg-[#0B0F19]` |
| `--bg-surface` | `rgba(255,255,255,0.7)` | `rgba(20,26,45,0.6)` | `bg-white/70 dark:bg-surface/60` |
| `--bg-surface-solid` | `#FFFFFF` | `#141A2D` | `bg-white dark:bg-surface-solid` |
| `--text-primary` | `#1F2937` | `#F3F4F6` | `text-gray-800 dark:text-text-primary` |
| `--text-secondary` | `#4B5563` | `#9CA3AF` | `text-gray-600 dark:text-text-secondary` |
| `--text-muted` | `#9CA3AF` | `#6B7280` | `text-gray-400 dark:text-text-muted` |
| `--border-color` | `rgba(0,0,0,0.06)` | `rgba(255,255,255,0.08)` | `border-gray-200 dark:border-border` |
| `--primary` | `#4F46E5` | `#6366F1` | `bg-primary-hover dark:bg-primary` |
| `--shadow-main` | `0 10px 35px -12px rgba(0,0,0,0.1)` | `0 10px 30px -10px rgba(0,0,0,0.7)` | `shadow-sm dark:shadow-card` |

## Files

### Files to Modify

1. **`resources/css/app.css`** — Complete rewrite
   - Add `@custom-variant dark` for dark mode
   - Expand `@theme` block with all custom design tokens (colors, shadows, radii)
   - Add minimal custom CSS only for things impossible with utilities:
     - Glow backdrop radial gradients (`.glow-backdrop-1`, `.glow-backdrop-2`)
     - Video player overlay styles (`.video-controls`, `.video-wrapper:hover .video-controls`)
     - Certificate print styles (`@media print`)
     - Scrollbar styling
     - Glassmorphism `backdrop-filter` utilities (if not using Tailwind's `backdrop-blur-*`)
     - Gradient text for logo (`.bg-gradient-text`)
   - Keep `@source` directives for Laravel pagination and cached views

2. **`vite.config.js`** — No changes needed (already correctly configured)

3. **`package.json`** — No changes needed (already has Tailwind v4)

4. **`resources/views/layouts/app.blade.php`** — Major rewrite
   - Replace `{{ asset('css/style.css') }}` with `@vite(['resources/css/app.css', 'resources/js/app.js'])`
   - Add `@fonts` directive for font loading
   - Convert all custom CSS classes to Tailwind utility classes
   - Keep JavaScript theme toggle logic

5. **`resources/views/landing.blade.php`** — Full conversion to Tailwind utilities

6. **`resources/views/dashboard.blade.php`** — Full conversion to Tailwind utilities

7. **`resources/views/auth/login.blade.php`** — Full conversion to Tailwind utilities

8. **`resources/views/auth/register.blade.php`** — Full conversion to Tailwind utilities

9. **`resources/views/courses/show.blade.php`** — Full conversion to Tailwind utilities

10. **`resources/views/courses/player.blade.php`** — Full conversion to Tailwind utilities (keep inline `<style>` for immersive player override)

11. **`resources/views/courses/certificate.blade.php`** — Special handling (standalone page, no layout)
    - Replace `{{ asset('css/style.css') }}` with `@vite(['resources/css/app.css'])`
    - Convert certificate-specific styles to Tailwind utilities or keep as scoped `<style>` for print

12. **`resources/views/forums/index.blade.php`** — Full conversion to Tailwind utilities

13. **`resources/views/forums/show.blade.php`** — Full conversion to Tailwind utilities

14. **`resources/views/quizzes/show.blade.php`** — Full conversion to Tailwind utilities

15. **`resources/views/quizzes/result.blade.php`** — Full conversion to Tailwind utilities

16. **`resources/views/admin/dashboard.blade.php`** — Full conversion to Tailwind utilities

17. **`resources/views/instructor/dashboard.blade.php`** — Full conversion to Tailwind utilities

18. **`resources/views/instructor/courses.blade.php`** — Full conversion to Tailwind utilities

19. **`resources/views/instructor/create_course.blade.php`** — Full conversion to Tailwind utilities

20. **`resources/views/instructor/builder.blade.php`** — Full conversion to Tailwind utilities (keep JavaScript logic intact)

### Files to Delete

21. **`public/css/style.css`** — Delete after all views are converted (682 lines of custom CSS replaced by Tailwind utilities + minimal custom CSS in `app.css`)

### Files Unchanged

- `resources/views/welcome.blade.php` — Already uses Tailwind utility classes (Laravel default)
- All PHP files (Controllers, Models, Services, Providers)
- All configuration files except CSS-related ones
- `resources/js/app.js` — No changes needed

## Functions

### New Functions/Components

No new PHP functions needed. The conversion is purely CSS/template level.

### Modified Blade Sections

Each Blade template's `@section('content')` and `@section('styles')` blocks need complete rewrites to replace:
- `class="card"` → `class="bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 shadow-sm dark:shadow-card transition-all duration-300 hover:border-primary hover:shadow-glow hover:-translate-y-1 relative overflow-hidden"`
- `class="btn btn-primary"` → `class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white rounded-lg font-semibold cursor-pointer shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(99,102,241,0.4)] transition-all duration-300"`
- Inline `style=""` attributes → Tailwind utility classes

## Classes

### Custom CSS Classes to Convert to Tailwind Utilities

| Current Class | Tailwind Utility Replacement |
|---|---|
| `.card` | `bg-white/70 dark:bg-surface/60 backdrop-blur-xl border border-gray-200 dark:border-border rounded-[14px] p-8 shadow-sm dark:shadow-card transition-all duration-300 hover:border-primary hover:shadow-glow hover:-translate-y-1` |
| `.btn` | `inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg font-semibold cursor-pointer transition-all duration-300` |
| `.btn-primary` | `bg-primary text-white shadow-[0_4px_14px_rgba(99,102,241,0.4)] hover:bg-primary-hover hover:-translate-y-0.5` |
| `.btn-secondary` | `bg-surface-solid dark:bg-surface-solid text-text-primary border border-gray-200 dark:border-border hover:bg-gray-100 dark:hover:bg-border hover:-translate-y-0.5` |
| `.btn-accent` | `bg-gradient-to-br from-primary to-accent text-white hover:-translate-y-0.5 hover:shadow-[0_5px_15px_rgba(236,72,153,0.3)]` |
| `.btn-sm` | `px-3 py-1.5 text-sm rounded-lg` |
| `.badge` | `inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider` |
| `.badge-primary` | `bg-primary/40 text-primary` |
| `.badge-success` | `bg-success/10 text-success` |
| `.badge-warning` | `bg-warning/10 text-warning` |
| `.badge-danger` | `bg-danger/10 text-danger` |
| `.form-group` | `mb-6` |
| `.form-label` | `block font-semibold mb-2 text-sm` |
| `.form-control` | `w-full py-3 px-4 bg-surface-solid dark:bg-surface-solid border border-gray-200 dark:border-border rounded-lg text-text-primary transition-all duration-300 focus:border-primary focus:shadow-[0_0_0_3px_rgba(99,102,241,0.4)]` |
| `.form-row` | `grid grid-cols-2 gap-4` |
| `.hero` | `py-32 text-center relative` |
| `.hero h1` | `text-5xl font-extrabold leading-tight mb-6 bg-gradient-to-br from-gray-800 to-gray-400 dark:from-white dark:to-gray-400 bg-clip-text text-transparent` |
| `.grid-3` | `grid grid-cols-[repeat(auto-fill,minmax(350px,1fr))] gap-8 mt-12` |
| `.grid-sidebar` | `grid grid-cols-[280px_1fr] gap-8 mt-8 max-lg:!grid-cols-1` |
| `.navbar` | `sticky top-0 z-50 bg-white/70 dark:bg-surface/60 backdrop-blur-xl border-b border-gray-200 dark:border-border py-4` |
| `.container` | `max-w-[1300px] mx-auto px-8` |
| `.progress-bar-container` | `w-full h-2 bg-gray-200 dark:bg-border rounded-full overflow-hidden my-4` |
| `.progress-bar-fill` | `h-full bg-gradient-to-r from-primary to-accent rounded-full transition-[width] duration-600` |
| `.player-container` | `grid grid-cols-[350px_1fr] h-[calc(100vh-73px)] overflow-hidden max-[900px]:!grid-cols-1 max-[900px]:!h-auto max-[900px]:!overflow-visible` |
| `.player-sidebar` | `border-r border-gray-200 dark:border-border bg-surface-solid overflow-y-auto p-6 flex flex-col gap-6` |
| `.player-content-area` | `overflow-y-auto p-10 flex flex-col gap-8` |
| `.quiz-card` | `max-w-[800px] mx-auto` |
| `.quiz-option` | `block my-3 p-4 bg-surface-solid border border-gray-200 dark:border-border rounded-lg cursor-pointer transition-all duration-300 hover:border-primary hover:bg-primary/10` |
| `.forum-reply-card` | `bg-white/70 dark:bg-surface/60 border border-gray-200 dark:border-border rounded-lg p-5 mb-4` |
| `.forum-reply-nested` | `ml-10 border-l-2 border-primary pl-5` |
| `.alert` | `py-4 px-6 rounded-lg mb-6 font-semibold` |
| `.alert-success` | `bg-success/10 text-success border border-success` |
| `.alert-danger` | `bg-danger/10 text-danger border border-danger` |
| `.theme-toggle` | `cursor-pointer text-xl bg-transparent border-none text-text-secondary hover:text-text-primary transition-all duration-300` |
| `.certificate-preview-box` | Keep as scoped CSS in certificate.blade.php (print-specific styles) |

### Classes Kept as Custom CSS in `app.css`

These require CSS features not available as Tailwind utilities:
- `.glow-backdrop-1` / `.glow-backdrop-2` — Fixed radial gradient backgrounds
- `.video-wrapper` / `.video-controls` — Complex video player overlay with hover opacity
- `.video-progress-container` / `.video-progress-bar` — Custom video progress bar
- `.certificate-preview-box` — Print-optimized certificate with `::before` pseudo-element border
- Scrollbar styling (`::-webkit-scrollbar-*`)
- Body background/transition base styles

## Dependencies

### No New Dependencies

The project already has:
- `tailwindcss` v4.0.0
- `@tailwindcss/vite` v4.0.0
- `vite` v8.0.0
- `laravel-vite-plugin` v3.1

### Font Loading

The project currently loads Google Fonts via:
1. `public/css/style.css` — `@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:...')` (to be removed)
2. `vite.config.js` — `bunny('Instrument Sans', ...)` via Laravel Vite Plugin (already configured)

After conversion, only the Vite font loading mechanism will be used. Add `@fonts` directive in the layout. Consider adding Plus Jakarta Sans to the Vite font config if needed for the certificate page.

## Testing

### Visual Verification Checklist

1. **Landing page** — Hero gradient text, course card grid, buttons, navigation
2. **Login/Register** — Form styling, validation error colors, card layout
3. **Dashboard** — Stats grid, enrolled courses with progress bars, certificates section
4. **Course show** — Sidebar layout, module accordion, enrollment card
5. **Course player** — Immersive layout, sidebar, video player controls, lesson completion
6. **Certificate** — Standalone print layout, gold badge, borders
7. **Forums** — Topic list, nested replies, form styling
8. **Quizzes** — Question cards, radio/checkbox options, result breakdown
9. **Admin dashboard** — Tables, stat cards, approve/reject buttons
10. **Instructor dashboard** — Payout history table, request form
11. **Instructor courses** — Course list, publish/unpublish buttons
12. **Instructor builder** — Module/lesson tree, modals, reorder controls
13. **Theme toggle** — Dark/light switching works on all pages
14. **Responsive** — All layouts work on mobile (grid-sidebar collapses, player adapts)
15. **Print certificate** — Print media query preserves certificate styling

### Build Verification

- Run `npm run build` to ensure Tailwind processes all templates
- Verify no CSS file references to `public/css/style.css` remain
- Check browser console for 404 errors on CSS

## Implementation Order

1. **Update `resources/css/app.css`** — Define complete theme with `@custom-variant dark`, `@theme` tokens, and minimal custom CSS for non-utility styles (glow backdrops, video player, certificate, scrollbar)
2. **Update `resources/views/layouts/app.blade.php`** — Replace CSS link with `@vite`, convert navbar/footer/main to Tailwind utilities, add `@fonts` directive
3. **Convert `resources/views/landing.blade.php`** — Hero section, course grid
4. **Convert `resources/views/auth/login.blade.php`** and `resources/views/auth/register.blade.php`** — Auth forms
5. **Convert `resources/views/dashboard.blade.php`** — Stats, enrolled courses, certificates
6. **Convert `resources/views/courses/show.blade.php`** — Course detail with sidebar
7. **Convert `resources/views/courses/player.blade.php`** — Immersive player layout
8. **Convert `resources/views/courses/certificate.blade.php`** — Standalone certificate page
9. **Convert `resources/views/forums/index.blade.php`** and `resources/views/forums/show.blade.php`** — Forum pages
10. **Convert `resources/views/quizzes/show.blade.php`** and `resources/views/quizzes/result.blade.php`** — Quiz pages
11. **Convert `resources/views/admin/dashboard.blade.php`** — Admin panel
12. **Convert `resources/views/instructor/dashboard.blade.php`**, `courses.blade.php`, `create_course.blade.php`, and `builder.blade.php`** — Instructor pages
13. **Delete `public/css/style.css`** — Remove the old CSS file
14. **Run `npm run build`** and verify the build succeeds
15. **Visual testing** — Check all pages in both dark and light themes
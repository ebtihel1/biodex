# TODO: Update home.blade.php with Futuristic Ecological Design

- [x] Replace the entire @section('content') block in resources/views/front/home.blade.php with the new advanced and futuristic design, including custom CSS for glassmorphism, animations, and modern styling.
- [x] Verify the updated file loads correctly in the browser.
- [x] If needed, add or update the hero image at public/images/eco-hero.jpg — not needed; the design reuses the existing `public/images/EarthFront.png`.

## Notes

- `front.layout` now exposes two opt-ins used by the home page:
  - `@section('hero')` overrides the default site banner (other pages keep it unchanged).
  - `@stack('head')` lets a view push styles into `<head>`.
- The layout's duplicated navbar markup (a second `#navbarNav` list plus stray
  `</div></nav>` tags) was removed and `@extends('front.navbar')` was replaced with
  `@include('front.navbar')`, so the navbar now renders once, in the right place.
- `<title>` is now driven by a `title` section (fallback `$title ?? 'Home'`).

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Slot Store â€” The Platform Behind Grocery Stores That Sell Online</title>
  <meta name="description"
    content="Slot Store is the SaaS platform grocery businesses use to build, price, promote, and run their own online stores â€” pricing engine, promotions, themes, analytics, and payments in one place.">
  <meta name="keywords"
    content="grocery ecommerce, saas for grocery stores, create grocery website, online supermarket platform, delivery software, grocery POS integration">
  <link rel="icon"
    href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>S</text></svg>">

  <link rel="canonical" href="{{ url()->current() }}">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:title" content="Slot Store â€” Start Your Online Grocery Business">
  <meta property="og:description"
    content="Slot Store is the SaaS platform grocery businesses use to build, price, promote, and run their own online stores.">
  <meta property="og:image" content="{{ asset('images/og-image.jpg') }}">

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="{{ url()->current() }}">
  <meta property="twitter:title" content="Slot Store â€” Start Your Online Grocery Business">
  <meta property="twitter:description"
    content="Slot Store is the SaaS platform grocery businesses use to build, price, promote, and run their own online stores.">
  <meta property="twitter:image" content="{{ asset('images/og-image.jpg') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    /* =========================================================
   Slot Store â€” Premium SaaS Homepage
   ========================================================= */

    :root {
      --primary: #2E7D32;
      --primary-dark: #1B5E20;
      --secondary: #43A047;
      --accent: #FFB300;
      --bg: #F8FAF7;
      --dark: #1F2937;
      --white: #FFFFFF;

      --ink: #16241A;
      --muted: #5B6B60;
      --line: rgba(31, 41, 55, 0.08);

      --radius-sm: 10px;
      --radius-md: 16px;
      --radius-lg: 24px;
      --radius-xl: 32px;

      --shadow-sm: 0 2px 8px rgba(31, 41, 55, 0.06);
      --shadow-md: 0 12px 32px rgba(31, 41, 55, 0.10);
      --shadow-lg: 0 24px 64px rgba(31, 41, 55, 0.14);

      --container: 1200px;
      --ease: cubic-bezier(.16, .84, .44, 1);
    }

    @media (prefers-reduced-motion: reduce) {

      *,
      *::before,
      *::after {
        animation-duration: 0.001ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.001ms !important;
        scroll-behavior: auto !important;
      }
    }

    * {
      box-sizing: border-box;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      color: var(--ink);
      background: var(--bg);
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }

    h1,
    h2,
    h3,
    h4 {
      font-family: 'Poppins', sans-serif;
      color: var(--dark);
      margin: 0;
      line-height: 1.15;
      letter-spacing: -0.02em;
    }

    p {
      margin: 0;
      color: var(--muted);
      line-height: 1.7;
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    ul {
      list-style: none;
      margin: 0;
      padding: 0;
    }

    img,
    svg {
      display: block;
      max-width: 100%;
    }

    button {
      font-family: inherit;
      cursor: pointer;
    }

    :focus-visible {
      outline: 3px solid var(--accent);
      outline-offset: 2px;
      border-radius: 4px;
    }

    .eyebrow {
      display: inline-block;
      font-family: 'Poppins', sans-serif;
      font-size: 0.78rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--primary);
      background: rgba(46, 125, 50, 0.08);
      padding: 6px 14px;
      border-radius: 999px;
      margin-bottom: 18px;
    }

    section {
      position: relative;
      padding: 110px 8vw;
    }

    @media (max-width: 768px) {
      section {
        padding: 72px 6vw;
      }
    }

    .section-head {
      max-width: 640px;
      margin: 0 auto 60px;
      text-align: center;
    }

    .section-head h2 {
      font-size: clamp(1.9rem, 3.2vw, 2.6rem);
      margin-bottom: 16px;
    }

    .section-head p {
      font-size: 1.05rem;
    }

    .section-head.light .eyebrow {
      background: rgba(255, 255, 255, 0.12);
      color: var(--accent);
    }

    .section-head.light h2,
    .section-head.light p {
      color: var(--white);
    }

    .section-head.light p {
      opacity: 0.75;
    }

    /* ---------- Buttons ---------- */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 24px;
      font-weight: 600;
      font-size: 0.95rem;
      border-radius: 999px;
      border: 1px solid transparent;
      transition: transform .25s var(--ease), box-shadow .25s var(--ease), background .25s var(--ease), color .25s var(--ease);
      white-space: nowrap;
      position: relative;
      overflow: hidden;
    }

    .btn-lg {
      padding: 16px 32px;
      font-size: 1.02rem;
    }

    .btn-sm {
      padding: 8px 18px;
      font-size: 0.85rem;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: var(--white);
      box-shadow: 0 8px 24px rgba(46, 125, 50, 0.28);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 14px 32px rgba(46, 125, 50, 0.36);
    }

    .btn-outline {
      border-color: rgba(31, 41, 55, 0.16);
      color: var(--dark);
      background: rgba(255, 255, 255, 0.6);
    }

    .btn-outline:hover {
      border-color: var(--primary);
      color: var(--primary);
      transform: translateY(-2px);
    }

    .btn-outline-light {
      border-color: rgba(255, 255, 255, 0.4);
      color: var(--white);
    }

    .btn-outline-light:hover {
      background: rgba(255, 255, 255, 0.12);
      transform: translateY(-2px);
    }

    .btn-ghost {
      color: var(--dark);
      font-weight: 500;
    }

    .btn-ghost:hover {
      color: var(--primary);
    }

    .btn .ripple {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.5);
      transform: scale(0);
      animation: ripple .6s var(--ease);
      pointer-events: none;
    }

    @keyframes ripple {
      to {
        transform: scale(3);
        opacity: 0;
      }
    }

    /* ---------- Header ---------- */
    .site-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      padding: 18px 8vw;
      transition: background .35s var(--ease), box-shadow .35s var(--ease), padding .35s var(--ease);
    }

    .site-header.scrolled {
      background: rgba(248, 250, 247, 0.72);
      backdrop-filter: blur(16px) saturate(160%);
      -webkit-backdrop-filter: blur(16px) saturate(160%);
      box-shadow: 0 4px 24px rgba(31, 41, 55, 0.06);
      padding: 12px 8vw;
      border-bottom: 1px solid var(--line);
    }

    .header-inner {
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 24px;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo-text {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 1.25rem;
      color: var(--dark);
    }

    .logo-accent {
      color: var(--primary);
    }

    .main-nav {
      display: flex;
      gap: 32px;
    }

    .main-nav a {
      font-size: 0.92rem;
      font-weight: 500;
      color: var(--dark);
      position: relative;
      padding: 4px 0;
    }

    .main-nav a::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: -2px;
      width: 0;
      height: 2px;
      background: var(--primary);
      transition: width .25s var(--ease);
    }

    .main-nav a:hover::after {
      width: 100%;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .nav-toggle {
      display: none;
      flex-direction: column;
      gap: 5px;
      background: none;
      border: none;
      padding: 6px;
    }

    .nav-toggle span {
      width: 24px;
      height: 2px;
      background: var(--dark);
      border-radius: 2px;
      transition: transform .3s var(--ease), opacity .3s var(--ease);
    }

    @media (max-width: 980px) {
      .main-nav {
        position: fixed;
        top: 68px;
        left: 0;
        right: 0;
        background: var(--white);
        flex-direction: column;
        padding: 20px 8vw;
        gap: 18px;
        box-shadow: var(--shadow-md);
        transform: translateY(-130%);
        opacity: 0;
        transition: transform .35s var(--ease), opacity .3s var(--ease);
      }

      .main-nav.open {
        transform: translateY(0);
        opacity: 1;
      }

      .header-actions .btn-ghost {
        display: none;
      }

      .header-actions .btn-primary {
        padding: 8px 16px;
        font-size: 0.85rem;
      }

      .nav-toggle {
        display: flex;
        margin-left: 8px;
      }

      .header-inner {
        gap: 12px;
      }
    }

    /* ---------- Hero ---------- */
    .hero {
      padding-top: 160px;
      padding-bottom: 100px;
      background: radial-gradient(ellipse at top right, rgba(67, 160, 71, 0.10), transparent 60%), var(--bg);
      overflow: hidden;
    }

    .hero-blob {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      opacity: 0.35;
      z-index: 0;
      animation: blobFloat 14s ease-in-out infinite;
    }

    .blob-a {
      width: 420px;
      height: 420px;
      background: var(--secondary);
      top: -120px;
      right: -80px;
    }

    .blob-b {
      width: 320px;
      height: 320px;
      background: var(--accent);
      bottom: -100px;
      left: -60px;
      animation-delay: -6s;
    }

    @keyframes blobFloat {

      0%,
      100% {
        transform: translate(0, 0) scale(1);
      }

      50% {
        transform: translate(30px, -30px) scale(1.08);
      }
    }

    .hero-inner {
      position: relative;
      z-index: 1;
      max-width: 1300px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1.05fr 1fr;
      gap: 60px;
      align-items: center;
    }

    @media (max-width: 980px) {
      .hero-inner {
        grid-template-columns: 1fr;
        text-align: center;
      }
    }

    .hero-copy h1 {
      font-size: clamp(2.4rem, 4.6vw, 3.6rem);
      margin: 0 0 22px;
    }

    .highlight {
      color: var(--primary);
      position: relative;
      white-space: nowrap;
    }

    .hero-sub {
      font-size: 1.12rem;
      max-width: 520px;
      margin-bottom: 34px;
    }

    @media (max-width: 980px) {
      .hero-sub {
        margin-left: auto;
        margin-right: auto;
      }
    }

    .hero-cta {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
      margin-bottom: 40px;
    }

    @media (max-width: 980px) {
      .hero-cta {
        justify-content: center;
      }
    }

    .hero-proof {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    @media (max-width: 980px) {
      .hero-proof {
        justify-content: center;
      }
    }

    .hero-proof p {
      font-size: 0.9rem;
    }

    .hero-proof strong {
      color: var(--dark);
    }

    .avatars {
      display: flex;
    }

    .avatars span {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      border: 2px solid var(--bg);
      margin-left: -10px;
    }

    .avatars span:first-child {
      margin-left: 0;
    }

    /* Hero dashboard mockup */
    .hero-visual {
      position: relative;
      min-height: 420px;
    }

    .dash-card {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.6);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-lg);
      padding: 26px;
    }

    .dash-main {
      max-width: 460px;
      margin: 0 auto;
    }

    .dash-topbar {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 20px;
    }

    .dash-dots {
      display: flex;
      gap: 6px;
    }

    .dash-dots span {
      width: 9px;
      height: 9px;
      border-radius: 50%;
      background: rgba(31, 41, 55, 0.16);
    }

    .dash-title {
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      font-size: 0.85rem;
      color: var(--muted);
    }

    .dash-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
      margin-bottom: 20px;
    }

    .dash-tile {
      background: var(--white);
      border-radius: var(--radius-sm);
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 4px;
      box-shadow: var(--shadow-sm);
      transition: transform .3s var(--ease);
    }

    .dash-tile:hover {
      transform: translateY(-3px);
    }

    .tile-label {
      font-size: 0.78rem;
      color: var(--muted);
    }

    .tile-value {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 1.4rem;
      color: var(--dark);
    }

    .tile-trend {
      font-size: 0.75rem;
      color: var(--muted);
    }

    .tile-trend.up {
      color: var(--secondary);
    }

    .dash-chart {
      background: var(--white);
      border-radius: var(--radius-sm);
      padding: 10px 4px;
      box-shadow: var(--shadow-sm);
    }

    .dash-chart svg {
      width: 100%;
      height: 90px;
    }

    .float-card {
      position: absolute;
      display: flex;
      align-items: center;
      gap: 10px;
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(14px);
      border: 1px solid rgba(255, 255, 255, 0.6);
      border-radius: var(--radius-md);
      padding: 12px 16px;
      box-shadow: var(--shadow-md);
      font-size: 0.82rem;
      animation: floaty 5s ease-in-out infinite;
    }

    .float-card strong {
      display: block;
      font-family: 'Poppins', sans-serif;
      font-size: 0.85rem;
      color: var(--dark);
    }

    .float-card small {
      color: var(--muted);
    }

    .float-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: rgba(46, 125, 50, 0.1);
      color: var(--primary);
      font-size: 0.95rem;
      flex-shrink: 0;
    }

    .float-coupon {
      top: -6%;
      left: -8%;
      animation-delay: 0s;
    }

    .float-order {
      bottom: 10%;
      right: -10%;
      animation-delay: -1.6s;
    }

    .float-rule {
      bottom: -8%;
      left: 6%;
      animation-delay: -3.2s;
    }

    @media (max-width: 980px) {
      .float-card {
        display: none;
      }
    }

    @keyframes floaty {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-10px);
      }
    }

    .section-divider {
      position: absolute;
      left: 0;
      right: 0;
      bottom: -1px;
      height: 80px;
      background: var(--white);
      clip-path: ellipse(60% 100% at 50% 100%);
    }

    /* ---------- Fade-up ---------- */
    .fade-up {
      opacity: 0;
      transform: translateY(24px);
      animation: fadeUp .8s var(--ease) forwards;
    }

    @keyframes fadeUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .reveal {
      opacity: 0;
      transform: translateY(28px);
      transition: opacity .7s var(--ease), transform .7s var(--ease);
    }

    .reveal.in-view {
      opacity: 1;
      transform: translateY(0);
    }

    /* ---------- Trusted / marquee ---------- */
    .trusted {
      background: var(--white);
      padding: 56px 8vw;
      text-align: center;
    }

    .trusted-label {
      font-size: 0.85rem;
      color: var(--muted);
      letter-spacing: 0.04em;
      margin-bottom: 26px;
      text-transform: uppercase;
    }

    .marquee {
      overflow: hidden;
      mask-image: linear-gradient(90deg, transparent, #000 10%, #000 90%, transparent);
    }

    .marquee-track {
      display: flex;
      gap: 64px;
      width: max-content;
      animation: marquee 24s linear infinite;
    }

    .marquee-track span {
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      font-size: 1.2rem;
      color: rgba(31, 41, 55, 0.28);
      white-space: nowrap;
    }

    @keyframes marquee {
      from {
        transform: translateX(0);
      }

      to {
        transform: translateX(-50%);
      }
    }

    /* ---------- Features ---------- */
    .features {
      background: var(--bg);
    }

    .feature-grid {
      max-width: var(--container);
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 22px;
    }

    @media (max-width: 1080px) {
      .feature-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 560px) {
      .feature-grid {
        grid-template-columns: 1fr;
      }
    }

    .feature-card {
      background: var(--white);
      border: 1px solid var(--line);
      border-radius: var(--radius-md);
      padding: 28px 24px;
      transition: transform .3s var(--ease), box-shadow .3s var(--ease), border-color .3s var(--ease);
    }

    .feature-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-md);
      border-color: rgba(46, 125, 50, 0.3);
    }

    .f-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 48px;
      height: 48px;
      margin-bottom: 16px;
      border-radius: var(--radius-sm);
      background: rgba(46, 125, 50, 0.09);
      color: var(--primary);
      font-size: 1.15rem;
    }

    .feature-card h3 {
      font-size: 1.02rem;
      margin-bottom: 8px;
    }

    .feature-card p {
      font-size: 0.9rem;
    }

    /* ---------- Pricing engine ---------- */
    .pricing-engine {
      background: var(--white);
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
      max-width: var(--container);
      margin: 0 auto;
    }

    @media (max-width: 900px) {
      .pricing-engine {
        grid-template-columns: 1fr;
      }
    }

    .engine-copy h2 {
      font-size: clamp(1.8rem, 3vw, 2.4rem);
      margin: 0 0 14px;
    }

    .engine-copy p {
      margin-bottom: 24px;
      max-width: 460px;
    }

    .engine-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .engine-list li {
      font-weight: 600;
      font-family: 'Poppins', sans-serif;
      font-size: 0.95rem;
      color: var(--dark);
      padding-left: 26px;
      position: relative;
    }

    .engine-list li::before {
      content: '\2713';
      position: absolute;
      left: 0;
      top: 0;
      color: var(--primary);
      font-weight: 700;
    }

    .rule-card {
      background: var(--bg);
      border: 1px solid var(--line);
      border-radius: var(--radius-lg);
      padding: 10px;
      box-shadow: var(--shadow-md);
    }

    .rule-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 18px;
      border-radius: var(--radius-sm);
      transition: background .3s var(--ease);
    }

    .rule-row.active {
      background: var(--white);
      box-shadow: var(--shadow-sm);
    }

    .rule-tag {
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      font-size: 0.85rem;
      padding: 6px 14px;
      border-radius: 999px;
      color: var(--white);
    }

    .rule-tag.retail {
      background: #6B7280;
    }

    .rule-tag.group {
      background: var(--secondary);
    }

    .rule-tag.wholesale {
      background: var(--primary);
    }

    .rule-tag.bulk {
      background: var(--accent);
      color: var(--dark);
    }

    .rule-tag.regional {
      background: var(--primary-dark);
    }

    .rule-price {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      color: var(--dark);
    }

    /* ---------- Promotions ---------- */
    .promotions {
      background: var(--bg);
    }

    .promo-grid {
      max-width: var(--container);
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
    }

    @media (max-width: 1080px) {
      .promo-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 560px) {
      .promo-grid {
        grid-template-columns: 1fr;
      }
    }

    .promo-card {
      background: linear-gradient(160deg, var(--white), #F1F7EF);
      border: 1px solid var(--line);
      border-radius: var(--radius-md);
      padding: 26px;
      text-align: center;
      transition: transform .3s var(--ease), box-shadow .3s var(--ease);
    }

    .promo-card:hover {
      transform: translateY(-6px) scale(1.02);
      box-shadow: var(--shadow-md);
    }

    .p-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 48px;
      height: 48px;
      margin: 0 auto 14px;
      border-radius: 50%;
      background: rgba(255, 179, 0, 0.14);
      color: #B45309;
      font-size: 1.15rem;
    }

    .promo-card h3 {
      font-size: 1rem;
      margin-bottom: 6px;
    }

    .promo-card p {
      font-size: 0.86rem;
    }

    @media (max-width: 560px) {
      .promo-card {
        text-align: left;
        display: grid;
        grid-template-columns: 48px 1fr;
        gap: 16px;
        align-items: center;
        padding: 20px;
        background: linear-gradient(135deg, var(--white), #f8faf7);
      }

      .p-icon {
        margin: 0;
        grid-row: 1 / span 2;
        align-self: start;
        box-shadow: 0 4px 12px rgba(255, 179, 0, 0.15);
      }

      .promo-card h3 {
        margin-bottom: 2px;
        align-self: end;
      }

      .promo-card p {
        align-self: start;
      }
    }

    /* ---------- Product management ---------- */
    .product-mgmt {
      background: var(--white);
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
      max-width: var(--container);
      margin: 0 auto;
    }

    @media (max-width: 900px) {
      .product-mgmt {
        grid-template-columns: 1fr;
      }
    }

    .pm-card {
      background: var(--bg);
      border-radius: var(--radius-lg);
      border: 1px solid var(--line);
      padding: 20px;
      box-shadow: var(--shadow-md);
    }

    .pm-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 10px;
      border-bottom: 1px solid var(--line);
      font-size: 0.92rem;
    }

    .pm-row i {
      width: 20px;
      margin-right: 8px;
      color: var(--primary);
      text-align: center;
    }

    .pm-search i {
      color: var(--muted);
    }

    .pm-row:last-of-type {
      border-bottom: none;
    }

    .pm-stock {
      font-size: 0.78rem;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 999px;
    }

    .pm-stock.ok {
      color: var(--primary);
      background: rgba(46, 125, 50, 0.1);
    }

    .pm-stock.low {
      color: #B45309;
      background: rgba(255, 179, 0, 0.16);
    }

    .pm-search {
      margin-top: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--white);
      border-radius: 999px;
      padding: 10px 16px;
      font-size: 0.85rem;
      color: var(--muted);
      box-shadow: var(--shadow-sm);
    }

    .pm-copy h2 {
      font-size: clamp(1.8rem, 3vw, 2.4rem);
      margin-bottom: 20px;
    }

    .pm-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .pm-tags span {
      background: var(--bg);
      border: 1px solid var(--line);
      font-size: 0.85rem;
      font-weight: 500;
      padding: 8px 16px;
      border-radius: 999px;
    }

    /* ---------- Analytics ---------- */
    .analytics {
      background: var(--dark);
    }

    .analytics-grid {
      max-width: var(--container);
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 18px;
    }

    @media (max-width: 980px) {
      .analytics-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 560px) {
      .analytics-grid {
        grid-template-columns: 1fr;
      }
    }

    .an-card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: var(--radius-md);
      padding: 22px;
      color: var(--white);
      transition: transform .3s var(--ease), background .3s var(--ease);
    }

    .an-card:hover {
      transform: translateY(-4px);
      background: rgba(255, 255, 255, 0.08);
    }

    .an-label {
      display: block;
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.55);
      margin-bottom: 10px;
    }

    .an-value {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 1.5rem;
    }

    .an-big {
      grid-column: span 2;
    }

    .an-chart {
      width: 100%;
      height: 90px;
    }

    .an-bars .bar-row {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 10px;
      font-size: 0.78rem;
      color: rgba(255, 255, 255, 0.7);
    }

    .bar-row span {
      width: 74px;
      flex-shrink: 0;
    }

    .bar-row i {
      flex: 1;
      height: 6px;
      border-radius: 4px;
      background: rgba(255, 255, 255, 0.1);
      position: relative;
      overflow: hidden;
    }

    .bar-row i::after {
      content: '';
      position: absolute;
      inset: 0;
      width: var(--w);
      background: linear-gradient(90deg, var(--secondary), var(--accent));
      border-radius: 4px;
      transform: scaleX(0);
      transform-origin: left;
      transition: transform 1s var(--ease);
    }

    .an-bars.in-view i::after,
    .in-view .bar-row i::after {
      transform: scaleX(1);
    }

    @media (max-width: 980px) {
      .an-big {
        grid-column: span 2;
      }
    }

    @media (max-width: 560px) {
      .an-big {
        grid-column: span 1;
      }
    }

    /* ---------- Themes ---------- */
    .themes {
      background: var(--bg);
    }

    .theme-grid {
      max-width: var(--container);
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
    }

    @media (max-width: 900px) {
      .theme-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 560px) {
      .theme-grid {
        grid-template-columns: 1fr;
      }
    }

    .theme-card {
      background: var(--white);
      border: 1px solid var(--line);
      border-radius: var(--radius-md);
      padding: 16px;
      text-align: center;
      transition: transform .3s var(--ease), box-shadow .3s var(--ease);
    }

    .theme-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-md);
    }

    .theme-preview {
      height: 140px;
      border-radius: var(--radius-sm);
      margin-bottom: 14px;
    }

    .theme-fresh {
      background: linear-gradient(135deg, #C8E6C9, #43A047);
    }

    .theme-luxury {
      background: linear-gradient(135deg, #2C2C2C, #FFB300);
    }

    .theme-organic {
      background: linear-gradient(135deg, #F1F8E9, #7CB342);
    }

    .theme-minimal {
      background: linear-gradient(135deg, #F5F5F5, #BDBDBD);
    }

    .theme-dark {
      background: linear-gradient(135deg, #1F2937, #374151);
    }

    .theme-modern {
      background: linear-gradient(135deg, #E8F5E9, #2E7D32);
    }

    .theme-card h3 {
      font-size: 1rem;
      margin-bottom: 12px;
    }

    /* ---------- Timeline ---------- */
    .timeline-section {
      background: var(--white);
    }

    .timeline {
      max-width: var(--container);
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(8, 1fr);
      gap: 16px;
      position: relative;
    }

    .timeline::before {
      content: '';
      position: absolute;
      top: 22px;
      left: 4%;
      right: 4%;
      height: 2px;
      background: var(--line);
    }

    @media (max-width: 980px) {
      .timeline {
        grid-template-columns: repeat(4, 1fr);
      }

      .timeline::before {
        display: none;
      }
    }

    @media (max-width: 560px) {
      .timeline {
        grid-template-columns: 1fr;
        gap: 32px;
        position: relative;
      }

      .timeline::before {
        content: '';
        position: absolute;
        left: 22px;
        top: 20px;
        bottom: 20px;
        width: 2px;
        background: rgba(46, 125, 50, 0.2);
        z-index: 0;
      }
    }

    .timeline-step {
      text-align: center;
      position: relative;
    }

    .step-num {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: var(--white);
      border: 2px solid var(--primary);
      color: var(--primary);
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      margin-bottom: 14px;
      position: relative;
      z-index: 1;
    }

    .timeline-step h4 {
      font-size: 0.92rem;
      margin-bottom: 6px;
    }

    .timeline-step p {
      font-size: 0.8rem;
    }

    @media (max-width: 560px) {
      .timeline-step {
        display: grid;
        grid-template-columns: 44px 1fr;
        gap: 20px;
        text-align: left;
        align-items: center;
      }

      .step-num {
        margin-bottom: 0;
        grid-row: 1 / span 2;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.15);
      }

      .timeline-step h4 {
        margin-bottom: 2px;
        align-self: end;
      }

      .timeline-step p {
        align-self: start;
      }
    }

    /* ---------- Payments ---------- */
    .payments {
      background: var(--bg);
    }

    .payment-grid {
      max-width: var(--container);
      margin: 0 auto;
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 14px;
    }

    .pay-chip {
      background: var(--white);
      border: 1px solid var(--line);
      border-radius: 999px;
      padding: 12px 24px;
      font-weight: 600;
      font-family: 'Poppins', sans-serif;
      font-size: 0.9rem;
      color: var(--dark);
      transition: transform .3s var(--ease), border-color .3s var(--ease), color .3s var(--ease);
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }

    .pay-chip:hover {
      transform: translateY(-3px);
      border-color: var(--primary);
      color: var(--primary);
    }

    .pay-chip svg {
      width: 20px;
      height: 20px;
      flex-shrink: 0;
      display: block;
      transition: transform .3s var(--ease);
    }

    .pay-chip:hover svg {
      transform: scale(1.15);
    }

    /* ---------- Comparison ---------- */
    .comparison {
      background: var(--white);
    }

    .compare-table-wrap {
      max-width: 900px;
      margin: 0 auto;
      overflow-x: auto;
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-sm);
    }

    .compare-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.92rem;
    }

    .compare-table th,
    .compare-table td {
      padding: 16px 20px;
      text-align: left;
      border-bottom: 1px solid var(--line);
    }

    .compare-table th {
      font-family: 'Poppins', sans-serif;
      background: var(--bg);
      font-weight: 600;
    }

    .compare-table td:first-child {
      font-weight: 600;
      color: var(--dark);
    }

    .compare-table td.win {
      color: var(--primary);
      font-weight: 600;
    }

    .compare-table tbody tr:hover {
      background: rgba(46, 125, 50, 0.03);
    }

    /* ---------- Testimonials ---------- */
    .testimonials {
      background: var(--bg);
      text-align: center;
    }

    .testi-carousel {
      max-width: 680px;
      margin: 0 auto;
      position: relative;
      min-height: 200px;
    }

    .testi-slide {
      position: absolute;
      inset: 0;
      opacity: 0;
      transform: translateY(10px);
      transition: opacity .5s var(--ease), transform .5s var(--ease);
      pointer-events: none;
    }

    .testi-slide.active {
      opacity: 1;
      transform: translateY(0);
      position: relative;
      pointer-events: auto;
    }

    .testi-quote {
      font-family: 'Poppins', sans-serif;
      font-size: 1.2rem;
      color: var(--dark);
      font-weight: 500;
      margin-bottom: 26px;
    }

    .testi-author {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-bottom: 10px;
    }

    .testi-avatar {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--secondary), var(--primary));
      color: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-family: 'Poppins', sans-serif;
    }

    .testi-author div {
      text-align: left;
    }

    .testi-author strong {
      display: block;
      font-size: 0.92rem;
      color: var(--dark);
    }

    .testi-author small {
      color: var(--muted);
    }

    .testi-stars {
      color: var(--accent);
      letter-spacing: 2px;
    }

    .testi-dots {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 30px;
    }

    .testi-dots button {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      border: none;
      background: rgba(31, 41, 55, 0.18);
      transition: background .3s var(--ease), transform .3s var(--ease);
    }

    .testi-dots button.active {
      background: var(--primary);
      transform: scale(1.3);
    }

    /* ---------- Plans ---------- */
    .plans {
      background: var(--white);
    }

    .plan-grid {
      max-width: var(--container);
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 28px;
      align-items: stretch;
    }

    @media (max-width: 900px) {
      .plan-grid {
        grid-template-columns: 1fr;
      }
    }

    .plan-card {
      background: var(--bg);
      border: 1px solid var(--line);
      border-radius: var(--radius-lg);
      padding: 36px 30px;
      display: flex;
      flex-direction: column;
      transition: transform .3s var(--ease), box-shadow .3s var(--ease);
    }

    .plan-card:hover {
      transform: translateY(-6px);
      box-shadow: var(--shadow-md);
    }

    .plan-card.featured {
      background: var(--dark);
      color: var(--white);
      position: relative;
      border: none;
      box-shadow: var(--shadow-lg);
    }

    .plan-card.featured::before {
      content: '';
      position: absolute;
      inset: -2px;
      border-radius: var(--radius-lg);
      padding: 2px;
      background: linear-gradient(135deg, var(--accent), var(--secondary));
      -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;
      z-index: -1;
    }

    .plan-badge {
      position: absolute;
      top: -14px;
      left: 50%;
      transform: translateX(-50%);
      background: var(--accent);
      color: var(--dark);
      font-size: 0.75rem;
      font-weight: 700;
      padding: 6px 16px;
      border-radius: 999px;
      font-family: 'Poppins', sans-serif;
    }

    .plan-card h3 {
      font-size: 1.3rem;
      margin-bottom: 6px;
    }

    .plan-card.featured h3 {
      color: var(--white);
    }

    .plan-desc {
      font-size: 0.88rem;
      margin-bottom: 20px;
    }

    .plan-card.featured .plan-desc {
      color: rgba(255, 255, 255, 0.65);
    }

    .plan-price {
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 2.4rem;
      margin-bottom: 24px;
      color: var(--dark);
    }

    .plan-card.featured .plan-price {
      color: var(--white);
    }

    .plan-price span {
      font-size: 1rem;
      font-weight: 500;
      color: var(--muted);
    }

    .plan-card ul {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 28px;
      flex: 1;
    }

    .plan-card li {
      font-size: 0.9rem;
      padding-left: 24px;
      position: relative;
    }

    .plan-card li::before {
      content: '\2713';
      position: absolute;
      left: 0;
      color: var(--secondary);
      font-weight: 700;
    }

    .plan-card.featured li {
      color: rgba(255, 255, 255, 0.85);
    }

    /* ---------- FAQ ---------- */
    .faq {
      background: var(--bg);
    }

    .faq-list {
      max-width: 760px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .faq-item {
      background: var(--white);
      border: 1px solid var(--line);
      border-radius: var(--radius-md);
      overflow: hidden;
    }

    .faq-question {
      width: 100%;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 24px;
      background: none;
      border: none;
      font-family: 'Poppins', sans-serif;
      font-weight: 600;
      font-size: 0.98rem;
      color: var(--dark);
      text-align: left;
    }

    .faq-toggle {
      font-size: 1.2rem;
      color: var(--primary);
      transition: transform .3s var(--ease);
    }

    .faq-item.open .faq-toggle {
      transform: rotate(45deg);
    }

    .faq-answer {
      max-height: 0;
      overflow: hidden;
      transition: max-height .4s var(--ease), padding .4s var(--ease);
      padding: 0 24px;
    }

    .faq-item.open .faq-answer {
      max-height: 220px;
      padding: 0 24px 22px;
    }

    .faq-answer p {
      font-size: 0.92rem;
    }

    /* ---------- Final CTA ---------- */
    .final-cta {
      background: linear-gradient(135deg, var(--primary-dark), var(--primary) 55%, var(--secondary));
      text-align: center;
      padding: 120px 8vw;
    }

    .final-cta h2 {
      color: var(--white);
      font-size: clamp(2rem, 3.6vw, 2.8rem);
      margin-bottom: 18px;
    }

    .final-cta p {
      color: rgba(255, 255, 255, 0.82);
      max-width: 520px;
      margin: 0 auto 36px;
      font-size: 1.05rem;
    }

    .final-cta .hero-cta {
      justify-content: center;
    }

    /* ---------- Footer ---------- */
    .site-footer {
      background: var(--dark);
      color: rgba(255, 255, 255, 0.7);
      padding: 80px 8vw 30px;
    }

    .footer-top {
      max-width: 1300px;
      margin: 0 auto 50px;
      display: grid;
      grid-template-columns: 1.6fr repeat(4, 1fr);
      gap: 32px;
    }

    @media (max-width: 980px) {
      .footer-top {
        grid-template-columns: repeat(3, 1fr);
      }

      .footer-brand {
        grid-column: 1 / -1;
      }
    }

    @media (max-width: 620px) {
      .footer-top {
        grid-template-columns: repeat(2, 1fr);
      }

      .footer-col {
        margin-bottom: 24px;
      }
    }

    .footer-brand p {
      color: rgba(255, 255, 255, 0.55);
      font-size: 0.88rem;
      margin: 14px 0 22px;
      max-width: 280px;
    }

    .newsletter {
      display: flex;
      gap: 8px;
      max-width: 320px;
    }

    .newsletter input {
      flex: 1;
      padding: 11px 14px;
      border-radius: 999px;
      border: 1px solid rgba(255, 255, 255, 0.16);
      background: rgba(255, 255, 255, 0.06);
      color: var(--white);
      font-size: 0.86rem;
    }

    .newsletter input::placeholder {
      color: rgba(255, 255, 255, 0.4);
    }

    .footer-col h4 {
      color: var(--white);
      font-size: 0.9rem;
      margin-bottom: 16px;
    }

    .footer-col {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .footer-col a {
      font-size: 0.88rem;
      color: rgba(255, 255, 255, 0.55);
      transition: color .25s var(--ease);
    }

    .footer-col a:hover {
      color: var(--accent);
    }

    .footer-bottom {
      max-width: 1300px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 26px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      font-size: 0.82rem;
      flex-wrap: wrap;
      gap: 16px;
    }

    .footer-bottom-left {
      display: flex;
      align-items: center;
      gap: 24px;
      flex-wrap: wrap;
    }

    .footer-legal-links {
      display: flex;
      gap: 16px;
    }

    .footer-legal-links a {
      color: rgba(255, 255, 255, 0.55);
      transition: color .25s var(--ease);
    }

    .footer-legal-links a:hover {
      color: var(--white);
    }

    .social-icons {
      display: flex;
      gap: 14px;
    }

    .social-icons a {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.06);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.75);
      transition: background .25s var(--ease), transform .25s var(--ease), color .25s var(--ease);
    }

    .social-icons a:hover {
      color: var(--white);
    }

    .social-icons a:hover {
      background: var(--primary);
      transform: translateY(-3px);
    }
  </style>
  <!-- JSON-LD Structured Data for SEO -->
  <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "SoftwareApplication",
      "name": "Slot Store",
      "applicationCategory": "BusinessApplication",
      "operatingSystem": "Web",
      "description": "Slot Store is the SaaS platform grocery businesses use to build, price, promote, and run their own online stores.",
      "url": "{{ url()->current() }}",
      "publisher": {
        "@@type": "Organization",
        "name": "Slot Store"
      }
    }
  </script>
</head>

<body>

  <!-- ================= HEADER ================= -->
  <header class="site-header" id="siteHeader">
    <div class="header-inner">
      <a href="#" class="logo" aria-label="Slot Store home">
        <span class="logo-mark" aria-hidden="true">
          <svg viewBox="0 0 32 32" width="30" height="30">
            <rect x="3" y="10" width="26" height="18" rx="4" fill="var(--primary)" />
            <rect x="3" y="10" width="26" height="6" rx="3" fill="var(--accent)" />
            <path d="M9 10 L11 4 H21 L23 10" stroke="var(--primary)" stroke-width="2.4" fill="none"
              stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </span>
        <span class="logo-text">Slot<span class="logo-accent">Store</span></span>
      </a>

      <nav class="main-nav" id="mainNav" aria-label="Primary">
        <a href="{{ route('landing.old') }}">Home</a>
        <a href="{{ route('landing.features') }}">Features</a>
        <a href="{{ route('landing.solutions') }}">Solutions</a>
        <a href="{{ route('landing.pricing') }}">Pricing</a>
        <a href="{{ route('landing.themes') }}">Themes</a>
        <a href="{{ route('landing.analytics') }}">Analytics</a>
        <a href="{{ route('landing.contact') }}">Contact</a>
      </nav>

      <div class="header-actions">
        <a href="{{ route('admin.common.login') }}" class="btn btn-ghost">Log in</a>
        <a href="javascript:void(0)" class="btn btn-primary pricing-btn-trigger" data-plan="sprout">Get Started</a>
      </div>

      <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <!-- ================= HERO ================= -->
@yield('content')
  <footer class="site-footer" id="contact">
    <div class="footer-top">
      <div class="footer-brand">
        <a href="#" class="logo text-decoration-none" aria-label="Slot Store home" style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
          <span class="logo-mark" aria-hidden="true">
            <svg viewBox="0 0 32 32" width="30" height="30">
              <rect x="3" y="10" width="26" height="18" rx="4" fill="var(--primary)" />
              <rect x="3" y="10" width="26" height="6" rx="3" fill="var(--accent)" />
              <path d="M9 10 L11 4 H21 L23 10" stroke="var(--primary)" stroke-width="2.4" fill="none"
                stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
          <span class="logo-text text-white" style="color: white;">Slot<span class="logo-accent">Store</span></span>
        </a>
        <p>The platform grocery businesses use to build and run their own online stores.</p>
        <form class="newsletter" id="newsletterForm">
          <input type="email" placeholder="Your work email" required aria-label="Email address for newsletter">
          <button type="submit" class="btn btn-primary">Subscribe</button>
        </form>
      </div>
      <div class="footer-col">
        <h4>Company</h4>
        <a href="{{ route('landing.about') }}">About</a><a href="#">Careers</a><a href="#">Press</a><a href="{{ route('landing.blogs') }}">Blog</a>
      </div>
      <div class="footer-col">
        <h4>Platform</h4>
        <a href="{{ route('landing.features') }}">Features</a><a href="{{ route('landing.analytics') }}">Analytics</a><a href="{{ route('landing.themes') }}">Themes</a><a
          href="{{ route('landing.pricing') }}">Pricing</a>
      </div>
      <div class="footer-col">
        <h4>Solutions</h4>
        <a href="#">Independent Grocers</a><a href="#">Grocery Chains</a><a href="#">Farmers Markets</a>
      </div>
      <div class="footer-col">
        <h4>Resources</h4>
        <a href="#">Help Center</a><a href="#">API Docs</a><a href="#">Guides</a>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="footer-bottom-left">
        <span>Â© 2026 Slot Store. All rights reserved.</span>
        <div class="footer-legal-links">
          <a href="{{ route('landing.privacy') }}">Privacy</a>
          <a href="{{ route('landing.terms') }}">Terms</a>
          <a href="#">Security</a>
        </div>
      </div>
      <div class="social-icons" aria-label="Social media">
        <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
      </div>
    </div>
  </footer>

  <script>
    (function () {
      'use strict';

      /* ---------- Sticky glass header ---------- */
      const header = document.getElementById('siteHeader');
      const onScroll = () => {
        if (window.scrollY > 24) header.classList.add('scrolled');
        else header.classList.remove('scrolled');
      };
      document.addEventListener('scroll', onScroll, { passive: true });
      onScroll();

      /* ---------- Mobile nav toggle ---------- */
      const navToggle = document.getElementById('navToggle');
      const mainNav = document.getElementById('mainNav');
      navToggle.addEventListener('click', () => {
        const open = mainNav.classList.toggle('open');
        navToggle.setAttribute('aria-expanded', open);
        navToggle.classList.toggle('active', open);
      });
      mainNav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
          mainNav.classList.remove('open');
          navToggle.setAttribute('aria-expanded', 'false');
        });
      });

      /* ---------- Reveal-on-scroll ---------- */
      const revealTargets = document.querySelectorAll(
        '.feature-card, .promo-card, .theme-card, .an-card, .timeline-step, .plan-card, .faq-item, .pay-chip, .section-head, .rule-card, .pm-card, .compare-table-wrap'
      );
      revealTargets.forEach((el) => el.classList.add('reveal'));

      const io = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add('in-view');
              io.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.15 }
      );
      revealTargets.forEach((el) => io.observe(el));

      /* ---------- Animated counters ---------- */
      const counters = document.querySelectorAll('[data-count]');
      const animateCounter = (el) => {
        const target = parseInt(el.getAttribute('data-count'), 10);
        const prefix = el.getAttribute('data-prefix') || '';
        const duration = 1400;
        const start = performance.now();
        const step = (now) => {
          const progress = Math.min((now - start) / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          const value = Math.floor(eased * target);
          el.textContent = prefix + value.toLocaleString();
          if (progress < 1) requestAnimationFrame(step);
          else el.textContent = prefix + target.toLocaleString();
        };
        requestAnimationFrame(step);
      };
      const counterIO = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              animateCounter(entry.target);
              counterIO.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.4 }
      );
      counters.forEach((el) => counterIO.observe(el));

      /* ---------- Testimonial carousel ---------- */
      const slides = document.querySelectorAll('.testi-slide');
      const dotsWrap = document.getElementById('testiDots');
      let activeSlide = 0;
      let testiTimer;

      slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.setAttribute('aria-label', 'Show testimonial ' + (i + 1));
        if (i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => showSlide(i, true));
        dotsWrap.appendChild(dot);
      });
      const dots = dotsWrap.querySelectorAll('button');

      function showSlide(index, manual) {
        slides[activeSlide].classList.remove('active');
        dots[activeSlide].classList.remove('active');
        activeSlide = index;
        slides[activeSlide].classList.add('active');
        dots[activeSlide].classList.add('active');
        if (manual) restartTestiTimer();
      }
      function nextSlide() { showSlide((activeSlide + 1) % slides.length, false); }
      function restartTestiTimer() {
        clearInterval(testiTimer);
        testiTimer = setInterval(nextSlide, 5500);
      }
      if (slides.length) restartTestiTimer();

      /* ---------- FAQ accordion ---------- */
      document.querySelectorAll('.faq-item').forEach((item) => {
        const btn = item.querySelector('.faq-question');
        btn.addEventListener('click', () => {
          const isOpen = item.classList.contains('open');
          document.querySelectorAll('.faq-item.open').forEach((other) => {
            if (other !== item) {
              other.classList.remove('open');
              other.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            }
          });
          item.classList.toggle('open', !isOpen);
          btn.setAttribute('aria-expanded', String(!isOpen));
        });
      });

      /* ---------- Button ripple effect ---------- */
      document.querySelectorAll('.btn').forEach((btn) => {
        btn.addEventListener('click', function (e) {
          const rect = btn.getBoundingClientRect();
          const ripple = document.createElement('span');
          const size = Math.max(rect.width, rect.height);
          ripple.className = 'ripple';
          ripple.style.width = ripple.style.height = size + 'px';
          ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
          ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
          btn.appendChild(ripple);
          setTimeout(() => ripple.remove(), 650);
        });
      });

      /* ---------- Newsletter form ---------- */
      const newsletterForm = document.getElementById('newsletterForm');
      if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
          e.preventDefault();
          const input = newsletterForm.querySelector('input');
          const btn = newsletterForm.querySelector('button');
          const original = btn.textContent;
          btn.innerHTML = 'Subscribed <i class="fa-solid fa-check"></i>';
          input.value = '';
          setTimeout(() => { btn.textContent = original; }, 2200);
        });
      }

      /* ---------- Smooth anchor scroll offset ---------- */
      document.querySelectorAll('a[href^="#"]').forEach((link) => {
        link.addEventListener('click', (e) => {
          const id = link.getAttribute('href');
          if (id.length > 1) {
            const target = document.querySelector(id);
            if (target) {
              e.preventDefault();
              const y = target.getBoundingClientRect().top + window.pageYOffset - 90;
              window.scrollTo({ top: y, behavior: 'smooth' });
            }
          }
        });
      });
    })();
  </script>

  <!-- SAAS SIGN-UP MODAL -->
  <div class="saas-modal-overlay" id="saasModal">
    <div class="saas-modal-card">
      <button class="saas-modal-close" id="closeSaasBtn" aria-label="Close Registration">&times;</button>

      <!-- STAGE 2: Sign-Up Form (Multi-Step Onboarding) -->
      <div id="saasStageSignUp" class="saas-stage active">
        <form id="saasRegisterForm" onsubmit="handleSaasRegister(event)">

          <!-- STEP 1: Tell us about yourself -->
          <div id="saasFormStep1" class="saas-form-step active">
            <div class="saas-modal-header">
              <span class="saas-badge class-plan-badge">Starter</span>
              <h2>Tell us about yourself</h2>
              <p class="saas-lead">Provide your details to initiate your premium grocery store</p>
            </div>

            <div class="saas-form-group">
              <label for="saas_name">Full Name *</label>
              <input type="text" id="saas_name" placeholder="e.g. Priya Sharma" required />
              <span class="saas-error" id="err_name"></span>
            </div>

            <div class="saas-form-group">
              <label for="saas_email">Email Address *</label>
              <input type="email" id="saas_email" placeholder="you@yourbrand.com" required />
              <span class="saas-error" id="err_email"></span>
            </div>
          </div>

          <!-- STEP 2: Tell us about your brand -->
          <div id="saasFormStep2" class="saas-form-step">
            <div class="saas-modal-header">
              <span class="saas-badge class-plan-badge">Starter</span>
              <h2>Tell us about your brand</h2>
              <p class="saas-lead">We will optimize your dashboard tailored to your company goals</p>
            </div>

            <div class="saas-form-group">
              <label for="saas_business">Business / Brand Name *</label>
              <input type="text" id="saas_business" placeholder="e.g. Noir Atelier" required />
              <span class="saas-error" id="err_business"></span>
            </div>

            <div class="saas-form-grid">
              <div class="saas-form-group">
                <label for="saas_country">Country *</label>
                <select id="saas_country" required>
                  <option value="" disabled selected>Select country</option>
                  <option value="India">India</option>
                  <option value="United Arab Emirates">United Arab Emirates</option>
                  <option value="Saudi Arabia">Saudi Arabia</option>
                  <option value="United Kingdom">United Kingdom</option>
                  <option value="United States">United States</option>
                  <option value="France">France</option>
                  <option value="Singapore">Singapore</option>
                  <option value="Australia">Australia</option>
                  <option value="Other">Other</option>
                </select>
                <span class="saas-error" id="err_country"></span>
              </div>

              <div class="saas-form-group">
                <label for="saas_whatsapp">WhatsApp Number *</label>
                <div class="saas-phone-input-wrapper">
                  <select id="saas_phone_code" class="saas-phone-code">
                    <option value="+91" selected>ðŸ‡®ðŸ‡³ +91</option>
                    <option value="+971">ðŸ‡¦ðŸ‡ª +971</option>
                    <option value="+966">ðŸ‡¸ðŸ‡¦ +966</option>
                    <option value="+44">ðŸ‡¬ðŸ‡§ +44</option>
                    <option value="+1">ðŸ‡ºðŸ‡¸ +1</option>
                    <option value="+33">ðŸ‡«ðŸ‡· +33</option>
                    <option value="+65">ðŸ‡¸ðŸ‡¬ +65</option>
                    <option value="+61">ðŸ‡¦ðŸ‡º +61</option>
                  </select>
                  <input type="tel" id="saas_whatsapp" placeholder="98765 43210" required />
                </div>
                <span class="saas-error" id="err_whatsapp"></span>
              </div>
            </div>
          </div>

          <!-- STEP 3: Configure your credentials & theme -->
          <div id="saasFormStep3" class="saas-form-step">
            <div class="saas-modal-header">
              <span class="saas-badge class-plan-badge">Starter</span>
              <h2>Configure store settings</h2>
              <p class="saas-lead">Secure your brand dashboard and select your pricing plan</p>
            </div>

            <div class="saas-form-grid">
              <div class="saas-form-group">
                <label for="saas_password">Password *</label>
                <input type="password" id="saas_password" name="password" autocomplete="new-password"
                  placeholder="At least 8 characters" required />
                <span class="saas-error" id="err_password"></span>
              </div>

              <div class="saas-form-group">
                <label for="saas_confirm_password">Confirm Password *</label>
                <input type="password" id="saas_confirm_password" name="password_confirmation"
                  autocomplete="new-password" placeholder="Re-enter password" required />
                <span class="saas-error" id="err_confirm_password"></span>
              </div>
            </div>


            <div class="saas-form-grid">
              <div class="saas-form-group" style="grid-column: 1 / -1;">
                <label for="saas_plan">Plan Selection (Optional)</label>
                <select id="saas_plan">
                  <option value="sprout">Sprout â€” $9/month (20 products)</option>
                  <option value="maison">Maison â€” $19/month (100 products)</option>
                  <option value="heritage">Heritage â€” $49/month (Unlimited)</option>
                  <option value="not_sure">Not sure yet</option>
                </select>
              </div>
              <input type="hidden" id="saas_theme" value="aura_luxe" />
            </div>
          </div>

          <!-- PROGRESS STEPS NAVIGATION BAR -->
          <div class="saas-step-nav">
            <div class="saas-dots" id="saasStepDots">
              <span class="saas-dot active" onclick="goToStep(1)"></span>
              <span class="saas-dot" onclick="goToStep(2)"></span>
              <span class="saas-dot" onclick="goToStep(3)"></span>
            </div>

            <div class="saas-nav-actions">
              <button type="button" class="saas-back-btn" id="saasBackBtn" onclick="prevStep()">Back</button>
              <button type="button" class="saas-next-btn" id="saasNextBtn" onclick="handleStepNavNext()">Next</button>
            </div>
          </div>

        </form>
      </div>

      <!-- STAGE 3: Email Verification -->
      <div id="saasStageVerify" class="saas-stage">
        <div class="saas-verify-container">
          <div class="saas-verify-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
              <polyline points="22,6 12,13 2,6" />
            </svg>
          </div>

          <h2>Check your inbox</h2>
          <p class="saas-verify-desc">We've sent a verification link to <strong id="verifyEmailDisplay">your
              email</strong>. Please click the link to verify your email and continue setting up your store.</p>

          <div class="saas-verify-meta">
            <p>Didn't receive the email?</p>
            <button id="resendBtn" class="saas-resend-btn" onclick="handleResendCode()">Resend link</button>
            <p id="resendCountdown" class="saas-countdown-label"></p>
          </div>

          <!-- Simulation Tool to Help User Verify Frontend Flow -->
          <div class="saas-simulation-box">
            <span class="sim-badge">SaaS Simulation Tool</span>
            <p>Click below to simulate clicking the verification link in your email.</p>
            <button onclick="simulateVerificationSuccess()" class="saas-simulate-btn">Simulate Email Verification Link
              Click ✓</button>
          </div>
        </div>
      </div>

      <!-- STAGE 4: Success / Welcome Screen -->
      <div id="saasStageSuccess" class="saas-stage">
        <div class="saas-success-container">
          <div class="saas-success-icon-check">✓</div>
          <h2>Account Verified!</h2>
          <p>Your email has been verified successfully. Welcome to Slot Store! Let's get started setting up your
            customized
            grocery store.</p>
          <button onclick="closeSaaSModal()" class="saas-success-finish-btn">Go to your SaaS dashboard â†’</button>
        </div>
      </div>

    </div>
  </div>

  <style>
    .saas-modal-overlay,
    .demo-modal-overlay {
      --black: #1F2937;
      --white: #FFFFFF;
      --cream: #F8FAF7;
      --gold: #2E7D32;
      --gold-light: rgba(46, 125, 50, 0.08);
      --gold-dark: #1B5E20;
      --gray-100: #F3F4F6;
      --gray-200: #E5E7EB;
      --gray-400: #9CA3AF;
      --gray-600: #4B5563;
      --gray-800: #1B5E20;
      --text: #16241A;
      --text-muted: #5B6B60;
      --border: rgba(31, 41, 55, 0.08);
    }

    /* â”€â”€ MOBILE PREVIEW MODAL â”€â”€ */
    .demo-modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(10, 10, 10, 0.75);
      backdrop-filter: blur(16px);
      z-index: 2000;
      display: none;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .demo-modal-overlay.active {
      display: flex;
      opacity: 1;
    }

    .demo-modal-close {
      position: absolute;
      top: 24px;
      right: 24px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      width: 48px;
      height: 48px;
      border-radius: 50%;
      color: var(--white);
      font-size: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background 0.3s, transform 0.3s, border-color 0.3s;
      z-index: 2010;
    }

    .demo-modal-close:hover {
      background: rgba(201, 168, 76, 0.2);
      border-color: rgba(201, 168, 76, 0.4);
      color: var(--gold);
      transform: scale(1.08) rotate(90deg);
    }

    .phone-mockup-wrapper {
      position: relative;
      width: 414px;
      height: 860px;
      background: #09090b;
      border: 12px solid #1c1c1e;
      border-radius: 48px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.55),
        0 0 0 2px rgba(255, 255, 255, 0.05),
        inset 0 0 8px rgba(0, 0, 0, 0.8);
      transform: scale(0.8) translateY(40px);
      transform-origin: center;
      opacity: 0;
      transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease;
    }

    .demo-modal-overlay.active .phone-mockup-wrapper {
      transform: scale(0.85) translateY(0);
      opacity: 1;
    }

    /* Physical Side Buttons */
    .phone-button {
      position: absolute;
      background: #27272a;
      border-radius: 3px;
      z-index: -1;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
    }

    .phone-button.volume-up {
      left: -15px;
      top: 150px;
      width: 3px;
      height: 50px;
    }

    .phone-button.volume-down {
      left: -15px;
      top: 212px;
      width: 3px;
      height: 50px;
    }

    .phone-button.power {
      right: -15px;
      top: 180px;
      width: 3px;
      height: 80px;
    }

    .phone-screen {
      width: 100%;
      height: 100%;
      background: #fff;
      border-radius: 36px;
      overflow: hidden;
      position: relative;
      display: flex;
      flex-direction: column;
    }

    /* iOS Status Bar */
    .phone-status-bar {
      position: relative;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      background: #fff;
      z-index: 15;
      font-size: 11px;
      font-weight: 600;
      color: #000;
      user-select: none;
      transition: background-color 0.4s ease, color 0.4s ease;
    }

    .status-time {
      letter-spacing: -0.1px;
    }

    .phone-notch {
      position: absolute;
      top: 6px;
      left: 50%;
      transform: translateX(-50%);
      width: 110px;
      height: 24px;
      background: #000;
      border-radius: 12px;
      z-index: 20;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .notch-camera {
      width: 8px;
      height: 8px;
      background: #05051a;
      border-radius: 50%;
      box-shadow: inset 0 0 2px rgba(255, 255, 255, 0.4);
    }

    .status-icons {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .status-icon {
      width: 14px;
      height: 14px;
      fill: currentColor;
    }

    .battery-percentage {
      font-size: 10px;
    }

    .battery-icon {
      width: 20px;
      height: 10px;
      border: 1px solid currentColor;
      border-radius: 3px;
      padding: 1px;
      display: flex;
      align-items: center;
      position: relative;
    }

    .battery-level {
      width: 80%;
      height: 100%;
      background: currentColor;
      border-radius: 1px;
    }

    .phone-screen iframe {
      flex: 1;
      width: 100%;
      border: none;
      background: #fff;
    }

    .phone-home-indicator {
      position: absolute;
      bottom: 8px;
      left: 50%;
      transform: translateX(-50%);
      width: 120px;
      height: 4.5px;
      background: rgba(0, 0, 0, 0.5);
      border-radius: 2.25px;
      z-index: 15;
      pointer-events: none;
      transition: background-color 0.4s ease;
    }

    .phone-loader {
      position: absolute;
      inset: 0;
      background: var(--cream);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 5;
      transition: opacity 0.4s ease, visibility 0.4s ease;
    }

    .phone-loader.hidden {
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
    }

    .spinner {
      width: 40px;
      height: 40px;
      border: 3px solid rgba(201, 168, 76, 0.1);
      border-top: 3px solid var(--gold);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 16px;
    }

    /* â”€â”€ DEVICE TOGGLE â”€â”€ */
    .device-toggle-wrapper {
      position: absolute;
      top: 24px;
      left: 50%;
      transform: translateX(-50%);
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      padding: 4px;
      border-radius: 30px;
      display: flex;
      gap: 4px;
      z-index: 2010;
      backdrop-filter: blur(8px);
    }

    .toggle-btn {
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.5);
      padding: 8px 16px;
      border-radius: 20px;
      cursor: pointer;
      font-size: 12px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s ease;
    }

    .toggle-btn.active {
      background: var(--gold);
      color: var(--black);
    }

    .toggle-btn svg {
      width: 16px;
      height: 16px;
    }

    /* â”€â”€ DESKTOP PREVIEW MODE â”€â”€ */
    .demo-modal-overlay.desktop-mode .phone-mockup-wrapper {
      width: 90%;
      height: 85vh;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      background: #fff;
      padding: 0;
      box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6);
      transform: scale(1) translateY(20px);
    }

    .demo-modal-overlay.desktop-mode .phone-button,
    .demo-modal-overlay.desktop-mode .phone-status-bar,
    .demo-modal-overlay.desktop-mode .phone-home-indicator,
    .demo-modal-overlay.desktop-mode .phone-notch {
      display: none;
    }

    .demo-modal-overlay.desktop-mode .phone-screen {
      border-radius: 12px;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .loader-text {
      font-size: 13px;
      color: var(--text-muted);
      font-weight: 500;
      letter-spacing: 0.5px;
    }

    /* Responsive Device scaling for different viewport heights */
    @media (max-height: 950px) {
      .phone-mockup-wrapper {
        transform: scale(0.8) translateY(20px);
      }

      .demo-modal-overlay.active .phone-mockup-wrapper {
        transform: scale(0.85) translateY(0);
      }
    }

    @media (max-height: 850px) {
      .phone-mockup-wrapper {
        transform: scale(0.7) translateY(15px);
      }

      .demo-modal-overlay.active .phone-mockup-wrapper {
        transform: scale(0.75) translateY(0);
      }
    }

    @media (max-height: 750px) {
      .phone-mockup-wrapper {
        transform: scale(0.6) translateY(10px);
      }

      .demo-modal-overlay.active .phone-mockup-wrapper {
        transform: scale(0.65) translateY(0);
      }
    }

    @media (max-height: 650px) {
      .phone-mockup-wrapper {
        transform: scale(0.5) translateY(5px);
      }

      .demo-modal-overlay.active .phone-mockup-wrapper {
        transform: scale(0.55) translateY(0);
      }
    }


    /* SaaS Modal Styling */
    .saas-modal-overlay {
      position: fixed;
      inset: 0;
      z-index: 9999;
      background: rgba(10, 10, 10, 0.45);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      padding: 20px;
    }

    .saas-modal-overlay.active {
      opacity: 1;
      pointer-events: auto;
    }

    .saas-modal-card {
      background: var(--cream);
      border: 1px solid var(--border);
      border-radius: 16px;
      width: 100%;
      max-width: 620px;
      position: relative;
      padding: 36px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
      transform: translateY(20px) scale(0.98);
      transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      max-height: 90vh;
      overflow-y: auto;
    }

    .saas-modal-overlay.active .saas-modal-card {
      transform: translateY(0) scale(1);
    }

    .saas-modal-close {
      position: absolute;
      top: 24px;
      right: 24px;
      background: transparent;
      border: none;
      font-size: 28px;
      color: var(--gray-400);
      cursor: pointer;
      line-height: 1;
      transition: color 0.2s;
    }

    .saas-modal-close:hover {
      color: var(--black);
    }

    .saas-stage {
      display: none;
    }

    .saas-stage.active {
      display: block;
      animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(8px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .saas-modal-header {
      margin-bottom: 24px;
    }

    .saas-badge {
      background: var(--gold-light);
      color: var(--gold-dark);
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      padding: 4px 10px;
      border-radius: 4px;
      display: inline-block;
      margin-bottom: 12px;
    }

    .saas-modal-header h2 {
      font-size: 28px;
      font-weight: 700;
      letter-spacing: -0.5px;
      margin-bottom: 6px;
      color: var(--black);
    }

    .saas-lead {
      font-size: 14px;
      color: var(--text-muted);
    }

    /* Form Elements */
    .saas-form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    @media (max-width: 500px) {
      .saas-form-grid {
        grid-template-columns: 1fr;
        gap: 16px;
      }

      .saas-modal-card {
        padding: 24px 20px;
      }
    }

    .saas-form-group {
      margin-bottom: 18px;
      display: flex;
      flex-direction: column;
    }

    .saas-phone-input-wrapper {
      display: flex;
      gap: 8px;
    }

    .saas-phone-code {
      width: 105px !important;
      flex-shrink: 0;
      padding: 12px 6px !important;
    }

    .saas-form-group label {
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.5px;
      color: var(--gray-800);
      margin-bottom: 6px;
      text-transform: uppercase;
    }

    .saas-form-group input,
    .saas-form-group select {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 12px 14px;
      font-family: inherit;
      font-size: 14px;
      color: var(--black);
      width: 100%;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .saas-form-group input:focus,
    .saas-form-group select:focus {
      border-color: var(--gold);
      outline: none;
      box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.15);
    }

    .saas-error {
      color: #DC2626;
      font-size: 11px;
      margin-top: 4px;
      min-height: 16px;
    }

    /* Onboarding wizard step states */
    .saas-form-step {
      display: none;
    }

    .saas-form-step.active {
      display: block;
      animation: fadeIn 0.4s ease-out;
    }

    /* Wizard bottom navigation */
    .saas-step-nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 32px;
      padding-top: 24px;
      border-top: 1px solid var(--border);
    }

    .saas-dots {
      display: flex;
      gap: 8px;
    }

    .saas-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: var(--border);
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
    }

    .saas-dot.active {
      background: var(--gold-dark);
      transform: scale(1.2);
    }

    .saas-nav-actions {
      display: flex;
      gap: 12px;
    }

    .saas-back-btn {
      background: transparent;
      border: 1px solid var(--border);
      color: var(--text-muted);
      font-family: inherit;
      font-weight: 700;
      font-size: 13px;
      border-radius: 8px;
      padding: 10px 20px;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
    }

    .saas-back-btn:hover {
      background: var(--gray-100);
      color: var(--black);
    }

    .saas-next-btn {
      background: var(--black);
      color: var(--white);
      border: none;
      font-family: inherit;
      font-weight: 700;
      font-size: 13px;
      border-radius: 8px;
      padding: 10px 24px;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .saas-next-btn:hover {
      background: var(--gray-800);
      transform: translateY(-1px);
    }

    /* Verification Layout */
    .saas-verify-container,
    .saas-success-container {
      text-align: center;
      padding: 20px 0;
    }

    .saas-verify-icon {
      width: 64px;
      height: 64px;
      background: var(--gold-light);
      color: var(--gold-dark);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
    }

    .saas-verify-icon svg {
      width: 32px;
      height: 32px;
    }

    .saas-verify-container h2,
    .saas-success-container h2 {
      font-size: 28px;
      font-weight: 700;
      margin-bottom: 12px;
    }

    .saas-verify-desc {
      color: var(--text-muted);
      font-size: 15px;
      margin-bottom: 32px;
      line-height: 1.6;
    }

    .saas-verify-meta {
      padding-top: 20px;
      border-top: 1px solid var(--border);
      margin-bottom: 24px;
    }

    .saas-verify-meta p {
      font-size: 13px;
      color: var(--text-muted);
    }

    .saas-resend-btn {
      background: transparent;
      border: none;
      color: var(--gold-dark);
      font-weight: 700;
      text-decoration: underline;
      cursor: pointer;
      padding: 4px 8px;
      font-family: inherit;
    }

    .saas-resend-btn:disabled {
      color: var(--gray-400);
      text-decoration: none;
      cursor: not-allowed;
    }

    .saas-countdown-label {
      font-size: 12px;
      color: var(--gray-600);
      margin-top: 6px;
      font-weight: 500;
    }

    /* Simulation Styling */
    .saas-simulation-box {
      background: rgba(201, 168, 76, 0.08);
      border: 1px dashed var(--gold);
      border-radius: 10px;
      padding: 20px;
      margin-top: 30px;
    }

    .sim-badge {
      background: var(--gold);
      color: var(--white);
      font-size: 10px;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: 4px;
      text-transform: uppercase;
      letter-spacing: 1px;
      display: inline-block;
      margin-bottom: 8px;
    }

    .saas-simulation-box p {
      font-size: 13px;
      color: var(--gray-800);
      margin-bottom: 12px;
    }

    .saas-simulate-btn {
      background: var(--gold-dark);
      color: var(--white);
      font-family: inherit;
      font-weight: 700;
      font-size: 13px;
      border: none;
      border-radius: 6px;
      padding: 10px 16px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .saas-simulate-btn:hover {
      background: #6B510F;
    }

    /* Success Screen */
    .saas-success-icon-check {
      width: 64px;
      height: 64px;
      background: #DCFCE7;
      color: #15803D;
      border-radius: 50%;
      font-size: 32px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
    }

    .saas-success-finish-btn {
      background: var(--black);
      color: var(--white);
      font-family: inherit;
      font-weight: 500;
      border: none;
      border-radius: 8px;
      padding: 14px 28px;
      cursor: pointer;
      font-size: 15px;
      margin-top: 24px;
      transition: background 0.2s;
    }

    .saas-success-finish-btn:hover {
      background: var(--gray-800);
    }
  </style>

  <script>
    // Resend code countdown state
    let resendTimer = null;
    let countdownSeconds = 0;
    let currentStep = 1;

    document.addEventListener('DOMContentLoaded', () => {
      const triggerButtons = document.querySelectorAll('.pricing-btn-trigger');
      const saasModal = document.getElementById('saasModal');
      const closeSaasBtn = document.getElementById('closeSaasBtn');
      const planSelector = document.getElementById('saas_plan');

      // Auto-trigger registration modal if URL query has get_started=1
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('get_started') && saasModal) {
        if (planSelector) {
          planSelector.value = 'sprout';
          updatePlanBadges('sprout');
        }
        saasModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        showSaasStage('saasStageSignUp');
        goToStep(1);
      }

      triggerButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          const plan = btn.getAttribute('data-plan');
          if (planSelector && plan) {
            planSelector.value = plan;
            updatePlanBadges(plan);
          }

          // Show Modal
          saasModal.classList.add('active');
          document.body.style.overflow = 'hidden';

          // Reset stages and onboarding wizard step
          showSaasStage('saasStageSignUp');
          goToStep(1);
        });
      });

      if (planSelector) {
        planSelector.addEventListener('change', () => {
          updatePlanBadges(planSelector.value);
        });
      }

      const countrySelector = document.getElementById('saas_country');
      const phoneCodeSelector = document.getElementById('saas_phone_code');
      if (countrySelector && phoneCodeSelector) {
        const countryToCode = {
          'India': '+91',
          'United Arab Emirates': '+971',
          'Saudi Arabia': '+966',
          'United Kingdom': '+44',
          'United States': '+1',
          'France': '+33',
          'Singapore': '+65',
          'Australia': '+61'
        };
        countrySelector.addEventListener('change', () => {
          const countryVal = countrySelector.value;
          const matchingCode = countryToCode[countryVal];
          if (matchingCode) {
            phoneCodeSelector.value = matchingCode;
          }
        });
      }

      closeSaasBtn.addEventListener('click', closeSaaSModal);

      saasModal.addEventListener('click', (e) => {
        if (e.target === saasModal) {
          closeSaaSModal();
        }
      });
    });

    function updatePlanBadges(planName) {
      const capitalized = planName.charAt(0).toUpperCase() + planName.slice(1);
      document.querySelectorAll('.class-plan-badge').forEach(badge => {
        badge.textContent = capitalized;
      });
    }

    function validateStep(step) {
      let stepValid = true;

      // Clear error tags for the current step
      if (step === 1) {
        document.getElementById('err_name').textContent = '';
        document.getElementById('err_email').textContent = '';

        const name = document.getElementById('saas_name').value.trim();
        const email = document.getElementById('saas_email').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!name) {
          document.getElementById('err_name').textContent = 'Full name is required';
          stepValid = false;
        }
        if (!email) {
          document.getElementById('err_email').textContent = 'Email address is required';
          stepValid = false;
        } else if (!emailRegex.test(email)) {
          document.getElementById('err_email').textContent = 'Please enter a valid email address';
          stepValid = false;
        }
      } else if (step === 2) {
        document.getElementById('err_business').textContent = '';
        document.getElementById('err_country').textContent = '';
        document.getElementById('err_whatsapp').textContent = '';

        const business = document.getElementById('saas_business').value.trim();
        const country = document.getElementById('saas_country').value;
        const whatsapp = document.getElementById('saas_whatsapp').value.trim();
        const whatsappRegex = /^\+?[0-9\s\-()]{7,18}$/;

        if (!business) {
          document.getElementById('err_business').textContent = 'Business name is required';
          stepValid = false;
        }
        if (!country) {
          document.getElementById('err_country').textContent = 'Please select your country';
          stepValid = false;
        }
        if (!whatsapp) {
          document.getElementById('err_whatsapp').textContent = 'WhatsApp number is required';
          stepValid = false;
        } else if (!whatsappRegex.test(whatsapp)) {
          document.getElementById('err_whatsapp').textContent = 'Please enter a valid phone number';
          stepValid = false;
        }
      } else if (step === 3) {
        document.getElementById('err_password').textContent = '';
        document.getElementById('err_confirm_password').textContent = '';

        const password = document.getElementById('saas_password').value;
        const confirmPassword = document.getElementById('saas_confirm_password').value;

        if (!password) {
          document.getElementById('err_password').textContent = 'Password is required';
          stepValid = false;
        } else if (password.length < 8) {
          document.getElementById('err_password').textContent = 'Password must be at least 8 characters';
          stepValid = false;
        }
        if (password !== confirmPassword) {
          document.getElementById('err_confirm_password').textContent = 'Passwords do not match';
          stepValid = false;
        }
      }
      return stepValid;
    }

    function goToStep(step) {
      // If navigating forward, validate preceding steps
      if (step > currentStep) {
        for (let i = currentStep; i < step; i++) {
          if (!validateStep(i)) return;
        }
      }

      currentStep = step;

      // Toggle form steps visibility
      document.querySelectorAll('.saas-form-step').forEach((el, idx) => {
        if (idx + 1 === step) {
          el.classList.add('active');
        } else {
          el.classList.remove('active');
        }
      });

      // Toggle dots active state
      document.querySelectorAll('#saasStepDots .saas-dot').forEach((el, idx) => {
        if (idx + 1 === step) {
          el.classList.add('active');
        } else {
          el.classList.remove('active');
        }
      });

      // Toggle Back button visibility
      const backBtn = document.getElementById('saasBackBtn');
      if (step === 1) {
        backBtn.style.visibility = 'hidden';
      } else {
        backBtn.style.visibility = 'visible';
      }

      // Update Next button label
      const nextBtn = document.getElementById('saasNextBtn');
      if (step === 3) {
        nextBtn.textContent = 'Create my account â†’';
      } else {
        nextBtn.textContent = 'Next';
      }
    }

    function prevStep() {
      if (currentStep > 1) {
        goToStep(currentStep - 1);
      }
    }

    function nextStep() {
      if (currentStep < 3) {
        if (validateStep(currentStep)) {
          goToStep(currentStep + 1);
        }
      }
    }

    function handleStepNavNext() {
      if (currentStep === 3) {
        handleSaasRegister(new Event('submit'));
      } else {
        nextStep();
      }
    }

    function closeSaaSModal() {
      const saasModal = document.getElementById('saasModal');
      saasModal.classList.remove('active');
      document.body.style.overflow = '';
      // Clear resend countdown timer if active
      if (resendTimer) {
        clearInterval(resendTimer);
        resendTimer = null;
      }
    }

    function showSaasStage(stageId) {
      document.querySelectorAll('.saas-stage').forEach(stage => {
        stage.classList.remove('active');
      });
      const target = document.getElementById(stageId);
      if (target) target.classList.add('active');
    }

    function handleSaasRegister(e) {
      if (e) e.preventDefault();

      // Verify all steps are fully valid
      if (!validateStep(1) || !validateStep(2) || !validateStep(3)) {
        return;
      }

      const nextBtn = document.getElementById('saasNextBtn');
      const originalText = nextBtn.textContent;
      nextBtn.disabled = true;
      nextBtn.textContent = 'Registering...';

      const name = document.getElementById('saas_name').value.trim();
      const business = document.getElementById('saas_business').value.trim();
      const country = document.getElementById('saas_country').value;
      const email = document.getElementById('saas_email').value.trim();
      const whatsappPrefix = document.getElementById('saas_phone_code').value;
      const whatsappNumber = document.getElementById('saas_whatsapp').value.trim();
      const password = document.getElementById('saas_password').value;
      const plan = document.getElementById('saas_plan').value;
      const theme = document.getElementById('saas_theme').value;

      const fullWhatsapp = whatsappPrefix + ' ' + whatsappNumber;

      fetch('{{ route("saas.register") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          name: name,
          business: business,
          country: country,
          email: email,
          whatsapp: fullWhatsapp,
          password: password,
          plan: plan,
          theme: theme
        })
      })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
          nextBtn.disabled = false;
          nextBtn.textContent = originalText;

          if (status === 200 && body.success) {
            // Success! Show Step 2: Email verification
            document.getElementById('verifyEmailDisplay').textContent = email;

            // Store the dynamic verification URL on the simulator button!
            const simBtn = document.querySelector('.saas-simulate-btn');
            if (simBtn && body.verify_url) {
              simBtn.setAttribute('onclick', `window.location.href="${body.verify_url}"`);
            }

            showSaasStage('saasStageVerify');
            startResendCountdown();
          } else {
            // Handle validation errors from backend
            if (body.errors) {
              Object.keys(body.errors).forEach(key => {
                const errEl = document.getElementById(`err_${key}`);
                if (errEl) {
                  errEl.textContent = body.errors[key][0];
                }
              });

              // Switch to the step with errors
              if (body.errors.name || body.errors.email) {
                goToStep(1);
              } else if (body.errors.business || body.errors.country || body.errors.whatsapp) {
                goToStep(2);
              } else if (body.errors.password || body.errors.theme) {
                goToStep(3);
              }
            } else {
              alert(body.message || 'An unexpected error occurred. Please try again.');
            }
          }
        })
        .catch(error => {
          nextBtn.disabled = false;
          nextBtn.textContent = originalText;
          console.error('Error:', error);
          alert('A connection error occurred. Please check your network and try again.');
        });
    }

    function startResendCountdown() {
      const resendBtn = document.getElementById('resendBtn');
      const resendCountdown = document.getElementById('resendCountdown');

      if (resendTimer) clearInterval(resendTimer);

      countdownSeconds = 60;
      resendBtn.disabled = true;
      resendCountdown.textContent = `You can resend the link in ${countdownSeconds}s`;

      resendTimer = setInterval(() => {
        countdownSeconds--;
        if (countdownSeconds <= 0) {
          clearInterval(resendTimer);
          resendTimer = null;
          resendBtn.disabled = false;
          resendCountdown.textContent = '';
        } else {
          resendCountdown.textContent = `You can resend the link in ${countdownSeconds}s`;
        }
      }, 1000);
    }

    function handleResendCode() {
      alert('A new verification email has been sent successfully!');
      startResendCountdown();
    }

    function simulateVerificationSuccess() {
      showSaasStage('saasStageSuccess');
    }
  </script>

  <!-- TEMPLATE PREVIEW MODAL -->
  <div class="demo-modal-overlay" id="demoModal">
    <button class="demo-modal-close" id="closeDemoBtn" aria-label="Close Preview">&times;</button>

    <div class="device-toggle-wrapper">
      <button class="toggle-btn active" id="mobileToggle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round">
          <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
          <line x1="12" y1="18" x2="12.01" y2="18"></line>
        </svg>
        Mobile
      </button>
      <button class="toggle-btn" id="desktopToggle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round">
          <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
          <line x1="8" y1="21" x2="16" y2="21"></line>
          <line x1="12" y1="17" x2="12" y2="21"></line>
        </svg>
        Desktop
      </button>
    </div>

    <div class="phone-mockup-wrapper">
      <!-- Physical Side Buttons -->
      <div class="phone-button volume-up"></div>
      <div class="phone-button volume-down"></div>
      <div class="phone-button power"></div>

      <div class="phone-screen">
        <!-- iOS Status Bar -->
        <div class="phone-status-bar">
          <span class="status-time" id="statusTime">9:41</span>
          <div class="phone-notch">
            <span class="notch-camera"></span>
          </div>
          <div class="status-icons">
            <!-- Network SVG -->
            <svg class="status-icon" viewBox="0 0 24 24" fill="currentColor">
              <path d="M2 22h20V2z" opacity="0.3" />
              <path d="M2 22h16V6z" />
            </svg>
            <!-- Wifi SVG -->
            <svg class="status-icon" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M12 21a2 2 0 0 1-1.41-.59l-8.5-8.5a1 1 0 0 1 0-1.41A15.91 15.91 0 0 1 12 6a15.91 15.91 0 0 1 9.91 4.5 1 1 0 0 1 0 1.41l-8.5 8.5A2 2 0 0 1 12 21z" />
            </svg>
            <!-- Battery Percentage -->
            <span class="battery-percentage">88%</span>
            <div class="battery-icon">
              <span class="battery-level"></span>
            </div>
          </div>
        </div>

        <!-- Loader Overlay -->
        <div class="phone-loader">
          <div class="spinner"></div>
          <div class="loader-text" id="loaderText">Curating storefront...</div>
        </div>

        <!-- Live IFrame -->
        <iframe src="" id="demoIframe"></iframe>

        <!-- Home Indicator -->
        <div class="phone-home-indicator"></div>
      </div>
    </div>
  </div>

  <script>
    function submitForm() {
      const name = document.getElementById('name').value;
      const business = document.getElementById('business').value;
      const email = document.getElementById('email').value;
      const whatsapp = document.getElementById('whatsapp').value;
      const country = document.getElementById('country').value;

      if (!name || !business || !email || !whatsapp || !country) {
        alert('Please fill in all required fields.');
        return;
      }

      document.getElementById('form-fields').style.display = 'none';
      const success = document.getElementById('form-success');
      success.style.display = 'block';
    }

    // Template Iframe Modal Trigger Logic
    const templateCards = document.querySelectorAll('.template-card');
    const demoModal = document.getElementById('demoModal');
    const demoIframe = document.getElementById('demoIframe');
    const phoneLoader = document.querySelector('.phone-loader');
    const loaderText = document.getElementById('loaderText');
    const closeDemoBtn = document.getElementById('closeDemoBtn');
    const statusBar = document.querySelector('.phone-status-bar');
    const homeIndicator = document.querySelector('.phone-home-indicator');
    const mobileToggle = document.getElementById('mobileToggle');
    const desktopToggle = document.getElementById('desktopToggle');

    // Toggle Logic
    mobileToggle.addEventListener('click', () => {
      mobileToggle.classList.add('active');
      desktopToggle.classList.remove('active');
      demoModal.classList.remove('desktop-mode');
    });

    desktopToggle.addEventListener('click', () => {
      desktopToggle.classList.add('active');
      mobileToggle.classList.remove('active');
      demoModal.classList.add('desktop-mode');
    });

    // Custom status bar themes and personalized loading messages
    const themeStyles = {
      'Aura Luxe': { bg: '#1a101e', text: '#ffffff', indicator: 'rgba(255, 255, 255, 0.45)', msg: 'Curating Aura Luxe experience...' },
      'Velvet Dark': { bg: '#1a1208', text: '#ffffff', indicator: 'rgba(255, 255, 255, 0.45)', msg: 'Polishing Velvet Dark storefront...' },
      'Editorial Cream': { bg: '#fdfaf6', text: '#3d2b0e', indicator: 'rgba(0, 0, 0, 0.45)', msg: 'Styling Editorial Cream showcase...' },
      'Modern Minimal': { bg: '#0f1923', text: '#ffffff', indicator: 'rgba(255, 255, 255, 0.45)', msg: 'Aligning Modern Minimal grid...' }
    };

    // Update digital clock on the mockup status bar
    function updatePhoneClock() {
      const now = new Date();
      let hours = now.getHours();
      let minutes = now.getMinutes();
      hours = hours < 10 ? '0' + hours : hours;
      minutes = minutes < 10 ? '0' + minutes : minutes;
      const timeStr = hours + ':' + minutes;
      const clockEl = document.getElementById('statusTime');
      if (clockEl) clockEl.textContent = timeStr;
    }

    updatePhoneClock();
    setInterval(updatePhoneClock, 60000);

    templateCards.forEach(card => {
      card.addEventListener('click', () => {
        const demoUrl = card.getAttribute('data-demo-url');
        if (demoUrl) {
          // Read template name
          const cardTitleEl = card.querySelector('h3');
          const templateName = cardTitleEl ? cardTitleEl.textContent.trim() : '';
          const activeTheme = themeStyles[templateName] || {
            bg: '#ffffff',
            text: '#000000',
            indicator: 'rgba(0, 0, 0, 0.45)',
            msg: 'Curating premium storefront...'
          };

          // Apply theme-matched styles to mockup status bar & home indicator
          if (statusBar) {
            statusBar.style.backgroundColor = activeTheme.bg;
            statusBar.style.color = activeTheme.text;
          }
          if (homeIndicator) {
            homeIndicator.style.backgroundColor = activeTheme.indicator;
          }
          if (loaderText) {
            loaderText.textContent = activeTheme.msg;
          }

          // Reset loader & update src
          phoneLoader.classList.remove('hidden');
          demoIframe.src = demoUrl;

          // Show modal & disable background scroll
          demoModal.classList.add('active');
          document.body.style.overflow = 'hidden';
        }
      });
    });

    const closeModal = () => {
      demoModal.classList.remove('active');
      // Reset to mobile mode on close
      demoModal.classList.remove('desktop-mode');
      mobileToggle.classList.add('active');
      desktopToggle.classList.remove('active');
      document.body.style.overflow = '';
      setTimeout(() => {
        demoIframe.src = '';
      }, 400);
    };

    closeDemoBtn.addEventListener('click', closeModal);

    demoModal.addEventListener('click', (e) => {
      if (e.target === demoModal) {
        closeModal();
      }
    });

    // Close on Escape key press
    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && demoModal.classList.contains('active')) {
        closeModal();
      }
    });

    demoIframe.onload = () => {
      phoneLoader.classList.add('hidden');
    };
  </script>
</body>

</html>

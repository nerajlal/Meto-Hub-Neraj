<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vespr — Launch Your Grocery Store</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    *,
    *::before,
    *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    :root {
      --black: #0A0A0A;
      --white: #FFFFFF;
      --cream: #FAF8F4;
      --gold: #C9A84C;
      --gold-light: #F0E4C2;
      --gold-dark: #8B6914;
      --gray-100: #F5F4F0;
      --gray-200: #E8E6E0;
      --gray-400: #9E9C96;
      --gray-600: #5C5A54;
      --gray-800: #2A2924;
      --text: #0A0A0A;
      --text-muted: #5C5A54;
      --border: #E0DDD6;
    }

    html {
      scroll-behavior: smooth;
    }

    body {
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 400;
      line-height: 1.7;
      color: var(--text);
      background: var(--white);
      -webkit-font-smoothing: antialiased;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      font-family: 'Inter', sans-serif;
      line-height: 1.2;
      color: var(--black);
    }

    /* NAV SYSTEM */
    nav {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      width: 100%;
      z-index: 1000;
      padding: 0 40px;
      height: 64px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: background-color 0.3s ease, border-color 0.3s ease, backdrop-filter 0.3s ease;
    }

    /* Transparent state over Dark Hero */
    nav.nav-transparent {
      background: transparent;
      border-bottom: 1px solid transparent;
      backdrop-filter: none;
    }

    nav.nav-transparent.nav-transparent-blur {
      background: rgba(6, 6, 8, 0.45);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    nav.nav-transparent .nav-logo {
      color: var(--white);
    }

    nav.nav-transparent .nav-links a {
      color: rgba(255, 255, 255, 0.7);
    }

    nav.nav-transparent .nav-links a:hover {
      color: var(--white);
    }

    nav.nav-transparent .nav-login-link {
      color: rgba(255, 255, 255, 0.7);
    }

    nav.nav-transparent .nav-login-link:hover {
      color: var(--white);
    }

    nav.nav-transparent .btn-nav-action {
      background: var(--gold);
      color: var(--black);
    }

    nav.nav-transparent .btn-nav-action:hover {
      background: #D4A83F;
      transform: translateY(-1px);
    }

    /* Scrolled light state */
    nav.nav-scrolled {
      background: rgba(255, 255, 255, 0.96);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--border);
    }

    nav.nav-scrolled .nav-logo {
      color: var(--black);
    }

    nav.nav-scrolled .nav-links a {
      color: var(--text-muted);
    }

    nav.nav-scrolled .nav-links a:hover {
      color: var(--black);
    }

    nav.nav-scrolled .nav-login-link {
      color: var(--text-muted);
    }

    nav.nav-scrolled .nav-login-link:hover {
      color: var(--black);
    }

    nav.nav-scrolled .btn-nav-action {
      background: var(--black);
      color: var(--white);
    }

    nav.nav-scrolled .btn-nav-action:hover {
      background: var(--gray-800);
      transform: translateY(-1px);
    }

    /* Base nav elements style */
    .nav-logo {
      font-size: 20px;
      font-weight: 700;
      letter-spacing: -0.5px;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: color 0.2s;
    }

    .nav-logo span {
      color: var(--gold);
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 32px;
      list-style: none;
    }

    .nav-links a {
      font-size: 14px;
      font-weight: 500;
      text-decoration: none;
      transition: color 0.2s;
    }

    .nav-login-link {
      font-size: 14px;
      font-weight: 500;
      text-decoration: none;
      transition: color 0.2s;
    }

    .btn-nav-action {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 500;
      padding: 10px 22px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.2s, transform 0.15s, color 0.2s;
    }

    .btn-primary {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: var(--black);
      color: var(--white);
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 500;
      padding: 10px 22px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.2s, transform 0.15s;
    }

    .btn-primary:hover {
      background: var(--gray-800);
      transform: translateY(-1px);
    }

    .btn-outline {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: transparent;
      color: var(--black);
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 500;
      padding: 10px 22px;
      border: 1.5px solid var(--black);
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.2s;
    }

    .btn-outline:hover {
      background: var(--gray-100);
    }

    /* HERO */
    .hero {
      background: radial-gradient(circle at center, #1b1222 0%, #060608 80%);
      color: var(--white);
      padding: 120px 40px 100px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(201, 168, 76, 0.15) 0%, transparent 70%);
      pointer-events: none;
    }

    .hero-eyebrow {
      display: inline-block;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 32px;
    }

    .hero h1 {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-size: 60px;
      font-weight: 400;
      color: var(--white);
      letter-spacing: -0.5px;
      max-width: 900px;
      margin: 0 auto 24px;
      line-height: 1.25;
    }

    .hero h1 em {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-style: italic;
      color: var(--gold);
      font-weight: 400;
    }

    .hero-sub {
      font-size: 18px;
      font-weight: 400;
      color: rgba(255, 255, 255, 0.65);
      max-width: 600px;
      margin: 0 auto 40px;
      line-height: 1.6;
    }

    .hero-cta {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .btn-gold {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--gold);
      color: var(--black);
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 700;
      padding: 14px 30px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.2s, transform 0.15s;
    }

    .btn-gold:hover {
      background: #D4A83F;
      transform: translateY(-1px);
    }

    .btn-ghost {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: transparent;
      color: rgba(255, 255, 255, 0.8);
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 500;
      padding: 14px 28px;
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      transition: border-color 0.2s, color 0.2s;
    }

    .btn-ghost:hover {
      border-color: rgba(255, 255, 255, 0.5);
      color: var(--white);
    }

    .hero-bullets {
      display: flex;
      justify-content: center;
      gap: 28px;
      margin-top: 56px;
      font-size: 13px;
      color: rgba(255, 255, 255, 0.45);
      flex-wrap: wrap;
    }

    .hero-bullets span {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* SECTIONS */
    section {
      padding: 96px 40px;
    }

    .section-inner {
      max-width: 1080px;
      margin: 0 auto;
    }

    .section-label {
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 16px;
    }

    .section-title {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-size: 44px;
      font-weight: 400;
      letter-spacing: -0.5px;
      color: var(--black);
      line-height: 1.2;
      margin-bottom: 16px;
    }

    .section-title em {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-style: italic;
      color: var(--gold);
      font-weight: 400;
    }

    .section-sub {
      font-size: 17px;
      font-weight: 400;
      color: var(--text-muted);
      max-width: 560px;
      line-height: 1.6;
      margin-bottom: 56px;
    }

    .subheading {
      font-size: 24px;
      font-weight: 700;
      letter-spacing: -0.5px;
      color: var(--black);
      margin-bottom: 10px;
    }

    /* HOW IT WORKS */
    .steps-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
    }

    .step-card {
      padding: 32px 28px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 12px;
      position: relative;
    }

    .step-number {
      font-size: 12px;
      font-weight: 700;
      color: var(--gold);
      letter-spacing: 1.5px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .step-number::after {
      content: '';
      flex: 1;
      height: 1px;
      background: var(--gold-light);
    }

    .step-card h3 {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .step-card p {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.6;
    }

    .step-time {
      display: inline-block;
      font-size: 11px;
      font-weight: 700;
      color: var(--gold-dark);
      background: var(--gold-light);
      padding: 3px 10px;
      border-radius: 20px;
      margin-top: 16px;
    }

    /* FEATURES */
    .features-bg {
      background: var(--cream);
    }

    .features-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 2px;
      border: 1px solid var(--border);
      border-radius: 12px;
      overflow: hidden;
    }

    .feature-cell {
      background: var(--white);
      padding: 36px 32px;
      border-right: 1px solid var(--border);
      border-bottom: 1px solid var(--border);
      transition: background 0.2s;
    }

    .feature-cell:hover {
      background: var(--gray-100);
    }

    .feature-cell:nth-child(3n) {
      border-right: none;
    }

    .feature-cell:nth-last-child(-n+3) {
      border-bottom: none;
    }

    .feature-icon {
      width: 44px;
      height: 44px;
      background: var(--black);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      font-size: 20px;
    }

    .feature-cell h3 {
      font-size: 16px;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .feature-cell p {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.65;
    }

    .integrations-bar {
      background: radial-gradient(circle at center, #1b1222 0%, #060608 80%);
      padding: 60px 40px;
      text-align: center;
      position: relative;
      overflow: hidden;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .integrations-bar::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(201, 168, 76, 0.1) 0%, transparent 70%);
      pointer-events: none;
    }

    .integrations-label {
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.4);
      margin-bottom: 24px;
    }

    .integrations-list {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .integration-pill {
      font-size: 13px;
      font-weight: 500;
      color: rgba(255, 255, 255, 0.7);
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.12);
      padding: 7px 16px;
      border-radius: 20px;
    }

    /* TEMPLATES SECTION */
    #templates {
      background: var(--white);
      color: var(--text);
      padding: 80px 40px;
      position: relative;
    }

    .templates-header-wrapper {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 48px;
      align-items: flex-start;
      margin-bottom: 56px;
    }

    .templates-header-left {
      text-align: left;
    }

    .templates-header-left .section-label {
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 20px;
      display: inline-block;
    }

    .templates-header-left .section-title {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-size: 44px;
      font-weight: 400;
      color: var(--black);
      line-height: 1.2;
      margin-bottom: 24px;
      letter-spacing: -0.5px;
    }

    .templates-header-left .section-title em {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-style: italic;
      color: var(--gold);
      font-weight: 400;
    }

    .templates-header-left .section-sub {
      font-size: 16px;
      color: var(--text-muted);
      line-height: 1.6;
      margin-bottom: 0;
      max-width: 580px;
    }

    .templates-header-right {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      height: 100%;
      padding-top: 48px;
    }

    .templates-highlight-box {
      background: var(--gray-100);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 20px 24px;
      display: flex;
      flex-direction: column;
      gap: 12px;
      width: 100%;
      max-width: 320px;
    }

    .template-highlight-item {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 13.5px;
      font-weight: 500;
      color: var(--black);
      text-align: left;
    }

    .templates-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
    }

    @media (max-width: 1200px) {
      .templates-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    .template-card {
      border: 1px solid var(--border);
      background: var(--white);
      border-radius: 12px;
      overflow: hidden;
      transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
      cursor: pointer;
    }

    .template-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08);
      border-color: rgba(201, 168, 76, 0.3);
    }

    .template-preview {
      height: 220px;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
      font-size: 13px;
      font-weight: 500;
      color: rgba(255, 255, 255, 0.6);
      letter-spacing: 1px;
    }

    .template-preview-label {
      position: absolute;
      bottom: 16px;
      left: 16px;
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.6);
      background: rgba(0, 0, 0, 0.4);
      padding: 4px 10px;
      border-radius: 4px;
    }

    .template-info {
      padding: 20px 24px;
      background: var(--white);
      border-top: 1px solid var(--border);
    }

    .template-info h3 {
      font-size: 15px;
      font-weight: 700;
      margin-bottom: 6px;
      color: var(--black);
    }

    .template-info p {
      font-size: 13px;
      color: var(--text-muted);
      margin-bottom: 14px;
    }

    .template-tag {
      font-size: 11px;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 4px;
      letter-spacing: 0.5px;
    }

    /* PRODUCT SHOWCASE */
    .showcase-container {
      display: grid;
      grid-template-columns: 1fr 1.8fr 1fr;
      gap: 24px;
      align-items: center;
      margin-top: 24px;
    }

    .showcase-side-tabs {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .showcase-tab {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 16px 20px;
      cursor: pointer;
      text-align: left;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
    }

    .showcase-tab:hover {
      border-color: rgba(201, 168, 76, 0.4);
      transform: translateY(-2px);
    }

    .showcase-tab.active {
      border-color: var(--gold);
      background: var(--white);
      box-shadow: 0 12px 32px rgba(201, 168, 76, 0.06);
    }

    /* left active tab indicator */
    .showcase-side-left .showcase-tab.active::before {
      content: '';
      position: absolute;
      left: 0;
      top: 24px;
      bottom: 24px;
      width: 3px;
      background: var(--gold);
      border-radius: 0 4px 4px 0;
    }

    /* right active tab indicator */
    .showcase-side-right .showcase-tab.active::before {
      content: '';
      position: absolute;
      right: 0;
      top: 24px;
      bottom: 24px;
      width: 3px;
      background: var(--gold);
      border-radius: 4px 0 0 4px;
    }

    .showcase-tab-tag {
      font-size: 9px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: var(--gold-dark);
      background: var(--gold-light);
      padding: 2px 8px;
      border-radius: 4px;
      display: inline-block;
      margin-bottom: 8px;
    }

    .showcase-tab h3 {
      font-size: 16px;
      font-weight: 700;
      color: var(--black);
      margin-bottom: 8px;
    }

    .showcase-tab p {
      font-size: 13px;
      color: var(--text-muted);
      line-height: 1.5;
      margin: 0;
    }

    .showcase-preview-panel {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 16px;
      box-shadow: 0 20px 48px rgba(0, 0, 0, 0.04);
      overflow: hidden;
      aspect-ratio: 16/10;
      position: relative;
      cursor: zoom-in;
    }

    .showcase-preview-panel::after {
      content: '🔍 Click to expand';
      position: absolute;
      bottom: 24px;
      right: 24px;
      background: rgba(10, 10, 10, 0.7);
      backdrop-filter: blur(8px);
      color: #fff;
      font-size: 12px;
      font-family: 'Inter', sans-serif;
      padding: 6px 14px;
      border-radius: 20px;
      opacity: 0;
      transition: opacity 0.3s ease, transform 0.3s ease;
      transform: translateY(5px);
      pointer-events: none;
      z-index: 10;
    }

    .showcase-preview-panel:hover::after {
      opacity: 1;
      transform: translateY(0);
    }

    /* Lightbox Modal */
    .lightbox-overlay {
      display: none;
      position: fixed;
      z-index: 9999;
      padding: 40px;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      background-color: rgba(10, 10, 10, 0.95);
      backdrop-filter: blur(12px);
      opacity: 0;
      transition: opacity 0.3s ease;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    }

    .lightbox-overlay.active {
      display: flex;
      opacity: 1;
    }

    .lightbox-content {
      margin: auto;
      display: block;
      max-width: 90%;
      max-height: 80vh;
      border-radius: 12px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
      transform: scale(0.95);
      transition: transform 0.3s ease;
      object-fit: contain;
    }

    .lightbox-overlay.active .lightbox-content {
      transform: scale(1);
    }

    .lightbox-close {
      position: absolute;
      top: 30px;
      right: 35px;
      color: #f1f1f1;
      font-size: 40px;
      font-weight: 300;
      transition: 0.3s;
      cursor: pointer;
      line-height: 1;
    }

    .lightbox-close:hover {
      color: var(--gold);
      transform: scale(1.1);
    }

    #lightbox-caption {
      margin-top: 20px;
      text-align: center;
      color: #ccc;
      font-size: 16px;
      font-family: 'Inter', sans-serif;
    }

    .showcase-image-wrapper {
      position: absolute;
      inset: 16px;
      opacity: 0;
      transform: scale(0.98);
      transition: opacity 0.4s ease, transform 0.4s ease;
      pointer-events: none;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .showcase-image-wrapper.active {
      opacity: 1;
      transform: scale(1);
      pointer-events: auto;
    }

    .showcase-desktop-img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 8px;
      transition: transform 0.3s ease;
    }

    .showcase-mobile-mockup {
      position: absolute;
      bottom: -4%;
      right: 4%;
      width: 25%;
      height: 82%;
      min-width: 60px;
      min-height: 120px;
      background: #09090b;
      border: 3px solid #1c1c1e;
      border-radius: 12px;
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
      overflow: hidden;
      z-index: 5;
      transform: translateY(0);
      transition: transform 0.3s ease, border-color 0.3s ease;
      cursor: zoom-in;
    }

    .showcase-mobile-mockup:hover {
      transform: translateY(-8px) scale(1.03);
      border-color: var(--gold);
    }

    .showcase-mobile-mockup img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Product Features Conversion Banner */
    .showcase-features-banner {
      margin-top: 24px;
      padding: 24px 32px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 16px;
      box-shadow: 0 12px 36px rgba(0, 0, 0, 0.02);
    }

    .features-banner-title {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--gold-dark);
      margin-bottom: 16px;
      text-align: center;
    }

    .features-banner-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
    }

    .features-banner-item {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .features-banner-icon {
      font-size: 24px;
      color: var(--gold);
      line-height: 1;
    }

    .features-banner-item h4 {
      font-size: 15px;
      font-weight: 700;
      color: var(--black);
      margin: 0;
    }

    .features-banner-item p {
      font-size: 13px;
      color: var(--text-muted);
      margin: 0;
      line-height: 1.6;
    }

    @media (max-width: 991px) {
      .features-banner-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
      }
    }

    @media (max-width: 576px) {
      .features-banner-grid {
        grid-template-columns: 1fr;
        gap: 20px;
      }

      .showcase-features-banner {
        padding: 24px;
      }
    }

    .pricing-bg {
      background: var(--white);
    }

    .pricing-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    .price-card {
      background: var(--cream);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 36px 32px;
      position: relative;
      transition: transform 0.2s;
    }

    .price-card:hover {
      transform: translateY(-3px);
    }

    .price-card.featured {
      border: 2px solid var(--black);
      background: var(--black);
      color: var(--white);
    }

    .price-badge {
      position: absolute;
      top: -13px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
      background: var(--gold);
      color: var(--black);
      padding: 4px 14px;
      border-radius: 20px;
      white-space: nowrap;
    }

    .price-tier {
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 8px;
    }

    .price-name {
      font-size: 24px;
      font-weight: 700;
      color: inherit;
      margin-bottom: 6px;
    }

    .price-card.featured .price-desc {
      color: rgba(255, 255, 255, 0.55);
    }

    .price-desc {
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 28px;
      line-height: 1.5;
    }

    .price-amount {
      display: flex;
      align-items: flex-end;
      gap: 4px;
      margin-bottom: 28px;
      padding-bottom: 28px;
      border-bottom: 1px solid var(--border);
    }

    .price-card.featured .price-amount {
      border-bottom-color: rgba(255, 255, 255, 0.15);
    }

    .price-currency {
      font-size: 20px;
      font-weight: 700;
      color: var(--gold);
      line-height: 1.3;
    }

    .price-number {
      font-size: 48px;
      font-weight: 700;
      letter-spacing: -2px;
      line-height: 1;
      color: inherit;
    }

    .price-period {
      font-size: 13px;
      color: var(--text-muted);
      margin-bottom: 8px;
    }

    .price-card.featured .price-period {
      color: rgba(255, 255, 255, 0.5);
    }

    .price-features {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 32px;
    }

    .price-features li {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 14px;
      color: inherit;
    }

    .price-card.featured .price-features li {
      color: rgba(255, 255, 255, 0.85);
    }

    .check {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: var(--gold-light);
      color: var(--gold-dark);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      flex-shrink: 0;
      margin-top: 2px;
      font-weight: 700;
    }

    .price-card.featured .check {
      background: rgba(201, 168, 76, 0.2);
      color: var(--gold);
    }

    .btn-pricing {
      display: block;
      width: 100%;
      text-align: center;
      padding: 13px;
      border-radius: 7px;
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s;
      border: none;
    }

    .btn-pricing-default {
      background: var(--gray-100);
      color: var(--black);
      border: 1px solid var(--border);
    }

    .btn-pricing-default:hover {
      background: var(--gray-200);
    }

    .btn-pricing-featured {
      background: var(--gold);
      color: var(--black);
    }

    .btn-pricing-featured:hover {
      background: #D4A83F;
    }

    /* TESTIMONIALS */
    .testimonials-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }

    .testimonial-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 32px 28px;
    }

    .stars {
      color: var(--gold);
      font-size: 14px;
      letter-spacing: 2px;
      margin-bottom: 16px;
    }

    .testimonial-text {
      font-size: 15px;
      line-height: 1.7;
      color: var(--text);
      margin-bottom: 24px;
    }

    .testimonial-author {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .author-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--black);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      font-weight: 700;
      color: var(--gold);
      flex-shrink: 0;
    }

    .author-name {
      font-size: 14px;
      font-weight: 700;
      margin-bottom: 2px;
    }

    .author-role {
      font-size: 12px;
      color: var(--text-muted);
    }

    /* CONTACT */
    .contact-bg {
      background: var(--cream);
    }

    .contact-layout {
      display: grid;
      grid-template-columns: 1fr 1.4fr;
      gap: 72px;
      align-items: start;
    }

    .contact-info h2 {
      font-size: 45px;
      font-weight: 700;
      letter-spacing: -1.5px;
      line-height: 1.1;
      margin-bottom: 20px;
    }

    .contact-info p {
      font-size: 16px;
      color: var(--text-muted);
      line-height: 1.7;
      margin-bottom: 36px;
    }

    .contact-detail {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 14px;
      color: var(--text-muted);
      margin-bottom: 12px;
    }

    .contact-detail strong {
      color: var(--black);
    }

    .contact-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--gold);
      flex-shrink: 0;
    }

    .contact-form {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 44px 40px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 700;
      color: var(--black);
      margin-bottom: 7px;
      letter-spacing: 0.2px;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      height: 46px;
      padding: 0 16px;
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      color: var(--black);
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 7px;
      outline: none;
      transition: border-color 0.2s;
      appearance: none;
    }

    .form-group input:focus,
    .form-group select:focus {
      border-color: var(--black);
    }

    .form-group input::placeholder {
      color: var(--gray-400);
    }

    .form-submit {
      width: 100%;
      padding: 15px;
      background: var(--black);
      color: var(--white);
      font-family: 'Inter', sans-serif;
      font-size: 15px;
      font-weight: 700;
      border: none;
      border-radius: 7px;
      cursor: pointer;
      transition: background 0.2s;
      margin-top: 8px;
    }

    .form-submit:hover {
      background: var(--gray-800);
    }

    .form-note {
      font-size: 12px;
      color: var(--gray-400);
      text-align: center;
      margin-top: 14px;
    }

    /* FAQ */
    .faq-grid-wrapper {
      display: grid;
      grid-template-columns: 1fr 1.5fr;
      gap: 72px;
      align-items: flex-start;
    }

    .faq-header-left {
      text-align: left;
      position: sticky;
      top: 100px;
    }

    .faq-header-left .section-label {
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 20px;
      display: inline-block;
    }

    .faq-header-left .section-title {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-size: 44px;
      font-weight: 400;
      color: var(--black);
      line-height: 1.2;
      margin-bottom: 24px;
      letter-spacing: -0.5px;
    }

    .faq-header-left .section-title em {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-style: italic;
      color: var(--gold);
      font-weight: 400;
    }

    .faq-header-left .section-sub {
      font-size: 16px;
      color: var(--text-muted);
      line-height: 1.6;
      margin-bottom: 32px;
      max-width: 380px;
    }

    .btn-faq-contact {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: transparent;
      color: var(--black);
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 600;
      padding: 12px 28px;
      border: 1px solid rgba(10, 10, 10, 0.15);
      border-radius: 6px;
      cursor: pointer;
      text-decoration: none;
      transition: border-color 0.2s, background 0.2s;
    }

    .btn-faq-contact:hover {
      border-color: var(--black);
      background: rgba(10, 10, 10, 0.03);
    }

    .faq-list {
      width: 100%;
    }

    .faq-item {
      border-bottom: 1px solid var(--border);
      padding: 24px 0;
    }

    .faq-question {
      font-size: 16px;
      font-weight: 700;
      color: var(--black);
      cursor: pointer;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      user-select: none;
      list-style: none;
    }

    .faq-question::-webkit-details-marker {
      display: none;
    }

    details[open] .faq-toggle {
      transform: rotate(45deg);
    }

    .faq-toggle {
      font-size: 22px;
      color: var(--gold);
      transition: transform 0.2s;
      flex-shrink: 0;
      font-weight: 300;
    }

    .faq-answer {
      font-size: 14px;
      color: var(--text-muted);
      line-height: 1.75;
      padding-top: 14px;
    }

    /* CTA BANNER */
    .cta-banner {
      background: radial-gradient(circle at center, #1b1222 0%, #060608 80%);
      padding: 96px 40px;
      text-align: center;
      position: relative;
      overflow: hidden;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .cta-banner::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(201, 168, 76, 0.15) 0%, transparent 70%);
      pointer-events: none;
    }

    .cta-banner h2 {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-size: 45px;
      font-weight: 400;
      letter-spacing: -0.5px;
      color: var(--white);
      margin-bottom: 20px;
      line-height: 1.2;
    }

    .cta-banner h2 em {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-style: italic;
      color: var(--gold);
      font-weight: 400;
    }

    .cta-banner p {
      font-size: 17px;
      color: rgba(255, 255, 255, 0.65);
      margin-bottom: 36px;
      max-width: 480px;
      margin-left: auto;
      margin-right: auto;
      line-height: 1.6;
    }

    .cta-actions {
      display: flex;
      justify-content: center;
      gap: 14px;
      flex-wrap: wrap;
    }

    /* FOOTER */
    footer {
      background: var(--black);
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      padding: 48px 40px 32px;
    }

    .footer-inner {
      max-width: 1080px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 48px;
      margin-bottom: 48px;
    }

    .footer-brand {
      font-size: 20px;
      font-weight: 700;
      color: var(--white);
      margin-bottom: 12px;
    }

    .footer-brand span {
      color: var(--gold);
    }

    .footer-tagline {
      font-size: 14px;
      color: rgba(255, 255, 255, 0.4);
      max-width: 280px;
      line-height: 1.6;
    }

    .footer-col h4 {
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.4);
      margin-bottom: 16px;
    }

    .footer-col ul {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .footer-col a {
      font-size: 14px;
      color: rgba(255, 255, 255, 0.65);
      text-decoration: none;
      transition: color 0.2s;
    }

    .footer-col a:hover {
      color: var(--white);
    }

    .footer-bottom {
      max-width: 1080px;
      margin: 0 auto;
      padding-top: 24px;
      border-top: 1px solid rgba(255, 255, 255, 0.08);
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
    }

    .footer-bottom p {
      font-size: 13px;
      color: rgba(255, 255, 255, 0.3);
    }

    /* Form success */
    .form-success {
      display: none;
      text-align: center;
      padding: 48px 24px;
    }

    .success-icon {
      width: 56px;
      height: 56px;
      background: var(--gold-light);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
      font-size: 24px;
    }

    .form-success h3 {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: 10px;
    }

    .form-success p {
      font-size: 14px;
      color: var(--text-muted);
    }

    @media (max-width: 900px) {
      nav {
        padding: 0 20px;
      }

      .nav-links {
        display: none;
      }

      section {
        padding: 64px 20px;
      }

      .hero {
        padding: 72px 20px 64px;
      }

      .hero h1 {
        font-size: 34px;
      }

      .section-title,
      .contact-info h2,
      .cta-banner h2 {
        font-size: 32px;
      }

      .steps-grid,
      .features-grid,
      .templates-grid,
      .pricing-grid,
      .testimonials-grid {
        grid-template-columns: 1fr;
      }

      .features-grid {
        border: none;
        gap: 12px;
      }

      .feature-cell {
        border: 1px solid var(--border);
        border-radius: 10px;
      }

      .contact-layout {
        grid-template-columns: 1fr;
        gap: 40px;
      }

      .footer-inner {
        grid-template-columns: 1fr 1fr;
        gap: 32px;
      }

      .footer-inner > div:first-child {
        grid-column: 1 / -1;
      }

      .form-row {
        grid-template-columns: 1fr;
      }

      .hero-stats {
        gap: 28px;
      }

      .device-toggle-wrapper {
        display: none;
      }
    }

    /* ── FEATURES LIST SECTION ── */
    #features {
      background: var(--cream);
      color: var(--text);
      padding: 80px 40px;
      position: relative;
    }

    .features-header-wrapper {
      display: grid;
      grid-template-columns: 1.2fr 1fr;
      gap: 48px;
      align-items: flex-start;
      margin-bottom: 56px;
    }

    .features-header-left {
      text-align: left;
    }

    .features-header-left .section-label {
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 2px;
      text-transform: uppercase;
      color: var(--gold);
      margin-bottom: 20px;
      display: inline-block;
    }

    .features-header-left .section-title {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-size: 44px;
      font-weight: 400;
      color: var(--black);
      line-height: 1.2;
      margin-bottom: 24px;
      letter-spacing: -0.5px;
    }

    .features-header-left .section-title em {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-style: italic;
      color: var(--gold);
      font-weight: 400;
    }

    .features-header-left .section-sub {
      font-size: 16px;
      color: var(--text-muted);
      line-height: 1.6;
      margin-bottom: 0;
      max-width: 580px;
    }

    .features-header-right {
      display: flex;
      align-items: flex-start;
      height: 100%;
      padding-top: 48px;
    }

    .features-quote-box {
      border-left: 2px solid var(--gold);
      padding-left: 24px;
      text-align: left;
    }

    .features-quote-text {
      font-family: 'Playfair Display', 'Didot', 'Bodoni MT', 'Cinzel', Georgia, serif;
      font-size: 20px;
      font-style: italic;
      color: var(--black);
      line-height: 1.6;
      margin-bottom: 12px;
    }

    .features-quote-author {
      font-size: 13px;
      color: var(--text-muted);
      letter-spacing: 0.5px;
      display: block;
    }

    .features-grid-list {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px 36px;
    }

    .feature-item-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 14px 20px;
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: 8px;
      transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s, background 0.2s;
    }

    .feature-item-row:hover {
      transform: translateY(-2px);
      background: var(--gray-100);
      border-color: rgba(201, 168, 76, 0.3);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
    }

    .feature-item-left {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .feature-item-icon-box {
      width: 36px;
      height: 36px;
      background: rgba(201, 168, 76, 0.08);
      border: 1px solid rgba(201, 168, 76, 0.2);
      border-radius: 6px;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .feature-item-icon-box svg {
      width: 18px;
      height: 18px;
    }

    .feature-item-title {
      font-size: 14px;
      font-weight: 700;
      color: var(--black);
    }

    .feature-item-badge {
      font-size: 10px;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 4px;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      background: rgba(201, 168, 76, 0.1);
      color: var(--gold-dark);
      border: 1px solid rgba(201, 168, 76, 0.25);
    }

    @media (max-width: 900px) {
      #features {
        padding: 60px 20px;
      }

      .features-header-wrapper {
        grid-template-columns: 1fr;
        gap: 32px;
        margin-bottom: 40px;
      }

      .features-header-right {
        padding-top: 0;
      }

      .features-header-left .section-title {
        font-size: 32px;
      }

      .features-quote-text {
        font-size: 18px;
      }

      .features-grid-list {
        grid-template-columns: 1fr;
        gap: 10px;
      }

      /* TEMPLATES MOBILE */
      #templates {
        padding: 60px 20px;
      }

      .templates-header-wrapper {
        grid-template-columns: 1fr;
        gap: 32px;
        margin-bottom: 40px;
      }

      .templates-header-right {
        padding-top: 0;
        justify-content: flex-start;
      }

      .templates-highlight-box {
        max-width: 100%;
      }

      .templates-header-left .section-title {
        font-size: 32px;
      }

      /* FAQ MOBILE */
      #faq {
        padding: 60px 20px;
      }

      .faq-grid-wrapper {
        grid-template-columns: 1fr;
        gap: 40px;
      }

      .faq-header-left {
        position: static;
      }

      .faq-header-left .section-title {
        font-size: 32px;
      }

      .faq-header-left .section-sub {
        max-width: 100%;
        margin-bottom: 24px;
      }

      /* SHOWCASE MOBILE */
      .showcase-container {
        grid-template-columns: 1fr;
        gap: 32px;
      }

      .showcase-preview-panel {
        order: -1;
        aspect-ratio: 16/10;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .showcase-side-tabs {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        gap: 16px;
      }

      .showcase-tab {
        flex: 1 1 280px;
        max-width: 400px;
      }

      .showcase-tab.active::before {
        display: none;
      }
    }

    /* ── MOBILE PREVIEW MODAL ── */
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

    /* ── DEVICE TOGGLE ── */
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

    /* ── DESKTOP PREVIEW MODE ── */
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
  </style>
</head>

<body>

  <!-- NAV -->
  <nav class="nav-transparent" id="main-nav">
    <a href="#" class="nav-logo">vespr<span>.</span></a>
    <ul class="nav-links">
      <li><a href="#how-it-works">How it works</a></li>
      <li><a href="#features">Features</a></li>
      <li><a href="#templates">Templates</a></li>
      <li><a href="#pricing">Pricing</a></li>
    </ul>
    <div style="display: flex; align-items: center; gap: 24px;">
      <a href="{{ route('admin.common.login') }}" class="nav-login-link">Log in</a>
      <a href="javascript:void(0)" class="btn-nav-action pricing-btn-trigger" data-plan="sprout">Get started</a>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero" id="hero">
    <span class="hero-eyebrow">— ECOMMERCE FOR GROCERY STORES & SUPERMARKETS —</span>
    <h1>Sell your groceries <br> online. <br> <em>No tech skills needed.</em></h1>
    <p class="hero-sub">Vespr gives grocery owners, supermarkets, and local shops a ready-to-launch store — beautiful themes, smart inventory tools, and instant delivery features. From $9/month.</p>
    <div class="hero-cta">
      <a href="javascript:void(0)" class="btn-gold pricing-btn-trigger" data-plan="sprout">Start Free — 1 Month on
        Us</a>
      <a href="#features" class="btn-ghost">Explore Features</a>
    </div>
    <div class="hero-bullets">
      <span>• Unlimited products on all plans</span>
      <span>• First month free, no credit card</span>
      <span>• Built for indie & luxury brands</span>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section id="how-it-works">
    <div class="section-inner">
      <div class="section-label">How it works</div>
      <h2 class="section-title">Up and selling in <em>four steps</em>.</h2>
      <p class="section-sub">No developer. No design agency. Go from signup to live store in under an hour.</p>
      <div class="steps-grid">
        <div class="step-card">
          <div class="step-number">01</div>
          <h3>Create your account</h3>
          <p>Sign up with your email. No credit card needed to start. Your store is ready instantly.</p>
          <span class="step-time">2 minutes</span>
        </div>
        <div class="step-card">
          <div class="step-number">02</div>
          <h3>Pick a theme</h3>
          <p>Choose from grocery-specific templates built to convert. Every design works on mobile.</p>
          <span class="step-time">5 minutes</span>
        </div>
        <div class="step-card">
          <div class="step-number">03</div>
          <h3>Add your products</h3>
          <p>Upload products, set prices, and organize categories. The editor makes it straightforward.</p>
          <span class="step-time">15 minutes</span>
        </div>
        <div class="step-card">
          <div class="step-number">04</div>
          <h3>Go live and sell</h3>
          <p>Connect your domain, link your payment gateway, and open your store to the world.</p>
          <span class="step-time">Today</span>
        </div>
      </div>
    </div>
  </section>

  <!-- INTEGRATIONS BAR -->
  <div class="integrations-bar">
    <div class="integrations-label">Works with any tool you already use</div>
    <div class="integrations-list">
      <span class="integration-pill">Razorpay</span>
      <span class="integration-pill">Stripe</span>
      <span class="integration-pill">PayPal</span>
      <span class="integration-pill">PayU</span>
      <span class="integration-pill">Cashfree</span>
      <span class="integration-pill">Delhivery</span>
      <span class="integration-pill">WhatsApp Business</span>
      <span class="integration-pill">Shiprocket</span>
      <span class="integration-pill">Bluedart</span>
      <span class="integration-pill">Custom gateways</span>
      <span class="integration-pill">+ any payment or shipping partner</span>
    </div>
  </div>

  <!-- FEATURES -->
  <section id="features" class="features-bg">
    <div class="section-inner">
      <div class="features-header-wrapper">
        <div class="features-header-left">
          <span class="section-label">Features</span>
          <h2 class="section-title">Every feature your <br> <em>grocery store</em> needs</h2>
          <p class="section-sub">General ecommerce platforms treat groceries like arbitrary items. <br> Vespr
            understands that fresh groceries need local delivery, batch tracking, and weight-based pricing.</p>
        </div>
        <div class="features-header-right">
          <div class="features-quote-box">
            <p class="features-quote-text">"We saw a 40% lift in daily orders after switching to Vespr's local delivery system."</p>
            <span class="features-quote-author">— Supermarket, UAE</span>
          </div>
        </div>
      </div>

      <div class="features-grid-list">
        <!-- Item 1: Grocery-first themes -->
        <div class="feature-item-row">
          <div class="feature-item-left">
            <div class="feature-item-icon-box">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold-dark)" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path
                  d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 14.7255 3.09032 17.1962 4.85857 19C5.35122 19.5 5.2539 20.5 4.5 20.5C3.7461 20.5 3 21.2539 3 22" />
                <circle cx="7.5" cy="10.5" r="1" fill="var(--gold-dark)" />
                <circle cx="11.5" cy="7.5" r="1" fill="var(--gold-dark)" />
                <circle cx="16.5" cy="9.5" r="1" fill="var(--gold-dark)" />
                <circle cx="15.5" cy="14.5" r="1" fill="var(--gold-dark)" />
              </svg>
            </div>
            <span class="feature-item-title">Grocery-first themes</span>
          </div>
          <span class="feature-item-badge">Design</span>
        </div>

        <!-- Item 2: Secure checkout -->
        <div class="feature-item-row">
          <div class="feature-item-left">
            <div class="feature-item-icon-box">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold-dark)" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
            </div>
            <span class="feature-item-title">Secure checkout</span>
          </div>
          <span class="feature-item-badge">Trust</span>
        </div>

        <!-- Item 3: Grocery bundle builder -->
        <div class="feature-item-row">
          <div class="feature-item-left">
            <div class="feature-item-icon-box">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold-dark)" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path
                  d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                <line x1="12" y1="22.08" x2="12" y2="12" />
              </svg>
            </div>
            <span class="feature-item-title">Grocery combo builder</span>
          </div>
          <span class="feature-item-badge">Sales</span>
        </div>

        <!-- Item 4: Fast-loading stores -->
        <div class="feature-item-row">
          <div class="feature-item-left">
            <div class="feature-item-icon-box">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold-dark)" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
              </svg>
            </div>
            <span class="feature-item-title">Fast-loading stores</span>
          </div>
          <span class="feature-item-badge">Perf</span>
        </div>

        <!-- Item 5: Multi-brand dashboard -->
        <div class="feature-item-row">
          <div class="feature-item-left">
            <div class="feature-item-icon-box">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold-dark)" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="9" />
                <rect x="14" y="3" width="7" height="5" />
                <rect x="14" y="12" width="7" height="9" />
                <rect x="3" y="16" width="7" height="5" />
              </svg>
            </div>
            <span class="feature-item-title">Multi-brand dashboard</span>
          </div>
          <span class="feature-item-badge">Manage</span>
        </div>

        <!-- Item 6: WhatsApp integration -->
        <div class="feature-item-row">
          <div class="feature-item-left">
            <div class="feature-item-icon-box">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold-dark)" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
              </svg>
            </div>
            <span class="feature-item-title">WhatsApp integration</span>
          </div>
          <span class="feature-item-badge">Support</span>
        </div>

        <!-- Item 7: Grocery discovery SEO -->
        <div class="feature-item-row">
          <div class="feature-item-left">
            <div class="feature-item-icon-box">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold-dark)" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                <line x1="11" y1="8" x2="11" y2="14" />
                <line x1="8" y1="11" x2="14" y2="11" />
              </svg>
            </div>
            <span class="feature-item-title">Local discovery SEO</span>
          </div>
          <span class="feature-item-badge">Growth</span>
        </div>

        <!-- Item 8: Any shipping partner -->
        <div class="feature-item-row">
          <div class="feature-item-left">
            <div class="feature-item-icon-box">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold-dark)" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <rect x="1" y="3" width="15" height="13" />
                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8" />
                <circle cx="5.5" cy="18.5" r="2.5" />
                <circle cx="18.5" cy="18.5" r="2.5" />
              </svg>
            </div>
            <span class="feature-item-title">Any shipping partner</span>
          </div>
          <span class="feature-item-badge">Ship</span>
        </div>

        <!-- Item 9: Inventory and analytics -->
        <div class="feature-item-row">
          <div class="feature-item-left">
            <div class="feature-item-icon-box">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold-dark)" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10" />
                <line x1="12" y1="20" x2="12" y2="4" />
                <line x1="6" y1="20" x2="6" y2="14" />
              </svg>
            </div>
            <span class="feature-item-title">Inventory and analytics</span>
          </div>
          <span class="feature-item-badge">Insights</span>
        </div>
      </div>
    </div>
  </section>

  <!-- TEMPLATES -->
  <section id="templates">
    <div class="section-inner">
      <div class="templates-header-wrapper">
        <div class="templates-header-left">
          <span class="section-label">Store templates</span>
          <h2 class="section-title">Choose your <em>aesthetic</em></h2>
          <p class="section-sub">Every template ships with product category pages, filterable search, and quick cart features
            pre-built. Pick the visual identity that matches your brand, then customise from there.</p>
        </div>
        <div class="templates-header-right">
          <div class="templates-highlight-box">
            <div class="template-highlight-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round" style="width:16px;height:16px;flex-shrink:0;">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>100% Customizable layout</span>
            </div>
            <div class="template-highlight-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round" style="width:16px;height:16px;flex-shrink:0;">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>Fully mobile responsive</span>
            </div>
            <div class="template-highlight-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2.5" stroke-linecap="round"
                stroke-linejoin="round" style="width:16px;height:16px;flex-shrink:0;">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <span>Pre-built category layouts</span>
            </div>
          </div>
        </div>
      </div>
      <div class="templates-grid">

        <div class="template-card" data-demo-url="{{ route('v3.home') }}?preview=1">
          <div class="template-preview"
            style="background: linear-gradient(135deg, #1a101e 0%, #372044 50%, #1a101e 100%);">
            <div style="text-align:center;">
              <div style="font-size:11px;letter-spacing:3px;color:rgba(216,180,254,0.7);margin-bottom:8px;">AURA ATELIER
              </div>
              <div style="font-size:26px;font-weight:700;color:#C9A84C;letter-spacing:-1px;">Aura Luxe</div>
              <div style="width:40px;height:1px;background:#C9A84C;margin:12px auto;opacity:0.5;"></div>
              <div style="font-size:11px;color:rgba(255,255,255,0.35);letter-spacing:1px;">Symmetry. Light. Presence.
              </div>
            </div>
            <span class="template-preview-label">Modern Luxury</span>
          </div>
          <div class="template-info">
            <h3>Aura Luxe</h3>
            <p>Central header navigation and high-impact layout, ideal for modern designer brands.</p>
            <span class="template-tag" style="background:#F3E8FF;color:#6B21A8;">Luxury</span>
          </div>
        </div>

        <div class="template-card" data-demo-url="{{ route('velvet.home') }}?preview=1">
          <div class="template-preview"
            style="background: linear-gradient(135deg, #1a1208 0%, #3d2b0e 50%, #1a1208 100%);">
            <div style="text-align:center;">
              <div style="font-size:11px;letter-spacing:3px;color:rgba(201,168,76,0.7);margin-bottom:8px;">MAISON NOIR
              </div>
              <div style="font-size:26px;font-weight:700;color:#C9A84C;letter-spacing:-1px;">Velvet Dark</div>
              <div style="width:40px;height:1px;background:#C9A84C;margin:12px auto;opacity:0.5;"></div>
              <div style="font-size:11px;color:rgba(255,255,255,0.35);letter-spacing:1px;">Exclusive. Rare. Refined.
              </div>
            </div>
            <span class="template-preview-label">Dark luxury</span>
          </div>
          <div class="template-info">
            <h3>Velvet Dark</h3>
            <p>Deep blacks and gold accents. Built for exclusive collections and boutique ateliers.</p>
            <span class="template-tag" style="background:#1A1A1A;color:#C9A84C;">Dark</span>
          </div>
        </div>

        <div class="template-card" data-demo-url="{{ route('v4.home') }}?preview=1">
          <div class="template-preview" style="background: linear-gradient(160deg, #fdfaf6 0%, #f0e8d8 100%);">
            <div style="text-align:center;">
              <div style="font-size:11px;letter-spacing:3px;color:#9E8A6E;margin-bottom:8px;">HERITAGE ATELIER</div>
              <div style="font-size:26px;font-weight:700;color:#3d2b0e;letter-spacing:-1px;">Editorial Cream</div>
              <div style="width:40px;height:1px;background:#C9A84C;margin:12px auto;opacity:0.6;"></div>
              <div style="font-size:11px;color:#9E8A6E;letter-spacing:1px;">Classic craftsmanship</div>
            </div>
            <span class="template-preview-label"
              style="color:rgba(0,0,0,0.5);background:rgba(255,255,255,0.6);">Editorial light</span>
          </div>
          <div class="template-info">
            <h3>Editorial Cream</h3>
            <p>Warm ivory tones with editorial typography. Perfect for organic and artisanal groceries.</p>
            <span class="template-tag" style="background:#F0E8D8;color:#8B6914;">Elegant</span>
          </div>
        </div>

        <div class="template-card" data-demo-url="{{ route('v1.home') }}?preview=1">
          <div class="template-preview"
            style="background: linear-gradient(135deg, #0f1923 0%, #1a2d3d 50%, #0f1923 100%);">
            <div style="text-align:center;">
              <div style="font-size:11px;letter-spacing:3px;color:rgba(100,180,220,0.6);margin-bottom:8px;">FRESH STUDIO
              </div>
              <div style="font-size:26px;font-weight:700;color:#fff;letter-spacing:-1px;">Modern Minimal</div>
              <div style="width:40px;height:1px;background:rgba(255,255,255,0.3);margin:12px auto;"></div>
              <div style="font-size:11px;color:rgba(255,255,255,0.3);letter-spacing:1px;">Clean. Contemporary.</div>
            </div>
            <span class="template-preview-label">Minimal</span>
          </div>
          <div class="template-info">
            <h3>Modern Minimal</h3>
            <p>Clean grids and sharp contrast. Lets your product photography do the talking.</p>
            <span class="template-tag" style="background:#E6EFF8;color:#185FA5;">Modern</span>
          </div>
        </div>

      </div>
      <!-- <div style="text-align:center;margin-top:40px;">
      <a href="#contact" class="btn-outline">Request a demo of all templates →</a>
    </div> -->
    </div>
  </section>

  <!-- PRODUCT PAGES SHOWCASE -->
  <section id="product-showcase" class="features-bg"
    style="border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
    <div class="section-inner">
      <div style="text-align: center; margin-bottom: 32px;">
        <span class="section-label">Product Pages</span>
        <h2 class="section-title"
          style="font-family: 'Playfair Display', serif; font-weight: 400; font-size: 44px; color: var(--black); margin-bottom: 16px;">
          Product-first layout, <em>designed to convert</em></h2>
        <p class="section-sub" style="margin: 0 auto; max-width: 620px;">Vespr showcases your product details, freshness tags, and key attributes in a beautiful, structured layout that is easy to navigate.</p>
      </div>

      <div class="showcase-container">
        <!-- Left Side: Tabs 1 & 2 -->
        <div class="showcase-side-tabs showcase-side-left">
          <div class="showcase-tab active" data-target="grocery-details">
            <span class="showcase-tab-tag">Interactive</span>
            <h3>Category Grid Layout</h3>
            <p>Highlight categories, freshness indicators, and weight/quantity options directly next to the product image.</p>
          </div>
          <div class="showcase-tab" data-target="editorial-bold">
            <span class="showcase-tab-tag">Modern</span>
            <h3>Clean Minimal style</h3>
            <p>Modern layout featuring clean product images, large pricing, and quick add-to-cart actions.</p>
          </div>
        </div>

        <!-- Middle: Preview Panel -->
        <div class="showcase-preview-panel">
          <div class="showcase-image-wrapper active has-mobile" id="grocery-details">
            <img class="showcase-desktop-img" src="{{ asset('Images/product pages/1.png') }}" alt="Category Grid Layout">
            <div class="showcase-mobile-mockup">
              <img src="{{ asset('Images/product pages/m1.png') }}" alt="Category Grid Layout (Mobile)">
            </div>
          </div>
          <div class="showcase-image-wrapper" id="luxury-split">
            <img class="showcase-desktop-img" src="{{ asset('Images/product pages/2.png') }}" alt="Luxury Split Grid">
          </div>
          <div class="showcase-image-wrapper has-mobile" id="editorial-bold">
            <img class="showcase-desktop-img" src="{{ asset('Images/product pages/3.png') }}" alt="Clean Minimal style">
            <div class="showcase-mobile-mockup">
              <img src="{{ asset('Images/product pages/m2.png') }}" alt="Clean Minimal style (Mobile)">
            </div>
          </div>
          <div class="showcase-image-wrapper has-mobile" id="bangalore-bloom">
            <img class="showcase-desktop-img" src="{{ asset('Images/product pages/4.png') }}" alt="Modern Grocery Attributes">
            <div class="showcase-mobile-mockup">
              <img src="{{ asset('Images/product pages/m3.png') }}" alt="Modern Grocery Attributes (Mobile)">
            </div>
          </div>
        </div>

        <!-- Right Side: Tabs 3 & 4 -->
        <div class="showcase-side-tabs showcase-side-right">
          <div class="showcase-tab" data-target="luxury-split">
            <span class="showcase-tab-tag">Luxury</span>
            <h3>Interactive Grocery Grid</h3>
            <p>Split screen layout showcasing fresh grocery items alongside pricing, collections, and weight selectors.
            </p>
          </div>
          <div class="showcase-tab" data-target="bangalore-bloom">
            <span class="showcase-tab-tag">Signature</span>
            <h3>Modern Grocery Attributes</h3>
            <p>Display organic status, freshness window, and volume deals to maximize order sizes.</p>
          </div>
        </div>
      </div>

      <!-- Conversion Boosters Banner -->
      <div class="showcase-features-banner">
        <div class="features-banner-title">Premium Conversion Boosters Included</div>
        <div class="features-banner-grid">
          <div class="features-banner-item">
            <div class="features-banner-icon"><i class="fa-solid fa-credit-card"></i></div>
            <h4>Split Payment Badging</h4>
            <p>Built-in integrations for Tabby & Tamara displaying interest-free splits to reduce cart abandonment.</p>
          </div>
          <div class="features-banner-item">
            <div class="features-banner-icon"><i class="fa-solid fa-truck-fast"></i></div>
            <h4>Delivery Date Estimator</h4>
            <p>Dynamic expected arrival countdowns (e.g. "Get it by tomorrow") showing immediate delivery timelines.</p>
          </div>
          <div class="features-banner-item">
            <div class="features-banner-icon"><i class="fa-solid fa-tags"></i></div>
            <h4>Volume Tier Discounts</h4>
            <p>Automatic triggers (e.g. buy 2 get 15% off) to increase Average Order Value (AOV).</p>
          </div>
          <div class="features-banner-item">
            <div class="features-banner-icon"><i class="fa-solid fa-fire-flame-curved"></i></div>
            <h4>Stock Scarcity Flags</h4>
            <p>High-conversion scarcity highlights and live-viewer counters to drive immediate conversions.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- LIGHTBOX MODAL FOR SHOWCASE IMAGES -->
  <div id="showcase-lightbox" class="lightbox-overlay">
    <span class="lightbox-close">&times;</span>
    <img class="lightbox-content" id="lightbox-img" src="" alt="Expanded View">
    <div id="lightbox-caption"></div>
  </div>

  <!-- PRICING -->
  <section id="pricing" class="pricing-bg">
    <div class="section-inner">
      <div class="section-label">Pricing</div>
      <h2 class="section-title">Straightforward pricing. <em>No surprises</em>.</h2>
      <p class="section-sub">Start with the plan that fits your size. Upgrade any time. We never take a cut of your
        revenue.</p>
      <div class="pricing-grid">

        <!-- Starter -->
        <div class="price-card">
          <div class="price-tier">Starter</div>
          <div class="price-name">Sprout</div>
          <p class="price-desc">For new grocery sellers getting started.</p>
          <div class="price-amount">
            <span class="price-currency">$</span>
            <span class="price-number">9</span>
            <span class="price-period">/month</span>
          </div>
          <ul class="price-features">
            <li><span class="check">✓</span>Up to 20 products</li>
            <li><span class="check">✓</span>All store themes</li>
            <li><span class="check">✓</span>Custom domain</li>
            <li><span class="check">✓</span>Any payment gateway</li>
            <li><span class="check">✓</span>Any shipping partner</li>
            <li><span class="check">✓</span>WhatsApp integration</li>
            <li><span class="check">✓</span>Order management</li>
            <li><span class="check">✓</span>Email support</li>
          </ul>
          <a href="javascript:void(0)" class="btn-pricing btn-pricing-default pricing-btn-trigger"
            data-plan="sprout">Get started</a>
        </div>

        <!-- Growth -->
        <div class="price-card featured">
          <div class="price-badge">Most popular</div>
          <div class="price-tier">Growth</div>
          <div class="price-name">Market</div>
          <p class="price-desc">For growing stores with an expanding catalogue.</p>
          <div class="price-amount">
            <span class="price-currency">$</span>
            <span class="price-number">19</span>
            <span class="price-period">/month</span>
          </div>
          <ul class="price-features">
            <li><span class="check">✓</span>Up to 100 products</li>
            <li><span class="check">✓</span>All store themes</li>
            <li><span class="check">✓</span>Custom domain</li>
            <li><span class="check">✓</span>Any payment gateway</li>
            <li><span class="check">✓</span>Any shipping partner</li>
            <li><span class="check">✓</span>WhatsApp integration</li>
            <li><span class="check">✓</span>Grocery combo builder</li>
            <li><span class="check">✓</span>Advanced SEO tools</li>
            <li><span class="check">✓</span>Priority WhatsApp support</li>
          </ul>
          <a href="javascript:void(0)" class="btn-pricing btn-pricing-featured pricing-btn-trigger"
            data-plan="maison">Get started</a>
        </div>

        <!-- Pro -->
        <div class="price-card">
          <div class="price-tier">Pro</div>
          <div class="price-name">Supermarket</div>
          <p class="price-desc">For established chains with no limits.</p>
          <div class="price-amount">
            <span class="price-currency">$</span>
            <span class="price-number">49</span>
            <span class="price-period">/month</span>
          </div>
          <ul class="price-features">
            <li><span class="check">✓</span>Unlimited products</li>
            <li><span class="check">✓</span>All store themes</li>
            <li><span class="check">✓</span>Custom domain + SSL</li>
            <li><span class="check">✓</span>Any payment gateway</li>
            <li><span class="check">✓</span>Any shipping partner</li>
            <li><span class="check">✓</span>WhatsApp integration</li>
            <li><span class="check">✓</span>Multi-brand dashboard</li>
            <li><span class="check">✓</span>White-label store</li>
            <li><span class="check">✓</span>API & webhook access</li>
            <li><span class="check">✓</span>Dedicated account partner</li>
          </ul>
          <a href="javascript:void(0)" class="btn-pricing btn-pricing-default pricing-btn-trigger"
            data-plan="heritage">Get started</a>
        </div>
      </div>

      <!-- Pricing note -->
      <div style="text-align:center;margin-top:32px;">
        <p style="font-size:14px;color:var(--text-muted);">All plans include a 0% commission rate. You keep every rupee
          you earn. Free setup, no hidden fees.</p>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section id="testimonials" class="features-bg">
    <div class="section-inner">
      <div class="section-label">Success stories</div>
      <h2 class="section-title">Trusted by <em>grocery stores</em>.</h2>
      <p class="section-sub">Independent stores and local supermarkets selling online with Vespr.</p>
      <div class="testimonials-grid">
        <div class="testimonial-card">
          <div class="stars">★★★★★</div>
          <p class="testimonial-text">"Vespr understood the nuance of local grocery retail. Our online store now
            feels extremely premium and user-friendly. We were live in under a day."</p>
          <div class="testimonial-author">
            <div class="author-avatar">MA</div>
            <div>
              <div class="author-name">Marc-Antoine</div>
              <div class="author-role">Operations Manager, L'Amour Grocers</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card">
          <div class="stars">★★★★★</div>
          <p class="testimonial-text">"The combo builder alone increased our average order value by 45%. It's the only
            platform that actually understands how grocery customers buy."</p>
          <div class="testimonial-author">
            <div class="author-avatar">ER</div>
            <div>
              <div class="author-name">Elena Rossi</div>
              <div class="author-role">Founder, Fresh & Fast</div>
            </div>
          </div>
        </div>
        <div class="testimonial-card">
          <div class="stars">★★★★★</div>
          <p class="testimonial-text">"We moved from Shopify in a weekend. The themes look far more premium, and
            managing all our brands from one dashboard is a real game-changer."</p>
          <div class="testimonial-author">
            <div class="author-avatar">JT</div>
            <div>
              <div class="author-name">James Thorne</div>
              <div class="author-role">CEO, Thorne Grocery Group</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq">
    <div class="section-inner">
      <div class="faq-grid-wrapper">
        <div class="faq-header-left">
          <span class="section-label">FAQ</span>
          <h2 class="section-title">Common <em>questions</em></h2>
          <p class="section-sub">Still not sure? Reach out to the Vespr team — we typically respond within a few hours.
          </p>
          <a href="#contact" class="btn-faq-contact">Contact us</a>
        </div>
        <div class="faq-list">

          <details class="faq-item">
            <summary class="faq-question">
              Do I need a developer to set up my store?
              <span class="faq-toggle">+</span>
            </summary>
            <p class="faq-answer">No. Vespr is built for business owners, not developers. You choose a theme, upload
              your
              products, connect your payment gateway, and go live. The whole process takes less than an hour.</p>
          </details>

          <details class="faq-item">
            <summary class="faq-question">
              Which payment gateways can I use?
              <span class="faq-toggle">+</span>
            </summary>
            <p class="faq-answer">Any. Vespr integrates with Razorpay, Stripe, PayPal, PayU, Cashfree, and any other
              gateway you prefer. We don't lock you into a specific processor. You connect the one you already use or
              trust.</p>
          </details>

          <details class="faq-item">
            <summary class="faq-question">
              Can I use my own domain name?
              <span class="faq-toggle">+</span>
            </summary>
            <p class="faq-answer">Yes. You can connect your existing domain on all plans. We handle the SSL certificate
              automatically so your store is secure from day one.</p>
          </details>

          <details class="faq-item">
            <summary class="faq-question">
              Does Vespr take a percentage of my sales?
              <span class="faq-toggle">+</span>
            </summary>
            <p class="faq-answer">No. Vespr charges a flat monthly fee only. We never take a commission on your sales.
              The
              only transaction fee is whatever your payment gateway charges, which goes directly to them — not to us.
            </p>
          </details>

          <details class="faq-item">
            <summary class="faq-question">
              Can I sell internationally?
              <span class="faq-toggle">+</span>
            </summary>
            <p class="faq-answer">Yes. You can connect any international shipping partner and accept payments in
              multiple
              currencies depending on your payment gateway. Vespr puts no geographic restrictions on your store.</p>
          </details>

          <details class="faq-item">
            <summary class="faq-question">
              What is WhatsApp integration used for?
              <span class="faq-toggle">+</span>
            </summary>
            <p class="faq-answer">You can connect WhatsApp Business to send order updates, answer customer queries,
              recover abandoned carts, and handle support — all from your store dashboard. It works with any WhatsApp
              Business account.</p>
          </details>

        </div>
      </div>
    </div>
  </section>

  <!-- CONTACT -->
  {{--
  <section id="contact" class="contact-bg">
    <div class="section-inner">
      <div class="contact-layout">
        <div class="contact-info">
          <div class="section-label">Get in touch</div>
          <h2>Ready to launch your grocery store?</h2>
          <p>Fill in your details and we'll get back to you on WhatsApp or email — usually within a few hours.</p>
          <div class="contact-detail">
            <span class="contact-dot"></span>
            <span>We'll reach out on <strong>WhatsApp or email</strong></span>
          </div>
          <div class="contact-detail">
            <span class="contact-dot"></span>
            <span>Free setup help included on all plans</span>
          </div>
          <div class="contact-detail">
            <span class="contact-dot"></span>
            <span>No commitment. No credit card needed to start.</span>
          </div>
          <div
            style="margin-top:40px;padding:24px;background:var(--white);border:1px solid var(--border);border-radius:10px;">
            <div
              style="font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gray-400);margin-bottom:16px;">
              Starting from</div>
            <div style="display:flex;gap:24px;flex-wrap:wrap;">
              <div>
                <div style="font-size:28px;font-weight:700;color:var(--black);">$9<span
                    style="font-size:14px;font-weight:400;color:var(--text-muted);">/mo</span></div>
                <div style="font-size:12px;color:var(--text-muted);">20 products</div>
              </div>
              <div>
                <div style="font-size:28px;font-weight:700;color:var(--black);">$19<span
                    style="font-size:14px;font-weight:400;color:var(--text-muted);">/mo</span></div>
                <div style="font-size:12px;color:var(--text-muted);">100 products</div>
              </div>
              <div>
                <div style="font-size:28px;font-weight:700;color:var(--black);">$49<span
                    style="font-size:14px;font-weight:400;color:var(--text-muted);">/mo</span></div>
                <div style="font-size:12px;color:var(--text-muted);">Unlimited</div>
              </div>
            </div>
          </div>
        </div>

        <div class="contact-form">
          <div id="form-fields">
            <h3 class="subheading" style="margin-bottom:28px;">Tell us about your brand</h3>
            <div class="form-row">
              <div class="form-group">
                <label for="name">Your name *</label>
                <input type="text" id="name" placeholder="e.g. Priya Sharma" required />
              </div>
              <div class="form-group">
                <label for="business">Business name *</label>
                <input type="text" id="business" placeholder="e.g. Noir Atelier" required />
              </div>
            </div>
            <div class="form-group">
              <label for="country">Country *</label>
              <select id="country" required>
                <option value="" disabled selected>Select your country</option>
                <option>India</option>
                <option>United Arab Emirates</option>
                <option>Saudi Arabia</option>
                <option>United Kingdom</option>
                <option>United States</option>
                <option>France</option>
                <option>Singapore</option>
                <option>Australia</option>
                <option>Other</option>
              </select>
            </div>
            <div class="form-group">
              <label for="email">Best email address *</label>
              <input type="email" id="email" placeholder="you@yourbrand.com" required />
            </div>
            <div class="form-group">
              <label for="whatsapp">WhatsApp number *</label>
              <input type="tel" id="whatsapp" placeholder="+91 98765 43210" required />
            </div>
            <div class="form-group">
              <label for="plan">Plan you're interested in</label>
              <select id="plan">
                <option value="" disabled selected>Select a plan</option>
                <option>Sprout — $9/month (20 products)</option>
                <option>Maison — $19/month (100 products)</option>
                <option>Heritage — $49/month (Unlimited)</option>
                <option>Not sure yet</option>
              </select>
            </div>
            <button class="form-submit" onclick="submitForm()">Send message →</button>
            <p class="form-note">We'll reply on WhatsApp or email within a few hours.</p>
          </div>

          <div class="form-success" id="form-success">
            <div class="success-icon">✓</div>
            <h3>Message sent!</h3>
            <p>We'll reach out on WhatsApp or email shortly. Looking forward to helping you launch.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  --}}

  <!-- CTA BANNER -->
  <div class="cta-banner">
    <h2>Your grocery store, <em>live today.</em></h2>
    <p>Join grocery brands already selling with Vespr. No setup fee. No developer needed.</p>
    <div class="cta-actions">
      <a href="#pricing" class="btn-gold">Start for free →</a>
      <a href="#pricing" class="btn-ghost">View pricing</a>
    </div>
  </div>

  <!-- FOOTER -->
  <footer>
    <div class="footer-inner">
      <div>
        <div class="footer-brand">vespr<span>.</span></div>
        <p class="footer-tagline">The e-commerce platform built for grocers and supermarkets. From local shops to global
          chains.</p>
        <p style="font-size:13px;color:rgba(255,255,255,0.35);margin-top:16px;">support@vespr.com</p>
      </div>
      <div class="footer-col">
        <h4>Platform</h4>
        <ul>
          <li><a href="#how-it-works">How it works</a></li>
          <li><a href="#features">Features</a></li>
          <li><a href="#templates">Templates</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="#faq">FAQ</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Company</h4>
        <ul>
          <li><a href="#contact">Contact</a></li>
          <li><a href="#">Privacy policy</a></li>
          <li><a href="#">Terms of service</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 Vespr. All rights reserved.</p>
      <p>Built for grocery brands worldwide.</p>
    </div>
  </footer>

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
                    <option value="+91" selected>🇮🇳 +91</option>
                    <option value="+971">🇦🇪 +971</option>
                    <option value="+966">🇸🇦 +966</option>
                    <option value="+44">🇬🇧 +44</option>
                    <option value="+1">🇺🇸 +1</option>
                    <option value="+33">🇫🇷 +33</option>
                    <option value="+65">🇸🇬 +65</option>
                    <option value="+61">🇦🇺 +61</option>
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
                  <option value="sprout">Sprout — $9/month (20 products)</option>
                  <option value="maison">Maison — $19/month (100 products)</option>
                  <option value="heritage">Heritage — $49/month (Unlimited)</option>
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
          <p>Your email has been verified successfully. Welcome to Vespr! Let's get started setting up your customized
            grocery store.</p>
          <button onclick="closeSaaSModal()" class="saas-success-finish-btn">Go to your SaaS dashboard →</button>
        </div>
      </div>

    </div>
  </div>

  <style>
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
        gap: 0;
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
        nextBtn.textContent = 'Create my account →';
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

    // Sticky Header Scroll Transition
    const headerNav = document.getElementById('main-nav');
    const heroSection = document.getElementById('hero');
    function handleScroll() {
      const heroHeight = heroSection ? heroSection.offsetHeight : 600;
      const transitionPoint = heroHeight - 64;

      if (window.scrollY >= transitionPoint) {
        headerNav.classList.remove('nav-transparent', 'nav-transparent-blur');
        headerNav.classList.add('nav-scrolled');
      } else {
        headerNav.classList.remove('nav-scrolled');
        headerNav.classList.add('nav-transparent');

        if (window.scrollY > 20) {
          headerNav.classList.add('nav-transparent-blur');
        } else {
          headerNav.classList.remove('nav-transparent-blur');
        }
      }
    }
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Run once initially

    // Product Page Showcase Tab Switcher & Auto-rotation
    const showcaseTabs = document.querySelectorAll('.showcase-tab');
    const showcaseImages = document.querySelectorAll('.showcase-image-wrapper');
    let activeIndex = 0;
    let rotationInterval;

    function switchTab(index) {
      activeIndex = index;
      const tab = showcaseTabs[activeIndex];

      showcaseTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      showcaseImages.forEach(img => img.classList.remove('active'));
      const targetId = tab.getAttribute('data-target');
      const targetImg = document.getElementById(targetId);
      if (targetImg) {
        targetImg.classList.add('active');
      }
    }

    function startAutoRotation() {
      stopAutoRotation();
      rotationInterval = setInterval(() => {
        let nextIndex = (activeIndex + 1) % showcaseTabs.length;
        switchTab(nextIndex);
      }, 5000);
    }

    function stopAutoRotation() {
      if (rotationInterval) {
        clearInterval(rotationInterval);
      }
    }

    showcaseTabs.forEach((tab, index) => {
      tab.addEventListener('click', () => {
        switchTab(index);
        // Reset the timer when a user interacts manually so it doesn't skip immediately
        startAutoRotation();
      });
    });

    // Pause auto-rotation when mouse is hovering the showcase container
    const showcaseContainer = document.querySelector('.showcase-container');
    if (showcaseContainer) {
      showcaseContainer.addEventListener('mouseenter', () => {
        stopAutoRotation();
      });
      showcaseContainer.addEventListener('mouseleave', () => {
        // Only resume if lightbox is not active
        const lightbox = document.getElementById('showcase-lightbox');
        if (lightbox && !lightbox.classList.contains('active')) {
          startAutoRotation();
        }
      });
    }

    // Lightbox modal functionality
    const showcasePreviewPanel = document.querySelector('.showcase-preview-panel');
    const lightbox = document.getElementById('showcase-lightbox');
    const lightboxImg = document.getElementById('lightbox-img');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const lightboxClose = document.querySelector('.lightbox-close');

    if (showcasePreviewPanel && lightbox && lightboxImg) {
      showcasePreviewPanel.addEventListener('click', (e) => {
        const clickedMobile = e.target.closest('.showcase-mobile-mockup');
        const activeImgWrapper = showcasePreviewPanel.querySelector('.showcase-image-wrapper.active');
        if (activeImgWrapper) {
          if (clickedMobile) {
            const mobileImg = clickedMobile.querySelector('img');
            lightboxImg.src = mobileImg.src;
            lightboxCaption.textContent = mobileImg.alt + " (Mobile View)";
          } else {
            const desktopImg = activeImgWrapper.querySelector('.showcase-desktop-img');
            lightboxImg.src = desktopImg.src;
            lightboxCaption.textContent = desktopImg.alt + " (Desktop View)";
          }
          lightbox.classList.add('active');
          document.body.style.overflow = 'hidden'; // Stop background scrolling
          stopAutoRotation();
        }
      });

      const closeLightbox = () => {
        lightbox.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
        startAutoRotation();
      };

      lightboxClose.addEventListener('click', closeLightbox);
      lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) {
          closeLightbox();
        }
      });
    }

    // Start auto-rotation initially
    startAutoRotation();
  </script>
</body>

</html>
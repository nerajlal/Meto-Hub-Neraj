# How to Build, Test, and Generate Code Coverage Reports

This guide documents the procedures for building the application, executing automated unit and feature test cases, running browser UI automation tests, observing repository health, and generating code coverage reports for **MetoHub**.

---

## 1. Building the Application

### 1.1 Backend Dependencies & Storage Setup
Install PHP packages and connect public storage:

```bash
# Install PHP dependencies from composer.lock
composer install

# Create the public storage symlink
php artisan storage:link
```

### 1.2 Frontend Assets Build
Compile and bundle frontend assets using Vite and Tailwind CSS:

```bash
# Install Node dependencies
npm install

# Build production assets (Vite)
npm run build

# Or run the Vite development server with hot module reload
npm run dev
```

### 1.3 Full Local Setup in One Step
You can run the consolidated setup script defined in `composer.json`:

```bash
composer run setup
```

---

## 2. Running Automated Test Cases

The application uses **PHPUnit** configured via [phpunit.xml](../phpunit.xml) with an isolated in-memory SQLite database (`:memory:`). Tests run without altering your local or production MySQL database.

### 2.1 Run the Full Test Suite
Execute all Unit and Feature tests:

```bash
composer test
# or:
php artisan test
```

### 2.2 Run Specific Test Suites or Files
To test a specific feature module:

```bash
# Test all 15 storefront themes and product presentation
php artisan test tests/Feature/StorefrontThemesTest.php

# Test subscription lifecycles and Razorpay billing
php artisan test tests/Feature/SubscriptionLifecycleTest.php

# Test landing page and marketing components
php artisan test tests/Feature/LandingHomepageTest.php

# Test security and multi-tenant isolation
php artisan test tests/Feature/CommerceSecurityTest.php
```

### 2.3 Useful Test Flags
```bash
# Stop immediately on the first failure
php artisan test --stop-on-failure

# Filter tests by method name
php artisan test --filter=test_overview_is_read_only

# Compact output for quick terminal summaries
php artisan test --compact
```

---

## 3. Running Browser Automation & UI Logic Testing

End-to-end browser automation scripts live in [`tests/browser/`](../tests/browser/). They drive headless **Google Chrome** via the Chrome DevTools Protocol ([`chrome_cdp.py`](../tests/browser/chrome_cdp.py)) to verify client-side UI logic, viewport responsiveness, sticky headings, carousel movements, and interactive modals.

### 3.1 Requirements
* Python 3
* Google Chrome or Chromium installed (`/Applications/Google Chrome.app` on macOS)

### 3.2 Running the Browser Automation Scripts
```bash
# 1. Landing page responsiveness (320px, 375px, 768px, 1440px), carousels, and onboarding wizard
python3 tests/browser/run_landing_page.py

# 2. Checkout & platform billing flow verification
python3 tests/browser/run_billing_checkout.py

# 3. Store integrations & analytics tracking runtime
python3 tests/browser/run_store_integrations.py

# 4. Settings overview interface
python3 tests/browser/run_settings_overview.py

# 5. Subscription expiry & store suspension behavior
python3 tests/browser/run_subscription_expiry.py
```

### 3.3 Capturing Visual Screenshots
Pass the `--screenshots` flag to save viewport screenshots during the run:

```bash
python3 tests/browser/run_landing_page.py --screenshots ./screenshots
```

---

## 4. Code Coverage Reporting

Code coverage measures the percentage of backend code executed during automated tests.

### 4.1 Prerequisites: Install a Coverage Driver
PHP requires either **PCOV** (recommended for speed) or **Xdebug** to collect code coverage.

#### For macOS (Homebrew PHP):
```bash
# Install and enable PCOV (fastest)
pecl install pcov
# Or install via Homebrew if packaged:
# brew install php-pcov
```

#### For XAMPP / Custom PHP:
Ensure your `php.ini` has PCOV or Xdebug enabled:
```ini
; In php.ini
extension=pcov.so
pcov.enabled=1
```
Verify the extension is active:
```bash
php -m | grep -iE 'pcov|xdebug'
```

### 4.2 Running CLI Code Coverage
Once PCOV or Xdebug is enabled:

```bash
# View terminal summary with coverage percentages
php artisan test --coverage

# Enforce a minimum coverage threshold (e.g., 80%)
php artisan test --coverage --min=80
```

### 4.3 Generating an Interactive HTML Coverage Report
To view a line-by-line visual report of covered vs. uncovered files:

```bash
# Generate the report in a coverage-report/ directory
./vendor/bin/phpunit --coverage-html coverage-report

# Open the report in your default browser
open coverage-report/index.html
```

---

## 5. Checking System & Code Status

Use these diagnostic commands to verify configuration, migrations, and production readiness:

| Command | Purpose |
| :--- | :--- |
| `php artisan about` | Displays environment, PHP/Laravel versions, active cache drivers, and storage status. |
| `php artisan migrate:status` | Shows which database migrations have run and which are pending. |
| `php artisan app:preflight` | Evaluates production readiness (verifies secure cookies, HTTPS domain, mailer, and VAPID push keys). |
| `composer check-platform-reqs` | Verifies that the host environment satisfies all PHP version and extension requirements. |
| `php artisan route:list` | Lists all registered web and API routes with their middleware. |

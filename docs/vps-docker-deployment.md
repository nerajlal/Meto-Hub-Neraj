# MetoHub VPS Docker Deployment Guide

This guide walks you through deploying **MetoHub** to any Linux VPS (Ubuntu/Debian, DigitalOcean, Hetzner, AWS, Linode, etc.) using Docker & Docker Compose.

---

## 1. Prerequisites on the VPS

SSH into your VPS and install Docker & Git (if not already installed):

```bash
# Update packages
sudo apt update && sudo apt upgrade -y

# Install Docker & Docker Compose plugin
sudo apt install -y git curl ufw
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER

# Log out and log back in for docker group to take effect
exit
```

---

## 2. Clone the Repository & Configure Environment

Clone the repository onto the VPS:

```bash
git clone https://github.com/nerajlal/Meto-Hub-Neraj.git /var/www/metohub
cd /var/www/metohub
```

Create your production `.env` file from the production template:

```bash
cp .env.production.example .env
nano .env
```

### Essential `.env` Variables to Configure:
```dotenv
APP_NAME=MetoHub
APP_ENV=production
APP_KEY=base64:YP6XwZAuRWqSCc3tshtwVvlAKrxT6+GQLDtBnh67Eg8=  # Or run php artisan key:generate
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=metohub
DB_USERNAME=metohub_user
DB_PASSWORD=your_secure_password
DB_ROOT_PASSWORD=your_secure_root_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

## 3. Build & Start the Containers

Run the compose stack in detached mode:

```bash
docker compose up -d --build
```

Verify that all containers are healthy and running:

```bash
docker compose ps
```

You should see:
- `metohub_app` (PHP-FPM)
- `metohub_web` (Nginx on Port 80)
- `metohub_worker` (Queue worker for order emails & push notifications)
- `metohub_scheduler` (Background cron for billing & shipment emails)
- `metohub_db` (MySQL 8.0)

---

## 4. Run Migrations & Setup

Run database migrations inside the app container:

```bash
docker compose exec app php artisan migrate --force
```

*(Optional: if you want to seed the demo stores and super admin)*:
```bash
docker compose exec app php artisan db:seed --class=FreshDemoSeeder --force
```

Optimize application caches:
```bash
docker compose exec app php artisan optimize
```

---

## 5. SSL / HTTPS (Let's Encrypt with Certbot)

If your VPS has Certbot installed on the host, or you use a reverse proxy (e.g. Caddy / Nginx on host / Cloudflare):

### Method A: Free Cloudflare SSL (Easiest)
1. Point your domain DNS to the VPS IP.
2. Enable Cloudflare Proxy (Orange Cloud).
3. Set SSL/TLS mode to **"Full"** or **"Flexible"**.

### Method B: Certbot on Host
Install Certbot on the VPS:
```bash
sudo apt install -y certbot python3-certbot-nginx
```

---

## 6. Daily Operations Cheat Sheet

| Task | Command |
|---|---|
| **View real-time logs** | `docker compose logs -f app` or `docker compose logs -f worker` |
| **Restart services** | `docker compose restart` |
| **Stop everything** | `docker compose down` *(Notice: never use `-v` so your database volume is preserved)* |
| **Deploy updates from git** | `git pull && docker compose up -d --build && docker compose exec app php artisan migrate --force && docker compose exec app php artisan optimize` |
| **Database backup** | `docker compose exec db mysqldump -u root -p[ROOT_PASSWORD] metohub > backup_$(date +%F).sql` |

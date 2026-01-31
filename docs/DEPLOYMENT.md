# Deployment Guide - Limon ERP v2.0

## Overview

This guide covers deploying Limon ERP v2.0 to production environments.

## 📋 Prerequisites

- Server with Docker and Docker Compose
- Domain name (optional but recommended)
- SSL certificate (Let's Encrypt recommended)
- PostgreSQL compatible hosting (or use Docker)
- Minimum 2GB RAM, 2 CPU cores

## 🚀 Deployment Options

### Option 1: Docker Compose (Recommended)

#### Step 1: Prepare Server

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Create application user
sudo useradd -m -s /bin/bash limon
sudo usermod -aG docker limon
```

#### Step 2: Clone Repository

```bash
# Switch to limon user
su - limon

# Clone repository
git clone https://github.com/dnlbertoni/limon.git
cd limon
git checkout main  # or your production branch
```

#### Step 3: Configure Environment

```bash
# Copy and edit environment file
cp .env.example.new .env
nano .env

# IMPORTANT: Set these variables:
# - POSTGRES_PASSWORD (strong password)
# - SECRET_KEY (32+ random characters)
# - BACKEND_CORS_ORIGINS (your domain)
# - HASAR_2_HOST (if using Hasar 2.0)
# - HASAR_2_PASSWORD (fiscal printer password)
```

Example production `.env`:
```env
POSTGRES_DATABASE=limon_prod
POSTGRES_USER=limon_prod
POSTGRES_PASSWORD=CHANGE_THIS_STRONG_PASSWORD

SECRET_KEY=CHANGE_THIS_TO_RANDOM_32_PLUS_CHARACTERS

BACKEND_CORS_ORIGINS=["https://erp.yourdomain.com"]

HASAR_LEGACY_PATH=/var/www/fiscal
HASAR_2_HOST=192.168.1.100
HASAR_2_PASSWORD=fiscal_printer_password

ENVIRONMENT=production
DEBUG=False
```

#### Step 4: Build and Start Services

```bash
# Build images
docker-compose -f docker-compose.new.yml build

# Start services
docker-compose -f docker-compose.new.yml up -d

# Check status
docker-compose -f docker-compose.new.yml ps

# View logs
docker-compose -f docker-compose.new.yml logs -f
```

#### Step 5: Initialize Database

```bash
# Create tables
docker-compose -f docker-compose.new.yml exec backend python -c "
from app.core.database import engine, Base
from app.models.user import User
from app.models.article import Article, Brand, Category
from app.models.invoice import Invoice, InvoiceType, InvoiceItem, Customer
Base.metadata.create_all(bind=engine)
print('Database tables created successfully')
"

# Create admin user
docker-compose -f docker-compose.new.yml exec backend python -c "
from app.core.database import SessionLocal
from app.models.user import User
from app.core.security import get_password_hash

db = SessionLocal()
admin = User(
    username='admin',
    email='admin@limon.com',
    hashed_password=get_password_hash('admin123'),
    full_name='Administrator',
    is_superuser=True
)
db.add(admin)
db.commit()
print('Admin user created: username=admin, password=admin123')
print('IMPORTANT: Change the password immediately!')
"
```

### Option 2: Manual Deployment

#### Backend Deployment

```bash
# Install Python and dependencies
sudo apt install python3.11 python3.11-venv python3-pip postgresql-client

# Create virtual environment
cd backend
python3.11 -m venv venv
source venv/bin/activate
pip install -r requirements.txt

# Configure systemd service
sudo nano /etc/systemd/system/limon-backend.service
```

Service file:
```ini
[Unit]
Description=Limon ERP Backend
After=network.target postgresql.service

[Service]
User=limon
WorkingDirectory=/home/limon/limon/backend
Environment="PATH=/home/limon/limon/backend/venv/bin"
EnvironmentFile=/home/limon/limon/.env
ExecStart=/home/limon/limon/backend/venv/bin/uvicorn app.main:app --host 0.0.0.0 --port 8000
Restart=always

[Install]
WantedBy=multi-user.target
```

```bash
# Enable and start service
sudo systemctl daemon-reload
sudo systemctl enable limon-backend
sudo systemctl start limon-backend
sudo systemctl status limon-backend
```

#### Frontend Deployment

```bash
# Build frontend
cd frontend
npm install
npm run build

# Serve with nginx
sudo apt install nginx

# Configure nginx
sudo nano /etc/nginx/sites-available/limon
```

Nginx configuration:
```nginx
server {
    listen 80;
    server_name erp.yourdomain.com;
    
    # Frontend
    location / {
        root /home/limon/limon/frontend/dist;
        try_files $uri $uri/ /index.html;
    }
    
    # Backend API
    location /api {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/limon /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## 🔒 SSL/HTTPS Setup

### Using Let's Encrypt (Free)

```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d erp.yourdomain.com

# Auto-renewal (already configured by certbot)
sudo certbot renew --dry-run
```

### Manual SSL Certificate

```bash
# If you have your own certificate
sudo nano /etc/nginx/sites-available/limon
```

Add SSL configuration:
```nginx
server {
    listen 443 ssl http2;
    server_name erp.yourdomain.com;
    
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    # SSL settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    
    # ... rest of configuration
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name erp.yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

## 🗄️ Database Setup

### External PostgreSQL

If using an external PostgreSQL server:

```bash
# Update .env
DATABASE_URL=postgresql://user:password@postgres-server:5432/limon_db

# Run migrations
docker-compose -f docker-compose.new.yml exec backend alembic upgrade head
```

## 📊 Monitoring

### Setup Monitoring

```bash
# Install monitoring tools
sudo apt install prometheus grafana

# Configure Prometheus
sudo nano /etc/prometheus/prometheus.yml
```

Add target:
```yaml
scrape_configs:
  - job_name: 'limon-backend'
    static_configs:
      - targets: ['localhost:8000']
```

### Log Management

```bash
# Configure log rotation
sudo nano /etc/logrotate.d/limon
```

```
/home/limon/limon/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 limon limon
    sharedscripts
}
```

## 🔄 Updates and Maintenance

### Updating the Application

```bash
# Pull latest changes
cd /home/limon/limon
git pull origin main

# Rebuild and restart (Docker)
docker-compose -f docker-compose.new.yml down
docker-compose -f docker-compose.new.yml build
docker-compose -f docker-compose.new.yml up -d

# Or restart services (Manual)
sudo systemctl restart limon-backend
cd frontend && npm run build
sudo systemctl reload nginx
```

### Database Backups

```bash
# Create backup script
nano ~/backup-limon.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/home/limon/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup PostgreSQL
docker-compose -f /home/limon/limon/docker-compose.new.yml exec -T postgres pg_dump -U limon_user limon_db > "$BACKUP_DIR/limon_db_$DATE.sql"

# Compress backup
gzip "$BACKUP_DIR/limon_db_$DATE.sql"

# Keep only last 30 days
find $BACKUP_DIR -name "limon_db_*.sql.gz" -mtime +30 -delete

echo "Backup completed: limon_db_$DATE.sql.gz"
```

```bash
# Make executable
chmod +x ~/backup-limon.sh

# Add to crontab (daily at 2 AM)
crontab -e
```

Add line:
```
0 2 * * * /home/limon/backup-limon.sh
```

## 🔥 Firewall Configuration

```bash
# UFW (Ubuntu)
sudo ufw allow ssh
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# If exposing PostgreSQL (not recommended)
# sudo ufw allow from YOUR_IP to any port 5432
```

## 📈 Performance Tuning

### PostgreSQL

```bash
# Edit PostgreSQL config
sudo nano /etc/postgresql/15/main/postgresql.conf
```

Recommended settings:
```conf
shared_buffers = 256MB
effective_cache_size = 1GB
work_mem = 16MB
maintenance_work_mem = 64MB
max_connections = 100
```

### Nginx

```nginx
# Add to nginx.conf
worker_processes auto;
worker_connections 1024;

# Enable gzip
gzip on;
gzip_types text/plain text/css application/json application/javascript;
```

## ✅ Deployment Checklist

- [ ] Server configured with Docker
- [ ] Repository cloned
- [ ] `.env` configured with production values
- [ ] Services started and running
- [ ] Database initialized
- [ ] Admin user created and password changed
- [ ] SSL certificate installed
- [ ] HTTPS enabled and working
- [ ] Firewall configured
- [ ] Backups configured
- [ ] Monitoring set up
- [ ] Log rotation configured
- [ ] DNS configured
- [ ] Email tested (if applicable)
- [ ] Hasar printer connection tested
- [ ] Performance tested
- [ ] Security audit completed

## 🆘 Troubleshooting

### Services won't start

```bash
# Check logs
docker-compose -f docker-compose.new.yml logs

# Check system resources
docker stats

# Check disk space
df -h
```

### Database connection issues

```bash
# Test connection
docker-compose -f docker-compose.new.yml exec postgres psql -U limon_user -d limon_db

# Check environment variables
docker-compose -f docker-compose.new.yml exec backend env | grep DATABASE
```

### Nginx errors

```bash
# Test configuration
sudo nginx -t

# Check error log
sudo tail -f /var/log/nginx/error.log

# Check access log
sudo tail -f /var/log/nginx/access.log
```

## 📞 Support

For deployment issues, contact your system administrator or create an issue on GitHub.

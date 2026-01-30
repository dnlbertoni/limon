# Quick Start Guide - Limon ERP v2.0

Get up and running with Limon ERP in minutes!

## 🚀 Quick Start with Docker (Recommended)

### 1. Prerequisites

- Docker and Docker Compose installed
- Git installed
- 4GB+ available RAM

### 2. Clone and Setup

```bash
# Clone repository
git clone https://github.com/dnlbertoni/limon.git
cd limon

# Copy environment file
cp .env.example.new .env

# Edit environment variables (optional for development)
nano .env
```

### 3. Start Application

```bash
# Start all services
docker-compose -f docker-compose.new.yml up -d

# Wait for services to be ready (check logs)
docker-compose -f docker-compose.new.yml logs -f
```

### 4. Initialize Database

```bash
# Create tables and admin user
docker-compose -f docker-compose.new.yml exec backend python -c "
from app.core.database import engine, Base, SessionLocal
from app.models.user import User
from app.models.article import Article, Brand, Category
from app.models.invoice import Invoice, InvoiceType, InvoiceItem, Customer
from app.core.security import get_password_hash

# Create tables
Base.metadata.create_all(bind=engine)

# Create admin user
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
db.close()

print('✓ Database initialized')
print('✓ Admin user created')
print('  Username: admin')
print('  Password: admin123')
print('  CHANGE PASSWORD AFTER FIRST LOGIN!')
"
```

### 5. Access Application

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000
- **API Documentation**: http://localhost:8000/api/docs
- **PgAdmin**: http://localhost:5050

**Default Login:**
- Username: `admin`
- Password: `admin123`

⚠️ **Change the default password immediately!**

## 🛠️ Development Setup (Without Docker)

### Backend

```bash
cd backend

# Create virtual environment
python -m venv venv
source venv/bin/activate  # Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Set up database (PostgreSQL must be running)
export DATABASE_URL="postgresql://user:password@localhost:5432/limon_db"
export SECRET_KEY="your-secret-key-min-32-characters"

# Initialize database
python -c "
from app.core.database import engine, Base
from app.models.user import User
from app.models.article import Article, Brand, Category
from app.models.invoice import Invoice, InvoiceType, InvoiceItem, Customer
Base.metadata.create_all(bind=engine)
"

# Run server
uvicorn app.main:app --reload
```

Backend will be available at http://localhost:8000

### Frontend

```bash
cd frontend

# Install dependencies
npm install

# Set API URL
export VITE_API_URL=http://localhost:8000

# Run development server
npm run dev
```

Frontend will be available at http://localhost:3000

## 📝 First Steps

### 1. Login

Navigate to http://localhost:3000 and login with:
- Username: `admin`
- Password: `admin123`

### 2. Change Password

Go to your profile and change the default password.

### 3. Create Test Data

#### Create a Brand
```bash
curl -X POST http://localhost:8000/api/articles/brands \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "Test Brand", "description": "Test brand for demo"}'
```

#### Create a Category
```bash
curl -X POST http://localhost:8000/api/articles/categories \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "Test Category", "description": "Test category"}'
```

#### Create an Article
```bash
curl -X POST http://localhost:8000/api/articles/ \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "ART001",
    "name": "Test Article",
    "description": "Test article for demo",
    "price": 100.00,
    "cost": 50.00,
    "stock": 10
  }'
```

Or use the web interface at http://localhost:3000/articles

### 4. Test Fiscal Printer (Optional)

Navigate to http://localhost:3000/fiscal-printer to test Hasar printer integration.

**For Legacy Hasar:**
- Set version to "Legacy"
- Ensure `/var/www/fiscal/{punto_venta}/` directories exist

**For Hasar 2.0:**
- Set version to "2.0"
- Configure printer IP and password in settings

## 🧪 Verify Installation

### Check All Services

```bash
# Using Docker
docker-compose -f docker-compose.new.yml ps

# All services should show "Up"
```

### Test API

```bash
# Health check
curl http://localhost:8000/health

# Should return: {"status": "healthy"}

# API documentation
open http://localhost:8000/api/docs
```

### Test Frontend

```bash
# Open in browser
open http://localhost:3000

# Should show login page
```

### Test Database

```bash
# Using Docker
docker-compose -f docker-compose.new.yml exec postgres psql -U limon_user -d limon_db

# List tables
\dt

# Exit
\q
```

## 🔧 Common Commands

### Docker Commands

```bash
# Start services
docker-compose -f docker-compose.new.yml up -d

# Stop services
docker-compose -f docker-compose.new.yml down

# View logs
docker-compose -f docker-compose.new.yml logs -f

# Restart a service
docker-compose -f docker-compose.new.yml restart backend

# Rebuild services
docker-compose -f docker-compose.new.yml build

# Clean up
docker-compose -f docker-compose.new.yml down -v
```

### Database Commands

```bash
# Backup database
docker-compose -f docker-compose.new.yml exec postgres pg_dump -U limon_user limon_db > backup.sql

# Restore database
docker-compose -f docker-compose.new.yml exec -T postgres psql -U limon_user -d limon_db < backup.sql

# Access database shell
docker-compose -f docker-compose.new.yml exec postgres psql -U limon_user -d limon_db
```

## 🐛 Troubleshooting

### Port Already in Use

```bash
# Change ports in docker-compose.new.yml
# Example: "3001:3000" instead of "3000:3000"
```

### Database Connection Error

```bash
# Check PostgreSQL is running
docker-compose -f docker-compose.new.yml ps postgres

# Check logs
docker-compose -f docker-compose.new.yml logs postgres

# Restart database
docker-compose -f docker-compose.new.yml restart postgres
```

### Frontend Can't Connect to Backend

```bash
# Check backend is running
docker-compose -f docker-compose.new.yml ps backend

# Check CORS settings in backend/.env
BACKEND_CORS_ORIGINS=["http://localhost:3000"]

# Restart backend
docker-compose -f docker-compose.new.yml restart backend
```

### Permission Denied Errors

```bash
# Fix permissions
sudo chown -R $USER:$USER .

# For fiscal printer directory
sudo mkdir -p /var/www/fiscal
sudo chown -R $USER:$USER /var/www/fiscal
```

## 📚 Next Steps

1. **Read Documentation**
   - [Full Documentation](README.new.md)
   - [Security Guide](docs/SECURITY.md)
   - [Deployment Guide](docs/DEPLOYMENT.md)
   - [API Documentation](docs/API.md)

2. **Configure Hasar Printers**
   - Set up printer connection
   - Test printing
   - Configure fiscal parameters

3. **Import Existing Data**
   - Use migration script for MySQL data
   - See `database/scripts/migrate_mysql_to_postgres.py`

4. **Customize Application**
   - Add your company logo
   - Configure invoice templates
   - Set up email notifications

## 🆘 Getting Help

- **API Documentation**: http://localhost:8000/api/docs
- **GitHub Issues**: https://github.com/dnlbertoni/limon/issues
- **Documentation**: See `docs/` folder

## ✅ Verification Checklist

- [ ] Docker containers running
- [ ] Database initialized
- [ ] Admin user created
- [ ] Frontend accessible
- [ ] Backend API responding
- [ ] API documentation available
- [ ] Can login to application
- [ ] Can create articles
- [ ] Can view invoices page
- [ ] Fiscal printer page loads

Congratulations! You're now ready to use Limon ERP v2.0! 🎉

# Migration Guide: From Legacy PHP to FastAPI + React

## Overview

This guide helps you migrate from the legacy PHP/CodeIgniter system to the new FastAPI + React architecture while maintaining all existing functionality, especially the Hasar fiscal printer integration.

## Pre-Migration Checklist

- [ ] Backup current database (MySQL)
- [ ] Export all fiscal printer configurations
- [ ] Document custom modifications
- [ ] Test current system completely
- [ ] Notify users of planned migration
- [ ] Schedule maintenance window

## Step 1: Data Backup

### Backup MySQL Database

```bash
# Full database backup
mysqldump -u root -p facturador > facturador_backup_$(date +%Y%m%d).sql

# Backup with compression
mysqldump -u root -p facturador | gzip > facturador_backup_$(date +%Y%m%d).sql.gz

# Verify backup
gunzip -c facturador_backup_$(date +%Y%m%d).sql.gz | head -n 20
```

### Backup Fiscal Printer Files

```bash
# Backup fiscal printer directory
tar -czf fiscal_backup_$(date +%Y%m%d).tar.gz /var/www/fiscal/

# Verify backup
tar -tzf fiscal_backup_$(date +%Y%m%d).tar.gz | head -n 20
```

### Backup Environment Configuration

```bash
# Backup current .env
cp .env .env.backup_$(date +%Y%m%d)

# Backup configuration files
cp -r conf conf_backup_$(date +%Y%m%d)
```

## Step 2: Parallel Installation

### Install New System

```bash
# Clone repository to new directory
cd /home/runner/work
git clone https://github.com/dnlbertoni/limon.git limon-v2
cd limon-v2

# Copy environment template
cp .env.example.new .env

# Configure new environment
nano .env
```

Configure these critical values:
```env
# PostgreSQL (new database)
POSTGRES_DATABASE=limon_v2
POSTGRES_USER=limon_v2_user
POSTGRES_PASSWORD=new_secure_password

# Keep old fiscal printer paths compatible
HASAR_LEGACY_PATH=/var/www/fiscal

# Configure Hasar 2.0 if upgrading printers
HASAR_2_HOST=192.168.1.100
HASAR_2_PASSWORD=fiscal_password
```

### Start New System

```bash
# Build and start
docker-compose -f docker-compose.new.yml up -d

# Check status
docker-compose -f docker-compose.new.yml ps
```

## Step 3: Data Migration

### Prepare Migration Environment

```bash
# Install migration dependencies
cd database/scripts
pip install pymysql psycopg2-binary python-dotenv
```

### Configure Migration Script

Edit `database/scripts/migrate_mysql_to_postgres.py` and add table mappings:

```python
def main():
    # ... existing code ...
    
    try:
        # Users (if table exists in MySQL)
        print("\nMigrating users...")
        # Note: May need to rehash passwords
        
        # Articles (articulos table)
        print("\nMigrating articles...")
        migrate_table(mysql_conn, pg_conn, 'articulos', transform_article)
        
        # Brands (marcas table)
        print("\nMigrating brands...")
        migrate_table(mysql_conn, pg_conn, 'marcas', transform_brand)
        
        # Categories (rubros table)
        print("\nMigrating categories...")
        migrate_table(mysql_conn, pg_conn, 'rubros', transform_category)
        
        # Customers (cuentas table)
        print("\nMigrating customers...")
        migrate_table(mysql_conn, pg_conn, 'cuentas', transform_customer)
        
        # Invoices (facencab table)
        print("\nMigrating invoices...")
        migrate_table(mysql_conn, pg_conn, 'facencab', transform_invoice)
        
        # Invoice items (facmovim table)
        print("\nMigrating invoice items...")
        migrate_table(mysql_conn, pg_conn, 'facmovim', transform_invoice_item)
        
        print("\n✓ Migration completed successfully")
    except Exception as e:
        print(f"\n✗ Migration failed: {e}")
        pg_conn.rollback()
        raise

def transform_article(row):
    """Transform article data from MySQL to PostgreSQL format"""
    # Adapt MySQL column names to new schema
    return row

def transform_customer(row):
    """Transform customer data"""
    return row

# Add other transform functions as needed
```

### Run Migration

```bash
# Set environment variables
export MYSQL_HOST=localhost
export MYSQL_PORT=3306
export MYSQL_USER=root
export MYSQL_PASSWORD=your_password
export MYSQL_DATABASE=facturador

export POSTGRES_HOST=localhost
export POSTGRES_PORT=5432
export POSTGRES_USER=limon_v2_user
export POSTGRES_PASSWORD=new_secure_password
export POSTGRES_DATABASE=limon_v2

# Run migration
python migrate_mysql_to_postgres.py

# Verify data
docker-compose -f docker-compose.new.yml exec postgres psql -U limon_v2_user -d limon_v2 -c "SELECT COUNT(*) FROM articles;"
```

## Step 4: Hasar Printer Migration

### Option A: Continue Using Legacy Hasar

No changes needed - the new system supports the legacy file-based communication:

```python
# In your frontend or API calls
printer_config = {
    "version": "legacy",
    "point_of_sale": 1
}
```

The fiscal printer files will continue to work in `/var/www/fiscal/`.

### Option B: Upgrade to Hasar 2.0

If you have Hasar 2.0 compatible printers:

1. **Configure printer network settings:**
   - Assign static IP to printer
   - Note down IP address and password
   - Test HTTP connectivity

2. **Update configuration:**
```env
HASAR_2_HOST=192.168.1.100
HASAR_2_PORT=80
HASAR_2_PASSWORD=fiscal_password
```

3. **Test connection:**
```bash
# Use the fiscal printer page in the new UI
# Or test via API
curl -X POST http://localhost:8000/api/hasar/status \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "version": "2.0",
    "host": "192.168.1.100",
    "password": "fiscal_password"
  }'
```

### Option C: Hybrid Approach

Use both versions for different points of sale:

```javascript
// Point of sale 1: Legacy
const printer1 = {
  version: "legacy",
  point_of_sale: 1
}

// Point of sale 2: Hasar 2.0
const printer2 = {
  version: "2.0",
  host: "192.168.1.101",
  password: "fiscal_password"
}
```

## Step 5: Testing

### Test Basic Functionality

```bash
# Test authentication
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=admin&password=admin123"

# Test articles
curl http://localhost:8000/api/articles/ \
  -H "Authorization: Bearer $TOKEN"

# Test customers
curl http://localhost:8000/api/invoices/customers \
  -H "Authorization: Bearer $TOKEN"
```

### Test Fiscal Printer

```bash
# Test printer status (Legacy)
curl -X POST http://localhost:8000/api/hasar/status \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"version": "legacy", "point_of_sale": 1}'

# Test printer status (2.0)
curl -X POST http://localhost:8000/api/hasar/status \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "version": "2.0",
    "host": "192.168.1.100",
    "password": "fiscal_password"
  }'
```

### Test Invoice Creation

Create a test invoice through the web interface:
1. Navigate to http://localhost:3000
2. Login with admin credentials
3. Go to Articles and create a test article
4. Go to Invoices and create a test invoice
5. Print the invoice using the fiscal printer

## Step 6: User Training

### Prepare Training Materials

1. **Screenshots of new interface**
   - Login screen
   - Dashboard
   - Articles management
   - Invoice creation
   - Fiscal printer interface

2. **Quick reference guides**
   - How to create articles
   - How to create invoices
   - How to use fiscal printer
   - How to run reports

3. **Video tutorials** (optional)

### Conduct Training Sessions

- Schedule training sessions for users
- Provide hands-on practice time
- Address questions and concerns
- Document common issues

## Step 7: Cutover

### Pre-Cutover Checklist

- [ ] All data migrated successfully
- [ ] All functionality tested
- [ ] Users trained
- [ ] Backup of old system available
- [ ] Rollback plan ready
- [ ] Support team available

### Cutover Steps

1. **Final Data Sync**
```bash
# Re-run migration for any new data
python migrate_mysql_to_postgres.py --incremental

# Or specify date range
python migrate_mysql_to_postgres.py --from-date 2024-01-01
```

2. **Stop Old System**
```bash
# Stop old Docker containers
cd /home/runner/work/limon
docker-compose down
```

3. **Update URLs/DNS**
```bash
# If using domain, update to point to new system
# Or update nginx configuration to proxy to new backend
```

4. **Start New System**
```bash
cd /home/runner/work/limon-v2
docker-compose -f docker-compose.new.yml up -d
```

5. **Verify**
- Check all services are running
- Test critical workflows
- Monitor logs for errors
- Verify fiscal printer connectivity

## Step 8: Post-Migration

### Monitoring

```bash
# Monitor logs
docker-compose -f docker-compose.new.yml logs -f

# Check resource usage
docker stats

# Monitor database
docker-compose -f docker-compose.new.yml exec postgres pg_stat_activity
```

### Performance Tuning

If needed, adjust:
- PostgreSQL connection pool size
- FastAPI worker count
- Nginx cache settings
- React build optimization

### Cleanup

After successful migration and stabilization (recommend 1 week):

```bash
# Keep backup of old system
tar -czf old_system_backup_$(date +%Y%m%d).tar.gz /home/runner/work/limon

# Archive to secure location
# Can then remove old system if desired
```

## Rollback Plan

If issues occur:

### Immediate Rollback

```bash
# Stop new system
cd /home/runner/work/limon-v2
docker-compose -f docker-compose.new.yml down

# Start old system
cd /home/runner/work/limon
docker-compose up -d

# Restore old database if needed
mysql -u root -p facturador < facturador_backup_YYYYMMDD.sql
```

### Partial Rollback

Keep new system running but revert specific features:
- Use legacy fiscal printer if 2.0 has issues
- Keep old invoice module if needed
- Gradual migration by module

## Common Issues and Solutions

### Data Migration Issues

**Issue:** Encoding problems (special characters)
```bash
# Set correct encoding
export LANG=es_AR.UTF-8
export LC_ALL=es_AR.UTF-8
```

**Issue:** Foreign key violations
- Migrate parent tables first (brands, categories)
- Then child tables (articles)
- Finally relationship tables (invoice items)

### Fiscal Printer Issues

**Issue:** Legacy printer not responding
- Check `/var/www/fiscal/` permissions
- Verify printer service is running
- Check file paths in configuration

**Issue:** Hasar 2.0 connection timeout
- Verify printer IP address
- Test network connectivity: `ping 192.168.1.100`
- Check firewall rules
- Verify printer password

### Performance Issues

**Issue:** Slow queries
- Add database indexes
- Optimize queries
- Increase connection pool

**Issue:** High memory usage
- Adjust Docker resource limits
- Optimize PostgreSQL settings
- Review frontend bundle size

## Support

For migration assistance:
- Review documentation in `docs/` folder
- Check API documentation at `/api/docs`
- Create GitHub issues for bugs
- Contact support team

## Timeline Recommendation

- **Week 1:** Setup and testing
- **Week 2:** Data migration and validation
- **Week 3:** User training
- **Week 4:** Parallel running
- **Week 5:** Cutover and monitoring

Total: **5 weeks** for safe migration

## Success Criteria

- [ ] All data migrated accurately
- [ ] All features working correctly
- [ ] Fiscal printers functioning
- [ ] Users trained and comfortable
- [ ] Performance acceptable
- [ ] No critical bugs
- [ ] Backup and recovery tested
- [ ] Documentation complete

---

**Remember:** Take your time, test thoroughly, and keep backups!

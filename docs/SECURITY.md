# Security Guide for Limon ERP v2.0

## Overview

This document outlines the security measures implemented in Limon ERP v2.0 and best practices for maintaining security.

## 🔐 Authentication & Authorization

### JWT Authentication
- **Token-based authentication** using JSON Web Tokens (JWT)
- Tokens expire after 30 minutes (configurable)
- Tokens include user information and permissions
- Secure password hashing using bcrypt

### Implementation Details

```python
# Generate token
from app.core.security import create_access_token
token = create_access_token(data={"sub": username})

# Verify token
from app.core.security import decode_access_token
payload = decode_access_token(token)
```

### Password Security
- Minimum password length: 8 characters (recommended)
- Passwords hashed with bcrypt (cost factor: 12)
- Never stored in plain text
- Password strength validation recommended

## 🛡️ API Security

### Rate Limiting
- **100 requests per minute** per IP address
- Configurable in middleware
- Returns 429 (Too Many Requests) when exceeded

### CORS (Cross-Origin Resource Sharing)
- Only allowed origins can access the API
- Configured in `backend/app/core/config.py`
- Default: localhost:3000 and localhost:5173

```python
BACKEND_CORS_ORIGINS = [
    "http://localhost:3000",
    "http://localhost:5173"
]
```

### Security Headers
All responses include security headers:
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`
- `Strict-Transport-Security: max-age=31536000`
- `Content-Security-Policy: default-src 'self'`

## 🗄️ Database Security

### SQL Injection Prevention
- Using SQLAlchemy ORM (prevents SQL injection)
- All queries parameterized
- Input validation with Pydantic schemas

### Connection Security
- Connection strings stored in environment variables
- Never commit database credentials to git
- Use `.env` file (excluded from version control)

### Data Encryption
- Passwords encrypted with bcrypt
- Sensitive data should be encrypted at rest (implement if needed)
- Use HTTPS for data in transit

## 🔒 Environment Variables

### Required Security Variables

```env
# CRITICAL: Change in production
SECRET_KEY=your-secret-key-here-min-32-characters-random

# Database credentials
POSTGRES_USER=limon_user
POSTGRES_PASSWORD=secure_password_here
POSTGRES_DATABASE=limon_db

# Hasar printer credentials
HASAR_2_PASSWORD=fiscal_printer_password
```

### Best Practices
1. Never commit `.env` file
2. Use strong, random secrets (minimum 32 characters)
3. Rotate secrets regularly
4. Use different credentials for each environment
5. Keep `.env.example` updated without actual values

## 🌐 HTTPS/SSL Configuration

### Production Setup

1. **Obtain SSL Certificate**
   - Use Let's Encrypt (free)
   - Or use a commercial certificate

2. **Configure Nginx as Reverse Proxy**

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    
    location / {
        proxy_pass http://localhost:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

3. **Update CORS Origins**
```python
BACKEND_CORS_ORIGINS = [
    "https://your-domain.com"
]
```

## 🔍 Input Validation

### Pydantic Schemas
All inputs validated using Pydantic:

```python
from pydantic import BaseModel, EmailStr, validator

class UserCreate(BaseModel):
    username: str
    email: EmailStr
    password: str
    
    @validator('password')
    def password_strength(cls, v):
        if len(v) < 8:
            raise ValueError('Password must be at least 8 characters')
        return v
```

### Sanitization
- HTML sanitization for text inputs
- SQL injection prevention through ORM
- XSS prevention through proper escaping

## 🚨 Security Monitoring

### Logging
- All authentication attempts logged
- Failed login attempts tracked
- API access logged with timestamps
- Error logging without exposing sensitive data

### Recommended Monitoring
1. Monitor failed login attempts
2. Track unusual API usage patterns
3. Monitor database access
4. Set up alerts for security events

## 🔧 Security Checklist for Production

- [ ] Change default SECRET_KEY
- [ ] Use strong database passwords
- [ ] Enable HTTPS/SSL
- [ ] Configure firewall rules
- [ ] Limit database access to backend only
- [ ] Regular security updates
- [ ] Enable database backups
- [ ] Configure log rotation
- [ ] Set up monitoring and alerts
- [ ] Review and update CORS origins
- [ ] Implement rate limiting per user
- [ ] Add IP whitelisting (if applicable)
- [ ] Enable database connection encryption
- [ ] Regular security audits
- [ ] Implement audit logging
- [ ] Set up intrusion detection

## 🛠️ Security Maintenance

### Regular Updates
- Update Python dependencies: `pip install -U -r requirements.txt`
- Update Node dependencies: `npm update`
- Monitor security advisories
- Apply database security patches

### Dependency Scanning
```bash
# Python
pip install safety
safety check

# Check for known vulnerabilities
pip-audit

# Node.js
npm audit
npm audit fix

# Regular updates
pip list --outdated
npm outdated
```

### Security Patches Applied

- **2024-01-30**: Updated FastAPI (0.109.0 → 0.110.0) and python-multipart (0.0.6 → 0.0.22) to fix ReDoS and file write vulnerabilities
- See [Security Patches](SECURITY_PATCH_2024-01-30.md) for details

### Backup Strategy
1. Daily database backups
2. Weekly full system backups
3. Test restoration procedures
4. Store backups securely off-site

## 📞 Incident Response

### In Case of Security Breach

1. **Immediate Actions**
   - Isolate affected systems
   - Change all passwords and secrets
   - Review access logs
   - Identify breach vector

2. **Investigation**
   - Analyze logs
   - Determine scope of breach
   - Document findings

3. **Recovery**
   - Patch vulnerabilities
   - Restore from clean backup if needed
   - Implement additional security measures
   - Monitor for further attempts

4. **Prevention**
   - Update security procedures
   - Train team members
   - Implement lessons learned

## 📚 Additional Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [FastAPI Security](https://fastapi.tiangolo.com/tutorial/security/)
- [PostgreSQL Security](https://www.postgresql.org/docs/current/security.html)
- [React Security Best Practices](https://react.dev/learn/security)

## 🤝 Reporting Security Issues

If you discover a security vulnerability, please email security@your-domain.com instead of creating a public issue.

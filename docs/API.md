# API Documentation - Limon ERP v2.0

## Base URL

```
Development: http://localhost:8000/api
Production: https://your-domain.com/api
```

## Authentication

All authenticated endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer <your-jwt-token>
```

## Interactive Documentation

- **Swagger UI**: http://localhost:8000/api/docs
- **ReDoc**: http://localhost:8000/api/redoc

---

## Authentication Endpoints

### POST /api/auth/login

Login and receive JWT token.

**Request:**
```http
POST /api/auth/login
Content-Type: application/x-www-form-urlencoded

username=admin&password=admin123
```

**Response:**
```json
{
  "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "token_type": "bearer"
}
```

### POST /api/auth/register

Register a new user.

**Request:**
```json
{
  "username": "newuser",
  "email": "user@example.com",
  "password": "securepassword",
  "full_name": "New User"
}
```

**Response:**
```json
{
  "id": 2,
  "username": "newuser",
  "email": "user@example.com",
  "full_name": "New User",
  "is_active": true,
  "is_superuser": false,
  "created_at": "2024-01-30T10:00:00Z"
}
```

### GET /api/auth/me

Get current user information.

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "id": 1,
  "username": "admin",
  "email": "admin@limon.com",
  "full_name": "Administrator",
  "is_active": true,
  "is_superuser": true,
  "created_at": "2024-01-30T10:00:00Z"
}
```

---

## Articles Endpoints

### GET /api/articles/

List all articles.

**Query Parameters:**
- `skip` (int): Number of records to skip (default: 0)
- `limit` (int): Maximum records to return (default: 100)

**Response:**
```json
[
  {
    "id": 1,
    "code": "ART001",
    "name": "Product 1",
    "description": "Description of product 1",
    "brand_id": 1,
    "category_id": 1,
    "price": 100.00,
    "cost": 50.00,
    "stock": 10.00,
    "min_stock": 5.00,
    "is_active": true,
    "created_at": "2024-01-30T10:00:00Z",
    "updated_at": null
  }
]
```

### POST /api/articles/

Create a new article.

**Request:**
```json
{
  "code": "ART001",
  "name": "Product 1",
  "description": "Description of product 1",
  "brand_id": 1,
  "category_id": 1,
  "price": 100.00,
  "cost": 50.00,
  "stock": 10.00,
  "min_stock": 5.00
}
```

**Response:** Same as GET single article

### GET /api/articles/{article_id}

Get a specific article.

**Response:**
```json
{
  "id": 1,
  "code": "ART001",
  "name": "Product 1",
  "description": "Description of product 1",
  "brand_id": 1,
  "category_id": 1,
  "price": 100.00,
  "cost": 50.00,
  "stock": 10.00,
  "min_stock": 5.00,
  "is_active": true,
  "created_at": "2024-01-30T10:00:00Z",
  "updated_at": null
}
```

### PUT /api/articles/{article_id}

Update an article.

**Request:**
```json
{
  "price": 120.00,
  "stock": 15.00
}
```

### DELETE /api/articles/{article_id}

Soft delete an article (sets is_active to false).

**Response:** 204 No Content

### GET /api/articles/brands

List all brands.

### POST /api/articles/brands

Create a new brand.

**Request:**
```json
{
  "name": "Brand Name",
  "description": "Brand description"
}
```

### GET /api/articles/categories

List all categories.

### POST /api/articles/categories

Create a new category.

**Request:**
```json
{
  "name": "Category Name",
  "parent_id": null,
  "description": "Category description"
}
```

---

## Invoices Endpoints

### GET /api/invoices/

List all invoices.

**Query Parameters:**
- `skip` (int): Number of records to skip (default: 0)
- `limit` (int): Maximum records to return (default: 100)

**Response:**
```json
[
  {
    "id": 1,
    "invoice_type_id": 1,
    "customer_id": 1,
    "point_of_sale": 1,
    "number": 123,
    "letter": "B",
    "date": "2024-01-30T10:00:00Z",
    "total_amount": 121.00,
    "net_amount": 100.00,
    "vat_min": 0.00,
    "vat_max": 21.00,
    "status": 1,
    "created_at": "2024-01-30T10:00:00Z",
    "items": []
  }
]
```

### POST /api/invoices/

Create a new invoice.

**Request:**
```json
{
  "invoice_type_id": 1,
  "customer_id": 1,
  "point_of_sale": 1,
  "letter": "B",
  "date": "2024-01-30T10:00:00Z",
  "items": [
    {
      "article_id": 1,
      "quantity": 2,
      "price": 100.00,
      "discount": 0.00,
      "vat_rate": 21.00
    }
  ]
}
```

### GET /api/invoices/{invoice_id}

Get a specific invoice.

### GET /api/invoices/customers

List all customers.

### POST /api/invoices/customers

Create a new customer.

**Request:**
```json
{
  "name": "Customer Name",
  "tax_id": "20-12345678-9",
  "address": "Street Address 123",
  "phone": "+54 11 1234-5678",
  "email": "customer@example.com",
  "vat_condition": "RI"
}
```

### GET /api/invoices/customers/{customer_id}

Get a specific customer.

---

## Hasar Fiscal Printer Endpoints

### POST /api/hasar/status

Get fiscal printer status.

**Request:**
```json
{
  "version": "2.0",
  "point_of_sale": 1,
  "host": "192.168.1.100",
  "password": "fiscal_password"
}
```

**Response:**
```json
{
  "status": {
    "printer_online": true,
    "fiscal_memory": "OK",
    "paper_status": "OK"
  },
  "printer_version": "2.0"
}
```

### POST /api/hasar/print-receipt

Print a complete fiscal receipt.

**Request:**
```json
{
  "customer": {
    "name": "Consumidor Final",
    "tax_id": "",
    "vat_condition": "CF",
    "document_type": "DNI",
    "receipt_type": "T",
    "letter": "B"
  },
  "items": [
    {
      "description": "Product 1",
      "quantity": 2,
      "price": 100.00,
      "vat_rate": 21.00,
      "discount": 0.00
    }
  ],
  "payments": [
    {
      "amount": 242.00,
      "type": "efectivo"
    }
  ]
}
```

**Query Parameters:**
```json
{
  "version": "2.0",
  "point_of_sale": 1,
  "host": "192.168.1.100",
  "password": "fiscal_password"
}
```

**Response:**
```json
{
  "success": true,
  "results": [
    {
      "abrirComprobante": {
        "success": true,
        "receipt_number": "123"
      }
    },
    {
      "imprimirItem": {
        "success": true
      }
    },
    {
      "cerrarComprobante": {
        "success": true,
        "total": "242.00"
      }
    }
  ],
  "printer_version": "2.0"
}
```

### POST /api/hasar/daily-close

Perform daily close (Z or X report).

**Query Parameters:**
- `close_type`: "Z" or "X" (default: "Z")
- Plus printer configuration (version, host, password, etc.)

**Response:**
```json
{
  "success": true,
  "result": {
    "date": "2024-01-30",
    "close_number": 45,
    "total": "12450.00"
  },
  "close_type": "Z",
  "printer_version": "2.0"
}
```

### POST /api/hasar/open-receipt

Open a fiscal receipt.

**Request:**
```json
{
  "customer": {
    "name": "Consumidor Final",
    "tax_id": "",
    "vat_condition": "CF",
    "document_type": "DNI",
    "receipt_type": "T",
    "letter": "B"
  }
}
```

### POST /api/hasar/close-receipt

Close the current fiscal receipt.

---

## Error Responses

### 400 Bad Request
```json
{
  "detail": "Validation error message"
}
```

### 401 Unauthorized
```json
{
  "detail": "Could not validate credentials"
}
```

### 403 Forbidden
```json
{
  "detail": "Not enough permissions"
}
```

### 404 Not Found
```json
{
  "detail": "Resource not found"
}
```

### 422 Validation Error
```json
{
  "detail": [
    {
      "loc": ["body", "email"],
      "msg": "value is not a valid email address",
      "type": "value_error.email"
    }
  ]
}
```

### 429 Too Many Requests
```json
{
  "detail": "Too many requests"
}
```

### 500 Internal Server Error
```json
{
  "detail": "Internal server error"
}
```

---

## Examples

### Complete Workflow: Create and Print Invoice

#### 1. Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "username=admin&password=admin123"
```

#### 2. Create Customer
```bash
curl -X POST http://localhost:8000/api/invoices/customers \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "tax_id": "20-12345678-9",
    "vat_condition": "RI"
  }'
```

#### 3. Create Invoice
```bash
curl -X POST http://localhost:8000/api/invoices/ \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "invoice_type_id": 1,
    "customer_id": 1,
    "point_of_sale": 1,
    "letter": "A",
    "date": "2024-01-30T10:00:00Z",
    "items": [
      {
        "article_id": 1,
        "quantity": 2,
        "price": 100.00,
        "vat_rate": 21.00
      }
    ]
  }'
```

#### 4. Print Fiscal Receipt
```bash
curl -X POST "http://localhost:8000/api/hasar/print-receipt?version=2.0&host=192.168.1.100&password=fiscal" \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "customer": {
      "name": "John Doe",
      "tax_id": "20-12345678-9",
      "vat_condition": "RI",
      "letter": "A"
    },
    "items": [
      {
        "description": "Product 1",
        "quantity": 2,
        "price": 100.00,
        "vat_rate": 21.00
      }
    ],
    "payments": [
      {
        "amount": 242.00,
        "type": "efectivo"
      }
    ]
  }'
```

---

## Rate Limiting

- **Rate**: 100 requests per minute per IP
- **Response on exceed**: 429 Too Many Requests

## Pagination

Endpoints that return lists support pagination:
- `skip`: Number of records to skip (default: 0)
- `limit`: Maximum records to return (default: 100, max: 100)

Example:
```
GET /api/articles/?skip=20&limit=10
```

## Filtering

Some endpoints support filtering through query parameters. Check individual endpoint documentation.

## Versioning

Current API version: **v2.0**

API versioning may be added in future releases through URL path (`/api/v2/...`) or headers.

---

For more details, use the interactive documentation at `/api/docs` which provides:
- Full request/response schemas
- Try-it-out functionality
- Authentication testing
- Model schemas

-- PostgreSQL Database Initialization Script
-- Create database and tables for Limon ERP

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    hashed_password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    is_superuser BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE
);

-- Brands table
CREATE TABLE IF NOT EXISTS brands (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INTEGER REFERENCES categories(id),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Articles table
CREATE TABLE IF NOT EXISTS articles (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    brand_id INTEGER REFERENCES brands(id),
    category_id INTEGER REFERENCES categories(id),
    price NUMERIC(10, 2) DEFAULT 0.0,
    cost NUMERIC(10, 2) DEFAULT 0.0,
    stock NUMERIC(10, 2) DEFAULT 0.0,
    min_stock NUMERIC(10, 2) DEFAULT 0.0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE
);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    tax_id VARCHAR(50) UNIQUE,
    address VARCHAR(255),
    phone VARCHAR(50),
    email VARCHAR(100),
    vat_condition VARCHAR(50),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE
);

-- Invoice types table
CREATE TABLE IF NOT EXISTS invoice_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10),
    vat_book INTEGER DEFAULT 0,
    description TEXT
);

-- Invoices table
CREATE TABLE IF NOT EXISTS invoices (
    id SERIAL PRIMARY KEY,
    invoice_type_id INTEGER REFERENCES invoice_types(id) NOT NULL,
    customer_id INTEGER REFERENCES customers(id) NOT NULL,
    point_of_sale INTEGER NOT NULL,
    number INTEGER NOT NULL,
    letter VARCHAR(1) NOT NULL,
    date TIMESTAMP WITH TIME ZONE NOT NULL,
    total_amount NUMERIC(10, 2) DEFAULT 0.0,
    net_amount NUMERIC(10, 2) DEFAULT 0.0,
    vat_min NUMERIC(10, 2) DEFAULT 0.0,
    vat_max NUMERIC(10, 2) DEFAULT 0.0,
    internal_tax NUMERIC(10, 2) DEFAULT 0.0,
    perception NUMERIC(10, 2) DEFAULT 0.0,
    gross_income NUMERIC(10, 2) DEFAULT 0.0,
    vat_period INTEGER,
    status INTEGER DEFAULT 1,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE
);

-- Invoice items table
CREATE TABLE IF NOT EXISTS invoice_items (
    id SERIAL PRIMARY KEY,
    invoice_id INTEGER REFERENCES invoices(id) NOT NULL,
    article_id INTEGER REFERENCES articles(id) NOT NULL,
    quantity NUMERIC(10, 2) NOT NULL,
    price NUMERIC(10, 2) NOT NULL,
    discount NUMERIC(5, 2) DEFAULT 0.0,
    vat_rate NUMERIC(5, 2) DEFAULT 0.0,
    total NUMERIC(10, 2) NOT NULL
);

-- Create indexes
CREATE INDEX idx_articles_code ON articles(code);
CREATE INDEX idx_articles_name ON articles(name);
CREATE INDEX idx_invoices_customer ON invoices(customer_id);
CREATE INDEX idx_invoices_date ON invoices(date);
CREATE INDEX idx_invoice_items_invoice ON invoice_items(invoice_id);

-- Insert default data
INSERT INTO users (username, email, hashed_password, full_name, is_superuser) 
VALUES ('admin', 'admin@limon.com', '$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYzS8Z8Z8Z8', 'Administrator', TRUE)
ON CONFLICT DO NOTHING;

INSERT INTO invoice_types (id, name, code, vat_book, description) VALUES
(1, 'Factura A', 'FA', 1, 'Factura A'),
(2, 'Factura B', 'FB', 1, 'Factura B'),
(3, 'Nota de Crédito A', 'NCA', 1, 'Nota de Crédito A'),
(4, 'Cierre Z', 'Z', 2, 'Cierre Diario')
ON CONFLICT DO NOTHING;

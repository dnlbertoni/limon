from sqlalchemy import Column, Integer, String, Float, DateTime, ForeignKey, Text
from sqlalchemy.orm import relationship
from sqlalchemy.sql import func
from app.core.database import Base


class Invoice(Base):
    """Invoice header model (facencab)"""
    __tablename__ = "invoices"
    
    id = Column(Integer, primary_key=True, index=True)
    invoice_type_id = Column(Integer, ForeignKey("invoice_types.id"), nullable=False)
    customer_id = Column(Integer, ForeignKey("customers.id"), nullable=False)
    point_of_sale = Column(Integer, nullable=False)  # puesto
    number = Column(Integer, nullable=False)
    letter = Column(String(1), nullable=False)  # A, B, C
    date = Column(DateTime(timezone=True), nullable=False)
    total_amount = Column(Float, default=0.0)  # importe
    net_amount = Column(Float, default=0.0)  # neto
    vat_min = Column(Float, default=0.0)  # ivamin
    vat_max = Column(Float, default=0.0)  # ivamax
    internal_tax = Column(Float, default=0.0)  # impint
    perception = Column(Float, default=0.0)  # percep
    gross_income = Column(Float, default=0.0)  # ingbru
    vat_period = Column(Integer)  # periva
    status = Column(Integer, default=1)
    created_at = Column(DateTime(timezone=True), server_default=func.now())
    updated_at = Column(DateTime(timezone=True), onupdate=func.now())
    
    invoice_type = relationship("InvoiceType", back_populates="invoices")
    customer = relationship("Customer", back_populates="invoices")
    items = relationship("InvoiceItem", back_populates="invoice")


class InvoiceType(Base):
    """Invoice type model (tipcom)"""
    __tablename__ = "invoice_types"
    
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(100), nullable=False)
    code = Column(String(10))
    vat_book = Column(Integer, default=0)  # libroiva
    description = Column(Text)
    
    invoices = relationship("Invoice", back_populates="invoice_type")


class InvoiceItem(Base):
    """Invoice line items"""
    __tablename__ = "invoice_items"
    
    id = Column(Integer, primary_key=True, index=True)
    invoice_id = Column(Integer, ForeignKey("invoices.id"), nullable=False)
    article_id = Column(Integer, ForeignKey("articles.id"), nullable=False)
    quantity = Column(Float, nullable=False)
    price = Column(Float, nullable=False)
    discount = Column(Float, default=0.0)
    vat_rate = Column(Float, default=0.0)
    total = Column(Float, nullable=False)
    
    invoice = relationship("Invoice", back_populates="items")
    article = relationship("Article")


class Customer(Base):
    """Customer account model (cuenta)"""
    __tablename__ = "customers"
    
    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(255), nullable=False)
    tax_id = Column(String(50), unique=True)  # CUIT/CUIL
    address = Column(String(255))
    phone = Column(String(50))
    email = Column(String(100))
    vat_condition = Column(String(50))  # IVA condition
    created_at = Column(DateTime(timezone=True), server_default=func.now())
    updated_at = Column(DateTime(timezone=True), onupdate=func.now())
    
    invoices = relationship("Invoice", back_populates="customer")

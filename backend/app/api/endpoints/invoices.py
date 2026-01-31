from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session
from typing import List

from app.core.database import get_db
from app.models.invoice import Invoice, InvoiceItem, Customer, InvoiceType
from app.schemas.invoice import (
    InvoiceCreate, InvoiceResponse,
    CustomerCreate, CustomerResponse,
    HasarReceiptRequest, HasarReceiptResponse, HasarStatusResponse, HasarPrinterConfig
)
from app.services.hasar import HasarService
from app.api.endpoints.auth import get_current_active_user
from app.models.user import User

router = APIRouter()


@router.post("/", response_model=InvoiceResponse, status_code=status.HTTP_201_CREATED)
def create_invoice(
    invoice: InvoiceCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Create a new invoice"""
    # Get next invoice number
    last_invoice = db.query(Invoice).filter(
        Invoice.point_of_sale == invoice.point_of_sale,
        Invoice.letter == invoice.letter,
        Invoice.invoice_type_id == invoice.invoice_type_id
    ).order_by(Invoice.number.desc()).first()
    
    next_number = (last_invoice.number + 1) if last_invoice else 1
    
    # Calculate totals
    total_amount = sum(item.quantity * item.price * (1 - item.discount/100) for item in invoice.items)
    vat_total = sum(
        item.quantity * item.price * (1 - item.discount/100) * item.vat_rate / 100
        for item in invoice.items
    )
    net_amount = total_amount - vat_total
    
    # Create invoice
    db_invoice = Invoice(
        invoice_type_id=invoice.invoice_type_id,
        customer_id=invoice.customer_id,
        point_of_sale=invoice.point_of_sale,
        number=next_number,
        letter=invoice.letter,
        date=invoice.date,
        total_amount=total_amount,
        net_amount=net_amount,
        vat_max=vat_total
    )
    db.add(db_invoice)
    db.flush()
    
    # Create invoice items
    for item in invoice.items:
        item_total = item.quantity * item.price * (1 - item.discount/100)
        db_item = InvoiceItem(
            invoice_id=db_invoice.id,
            article_id=item.article_id,
            quantity=item.quantity,
            price=item.price,
            discount=item.discount,
            vat_rate=item.vat_rate,
            total=item_total
        )
        db.add(db_item)
    
    db.commit()
    db.refresh(db_invoice)
    return db_invoice


@router.get("/", response_model=List[InvoiceResponse])
def list_invoices(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """List all invoices"""
    invoices = db.query(Invoice).offset(skip).limit(limit).all()
    return invoices


@router.get("/{invoice_id}", response_model=InvoiceResponse)
def get_invoice(
    invoice_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Get invoice by ID"""
    invoice = db.query(Invoice).filter(Invoice.id == invoice_id).first()
    if not invoice:
        raise HTTPException(status_code=404, detail="Invoice not found")
    return invoice


# Customer endpoints
@router.post("/customers", response_model=CustomerResponse, status_code=status.HTTP_201_CREATED)
def create_customer(
    customer: CustomerCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Create a new customer"""
    db_customer = Customer(**customer.dict())
    db.add(db_customer)
    db.commit()
    db.refresh(db_customer)
    return db_customer


@router.get("/customers", response_model=List[CustomerResponse])
def list_customers(
    skip: int = 0,
    limit: int = 100,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """List all customers"""
    customers = db.query(Customer).offset(skip).limit(limit).all()
    return customers


@router.get("/customers/{customer_id}", response_model=CustomerResponse)
def get_customer(
    customer_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_active_user)
):
    """Get customer by ID"""
    customer = db.query(Customer).filter(Customer.id == customer_id).first()
    if not customer:
        raise HTTPException(status_code=404, detail="Customer not found")
    return customer

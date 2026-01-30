from pydantic import BaseModel
from typing import Optional, List, Literal
from datetime import datetime


class InvoiceItemBase(BaseModel):
    article_id: int
    quantity: float
    price: float
    discount: float = 0.0
    vat_rate: float = 21.0


class InvoiceItemCreate(InvoiceItemBase):
    pass


class InvoiceItemResponse(InvoiceItemBase):
    id: int
    invoice_id: int
    total: float
    
    class Config:
        from_attributes = True


class InvoiceBase(BaseModel):
    invoice_type_id: int
    customer_id: int
    point_of_sale: int
    letter: str
    date: datetime


class InvoiceCreate(InvoiceBase):
    items: List[InvoiceItemCreate]


class InvoiceResponse(InvoiceBase):
    id: int
    number: int
    total_amount: float
    net_amount: float
    vat_min: float
    vat_max: float
    status: int
    created_at: datetime
    items: List[InvoiceItemResponse] = []
    
    class Config:
        from_attributes = True


class CustomerBase(BaseModel):
    name: str
    tax_id: Optional[str] = None
    address: Optional[str] = None
    phone: Optional[str] = None
    email: Optional[str] = None
    vat_condition: Optional[str] = None


class CustomerCreate(CustomerBase):
    pass


class CustomerResponse(CustomerBase):
    id: int
    created_at: datetime
    
    class Config:
        from_attributes = True


# Hasar Fiscal Printer Schemas
class HasarCustomerData(BaseModel):
    name: str
    tax_id: Optional[str] = ""
    vat_condition: str = "CF"  # CF, RI, MONO
    document_type: str = "CUIT"
    receipt_type: str = "T"  # T=Ticket, F=Factura
    letter: str = "B"


class HasarItem(BaseModel):
    description: str
    quantity: float
    price: float
    vat_rate: float = 21.0
    discount: float = 0.0


class HasarPayment(BaseModel):
    amount: float
    type: str = "efectivo"  # efectivo, tarjeta, cuenta_corriente


class HasarReceiptRequest(BaseModel):
    customer: HasarCustomerData
    items: List[HasarItem]
    payments: List[HasarPayment] = []


class HasarPrinterConfig(BaseModel):
    version: Literal["legacy", "2.0"]
    point_of_sale: Optional[int] = 1
    host: Optional[str] = None
    password: Optional[str] = None


class HasarStatusResponse(BaseModel):
    status: dict
    printer_version: str


class HasarReceiptResponse(BaseModel):
    success: bool
    results: List[dict]
    printer_version: str

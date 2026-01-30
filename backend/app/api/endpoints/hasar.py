from fastapi import APIRouter, Depends, HTTPException, status
from app.schemas.invoice import (
    HasarReceiptRequest, HasarReceiptResponse, 
    HasarStatusResponse, HasarPrinterConfig
)
from app.services.hasar import HasarService
from app.api.endpoints.auth import get_current_active_user
from app.models.user import User

router = APIRouter()


@router.post("/status", response_model=HasarStatusResponse)
async def get_printer_status(
    config: HasarPrinterConfig,
    current_user: User = Depends(get_current_active_user)
):
    """
    Get fiscal printer status
    Supports both legacy and 2.0 versions
    """
    printer = HasarService.get_printer_instance(config.dict())
    status = await printer.get_status()
    
    return HasarStatusResponse(
        status=status,
        printer_version=config.version
    )


@router.post("/print-receipt", response_model=HasarReceiptResponse)
async def print_receipt(
    receipt: HasarReceiptRequest,
    config: HasarPrinterConfig,
    current_user: User = Depends(get_current_active_user)
):
    """
    Print a complete fiscal receipt
    Supports both legacy and 2.0 versions
    Maintains the relationship between hasar and hasar2.0 controllers
    """
    printer = HasarService.get_printer_instance(config.dict())
    
    # Prepare receipt data
    receipt_data = {
        "customer": receipt.customer.dict(),
        "items": [item.dict() for item in receipt.items],
        "payments": [payment.dict() for payment in receipt.payments] if receipt.payments else []
    }
    
    # Print receipt
    results = await printer.print_complete_receipt(receipt_data)
    
    # Check for errors
    success = all(
        not result.get(list(result.keys())[0], {}).get("error") 
        for result in results 
        if result
    )
    
    return HasarReceiptResponse(
        success=success,
        results=results,
        printer_version=config.version
    )


@router.post("/daily-close")
async def daily_close(
    config: HasarPrinterConfig,
    close_type: str = "Z",
    current_user: User = Depends(get_current_active_user)
):
    """
    Perform daily close (Z or X report)
    Supports both legacy and 2.0 versions
    """
    if close_type not in ["Z", "X"]:
        raise HTTPException(status_code=400, detail="Invalid close type. Must be 'Z' or 'X'")
    
    printer = HasarService.get_printer_instance(config.dict())
    result = await printer.daily_close(close_type)
    
    return {
        "success": "error" not in result,
        "result": result,
        "close_type": close_type,
        "printer_version": config.version
    }


@router.post("/open-receipt")
async def open_receipt(
    receipt: HasarReceiptRequest,
    config: HasarPrinterConfig,
    current_user: User = Depends(get_current_active_user)
):
    """Open a fiscal receipt"""
    printer = HasarService.get_printer_instance(config.dict())
    result = await printer.open_fiscal_receipt(receipt.customer.dict())
    
    return {
        "success": "error" not in result,
        "result": result,
        "printer_version": config.version
    }


@router.post("/close-receipt")
async def close_receipt(
    config: HasarPrinterConfig,
    current_user: User = Depends(get_current_active_user)
):
    """Close a fiscal receipt"""
    printer = HasarService.get_printer_instance(config.dict())
    result = await printer.close_fiscal_receipt()
    
    return {
        "success": "error" not in result,
        "result": result,
        "printer_version": config.version
    }

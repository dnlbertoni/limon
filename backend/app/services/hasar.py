"""
Unified Hasar Fiscal Printer Service
Manages both legacy and 2.0 versions, maintaining the relationship between them
"""
from typing import Dict, List, Optional, Literal
from app.services.hasar_legacy import HasarLegacyService
from app.services.hasar2 import Hasar2Service


PrinterVersion = Literal["legacy", "2.0"]


class HasarService:
    """
    Unified service for Hasar fiscal printers
    Maintains the relationship between legacy (file-based) and 2.0 (HTTP API) controllers
    """
    
    def __init__(self, version: PrinterVersion = "2.0", point_of_sale: int = 1,
                 host: Optional[str] = None, password: Optional[str] = None):
        """
        Initialize the Hasar service
        
        Args:
            version: "legacy" for file-based or "2.0" for HTTP API
            point_of_sale: Point of sale number (for legacy)
            host: Host for 2.0 version
            password: Password for 2.0 version
        """
        self.version = version
        self.point_of_sale = point_of_sale
        
        if version == "legacy":
            self.printer = HasarLegacyService(point_of_sale=point_of_sale)
        else:  # version == "2.0"
            self.printer = Hasar2Service(host=host, password=password)
    
    async def get_status(self) -> Dict:
        """Get fiscal printer status"""
        if self.version == "legacy":
            return self.printer.get_status()
        else:
            return await self.printer.get_status()
    
    async def open_fiscal_receipt(self, customer_data: Dict) -> Dict:
        """Open a new fiscal receipt"""
        if self.version == "legacy":
            result = self.printer.open_fiscal_receipt(customer_data)
            return {"success": result}
        else:
            return await self.printer.open_fiscal_receipt(customer_data)
    
    async def print_item(self, description: str, quantity: float, price: float,
                        vat_rate: float = 21.0, discount: float = 0.0) -> Dict:
        """Print an item on the fiscal receipt"""
        if self.version == "legacy":
            result = self.printer.print_item(description, quantity, price, vat_rate)
            return {"success": result}
        else:
            return await self.printer.print_item(description, quantity, price, vat_rate, discount)
    
    async def close_fiscal_receipt(self) -> Dict:
        """Close the fiscal receipt"""
        if self.version == "legacy":
            return self.printer.close_fiscal_receipt()
        else:
            return await self.printer.close_fiscal_receipt()
    
    async def daily_close(self, close_type: str = "Z") -> Dict:
        """Perform daily close (Z or X report)"""
        if self.version == "legacy":
            return self.printer.daily_close(close_type)
        else:
            return await self.printer.daily_close(close_type)
    
    async def print_complete_receipt(self, receipt_data: Dict) -> List[Dict]:
        """
        Print a complete receipt with all commands
        Works with both legacy and 2.0 versions
        """
        if self.version == "2.0":
            return await self.printer.print_complete_receipt(receipt_data)
        
        # For legacy version, simulate the complete process
        results = []
        
        # Open receipt
        open_result = await self.open_fiscal_receipt(receipt_data.get("customer", {}))
        results.append({"abrirComprobante": open_result})
        
        if not open_result.get("success"):
            return results
        
        # Print items
        for item in receipt_data.get("items", []):
            item_result = await self.print_item(
                description=item.get("description", ""),
                quantity=item.get("quantity", 1),
                price=item.get("price", 0),
                vat_rate=item.get("vat_rate", 21.0)
            )
            results.append({"imprimirItem": item_result})
        
        # Close receipt
        close_result = await self.close_fiscal_receipt()
        results.append({"cerrarComprobante": close_result})
        
        return results
    
    @staticmethod
    def get_printer_instance(printer_config: Dict) -> "HasarService":
        """
        Factory method to create the appropriate printer instance based on configuration
        
        Args:
            printer_config: Configuration dict with keys:
                - version: "legacy" or "2.0"
                - point_of_sale: int (for legacy)
                - host: str (for 2.0)
                - password: str (for 2.0)
        
        Returns:
            HasarService instance
        """
        version = printer_config.get("version", "2.0")
        
        if version == "legacy":
            return HasarService(
                version="legacy",
                point_of_sale=printer_config.get("point_of_sale", 1)
            )
        else:
            return HasarService(
                version="2.0",
                host=printer_config.get("host"),
                password=printer_config.get("password")
            )

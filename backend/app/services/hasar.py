"""
Servicio Unificado de Impresora Fiscal Hasar
Gestiona las versiones legacy y 2.0, manteniendo la relación entre ambas
"""
from typing import Dict, List, Optional, Literal
from app.services.hasar_legacy import HasarLegacyService
from app.services.hasar2 import Hasar2Service


PrinterVersion = Literal["legacy", "2.0"]


class HasarService:
    """
    Servicio unificado para impresoras fiscales Hasar
    Mantiene la relación entre controladores legacy (basados en archivos) y 2.0 (HTTP API)
    """
    
    def __init__(self, version: PrinterVersion = "2.0", point_of_sale: int = 1,
                 host: Optional[str] = None, password: Optional[str] = None):
        """
        Inicializar el servicio Hasar
        
        Args:
            version: "legacy" para basado en archivos o "2.0" para HTTP API
            point_of_sale: Número de punto de venta (para legacy)
            host: Host para versión 2.0
            password: Contraseña para versión 2.0
        """
        self.version = version
        self.point_of_sale = point_of_sale
        
        if version == "legacy":
            self.printer = HasarLegacyService(point_of_sale=point_of_sale)
        else:  # version == "2.0"
            self.printer = Hasar2Service(host=host, password=password)
    
    async def get_status(self) -> Dict:
        """Obtener estado de la impresora fiscal"""
        if self.version == "legacy":
            return self.printer.get_status()
        else:
            return await self.printer.get_status()
    
    async def open_fiscal_receipt(self, customer_data: Dict) -> Dict:
        """Abrir un nuevo comprobante fiscal"""
        if self.version == "legacy":
            result = self.printer.open_fiscal_receipt(customer_data)
            return {"success": result}
        else:
            return await self.printer.open_fiscal_receipt(customer_data)
    
    async def print_item(self, description: str, quantity: float, price: float,
                        vat_rate: float = 21.0, discount: float = 0.0) -> Dict:
        """Imprimir un ítem en el comprobante fiscal"""
        if self.version == "legacy":
            result = self.printer.print_item(description, quantity, price, vat_rate)
            return {"success": result}
        else:
            return await self.printer.print_item(description, quantity, price, vat_rate, discount)
    
    async def close_fiscal_receipt(self) -> Dict:
        """Cerrar el comprobante fiscal"""
        if self.version == "legacy":
            return self.printer.close_fiscal_receipt()
        else:
            return await self.printer.close_fiscal_receipt()
    
    async def daily_close(self, close_type: str = "Z") -> Dict:
        """Realizar cierre diario (reporte Z o X)"""
        if self.version == "legacy":
            return self.printer.daily_close(close_type)
        else:
            return await self.printer.daily_close(close_type)
    
    async def print_complete_receipt(self, receipt_data: Dict) -> List[Dict]:
        """
        Imprimir un comprobante completo con todos los comandos
        Funciona con versiones legacy y 2.0
        """
        if self.version == "2.0":
            return await self.printer.print_complete_receipt(receipt_data)
        
        # Para versión legacy, simular el proceso completo
        results = []
        
        # Abrir comprobante
        open_result = await self.open_fiscal_receipt(receipt_data.get("customer", {}))
        results.append({"abrirComprobante": open_result})
        
        if not open_result.get("success"):
            return results
        
        # Imprimir ítems
        for item in receipt_data.get("items", []):
            item_result = await self.print_item(
                description=item.get("description", ""),
                quantity=item.get("quantity", 1),
                price=item.get("price", 0),
                vat_rate=item.get("vat_rate", 21.0)
            )
            results.append({"imprimirItem": item_result})
        
        # Cerrar comprobante
        close_result = await self.close_fiscal_receipt()
        results.append({"cerrarComprobante": close_result})
        
        return results
    
    @staticmethod
    def get_printer_instance(printer_config: Dict) -> "HasarService":
        """
        Método factory para crear la instancia de impresora apropiada basada en la configuración
        
        Args:
            printer_config: Diccionario de configuración con claves:
                - version: "legacy" o "2.0"
                - point_of_sale: int (para legacy)
                - host: str (para 2.0)
                - password: str (para 2.0)
        
        Returns:
            Instancia de HasarService
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

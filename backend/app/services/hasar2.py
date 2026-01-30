"""
Servicio de Impresora Fiscal Hasar - Versión 2.0
Comunicación HTTP/JSON API con impresoras fiscales modernas
"""
import httpx
import json
from typing import Dict, List, Optional
from app.core.config import settings


class Hasar2Service:
    """Servicio para comunicación con impresora fiscal Hasar 2.0 vía HTTP API"""
    
    def __init__(self, host: Optional[str] = None, password: Optional[str] = None):
        self.host = host or settings.HASAR_2_HOST
        self.password = password or settings.HASAR_2_PASSWORD
        self.port = settings.HASAR_2_PORT
        self.base_url = f"http://{self.host}:{self.port}/fiscal.json"
        
    def _get_auth(self) -> tuple:
        """Obtener credenciales de autenticación"""
        # Formato de contraseña es ":password"
        return ("", self.password.lstrip(":"))
    
    async def _send_command(self, command: Dict) -> Dict:
        """Enviar comando a la impresora fiscal vía HTTP"""
        headers = {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "Connection": "close"
        }
        
        try:
            async with httpx.AsyncClient() as client:
                response = await client.post(
                    self.base_url,
                    json=command,
                    headers=headers,
                    auth=self._get_auth(),
                    timeout=30.0
                )
                response.raise_for_status()
                return response.json()
        except httpx.HTTPError as e:
            return {"error": f"HTTP error: {str(e)}"}
        except Exception as e:
            return {"error": f"Error: {str(e)}"}
    
    async def get_status(self) -> Dict:
        """Obtener estado de la impresora fiscal"""
        command = {"obtenerEstado": {}}
        return await self._send_command(command)
    
    async def open_fiscal_receipt(self, customer_data: Dict) -> Dict:
        """Abrir un nuevo comprobante fiscal"""
        command = {
            "abrirComprobante": {
                "tipo": customer_data.get("receipt_type", "T"),  # T=Ticket, F=Factura
                "letra": customer_data.get("letter", "B"),
                "nombreCliente": customer_data.get("name", ""),
                "documentoCliente": customer_data.get("tax_id", ""),
                "tipoDocumento": customer_data.get("document_type", "CUIT"),
                "condicionIVA": customer_data.get("vat_condition", "CF")
            }
        }
        return await self._send_command(command)
    
    async def print_item(self, description: str, quantity: float, price: float,
                        vat_rate: float = 21.0, discount: float = 0.0) -> Dict:
        """Imprimir un ítem en el comprobante fiscal"""
        command = {
            "imprimirItem": {
                "descripcion": description,
                "cantidad": quantity,
                "precio": price,
                "tasaIVA": vat_rate,
                "descuento": discount,
                "unidad": "UNID"
            }
        }
        return await self._send_command(command)
    
    async def print_subtotal(self) -> Dict:
        """Imprimir subtotal"""
        command = {"imprimirSubtotal": {}}
        return await self._send_command(command)
    
    async def print_payment(self, amount: float, payment_type: str = "efectivo") -> Dict:
        """Imprimir pago"""
        command = {
            "imprimirPago": {
                "monto": amount,
                "tipo": payment_type,  # efectivo, tarjeta, cuenta_corriente
                "descripcion": payment_type.upper()
            }
        }
        return await self._send_command(command)
    
    async def close_fiscal_receipt(self) -> Dict:
        """Cerrar el comprobante fiscal"""
        command = {"cerrarComprobante": {}}
        return await self._send_command(command)
    
    async def cancel_fiscal_receipt(self) -> Dict:
        """Cancelar el comprobante fiscal actual"""
        command = {"cancelarComprobante": {}}
        return await self._send_command(command)
    
    async def daily_close(self, close_type: str = "Z") -> Dict:
        """Realizar cierre diario (reporte Z o X)"""
        command = {
            "cierreZ": {} if close_type == "Z" else "cierreX": {}
        }
        return await self._send_command(command)
    
    async def print_complete_receipt(self, receipt_data: Dict) -> List[Dict]:
        """
        Imprimir un comprobante completo con todos los comandos
        Esto mantiene compatibilidad con la estructura legacy
        """
        results = []
        
        # Abrir comprobante
        open_result = await self.open_fiscal_receipt(receipt_data.get("customer", {}))
        results.append({"abrirComprobante": open_result})
        
        if "error" in open_result:
            return results
        
        # Imprimir ítems
        for item in receipt_data.get("items", []):
            item_result = await self.print_item(
                description=item.get("description", ""),
                quantity=item.get("quantity", 1),
                price=item.get("price", 0),
                vat_rate=item.get("vat_rate", 21.0),
                discount=item.get("discount", 0.0)
            )
            results.append({"imprimirItem": item_result})
        
        # Imprimir subtotal
        subtotal_result = await self.print_subtotal()
        results.append({"imprimirSubtotal": subtotal_result})
        
        # Imprimir pagos
        for payment in receipt_data.get("payments", [{"amount": 0, "type": "efectivo"}]):
            payment_result = await self.print_payment(
                amount=payment.get("amount", 0),
                payment_type=payment.get("type", "efectivo")
            )
            results.append({"imprimirPago": payment_result})
        
        # Cerrar comprobante
        close_result = await self.close_fiscal_receipt()
        results.append({"cerrarComprobante": close_result})
        
        return results

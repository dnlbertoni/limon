"""
Servicio de Impresora Fiscal Hasar - Versión Legacy
Comunicación basada en archivos con impresoras fiscales
"""
import os
import time
from typing import Dict, Optional
from app.core.config import settings


class HasarLegacyService:
    """Servicio para comunicación con impresora fiscal Hasar legacy"""
    
    def __init__(self, point_of_sale: int = 1):
        self.point_of_sale = point_of_sale
        self.base_path = settings.HASAR_LEGACY_PATH
        self.fs = chr(28)  # Separador de campos
        self.display = 1 if point_of_sale == 4 else 0
        
    def _get_file_paths(self, file_type: str = "estado"):
        """Obtener rutas de archivos para comunicación"""
        base = f"{self.base_path}/{self.point_of_sale}"
        return {
            "tmp": f"{base}/log/{file_type}.txt",
            "send": f"{base}/mandar/{file_type}.txt",
            "receive": f"{base}/recibir/{file_type}.ans"
        }
    
    def _ensure_directories(self):
        """Asegurar que existan los directorios requeridos"""
        dirs = [
            f"{self.base_path}/{self.point_of_sale}/log",
            f"{self.base_path}/{self.point_of_sale}/mandar",
            f"{self.base_path}/{self.point_of_sale}/recibir"
        ]
        for directory in dirs:
            os.makedirs(directory, exist_ok=True)
    
    def send_command(self, command: str, file_type: str = "estado") -> bool:
        """Enviar comando a la impresora fiscal"""
        self._ensure_directories()
        paths = self._get_file_paths(file_type)
        
        try:
            # Escribir comando en archivo temporal
            with open(paths["tmp"], "w") as f:
                f.write(command)
            
            # Mover a carpeta de envío
            os.rename(paths["tmp"], paths["send"])
            return True
        except Exception as e:
            print(f"Error enviando comando: {e}")
            return False
    
    def read_response(self, file_type: str = "estado", timeout: int = 10) -> Optional[str]:
        """Leer respuesta de la impresora fiscal"""
        paths = self._get_file_paths(file_type)
        
        # Esperar archivo de respuesta
        for _ in range(timeout * 10):
            if os.path.exists(paths["receive"]):
                try:
                    with open(paths["receive"], "r") as f:
                        response = f.read()
                    # Delete response file after reading
                    os.remove(paths["receive"])
                    return response
                except Exception as e:
                    print(f"Error reading response: {e}")
                    return None
            time.sleep(0.1)
        
        return None
    
    def get_status(self) -> Dict:
        """Obtener estado de la impresora fiscal"""
        command = "S"  # Comando de estado
        if self.send_command(command, "estado"):
            response = self.read_response("estado")
            if response:
                return self._parse_status(response)
        return {"error": "No se pudo obtener el estado"}
    
    def _parse_status(self, response: str) -> Dict:
        """Parsear respuesta de estado"""
        # Parsear la respuesta de la impresora fiscal
        parts = response.split(self.fs) if response else []
        return {
            "raw_response": response,
            "status_code": parts[0] if len(parts) > 0 else "",
            "printer_status": parts[1] if len(parts) > 1 else "",
            "fiscal_status": parts[2] if len(parts) > 2 else ""
        }
    
    def open_fiscal_receipt(self, customer_data: Dict) -> bool:
        """Abrir un nuevo comprobante fiscal"""
        # Construir comando basado en datos del cliente
        command = f"@{self.fs}{customer_data.get('name', '')}{self.fs}"
        command += f"{customer_data.get('tax_id', '')}{self.fs}"
        command += f"{customer_data.get('vat_condition', 'C')}"
        
        if self.send_command(command, "receipt"):
            response = self.read_response("receipt")
            return response is not None
        return False
    
    def print_item(self, description: str, quantity: float, price: float, 
                   vat_rate: float = 21.0) -> bool:
        """Imprimir un ítem en el comprobante fiscal"""
        command = f"B{self.fs}{description}{self.fs}{quantity}{self.fs}"
        command += f"{price}{self.fs}{vat_rate}{self.fs}M{self.fs}0"
        
        if self.send_command(command, "item"):
            response = self.read_response("item")
            return response is not None
        return False
    
    def close_fiscal_receipt(self) -> Dict:
        """Cerrar el comprobante fiscal"""
        command = f"C"
        if self.send_command(command, "close"):
            response = self.read_response("close")
            if response:
                return self._parse_close_response(response)
        return {"error": "No se pudo cerrar el comprobante"}
    
    def _parse_close_response(self, response: str) -> Dict:
        """Parsear respuesta de cierre de comprobante"""
        parts = response.split(self.fs) if response else []
        return {
            "success": True,
            "receipt_number": parts[0] if len(parts) > 0 else "",
            "total": parts[1] if len(parts) > 1 else "0",
            "raw_response": response
        }
    
    def daily_close(self, close_type: str = "Z") -> Dict:
        """Realizar cierre diario (reporte Z o X)"""
        command = f"{close_type}"
        if self.send_command(command, "cierre"):
            response = self.read_response("cierre", timeout=30)
            if response:
                return self._parse_daily_close(response)
        return {"error": "No se pudo realizar el cierre diario"}
    
    def _parse_daily_close(self, response: str) -> Dict:
        """Parsear respuesta de cierre diario"""
        parts = response.split(self.fs) if response else []
        return {
            "success": True,
            "date": parts[0] if len(parts) > 0 else "",
            "close_number": parts[1] if len(parts) > 1 else "",
            "total": parts[2] if len(parts) > 2 else "0",
            "raw_response": response
        }

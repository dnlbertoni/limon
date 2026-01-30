"""
Hasar Fiscal Printer Service - Legacy Version
File-based communication with fiscal printers
"""
import os
import time
from typing import Dict, Optional
from app.core.config import settings


class HasarLegacyService:
    """Service for legacy Hasar fiscal printer communication"""
    
    def __init__(self, point_of_sale: int = 1):
        self.point_of_sale = point_of_sale
        self.base_path = settings.HASAR_LEGACY_PATH
        self.fs = chr(28)  # Field separator
        self.display = 1 if point_of_sale == 4 else 0
        
    def _get_file_paths(self, file_type: str = "estado"):
        """Get file paths for communication"""
        base = f"{self.base_path}/{self.point_of_sale}"
        return {
            "tmp": f"{base}/log/{file_type}.txt",
            "send": f"{base}/mandar/{file_type}.txt",
            "receive": f"{base}/recibir/{file_type}.ans"
        }
    
    def _ensure_directories(self):
        """Ensure required directories exist"""
        dirs = [
            f"{self.base_path}/{self.point_of_sale}/log",
            f"{self.base_path}/{self.point_of_sale}/mandar",
            f"{self.base_path}/{self.point_of_sale}/recibir"
        ]
        for directory in dirs:
            os.makedirs(directory, exist_ok=True)
    
    def send_command(self, command: str, file_type: str = "estado") -> bool:
        """Send command to fiscal printer"""
        self._ensure_directories()
        paths = self._get_file_paths(file_type)
        
        try:
            # Write command to temporary file
            with open(paths["tmp"], "w") as f:
                f.write(command)
            
            # Move to send folder
            os.rename(paths["tmp"], paths["send"])
            return True
        except Exception as e:
            print(f"Error sending command: {e}")
            return False
    
    def read_response(self, file_type: str = "estado", timeout: int = 10) -> Optional[str]:
        """Read response from fiscal printer"""
        paths = self._get_file_paths(file_type)
        
        # Wait for response file
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
        """Get fiscal printer status"""
        command = "S"  # Status command
        if self.send_command(command, "estado"):
            response = self.read_response("estado")
            if response:
                return self._parse_status(response)
        return {"error": "Unable to get status"}
    
    def _parse_status(self, response: str) -> Dict:
        """Parse status response"""
        # Parse the response from the fiscal printer
        parts = response.split(self.fs) if response else []
        return {
            "raw_response": response,
            "status_code": parts[0] if len(parts) > 0 else "",
            "printer_status": parts[1] if len(parts) > 1 else "",
            "fiscal_status": parts[2] if len(parts) > 2 else ""
        }
    
    def open_fiscal_receipt(self, customer_data: Dict) -> bool:
        """Open a new fiscal receipt"""
        # Build command based on customer data
        command = f"@{self.fs}{customer_data.get('name', '')}{self.fs}"
        command += f"{customer_data.get('tax_id', '')}{self.fs}"
        command += f"{customer_data.get('vat_condition', 'C')}"
        
        if self.send_command(command, "receipt"):
            response = self.read_response("receipt")
            return response is not None
        return False
    
    def print_item(self, description: str, quantity: float, price: float, 
                   vat_rate: float = 21.0) -> bool:
        """Print an item on the fiscal receipt"""
        command = f"B{self.fs}{description}{self.fs}{quantity}{self.fs}"
        command += f"{price}{self.fs}{vat_rate}{self.fs}M{self.fs}0"
        
        if self.send_command(command, "item"):
            response = self.read_response("item")
            return response is not None
        return False
    
    def close_fiscal_receipt(self) -> Dict:
        """Close the fiscal receipt"""
        command = f"C"
        if self.send_command(command, "close"):
            response = self.read_response("close")
            if response:
                return self._parse_close_response(response)
        return {"error": "Unable to close receipt"}
    
    def _parse_close_response(self, response: str) -> Dict:
        """Parse close receipt response"""
        parts = response.split(self.fs) if response else []
        return {
            "success": True,
            "receipt_number": parts[0] if len(parts) > 0 else "",
            "total": parts[1] if len(parts) > 1 else "0",
            "raw_response": response
        }
    
    def daily_close(self, close_type: str = "Z") -> Dict:
        """Perform daily close (Z or X report)"""
        command = f"{close_type}"
        if self.send_command(command, "cierre"):
            response = self.read_response("cierre", timeout=30)
            if response:
                return self._parse_daily_close(response)
        return {"error": "Unable to perform daily close"}
    
    def _parse_daily_close(self, response: str) -> Dict:
        """Parse daily close response"""
        parts = response.split(self.fs) if response else []
        return {
            "success": True,
            "date": parts[0] if len(parts) > 0 else "",
            "close_number": parts[1] if len(parts) > 1 else "",
            "total": parts[2] if len(parts) > 2 else "0",
            "raw_response": response
        }

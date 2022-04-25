<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ticket2G extends Hasar250{

  public function  __construct($puerto_cf=null) {
    if($puerto_cf!=null){
      $o = array();
      $o = (object) $o;
      $this->setComprobante($o);
      $this->Abrir();
      $this->setItems(null);
      $this->Cerrar();  
    }
  }

  private function Abrir(){  //abro docuemtno ticket

    $comprobante = $this->getComprobante();
    $comprobante->AbrirDocumento->CodigoComprobante="Tique";
    $this->setComprobante($comprobante);
  }

  public function setItems($items){//item documento ticket
    $Comprobante = $this->getComprobante();
    //print_r($items);
    $linea =array();
    if(is_array($items)){
      foreach($items as $item){
        $linea[] = array(
          "Descripcion" => $item->detalle,
          "Cantidad" => $item->cantidad,
          "PrecioUnitario" => $item->precio,
          "CondicionIVA" => "Gravado",
          "AlicuotaIVA" => $item->iva,
          "OperacionMonto" => "ModoSumaMonto",
          "TipoImpuestoInterno" => "IIVariableKIVA",
          "MagnitudImpuestoInterno" => "0.00",
          "ModoDisplay" => "DisplayNo",
          "ModoBaseTotal" => "ModoPrecioTotal",
          "UnidadReferencia" => 1 ,
          "CodigoProducto" => "",
          "CodigoInterno" => $item->id_articulo,
          "UnidadMedida" => "Pack"
        ); 
        //$iva = number_format($item->iva, 2, '.', '');
        //$precio = number_format($item->precio, 2, '.', '');
        //$cantidad = number_format($item->cantidad, 2, '.', '');
      }  
    }
    $Comprobante->ImprimirItem=$linea;
  }
  
  function setPagos($pagos){//Total documento ticket
    $total = number_format($pagos, 2, '.', '');
    $command="D" . $this->fs . "Efectivo" . $this->fs . $total . $this->fs . "T" . $this->fs . $this->display . "\n";
  }
	//cierro documento ticket
	function Cerrar(){
    $comprobante = $this->getComprobante();
    $comprobante->CerrarDocumento->Copias="0";
    $comprobante->CerrarDocumento->DireccionEMail="daniel.bertoni@gmail.com";
    $this->setComprobante($comprobante);	
  }		
}

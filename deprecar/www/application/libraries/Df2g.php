<?php
class Df2g {
    private $CargarDocumentoAsociado;
    private $CargarDatosCliente;
    private $AbrirDocumento;
    private $ImprimirTextoFiscal;
    private $ImprimirItem;
    private $ImprimirItemDescuento;
    private $ImprimirAnticipoBonificacionEnvases;
    private $ImprimirAjustes;
    private $ImprimirOtrosTributos;
    private $ImprimirPago;
    private $CerrarDocumento;
    private $ConsultarAcumuladosComprobante;
    private $ContinuarConsultaAcumulados;
    
    

    public function getCargarDocumentoAsociado() {
    	return $this->CargarDocumentoAsociado;
    }

    /**
    * @param $CargarDocumentoAsociado
    */
    public function setCargarDocumentoAsociado($CargarDocumentoAsociado) {
    	$this->CargarDocumentoAsociado = $CargarDocumentoAsociado;
    }

    public function getCargarDatosCliente() {
    	return $this->CargarDatosCliente;
    }

    /**
    * @param $CargarDatosCliente
    */
    public function setCargarDatosCliente($CargarDatosCliente) {
    	$this->CargarDatosCliente = $CargarDatosCliente;
    }

    public function getAbrirDocumento() {
    	return $this->AbrirDocumento;
    }

    /**
    * @param $AbrirDocumento
    */
    public function setAbrirDocumento($AbrirDocumento) {
    	$this->AbrirDocumento = $AbrirDocumento;
    }

    public function getImprimirTextoFiscal() {
    	return $this->ImprimirTextoFiscal;
    }

    /**
    * @param $ImprimirTextoFiscal
    */
    public function setImprimirTextoFiscal($ImprimirTextoFiscal) {
    	$this->ImprimirTextoFiscal = $ImprimirTextoFiscal;
    }

    public function getImprimirItem() {
    	return $this->ImprimirItem;
    }

    /**
    * @param $ImprimirItem
    */
    public function setImprimirItem($ImprimirItem) {
    	$this->ImprimirItem = $ImprimirItem;
    }

    public function getImprimirItemDescuento() {
    	return $this->ImprimirItemDescuento;
    }

    /**
    * @param $ImprimirItemDescuento
    */
    public function setImprimirItemDescuento($ImprimirItemDescuento) {
    	$this->ImprimirItemDescuento = $ImprimirItemDescuento;
    }

    public function getImprimirAnticipoBonificacionEnvases() {
    	return $this->ImprimirAnticipoBonificacionEnvases;
    }

    /**
    * @param $ImprimirAnticipoBonificacionEnvases
    */
    public function setImprimirAnticipoBonificacionEnvases($ImprimirAnticipoBonificacionEnvases) {
    	$this->ImprimirAnticipoBonificacionEnvases = $ImprimirAnticipoBonificacionEnvases;
    }

    public function getImprimirAjustes() {
    	return $this->ImprimirAjustes;
    }

    /**
    * @param $ImprimirAjustes
    */
    public function setImprimirAjustes($ImprimirAjustes) {
    	$this->ImprimirAjustes = $ImprimirAjustes;
    }

    public function getImprimirOtrosTributos() {
    	return $this->ImprimirOtrosTributos;
    }

    /**
    * @param $ImprimirOtrosTributos
    */
    public function setImprimirOtrosTributos($ImprimirOtrosTributos) {
    	$this->ImprimirOtrosTributos = $ImprimirOtrosTributos;
    }

    public function getImprimirPago() {
    	return $this->ImprimirPago;
    }

    /**
    * @param $ImprimirPago
    */
    public function setImprimirPago($ImprimirPago) {
    	$this->ImprimirPago = $ImprimirPago;
    }

    public function getCerrarDocumento() {
    	return $this->CerrarDocumento;
    }

    /**
    * @param $CerrarDocumento
    */
    public function setCerrarDocumento($CerrarDocumento) {
    	$this->CerrarDocumento = $CerrarDocumento;
    }

    public function getConsultarAcumuladosComprobante() {
    	return $this->ConsultarAcumuladosComprobante;
    }

    /**
    * @param $ConsultarAcumuladosComprobante
    */
    public function setConsultarAcumuladosComprobante($ConsultarAcumuladosComprobante) {
    	$this->ConsultarAcumuladosComprobante = $ConsultarAcumuladosComprobante;
    }

    public function getContinuarConsultaAcumulados() {
    	return $this->ContinuarConsultaAcumulados;
    }

    /**
    * @param $ContinuarConsultaAcumulados
    */
    public function setContinuarConsultaAcumulados($ContinuarConsultaAcumulados) {
    	$this->ContinuarConsultaAcumulados = $ContinuarConsultaAcumulados;
    }
}
<?php

/**
 * Class Ctacte
 * @property Fpdf $fpdf
 * @property Ctacte_movim_model $Ctacte_modim_model
 * @property Ctacte_liq_model $Ctacte_liq_model */
class Ctacte extends MY_Controller{
  var $Printer;
  function  __construct() {
    parent::__construct();
    $this->Printer = 'laser05';
    $this->load->model('Ctacte_movim_model', '', TRUE);
    $this->load->model('Ctacte_liq_model'  , '', TRUE);
    $this->load->model('Ctacte_rec_model'  , '', TRUE);
    $this->load->model('Cuenta_model'  , '', TRUE);
    $this->load->model('Numeradores_model', '',TRUE);
    $this->load->model('Facmovim_model');
    Template::set('title', 'Modulo Cuentas Corrientes');
    // panel de tareas Regulares
    $datos['tareasSet']=true;
    $datos['tareas'][] = array('cuenta/crear', 'Agregar CtaCte');
    $datos['tareas'][] = array('ctacte/estadisticas', 'Informacion Extra');

    Template::set($datos);
    Template::set_block('tareas', 'tareas'); // panel de tareas
  }
  function index(){
    Assets::add_js('ui-tableFilter');
    $fechoy=getdate();
    $pendientes = $this->Ctacte_movim_model->getTotalesAgrupados('P');
    $liquidadas = $this->Ctacte_liq_model->getAllEstado('P');
    $cuentas    = $this->Cuenta_model->listadoFiltrado(1,1);
    $diaQuery=$fechoy['year'].'-'.$fechoy['mon'].'-'.$fechoy['mday'];
    $ultimas    = $this->Ctacte_movim_model->getLast(10, $diaQuery);
    $pagadas    = $this->Ctacte_liq_model->getAllEstado('C',10);
    $data['pendientes'] = $pendientes;
    $data['liq']        = $this->Ctacte_movim_model->getTotalesAgrupados('L');
    $data['cuentas']    = $cuentas;
    $data['ultimas']    = $ultimas;
    $data['pagadas']    = $pagadas;
    $data['hoy']        = $fechoy['mday']."/".$fechoy['mon']."/".$fechoy['year'];
    Template::set($data);
    Template::render();
  }
  function liquidar($cuenta){
    $pendientes = $this->Ctacte_movim_model->getDetalle($cuenta,'P');
    $data['movimientos'] = $pendientes;
    $data['accion']  = 'ctacte/liquidarDo';
    $data['ocultos'] = array('cuenta'=>$cuenta, 'importe'=>0);
    Template::set($data);
    Template::set_view('ctacte/liqForm');
    Template::render();
  }
  function liquidarDo(){
    foreach($_POST as $key=>$valor){
      if(!preg_match('/^(cuenta)|^(importe)/', $key)){
        $datosMovim[] = $valor;
      }else{
        $cuenta  = $this->input->post('cuenta');
        $importe = $this->input->post('importe');
      };
    };
    $fecdes = $this->Ctacte_movim_model->getFecha('min','P', $cuenta);
    $fecfin = $this->Ctacte_movim_model->getFecha('max','P',$cuenta);
    $datosLiq = array(
        'fecini'    => $fecdes,
        'fecfin'    => $fecfin,
        'id_cuenta' => $cuenta,
        'importe'   => $importe
    );
    $idLiq = $this->Ctacte_liq_model->insertar($datosLiq);
    $this->Ctacte_movim_model->Liquidar($idLiq, $datosMovim );
    redirect('ctacte/pdf/liquidacion/'.$idLiq, 'location',301);
  }
  function historial($id=false){
    $fechoy = getdate();
    $id=($id)?$id:$this->input->post('cuenta');
    if(!$id){
        Template::redirect('ctacte/');
    };
    $data['pendientes'] = $this->Ctacte_movim_model->getDetalle($id,"P");
    $data['periodos']   = $this->Ctacte_liq_model->getPeriodos($id);
    $data['promedio']   = $this->Ctacte_liq_model->promedio($id);
    $data['cliente']    = $this->Cuenta_model->getNombre($id);
    $data['periodo']    = $fechoy['year'].'-'.$fechoy['mon'];
    $data['ocultos']    = array('cuenta'=>$id);
    $data['idCuenta']   = $id;
    $data['estado']     = "P";
    $data['liq']        = false;
    Template::set($data);
    Template::render();
  }
  function cobrar($idLiq){
    $liq   = $this->Ctacte_liq_model->getById($idLiq);
    $data['nombreCuenta'] = $this->Cuenta_model->getNombre($liq->id_cuenta);
    $movim = $this->Ctacte_movim_model->getByLiq($idLiq);
    $data['Liq'] = $liq;
    $data['movimientos'] = $movim;
    $data['ocultos'] = array('idLiq'=>$idLiq);
    Template::set($data);
    Template::set_view('ctacte/cobrarForm');
    Template::render();
  }
  function cobrarDo(){
    $liq = $this->Ctacte_liq_model->getById($this->input->post('idLiq'));
    //armo comprobante
    $puesto = 80 + PUESTO;
    $numero = $this->Numeradores_model->getNextRecibo($puesto);
    $idRec  = $this->Ctacte_rec_model->gabroRecibo($liq->id_cuenta, $liq->id, $this->input->post('pago1'), $formaPago=1);
    //imprimo
    //grabo
    $this->Ctacte_liq_model->cobroLiq($liq->id, $idRec);
    $this->Ctacte_movim_model->cobroFac($liq->id, $idRec);
    Template::redirect('ctacte/');
  }
  function adeudadas(){
    $data['pendientes']=$this->Ctacte_liq_model->getAllEstado('P');
    Template::set($data);
    Template::set_view('ctacte/listado');
    Template::render();
  }
  function detalleComprobante($id, $accion=0,$liq=FALSE){
    $data['borrar']=($accion==1)?true:false;
    $data['fac'] = $this->Ctacte_movim_model->getEncabezado($id,$liq);
    $data['art'] = $this->Ctacte_movim_model->getComprobante($id,$liq);
    $data['idMovim']=$id;
    $data['liquidado']=$liq;
    $this->load->view('ctacte/detalleComprobante', $data);
  }
  function quitarDeLaCuenta($id){
    $this->Ctacte_movim_model->quitarDeLaCuenta($id);
  }
  function reimpresion($id, $liq=FALSE){
    /**
     * Reimprime comprobante de remito por PDF
     *
     * lee el id del movimiento en la cuenta corriente y desglosa el comprobante original
     *
     * @param integer $id id del encabezado del comprobante
     *
     */
    $encabezado = $this->Ctacte_movim_model->getEncabezado($id,$liq);
    $detalle    = $this->Ctacte_movim_model->getComprobante($id,$liq);

    $this->load->library('fpdf');
    $renglon=0;
    $hoja=0;
    $firma=false;
    $total=0;
    $this->fpdf->Open();
    $this->fpdf->SetMargins(0,0,0);
    $this->fpdf->SetAutoPageBreak(true);
    $this->fpdf->SetDrawColor(128);
    $this->fpdf->SetTopMargin(10);
    $maxLin =($firma)?17:20;
    $resto = count($detalle);
    foreach($detalle as $item ){
      if($renglon==0){
        //imprimo encabezado
        $this->fpdf->AddPage('P',array('100','148'));
        $this->fpdf->SetFont('Arial','b','10');
        $this->fpdf->Cell(0,5,"Documento No Valido como Factura",0,1,'C');
        $this->fpdf->Cell(70,5,sprintf("( %s ) %s",$encabezado->cuenta_id,$encabezado->cuenta_nombre),0,0,'L');
        $this->fpdf->Cell(30,5,$encabezado->fecha,0,1,'R');

        if($firma){
          $this->fpdf->Cell(50,5,sprintf("Comp. CtaCte: %s", $encabezado->comprobante),0,0,'L');
        }
        $this->fpdf->Cell(50,5,sprintf("%s: %s", $encabezado->tipcom, $encabezado->comprobante),0,0,'L');
        $this->fpdf->Line(0,25,100,25);
        $this->fpdf->SetFont('Arial','b','8');
        $this->fpdf->SetXY(0,25);
        $this->fpdf->Cell(10,5,"Cant",0,0,'C');
        $this->fpdf->SetXY(10,25);
        $this->fpdf->Cell(80,5,"Detalle",0,0,'C');
        $this->fpdf->SetXY(70,25);
        $this->fpdf->Cell(15,5,"Unit",0,0,'C');
        $this->fpdf->SetXY(85,25);
        $this->fpdf->Cell(10,5,"Importe",0,1,'C');
        $this->fpdf->Line(0,30,100,30);
        if($hoja>0){
          $linea = $renglon*5 + 30;
          $this->fpdf->Cell(0,5,sprintf("Transporte --> %4.2f", $total),0,1,'R');
        };
      };
      $this->fpdf->SetFont('Arial','','10');
      $linea =($hoja==0)?$renglon*5 + 30:$renglon*5 + 35;
      $this->fpdf->SetXY(0,$linea);
      $this->fpdf->Cell(10,5,$item->Cantidad,0,0,'L');
      $this->fpdf->SetXY(10,$linea);
      $this->fpdf->Cell(80,5,substr($item->Nombre,0,27),0,0,'L');
      $this->fpdf->SetXY(70,$linea);
      $this->fpdf->Cell(10,5,$item->Precio,0,0,'R');
      $this->fpdf->SetXY(85,$linea);
      $this->fpdf->Cell(10,5,sprintf("%4.2f",$item->Precio*$item->Cantidad),0,1,'R');
      $total += ($item->Cantidad*$item->Precio);
      $renglon++;
      $resto--;
      if($renglon>$maxLin){
        //termino comprobante parcial
        if($resto>0){
          $this->fpdf->SetFont('Arial','b','10');
          $this->fpdf->Line(0,$linea+5,100,$linea+5);
          $this->fpdf->Cell(0,5,sprintf("Transporte --> %4.2f", $total),0,1,'R');
          if($firma){
            $this->fpdf->Line(20,$linea+20,80,$linea+20);
            $this->fpdf->SetXY(0,$linea+22);
            $this->fpdf->Cell(0,5,"Firma del Cliente",0,1,'C');
          };
        }
        $renglon=0;
        $hoja++;
      };
    };
    $this->fpdf->SetFont('Arial','b','10');
    $this->fpdf->Line(0,$linea+5,100,$linea+5);
    $this->fpdf->Cell(0,5,sprintf("Total --> %4.2f", $total),0,1,'R');
    if($firma){
      $this->fpdf->Line(20,$linea+20,80,$linea+20);
      $this->fpdf->SetXY(0,$linea+22);
      $this->fpdf->Cell(0,5,"Firma del Cliente",0,1,'C');
    };
    $nombre = "/var/www/fiscal/".PUESTO . "/pdf/ticket.pdf";
    $this->fpdf->Output($nombre, 'F');
    $cmd=sprintf("lp -o media=Custom.100x148mm %s -d %s", $nombre,$this->Printer);
    shell_exec($cmd);
    return $nombre;
  }
  function verLiquidacion($liq){
    $data['pendientes'] = $this->Ctacte_movim_model->getDetalleLiquidacion($liq);
    //$data['cliente']    = $this->Cuenta_model->getNombre($id);
    //$data['ocultos']    = array('cuenta'=>$id);
    //$data['idCuenta']   = $id;
    $data['estado'] = "L";
    $data['liq']        = $liq;
    Template::set($data);
    Template::set_view('historial');
    Template::render();
  }

}

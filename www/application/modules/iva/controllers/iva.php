<?php
class Iva extends MY_Controller{
  /**
   * @property Facencab_model $facencab_model
   * @property PHPExcel $PHPExcel
   */
  private $Printer;
  function __construct(){
    $this->load->library('fpdf');
    parent::__construct();
    $this->load->model('Facencab_model','',true);
	  //$this->Printer = PRREMITO;
    $this->Printer = 'laser01';
    Template::set('title','Modulo de I.V.A.');
    $datos['tareas'][] = array( 'iva/cierre', 'Cierro Periodo');
    $datos['tareas'][] = array( 'iva/libro' , 'Libro I.V.A.');
    $datos['tareas'][] = array( 'iva/ingbru', 'Percepciones');
    Template::set($datos);
    Template::set_block('tareas', 'tareas'); // panel de tareas
  }
  function index(){
    $data ['periven']= $this->Facencab_model->ListadoSumaPeriodos(1);
    $data ['pericom']= $this->Facencab_model->ListadoSumaPeriodos(2);
    Template::set($data);
    Template::render();
  }
  function cierre(){
    $data['selectLibro']=$this->input->post('libro');
    $data['selectPeriodo']=$this->input->post('periodo');
    $data['periodos'] = $this->Facencab_model->PeriodosToDropDown();
    if($this->input->post('periodo')){
      $facturas = $this->Facencab_model->ListadoFacturasPeriodoCerrar($this->input->post('libro'), $this->input->post('periodo') );
      $totimp = 0;
      $totiva = 0;
      foreach($facturas as $fac){
        $totimp += $fac->importe * $fac->suma * $fac->ivapass;
        $totiva += $fac->ivatot  * $fac->suma * $fac->ivapass;
      };
      $data['facturas'] = $facturas;
      $data['totimp'] = $totimp;
      $data['totiva']= $totiva;
    }
    $data['Libro'] = ($this->input->post('libro')==1)?'Ventas':'Compras';
    $data['Periodo'] = $this->input->post('periodo');
    //Assets::add_js('iva/cierre');
    Template::set($data);
    Template::render();
  }
  function cierreDo(){
    foreach($_POST as $key=>$valor){
      if($key!='periodo'){
        if( $key!='Cerrar'){
          $data['facturas_id'] [$key] = $valor;
        }
      }
    };
    //Template::set('contenido', print_r($data));
    //Template::render();
    $data['resultado']=$this->Facencab_model->ActualizoPeriva($this->input->post('periodo'), $data['facturas_id']);
    Template::redirect('iva');
  }
  function libro(){
    $data['selectLibro']=$this->input->post('libro');
    $data['selectPeriodo']=$this->input->post('periodo');
    $data['periodos'] = $this->Facencab_model->PeriodosToDropDown();
    if($this->input->post('periodo')){
      $facturas = $this->Facencab_model->ListadoFacturasPeriodo($this->input->post('libro'), $this->input->post('periodo') );
      $totimp = 0;
      $totiva = 0;
      foreach($facturas as $fac){
        $totimp += $fac->importe * $fac->suma * $fac->ivapass;
        $totiva += $fac->ivatot  * $fac->suma * $fac->ivapass;
      };
      $data['facturas'] = $facturas;
      $data['totimp'] = $totimp;
      $data['totiva']= $totiva;
    }
    $data['Libro'] = ($this->input->post('libro')==1)?'Ventas':'Compras';
    $data['libro'] = $this->input->post('libro');
    $data['Periodo'] = $this->input->post('periodo');
    Assets::add_js('iva/libro');
    Template::set($data);
    Template::render();
  }
  function ingbru(){
    $data['selectPeriodo']=$this->input->post('periodo');
    $data['periodos'] = $this->Facencab_model->PeriodosToDropDown();
    if($this->input->post('periodo')){
      $data['facturas'] = $this->Facencab_model->ListadoPercepciones($this->input->post('periodo') );
    }
    $data['Periodo'] = $this->input->post('periodo');
    Assets::add_js('iva/ingbru');
    Template::set($data);
    Template::render();
  }
  function PeriodotoExcel($libro, $periodo){
    $facturas = $this->Facencab_model->LibroIVA($libro, $periodo);
    $filename  = "Bertoni_";
    $filename .= ($libro==1)? "ventas_":"compras_";
    $filename .= $periodo . ".xls";
    $this->load->library('ms_excel');
    $this->ms_excel->MysqlToFile( $filename, $facturas );
  }
  function PeriodotoPdf($libro, $periodo, $destino='I'){
    $facturas = $this->Facencab_model->LibroIVA($libro, $periodo);
    // totalizdores
    $tot['imp']=0;
    $tot['net']=0;
    $tot['min']=0;
    $tot['max']=0;
    $tot['ing']=0;
    $tot['int']=0;
    $tot['per']=0;
    $linea=0;
    $pagina=0;
    $nota=false;
    foreach($facturas as $factura){
    //encabezado
      if($linea==0){
        $this->fpdf->AddPage();
        $this->fpdf->SetTextColor(0,0,0);
        $this->fpdf->SetFont('Arial','B',15);
        $titulo = sprintf("Ramon Jose Bertoni - CUIT: 20085805755");
        $this->fpdf->Cell(0,5,$titulo,0,1,'C',false);
        $titulo = sprintf("Libro de I.V.A de %s del Periodo %s", ($libro==1)?"Ventas":"Compras", $periodo);
        $this->fpdf->Cell(0,10,$titulo,0,1,'C',false);
        $this->fpdf->SetFont('Arial','',6);
        $this->fpdf->SetFillColor(0,0,0);
        $this->fpdf->SetTextColor(255,255,255);
        $this->fpdf->Cell(15,5,'Fecha',       1,0,'C',true);
        $this->fpdf->Cell(25,5,'Comprobante', 1,0,'C',true);
        $this->fpdf->Cell(45,5,'Razon Social', 1,0,'C',true);
        $this->fpdf->Cell(15,5,'CUIT',        1,0,'C',true);
        $this->fpdf->Cell(17,5,'Importe',     1,0,'C',true);
        $this->fpdf->Cell(17,5,'Neto',        1,0,'C',true);
        $this->fpdf->Cell(10,5,'Iva 10,5%',      1,0,'C',true);
        $this->fpdf->Cell(14,5,'Iva 21%',      1,0,'C',true);
        $this->fpdf->Cell(12,5,'I.B.',      1,0,'C',true);
        $this->fpdf->Cell(10,5,'Imp. Int.',      1,0,'C',true);
        $this->fpdf->Cell(10,5,'Percep.',      1,1,'C',true);
	    $linea++;
                //cuerpo
        $this->fpdf->SetFillColor(255,255,255);
        $this->fpdf->SetTextColor(0,0,0);
          if($factura->suma==0){
            $nota=false;

            //$this->fpdf->Cell(5,5,'*',0,1,'C',false);
          }else{
            $this->fpdf->Cell(15,5,$factura->fecha,       1,0,'C',false);
            $this->fpdf->Cell(25,5,$factura->comprobante, 1,0,'L',false);
            $this->fpdf->Cell(45,5,$factura->razonSocial, 1,0,'L',false);
            $this->fpdf->Cell(15,5,$factura->Cuit,        1,0,'R',false);
            $this->fpdf->Cell(17,5,money_format('%= (#8.2n',$factura->importe),     1,0,'R',false);
            $this->fpdf->Cell(17,5,money_format('%= (#8.2n',$factura->neto),        1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$factura->ivamin),      1,0,'R',false);
            $this->fpdf->Cell(14,5,money_format('%= (#4.2n',$factura->ivamax),      1,0,'R',false);
            $this->fpdf->Cell(12,5,money_format('%= (#4.2n',$factura->ingbru),      1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$factura->impint),      1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$factura->percep),      1,0,'R',false);
            $this->fpdf->Cell(5,5,' ',0,1,'C',false);
	    $linea++;

          }
          //sumo a los totales
          $tot['imp']+=$factura->importe * $factura->suma;
          $tot['net']+=$factura->neto * $factura->suma;
          $tot['min']+=$factura->ivamin * $factura->suma;
          $tot['max']+=$factura->ivamax * $factura->suma;
          $tot['ing']+=$factura->ingbru * $factura->suma;
          $tot['int']+=$factura->impint * $factura->suma;
          $tot['per']+=$factura->percep * $factura->suma;

      }else{
        if($linea==47 && count($facturas)>($linea+($pagina*47))){
         /**** agrego para que sume ***/
          if($factura->suma==0){
            $nota=false;
            //$this->fpdf->Cell(5,5,'*',0,1,'C',false);
          }else{
            $this->fpdf->Cell(15,5,$factura->fecha,       1,0,'C',false);
            $this->fpdf->Cell(25,5,$factura->comprobante, 1,0,'L',false);
            $this->fpdf->Cell(45,5,$factura->razonSocial, 1,0,'L',false);
            $this->fpdf->Cell(15,5,$factura->Cuit,        1,0,'R',false);
            $this->fpdf->Cell(17,5,money_format('%= (#8.2n',$factura->importe),     1,0,'R',false);
            $this->fpdf->Cell(17,5,money_format('%= (#8.2n',$factura->neto),        1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$factura->ivamin),      1,0,'R',false);
            $this->fpdf->Cell(14,5,money_format('%= (#4.2n',$factura->ivamax),      1,0,'R',false);
            $this->fpdf->Cell(12,5,money_format('%= (#4.2n',$factura->ingbru),      1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$factura->impint),      1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$factura->percep),      1,0,'R',false);
            $this->fpdf->Cell(5,5,' ',0,1,'C',false);
	    $linea++;
          }
          //sumo a los totales
          $tot['imp']+=$factura->importe * $factura->suma;
          $tot['net']+=$factura->neto * $factura->suma;
          $tot['min']+=$factura->ivamin * $factura->suma;
          $tot['max']+=$factura->ivamax * $factura->suma;
          $tot['ing']+=$factura->ingbru * $factura->suma;
          $tot['int']+=$factura->impint * $factura->suma;
          $tot['per']+=$factura->percep * $factura->suma;
          /**** agregue para qeu sume **/

          $this->fpdf->SetFillColor(0,0,0);
          $this->fpdf->SetTextColor(255,255,255);
          $this->fpdf->Cell(100,5,'Subotales',       1,0,'L',true);
          $this->fpdf->Cell(17,5, money_format('%= (#8.2n',$tot['imp']),     1,0,'R',true);
          $this->fpdf->Cell(17,5, money_format('%= (#8.2n',$tot['net']),        1,0,'R',true);
          $this->fpdf->Cell(10,5, money_format('%= (#4.2n',$tot['min']),      1,0,'R',true);
          $this->fpdf->Cell(14,5,money_format('%= (#4.2n',$tot['max']),      1,0,'R',true);
          $this->fpdf->Cell(12,5,money_format('%= (#4.2n',$tot['ing']),      1,0,'R',true);
          $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$tot['int']),      1,0,'R',true);
          $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$tot['per']),      1,1,'R',true);
          $linea=0;
          $pagina++;
        }else{
        //cuerpo
          if($factura->suma==0){
            $nota=false;
            //$this->fpdf->Cell(5,5,'*',0,1,'C',false);
          }else{
            $this->fpdf->Cell(15,5,$factura->fecha,       1,0,'C',false);
            $this->fpdf->Cell(25,5,$factura->comprobante, 1,0,'L',false);
            $this->fpdf->Cell(45,5,$factura->razonSocial, 1,0,'L',false);
            $this->fpdf->Cell(15,5,$factura->Cuit,        1,0,'R',false);
            $this->fpdf->Cell(17,5,money_format('%= (#8.2n',$factura->importe),     1,0,'R',false);
            $this->fpdf->Cell(17,5,money_format('%= (#8.2n',$factura->neto),        1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$factura->ivamin),      1,0,'R',false);
            $this->fpdf->Cell(14,5,money_format('%= (#4.2n',$factura->ivamax),      1,0,'R',false);
            $this->fpdf->Cell(12,5,money_format('%= (#4.2n',$factura->ingbru),      1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$factura->impint),      1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$factura->percep),      1,0,'R',false);
            $this->fpdf->Cell(5,5,' ',0,1,'C',false);
	    $linea++;
          }
          //sumo a los totales
          $tot['imp']+=$factura->importe * $factura->suma;
          $tot['net']+=$factura->neto * $factura->suma;
          $tot['min']+=$factura->ivamin * $factura->suma;
          $tot['max']+=$factura->ivamax * $factura->suma;
          $tot['ing']+=$factura->ingbru * $factura->suma;
          $tot['int']+=$factura->impint * $factura->suma;
          $tot['per']+=$factura->percep * $factura->suma;
        };
      };
    };
    if(count($facturas)>0){
      //totales
      $this->fpdf->SetFillColor(0,0,0);
      $this->fpdf->SetTextColor(255,255,255);
      $this->fpdf->Cell(100,5,'Totales',       1,0,'L',true);
      $this->fpdf->Cell(17,5, money_format('%= (#8.2n',$tot['imp']),     1,0,'R',true);
      $this->fpdf->Cell(17,5, money_format('%= (#8.2n',$tot['net']),        1,0,'R',true);
      $this->fpdf->Cell(10,5, money_format('%= (#4.2n',$tot['min']),      1,0,'R',true);
      $this->fpdf->Cell(14,5,money_format('%= (#4.2n',$tot['max']),      1,0,'R',true);
      $this->fpdf->Cell(12,5,money_format('%= (#4.2n',$tot['ing']),      1,0,'R',true);
      $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$tot['int']),      1,0,'R',true);
      $this->fpdf->Cell(10,5,money_format('%= (#4.2n',$tot['per']),      1,1,'R',true);

      //nota aclaratoria
      if($nota){
        $msj= "NOTA:los comprobantes con (*) no computan ya que estan incluidos en el resumen Z correspondiente";
        $this->fpdf->Ln(5);
        $this->fpdf->Cell(0,5,$msj ,      1,0,'J',true);
      }
    }
    $filename = TMP . "temporal.pdf";
    $filename  = "Bertoni_";
    $filename .= ($libro==1)? "ventas_":"compras_";
    $filename .= $periodo . ".pdf";

    $this->fpdf->OutPut($filename, $destino);
  }
  function PeriodotoPrint($libro, $periodo){
    $this->PeriodotoPdf($libro,$periodo,'F');
    $filename = TMP . "temporal.pdf";
    $this->fpdf->OutPut($filename, 'F');
    $comando = sprintf("lp $filename -d $this->Printer");
    exec($comando);
    $comando = sprintf("rm -f $filename");
    exec($comando);
  }
  function PercepcionestoExcel($periodo){
    $facturas = $this->Facencab_model->ListadoPercepciones($periodo);
    //$this->load->library('fb');
    //$this->fb->info($facturas, "info");
    $filename  = "Bertoni_Percepciones_";
    $filename .= $periodo . ".xls";
    $this->load->library('ms_excel');
    $this->ms_excel->MysqlToFile( $filename, $facturas );
  }
  function PercepcionestoPdf($periodo, $destino="I"){
    $facturas = $this->Facencab_model->ListadoPercepciones($periodo);
    $this->fpdf->AddPage();
      // totalizdores
      $tot['imp']=0;
      $tot['net']=0;
      $tot['min']=0;
      $tot['max']=0;
      $tot['ing']=0;
      $tot['int']=0;
      $tot['per']=0;
    $i=0;
    $nota=false;
    foreach($facturas as $factura){
    //encabezado
      if($i==0){
        $this->fpdf->SetFont('Arial','B',15);
        $titulo = sprintf("Listado de Percepciones - Periodo: %s", $periodo);
        $this->fpdf->Cell(0,15,$titulo,0,1,'C',false);
        $this->fpdf->SetFont('Arial','',6);
        $this->fpdf->SetFillColor(0,0,0);
        $this->fpdf->SetTextColor(255,255,255);
        $this->fpdf->Cell(80,5,'Razon Social', 1,0,'C',true);
        $this->fpdf->Cell(15,5,'CUIT',        1,0,'C',true);
        $this->fpdf->Cell(10,5,'Importe',     1,0,'C',true);
        $this->fpdf->Cell(10,5,'Neto',        1,0,'C',true);
        $this->fpdf->Cell(10,5,'Iva 10,5%',      1,0,'C',true);
        $this->fpdf->Cell(10,5,'Iva 21%',      1,0,'C',true);
        $this->fpdf->Cell(10,5,'I.B.',      1,0,'C',true);
        $this->fpdf->Cell(10,5,'Imp. Int.',      1,0,'C',true);
        $this->fpdf->Cell(10,5,'Percep.',      1,1,'C',true);
        $this->fpdf->SetFillColor(255,255,255);
        $this->fpdf->SetTextColor(0,0,0);
        $i++;
      }else{
        if($i==48){
          $i=0;
        }else{
          $i++;
        };
      };
    //cuerpo
      $this->fpdf->Cell(80,5,$factura->razonSocial, 1,0,'L',false);
      $this->fpdf->Cell(15,5,$factura->cuit,        1,0,'R',false);
      $this->fpdf->Cell(10,5,$factura->importe,     1,0,'R',false);
      $this->fpdf->Cell(10,5,$factura->neto,        1,0,'R',false);
      $this->fpdf->Cell(10,5,$factura->ivamin,      1,0,'R',false);
      $this->fpdf->Cell(10,5,$factura->ivamax,      1,0,'R',false);
      $this->fpdf->Cell(10,5,$factura->ingbru,      1,0,'R',false);
      $this->fpdf->Cell(10,5,$factura->impint,      1,0,'R',false);
      $this->fpdf->Cell(10,5,$factura->percep,      1,0,'R',false);
      $this->fpdf->Cell(5,5,' ',0,1,'C',false);
      //sumo a los totales
      $tot['imp']+=$factura->importe;
      $tot['net']+=$factura->neto;
      $tot['min']+=$factura->ivamin;
      $tot['max']+=$factura->ivamax;
      $tot['ing']+=$factura->ingbru;
      $tot['int']+=$factura->impint;
      $tot['per']+=$factura->percep;
    };
    //totales
    $this->fpdf->SetFillColor(0,0,0);
    $this->fpdf->SetTextColor(255,255,255);
    $this->fpdf->Cell(95,5,'Totales',       1,0,'L',true);
    $this->fpdf->Cell(10,5,$tot['imp'],     1,0,'R',true);
    $this->fpdf->Cell(10,5,$tot['net'],        1,0,'R',true);
    $this->fpdf->Cell(10,5,$tot['min'],      1,0,'R',true);
    $this->fpdf->Cell(10,5,$tot['max'],      1,0,'R',true);
    $this->fpdf->Cell(10,5,$tot['ing'],      1,0,'R',true);
    $this->fpdf->Cell(10,5,$tot['int'],      1,0,'R',true);
    $this->fpdf->Cell(10,5,$tot['per'],      1,1,'R',true);

    $filename = TMP . "temporal.pdf";
    $this->fpdf->OutPut($filename, $destino);
  }
  function PercepcionestoPrint($periodo){
    $this->PercepcionestoPdf($periodo,'F');
    $filename = TMP . "temporal.pdf";
    $this->fpdf->OutPut($filename, 'F');
    $comando = sprintf("lp $filename -d $this->Printer");
    exec($comando);
    $comando = sprintf("rm -f $filename");
    exec($comando);
  }
  function subirLista(){
      $error = array('error' => '');
      Template::set($error);
    Template::render();
  }
  function subirCSVDo(){
    $config['upload_path'] = TMP;
	$config['allowed_types'] = 'csv|txt';
	$config['max_size']	= '20480';
	$this->load->library('upload', $config);
	if ( ! $this->upload->do_upload()){
      $error = array('error' => $this->upload->display_errors());
      Template::set($error);
      Template::set_view('iva/subirLista');
	}else{
      $archivo =  $this->upload->data();
      $this->load->library('Getcsv');
      $facturas=$this->getcsv->set_file_path($archivo['full_path'], ";")->get_array();
      /*
       * grabo en las tablas
       */
      foreach ($facturas as $fac){
        $clave = array(
                        "fecha"     => $fac['fecha'],
                        "tipcom_id" => $fac['tipcom_id'],
                        "puesto"    => $fac['puesto'],
                        "numero"    => $fac['numero'],
                        "letra"     => $fac['letra'],
                        "cuenta_id" => $fac['cuenta_id']
        );
        //verifico que no exista
        $existe=$this->Facencab_model->existeClave($clave);
        //grabo en facencab
        if(!$existe){
          foreach($fac as $key=>$value){
             if( $key!='cuit' && $key !='nom'){
              $datos[$key]=$value;
             }
          }
          $id=$this->Facencab_model->add($datos);
          $facturasDo [] = $fac;
        }
      }
      $data['facturas'] = $facturasDo;
      Template::set($data);
      Template::set_view('iva/archivoAS');
	}
    Template::render();
  }
  function PeriodostoPdf($libro, $destino='I'){
    $this->load->library('fpdf');
    for($y=2007,$m=1;$y<2013;$m++){
      if($m>12){
        $m=1;
        $y++;
      }
      $periodos[]=$y*100+$m;
    }
    foreach($periodos as $periodo){
      $facturas = $this->Facencab_model->LibroIVA($libro, $periodo);
      // totalizdores
      $tot['imp']=0;
      $tot['net']=0;
      $tot['min']=0;
      $tot['max']=0;
      $tot['ing']=0;
      $tot['int']=0;
      $tot['per']=0;
      $linea=0;
      $pagina=0;
      $nota=false;
      foreach($facturas as $factura){
      //encabezado
        if($linea==0){
          $this->fpdf->AddPage();
          $this->fpdf->SetTextColor(0,0,0);
          $this->fpdf->SetFont('Arial','B',15);
          $titulo = sprintf("Libro de I.V.A de %s del Periodo %s", ($libro==1)?"Ventas":"Compras", $periodo);
          $this->fpdf->Cell(0,15,$titulo,0,1,'C',false);
          $this->fpdf->SetFont('Arial','',6);
          $this->fpdf->SetFillColor(0,0,0);
          $this->fpdf->SetTextColor(255,255,255);
          $this->fpdf->Cell(15,5,'Fecha',       1,0,'C',true);
          $this->fpdf->Cell(25,5,'Comprobante', 1,0,'C',true);
          $this->fpdf->Cell(55,5,'Razon Social', 1,0,'C',true);
          $this->fpdf->Cell(15,5,'CUIT',        1,0,'C',true);
          $this->fpdf->Cell(15,5,'Importe',     1,0,'C',true);
          $this->fpdf->Cell(15,5,'Neto',        1,0,'C',true);
          $this->fpdf->Cell(10,5,'Iva 10,5%',      1,0,'C',true);
          $this->fpdf->Cell(10,5,'Iva 21%',      1,0,'C',true);
          $this->fpdf->Cell(10,5,'I.B.',      1,0,'C',true);
          $this->fpdf->Cell(10,5,'Imp. Int.',      1,0,'C',true);
          $this->fpdf->Cell(10,5,'Percep.',      1,1,'C',true);
          $this->fpdf->SetFillColor(255,255,255);
          $this->fpdf->SetTextColor(0,0,0);
          $linea++;
        }else{
          if($linea==49 && count($facturas)>($linea+($pagina*48))){
            $this->fpdf->SetFillColor(0,0,0);
            $this->fpdf->SetTextColor(255,255,255);
            $this->fpdf->Cell(110,5,'Subotales',       1,0,'L',true);
            $this->fpdf->Cell(15,5, money_format('%= (#10.2n',$tot['imp']),     1,0,'R',true);
            $this->fpdf->Cell(15,5, money_format('%= (#10.2n',$tot['net']),        1,0,'R',true);
            $this->fpdf->Cell(10,5, money_format('%= (#6.2n',$tot['min']),      1,0,'R',true);
            $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$tot['max']),      1,0,'R',true);
            $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$tot['ing']),      1,0,'R',true);
            $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$tot['int']),      1,0,'R',true);
            $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$tot['per']),      1,1,'R',true);
            $linea=0;
            $pagina++;
          }else{
          //cuerpo
            $this->fpdf->Cell(15,5,$factura->fecha,       1,0,'C',false);
            $this->fpdf->Cell(25,5,$factura->comprobante, 1,0,'L',false);
            $this->fpdf->Cell(55,5,$factura->razonSocial, 1,0,'L',false);
            $this->fpdf->Cell(15,5,$factura->Cuit,        1,0,'J',false);
            $this->fpdf->Cell(15,5,money_format('%= (#10.2n',$factura->importe),     1,0,'R',false);
            $this->fpdf->Cell(15,5,money_format('%= (#10.2n',$factura->neto),        1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$factura->ivamin),      1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$factura->ivamax),      1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$factura->ingbru),      1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$factura->impint),      1,0,'R',false);
            $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$factura->percep),      1,0,'R',false);
            if($factura->suma==0){
              $nota=true;
              $this->fpdf->Cell(5,5,'*',0,1,'C',false);
            }else{
              $this->fpdf->Cell(5,5,' ',0,1,'C',false);
            }
            //sumo a los totales
            $tot['imp']+=$factura->importe * $factura->suma;
            $tot['net']+=$factura->neto * $factura->suma;
            $tot['min']+=$factura->ivamin * $factura->suma;
            $tot['max']+=$factura->ivamax * $factura->suma;
            $tot['ing']+=$factura->ingbru * $factura->suma;
            $tot['int']+=$factura->impint * $factura->suma;
            $tot['per']+=$factura->percep * $factura->suma;
            $linea++;
          };
        };
      };
      if(count($facturas)>0){
        //totales
        $this->fpdf->SetFillColor(0,0,0);
        $this->fpdf->SetTextColor(255,255,255);
        $this->fpdf->Cell(110,5,'Totales',       1,0,'L',true);
        $this->fpdf->Cell(15,5, money_format('%= (#10.2n',$tot['imp']),     1,0,'R',true);
        $this->fpdf->Cell(15,5, money_format('%= (#10.2n',$tot['net']),        1,0,'R',true);
        $this->fpdf->Cell(10,5, money_format('%= (#6.2n',$tot['min']),      1,0,'R',true);
        $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$tot['max']),      1,0,'R',true);
        $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$tot['ing']),      1,0,'R',true);
        $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$tot['int']),      1,0,'R',true);
        $this->fpdf->Cell(10,5,money_format('%= (#6.2n',$tot['per']),      1,1,'R',true);

        //nota aclaratoria
        if($nota){
          $msj= "NOTA:los comprobantes con (*) no computan ya que estan incluidos en el resumen Z correspondiente";
          $this->fpdf->Ln(5);
          $this->fpdf->Cell(0,5,$msj ,      1,0,'J',true);
        }
      }
    }
    $filename = TMP . "temporal.pdf";
    $this->fpdf->OutPut($filename, $destino);
    //Template::redirect('iva');
  }
  function borroFactura($id=false){
    if($id){
      $this->Facencab_model->borrar($id);
    }
    Template::redirect('iva/');
  }
  function PeriodoExcel($libro, $periodo){
    $fact =  $this->Facencab_model->LibroIVA($libro, $periodo);
    foreach($fact as $f){
      $factura=array();
      foreach ($f as $key => $value) {
        $factura[$key]=$value;
      }
      $facturas [] = $factura;
    }
    $this->load->library('PHPExcel');

    $Excel = new PHPExcel();


    // configuramos las propiedades del documento
    $Excel->getProperties()->setCreator($erpEmpresa)
        ->setLastModifiedBy($erpEmpresa)
        ->setTitle("Office 2007 XLSX Test Document")
        ->setSubject("Office 2007 XLSX Test Document")
        ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Test result file");

    $libroName=($libro==2)?"Compras":"Ventas";
    // agregamos información a las celdas
    $Excel->setActiveSheetIndex(0);

    // Renombramos la hoja de trabajo
    $Excel->getActiveSheet()->setTitle($periodo)
                            ->fromArray(array_keys($facturas[0]),null,'A1');
    $Excel->getActiveSheet()->fromArray($facturas,null,'A2');


    // configuramos el documento para que la hoja
    // de trabajo número 0 sera la primera en mostrarse
    // al abrir el documento
    $Excel->setActiveSheetIndex(0);


    // redireccionamos la salida al navegador del cliente (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$libroName.$periodo.'.xlsx"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel2007');
    $objWriter->save('php://output');
  }
}

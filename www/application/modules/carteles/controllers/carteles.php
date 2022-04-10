<?php
/*
 * Controlador de los carteles para imprimir precios
 */

 class Carteles extends MY_Controller{
   function __construct(){
     parent::__construct();
     $this->load->model('Articulos_model','',true);
   }
   function index(){

     $Menu[0]['boton']  = "bot_Navidad";
     $Menu[0]['link']   = "carteles/navidad";
     $Menu[0]['nombre'] = "Carteles Navidad";

     $Menu[1]['boton']  = "bot_Cartel3";
     $Menu[1]['link']   = "carteles/ofertas/3";
     $Menu[1]['nombre'] = "Oferta 3 X Hoja";

     $Menu[2]['boton']  = "bot_Cartel";
     $Menu[2]['link']   = "carteles/ofertas/1";
     $Menu[2]['nombre'] = "Oferta 1 X Hoja";

     $Menu[3]['boton']  = "bot_gondola";
     $Menu[3]['link']   = "carteles/precios/1";
     $Menu[3]['nombre'] = "Carteles Precios";

     $Menu[4]['boton']  = "bot_vinos";
     $Menu[4]['link']   = "carteles/precios/2";
     $Menu[4]['nombre'] = "Carteles Vinos";

     $Menu[5]['boton']  = "bot_ofertaMultiple";
     $Menu[5]['link']   = "carteles/ofertaMultiple";
     $Menu[5]['nombre'] = "Oferta Multiple";

     $Menu[6]['boton']  = "bot_listaPrecios";
     $Menu[6]['link']   = "carteles/listaDePrecios";
     $Menu[6]['nombre'] = "Lista de Precios";

     $Menu[7]['boton']  = "bot_listaPrecios";
     $Menu[7]['link']   = "carteles/cartelVerduras";
     $Menu[7]['nombre'] = "Carteles de Verduras";

     $Menu[7]['boton']  = "bot_ofertaEscrita";
     $Menu[7]['link']   = "carteles/ofertaEscrita";
     $Menu[7]['nombre'] = "Oferta Cualquier Cosa";

     $Menu[8]['boton']  = "bot_cuidados";
     $Menu[8]['link']   = "carteles/precios/3";
     $Menu[8]['nombre'] = "Cartel Grande";

     $Menu[9]['boton']  = "bot_ListaPreciosSelectiva";
     $Menu[9]['link']   = "carteles/listaDePreciosSelectiva";
     $Menu[9]['nombre'] = "Lista de Precios Selectiva";
     $data['Menu'] = $Menu;
     Assets::add_js('carteles/index');
     Template::set($data);
     Template::render();
   }
   function navidad(){
     Assets::add_js('carteles/navidad');
     Template::render();
   }
   function precios($tamano=1){
     if($this->input->post('dias')){
       $dias = $this->input->post('dias');
     }else{
       $dias = 0;
     };
     if($dias==0){
       $Articulos = $this->Articulos_model->PendientesImpresion();
     }else{
       $Articulos = $this->Articulos_model->ModificadosHace($dias);
     };
     $data['tamano'] = $tamano;
     $data['dias']   = $dias;
     switch($tamano){
       case 1:
         $data['accion']='carteles/topdf/cartelesPrecios';
         break;
       case 2:
         $data['accion']='carteles/topdf/cartelesVinos';
         break;
       case 3:
         $data['accion']='carteles/topdf/cartelesGrandes';
         break;
     };
     $data['articulos'] = $Articulos;
     $data['total']=count($Articulos);
     Template::set($data);
     Template::render();
   }
   function ofertas($tamano=1){
     $fechoy= new DateTime();
     $fechoy->modify("+1 week");
     $data['tamano'] = $tamano;
     $data['accion'] = ($tamano==1)? 'carteles/topdf/oferta/1' : 'carteles/topdf/oferta/3';
     $data['fecha']  = $fechoy->format('d/m/Y');
     Assets::add_js('carteles/ofertas');
     Template::set($data);
     Template::render();
   }
   function ofertaMultiple($tamano=1){
     $fechoy= new DateTime();
     $fechoy->modify("+1 week");
     $data['tamano'] = $tamano;
     $data['accion'] = ($tamano==1)? 'carteles/topdf/ofertaMultiple/1' : 'carteles/topdf/ofertaMultiple/3';
     $data['fecha']  = $fechoy->format('d/m/Y');
     $data['precio'] = true;
     Assets::add_js('carteles/ofertas');
     Template::set($data);
     Template::set_view('carteles/ofertas');
     Template::render();
   }
   function buscoDetalles(){
     $this->output->enable_profiler(false);
     $Articulos = $this->Articulos_model->getDetalle($this->input->post('codigobarra'));
     if($Articulos){
       $retornoAjax  = "<tr>";
       $retornoAjax .= "<td id='codart_". $Articulos->id ."'>". $Articulos->id ."</td>";
       $retornoAjax .= "<td width='50%'>".$Articulos->nombre."</td>";
       $retornoAjax .= "<td>".$Articulos->precio."</td>";
       $retornoAjax .= "<td>".$Articulos->codigobarra."</td>";
       $retornoAjax .= "<td><input type='hidden' name='". $Articulos->id ."' value='". $Articulos->id ."' /></td>";
       $retornoAjax .= "</tr>";
       echo $retornoAjax;
     };
   }
   function listaDePrecios(){
     $this->load->model('Rubros_model', '', true);
     $data['rubrosSel'] = $this->Rubros_model->ListaSelect();
     $data['rubro'] = 0;
     $data['accion'] = 'carteles/topdf/listaDePrecios';
     Template::set($data);
     Template::set_view('carteles/listaprecios');
     Template::render();
   }
   function listaDePreciosSelectiva(){
     $fechoy= new DateTime();
     $fechoy->modify("+2 week");
     $data['fecha']  = $fechoy->format('d/m/Y');
     $data['accion'] = 'carteles/topdf/listaDePreciosSelectiva';
     Template::set($data);
     Template::render();
   }
   function listaDePreciosDo(){
     $this->output->enable_profiler(false);
     $articulos = $this->Articulos_model->getArticulosFromRubro($this->input->post('rubro'));
     $retornoAjax = '';
     $artis = 0;
     foreach($articulos as $arti){
       $artis ++;
       $Articulo = $this->Articulos_model->getDetalle($arti->CB);
       if($Articulo){
         $retornoAjax .= "<tr>";
         $retornoAjax .= "<td id='codart_". $Articulo->id ."'>". $Articulo->id ."</td>";
         $retornoAjax .= "<td width='50%'>".$Articulo->nombre."</td>";
         $retornoAjax .= "<td>".$Articulo->precio."</td>";
         $retornoAjax .= "<td>".$Articulo->codigobarra."</td>";
         $retornoAjax .= "<td><input type='checkbox' name='". $Articulo->id ."' value='". $Articulo->id ."' /></td>";
         $retornoAjax .= "</tr>";
       };
     }
     $retornoAjax .= "<tr><td colsapn='5'>Total de Articulos ".$artis."</td></tr>";
     echo $retornoAjax;

   }
   function cartelVerduras(){
     $this->load->model('Rubros_model', '', true);
     $data['rubrosSel'] = $this->Rubros_model->ListaSelect();
     $data['rubro'] = 11;
     $data['accion'] = 'carteles/topdf/cartelVerduras';
     Template::set($data);
     Template::set_view('carteles/listaprecios');
     Template::render();
   }
   function ofertaEscrita(){
     $fechoy= new DateTime();
     $fechoy->modify("+1 week");
     $data['accion'] = 'carteles/topdf/ofertaEscrita/';
     $data['fecha']  = $fechoy->format('d/m/Y');
     Template::set($data);
     Template::render();
   }
}
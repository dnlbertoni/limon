<?php
/*
 * Modelo de tabla articulos para la impresion de carteles varios
 *
 */

class Articulos_model extends MY_Model{
  var $tabla = "tbl_articulos";
  function __construct(){
    parent::__construct();
    $this->setTable("tbl_articulos");
  }
  function getDatosBasicos($id){
  $id = intval($id);
  $this->db->_reset_select();
  $this->db->select("id_articulo AS id, descripcion_articulo AS descripcion, preciovta_articulo AS precio");
  $this->db->select('codigobarra_articulo as codigobarra');
  $this->db->from($this->tabla);
  $this->db->where('id_articulo', $id);
  return $this->db->get()->row();
  }
  function getDetalle($codigoBarra){
  $this->db->select('id_articulo as id');
  $this->db->select('descripcion_articulo as nombre');
  $this->db->select('preciovta_articulo as precio');
  $this->db->select('codigobarra_articulo as codigobarra');
  $this->db->from($this->tabla);
  $this->db->where('codigobarra_articulo', $codigoBarra);
  $this->db->order_by('descripcion_articulo');
  return $this->db->get()->row();
  }
  function PendientesImpresionFecha(){
    $this->db->select_max('fechaprint', 'fecha');
    $q=$this->db->get($this->tabla);
    $aux = $q->row();
    $fecha = $aux->fecha;
    $this->db->_reset_select();
    $this->db->select('id_articulo AS id,
                      codigobarra_articulo AS Codigobarra,
                      descripcion_articulo AS Descripcion,
                      preciovta_articulo AS Precio,
                      descripcion_subrubro AS Subrubro', false);
    $this->db->from($this->tabla);
    $this->db->join('tbl_subrubros', 'tbl_articulos.id_subrubro = tbl_subrubros.id_subrubro', 'inner');
    $condicion = "date_format(fechamodif_articulo, '%Y-%m-%d') >";
    $this->db->where($condicion, $fecha);
    $this->db->order_by('Subrubro');
    //echo $this->db->_compile_select();
    return $this->db->get()->result();
  }
  function PendientesImpresion($limite=false){
    $this->db->distinct();
    $this->db->select('tbl_preciosmovim.id_articulo      AS id');
    $this->db->select('codigobarra_articulo AS Codigobarra');
    $this->db->select('descripcion_articulo AS Descripcion');
    $this->db->select('preciovta_articulo   AS Precio');
    $this->db->select('descripcion_subrubro AS Subrubro');
    $this->db->select('detalle_submarca     AS Submarca');
    $this->db->from('tbl_preciosmovim');
    $this->db->join('tbl_articulos', 'tbl_preciosmovim.id_articulo = tbl_articulos.id_articulo', 'inner');
    $this->db->join('tbl_subrubros', 'tbl_articulos.id_subrubro = tbl_subrubros.id_subrubro', 'inner');
    $this->db->join('stk_submarcas', 'tbl_articulos.id_marca = stk_submarcas.id_submarca', 'inner');
    $this->db->where('impreso',0);
    if($limite){
      $this->db->limit($limite);
    }
    $this->db->order_by('Subrubro');
    $this->db->order_by('Submarca');
    return $this->db->get()->result();
  }
  function ModificadosHace($dia){
  /*
  $this->db->select_max('fechaprint', 'fecha');
  $q=$this->db->get($this->tabla);
  $aux = $q->row();
  *
  */
  //date_default_timezone_set('Europe/Madrid');
  $auxFecha=new DateTime();
  $auxFecha->modify(sprintf("-%s day", $dia));
  $fecha = $auxFecha->format('Y-m-d');
  $this->db->_reset_select();
  $this->db->select('id_articulo AS id,
                codigobarra_articulo AS Codigobarra,
                descripcion_articulo AS Descripcion,
                preciovta_articulo AS Precio,
                descripcion_subrubro AS Subrubro', false);
  $this->db->from($this->tabla);
  $this->db->join('tbl_subrubros', 'tbl_articulos.id_subrubro = tbl_subrubros.id_subrubro', 'inner');
  $condicion = "date_format(fechamodif_articulo, '%Y-%m-%d') >";
  $this->db->where($condicion, $fecha);
  $this->db->order_by('Subrubro');
  //echo $this->db->_compile_select();
  return $this->db->get()->result();
  }
  function GraboImpresionPrecios($codigos){
    $this->db->_reset_select();
    $this->db->trans_begin();
    $fechoy = getdate();
    $fecha = $fechoy['year'] . '-' . $fechoy['mon'] . '-' .$fechoy['mday'];
    foreach($codigos as $valor){
      $this->db->where('id_articulo', $valor);
      $this->db->update($this->tabla, array('fechaprint' => $fecha));
      $this->db->where('id_articulo', $valor);
      $this->db->update('tbl_preciosmovim', array('impreso' => 1));
    }
    if($this->db->trans_status() === FALSE){
      $this->db->trans_rollback();
      return false;
    }else{
      $this->db->trans_commit();
      return true;
    }
    //return $this->db->trans_complete();
  }
  function getArticulosFromRubro($id_rubro){
    $this->db->select('codigobarra_articulo as CB');
    $this->db->select('descripcion_articulo as nombre');
    $this->db->from($this->getTable());
    $this->db->join('tbl_subrubros', 'tbl_subrubros.id_subrubro=tbl_articulos.id_subrubro', 'inner');
    $this->db->where('id_rubro', $id_rubro);
    $this->db->order_by('nombre');
    return $this->db->get()->result();
  }
  function getNombreRubro($id){
    $this->db->select('descripcion_rubro as nombre');
    $this->db->from($this->getTable());
    $this->db->join('tbl_subrubros', 'tbl_articulos.id_subrubro=tbl_subrubros.id_subrubro', 'inner');
    $this->db->join('tbl_rubros', 'tbl_rubros.id_rubro=tbl_subrubros.id_rubro', 'inner');
    $this->db->where('id_articulo', $id);
    $q = $this->db->get()->row();
    return $q->nombre;
  }
  function getListaFull(){
    $this->db->_reset_select();
    $this->db->select("id_articulo AS id");    
    $this->db->select("descripcion_articulo AS descripcion");
    $this->db->select("preciovta_articulo AS precio");
    $this->db->select("tbl_rubros.descripcion_rubro AS rubro");
    $this->db->select("tbl_subrubros.descripcion_subrubro AS subrubro");
    $this->db->select('codigobarra_articulo as codigobarra');
    $this->db->from($this->tabla);
    $this->db->join('tbl_subrubros', 'tbl_subrubros.id_subrubro=tbl_articulos.id_subrubro', 'left');
    $this->db->join('tbl_rubros', 'tbl_rubros.id_rubro=tbl_subrubros.id_rubro', 'left');
    $this->db->order_by('rubro');
    $this->db->order_by('subrubro');
    return $this->db->get()->result();
  }  
 }

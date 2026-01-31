<?php
class Tmpmovim_model extends MY_Model{
  var $tabla = "tmp_movimientos";
  var $tablaArticulos = "tbl_articulos";
  function  __construct() {
    parent::__construct();
    $this->setTable('tmp_movimientos');
  }
  function agregoAlComprobante($id, $codigobarra, $cantidad, $precio, $puesto, $cuenta){
    $this->db->select('id_tmpmov');
    $this->db->from($this->getTable());
    $this->db->where('idencab_tmpmov', $id);
    $this->db->where('codigobarra_tmpmov', $codigobarra);
    $q = $this->db->get();
    $articulos = $q->result();
    if($q->num_rows()== 0 || trim($codigobarra)=='1' || trim($codigobarra)=='2' ){
      return $this->insertArticulo($id, $codigobarra, $cantidad, $precio, $puesto, $cuenta);
    }else{
      return $this->updateArticulo($id, $codigobarra, $cantidad, $puesto, $cuenta);
    };
  }
  function insertArticulo($id, $codigobarra, $cantidad, $precio, $puesto, $cuenta){
    $this->db->select('descripcion_articulo as nombre');
    $this->db->select('tasaiva_articulo as tasaiva');
    $this->db->from($this->tablaArticulos);
    $this->db->where('codigobarra_articulo', $codigobarra);
    $arti = $this->db->get()->row();
    //$this->db->_reset_select();
    $this->db->set('idencab_tmpmov',$id);
    $this->db->set('cantidad_tmpmov', $cantidad);
    $this->db->set('codigobarra_tmpmov', $codigobarra);
    $this->db->set('preciovta_tmpmov',$precio);
    $this->db->set('descripcion_tmpmov', $arti->nombre);
    $this->db->set('tasaiva_tmpmov', $arti->tasaiva);
    $this->db->set('puesto_tmpmov', $puesto);
    $this->db->set('cuentaid_tmpmov', $cuenta);
    $this->db->insert($this->tabla);
    return $this->db->insert_id(); 
  }
  function updateArticulo($id, $codigobarra, $cantidad, $puesto){
    $this->db->set('cantidad_tmpmov', 'cantidad_tmpmov + ' . $cantidad, false);
    $this->db->where('idencab_tmpmov', $id);
    $this->db->where('codigobarra_tmpmov', $codigobarra);
    $this->db->where('puesto_tmpmov', $puesto);
    $this->db->update($this->tabla);
    return true;
  }
  function delArticulo($codmov){
    $this->db->from($this->tabla);
    $this->db->where('id_tmpmov', $codmov);
    $idencab = $this->db->get()->row()->idencab_tmpmov;
    //$this->db->_reset_select();
    $this->db->where('id_tmpmov', $codmov);
    $this->db->delete($this->tabla);
    return $idencab;
  }
  function getTotales($id,$puesto){
    $this->db->select('SUM(cantidad_tmpmov*preciovta_tmpmov) AS Total',false);
    $this->db->select('COUNT(codigobarra_tmpmov) AS Bultos', false);
    $this->db->from($this->tabla);
    $this->db->where('idencab_tmpmov', $id);
    $this->db->where('puesto_tmpmov', $puesto);
    return $this->db->get()->row();
  }
  function getCuenta($id,$puesto){
    $this->db->select('cuentaid_tmpmov as cuenta');
    $this->db->from($this->tabla);
    $this->db->where('idencab_tmpmov', $id);
    $this->db->where('puesto_tmpmov', $puesto);
    $q = $this->db->get();
    if($q->num_rows()>0){
      return $q->row()->cuenta;
    }else{
      return false;
    }
  }
  function getArticulos($id,$puesto){
    $this->db->select('codigobarra_tmpmov AS Codigobarra');
    $this->db->select('descripcion_tmpmov AS Nombre');
    $this->db->select('cantidad_tmpmov AS Cantidad');
    $this->db->select('preciovta_tmpmov AS Precio');
    $this->db->select('tasaiva_tmpmov AS Tasa');
    $this->db->select('(cantidad_tmpmov * preciovta_tmpmov ) AS Importe', false);
    $this->db->select('id_tmpmov As codmov');
    $this->db->select('cuentaid_tmpmov as cuenta');
    $this->db->from($this->tabla);
    $this->db->where('idencab_tmpmov', $id);
    $this->db->where('puesto_tmpmov', $puesto);
    $this->db->order_by('id_tmpmov', 'DESC');
    $q = $this->db->get();
    if($q->num_rows()>0){
      return $q->result();
    }else{
      return false;
    }
  }
  function vacio($id, $puesto){
    $this->db->where('idencab_tmpmov', $id);
    $this->db->where('puesto_tmpmov', $puesto);
    $this->db->delete($this->tabla);
    return true;
  }
  function cambioCuenta($puesto, $id_tmpencab, $cuenta, $ctacte){
    $this->db->set('cuentaid_tmpmov', $cuenta);
    $this->db->set('ctacte_tmpmov', $ctacte);
    $this->db->where('idencab_tmpmov', $id_tmpencab);
    $this->db->where('puesto_tmpmov', $puesto);
    $this->db->update($this->tabla);
    return true;
  }
  function itemsComprobante($puesto, $idencab, $negativo=false){
    $this->db->select('descripcion_articulo as detalle');
    $this->db->select('cantidad_tmpmov      as cantidad');
    if($negativo)
      $this->db->select('(preciovta_tmpmov * -1)     as precio', false);
    else
      $this->db->select('preciovta_tmpmov     as precio');
    $this->db->select('tasaiva_articulo     as iva');
    $this->db->select('id_articulo          as id_articulo');
    $this->db->select('codigobarra_articulo as codigobarra');
    $this->db->from($this->tabla);
    $this->db->join($this->tablaArticulos, "codigobarra_articulo = codigobarra_tmpmov", "inner");
    $this->db->where("puesto_tmpmov", $puesto);
    $this->db->where("idencab_tmpmov", $idencab);
    $this->db->where("codigobarra_articulo = codigobarra_tmpmov", "",false );
    return $this->db->get()->result();
  }
  function totalComprobante($puesto, $idencab){
    $this->db->select("SUM(cantidad_tmpmov * preciovta_tmpmov) as Total",false);
    $this->db->from($this->tabla);
    $this->db->where("puesto_tmpmov", $puesto);
    $this->db->where("idencab_tmpmov", $idencab);
    return $this->db->get()->row()->Total;
  }
  function getDatosUltimo($puesto){
    $this->db->select_max('idencab_tmpmov');
    $this->db->from($this->tabla);
    $this->db->where('puesto_tmpmov', $puesto);
    $q = $this->db->get();
    if($q->num_rows()>0){
      $idencab = $q->row()->idencab_tmpmov;
      //$this->db->_reset_select();
      $this->db->from($this->tabla);
      $this->db->where('puesto_tmpmov', $puesto);
      $this->db->where('idencab_tmpmov', $idencab);
      $this->db->limit(1);
      return $this->db->get()->row();
    }else{
      return false;
    };
  }
}

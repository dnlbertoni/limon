<?php
class Cajeros_model extends MY_Model{
  var $tabla = "caja_cajeros";
  var $tablaPuestos = "cfg_puestos";
  function  __construct() {
    parent::__construct();
    $this->setTable($this->tabla);
  }
  function getNombre($id){
    if($id!=0){
      $this->db->from($this->tabla);
      $this->db->where('id', $id);
      return $this->db->get()->row()->nombre;
    }else{
      return false;
    }
  }
  function getPuestoById($id){
    if($id!=0){
      $this->db->from($this->tablaPuestos);
      $this->db->where('id', $id);
      return $this->db->get()->row();
    }else{
      return false;
    }
  }
  function getPuestoByIp($ip){
    if(!empty($ip)){
      $ip = trim($ip);
      $this->db->from($this->tablaPuestos);
      $this->db->where('ip', $ip);
      return $this->db->get()->row();
    }else{
      return false;
    }
  }

}
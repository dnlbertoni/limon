<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Articulos_model extends My_Model {


	public function __construct()
	{
		parent::__construct();
		$this->setTable('tbl_articulos');
	}

	public function getArticulos(){
		$this->db->select('id_articulo                    as id', FALSE);
		$this->db->select('codigobarra_articulo           as codigobarra');
		$this->db->select('descripcion_articulo           as detalle', FALSE);
		$this->db->select('tbl_articulos.id_subrubro      as idsubrubro', FALSE);
		$this->db->select('tbl_articulos.id_marca         as idsubmarca', FALSE);
		$this->db->select('descripcion_subrubro           as subrubroNombre', FALSE);
		$this->db->select('tbl_rubros.id_rubro            as idrubro', FALSE);
		$this->db->select('tbl_rubros.descripcion_rubro   as rubroNombre', FALSE);
		$this->db->select('stk_marcas.id_marca            as idmarca', FALSE);
		$this->db->select('stk_marcas.detalle_marca       as marcaNombre', FALSE);
		$this->db->select('stk_submarcas.id_submarca      as id_submarca', FALSE);
		$this->db->select('stk_submarcas.detalle_submarca as submarcaNombre', FALSE);
		$this->db->from($this->getTable());
		$this->db->join('stk_submarcas', 'stk_submarcas.id_submarca = tbl_articulos.id_marca', 'left');
		$this->db->join('stk_marcas', 'stk_marcas.id_marca = stk_submarcas.id_marca', 'left');
		$this->db->join('tbl_subrubros', 'tbl_subrubros.id_subrubro = tbl_articulos.id_subrubro', 'left');
		$this->db->join('tbl_rubros', 'tbl_rubros.id_rubro = tbl_subrubros.id_rubro', 'left');
		$this->db->order_by('idrubro', 'asc');
		$this->db->order_by('idsubrubro', 'asc');
		return $this->db->get()->result();
	}
    public function getArticulo($CB){
        $this->db->select('id_articulo                    as id', FALSE);
        $this->db->select('codigobarra_articulo           as codigobarra');
        $this->db->select('descripcion_articulo           as detalle', FALSE);
        $this->db->select('tbl_articulos.id_subrubro      as idsubrubro', FALSE);
        $this->db->select('tbl_articulos.id_marca         as idsubmarca', FALSE);
        $this->db->select('descripcion_subrubro           as subrubroNombre', FALSE);
        $this->db->select('tbl_rubros.id_rubro            as idrubro', FALSE);
        $this->db->select('tbl_rubros.descripcion_rubro   as rubroNombre', FALSE);
        $this->db->select('stk_marcas.id_marca            as idmarca', FALSE);
        $this->db->select('stk_marcas.detalle_marca       as marcaNombre', FALSE);
        $this->db->select('stk_submarcas.id_submarca      as id_submarca', FALSE);
        $this->db->select('stk_submarcas.detalle_submarca as submarcaNombre', FALSE);
        $this->db->from($this->getTable());
        $this->db->join('stk_submarcas', 'stk_submarcas.id_submarca = tbl_articulos.id_marca', 'left');
        $this->db->join('stk_marcas', 'stk_marcas.id_marca = stk_submarcas.id_marca', 'left');
        $this->db->join('tbl_subrubros', 'tbl_subrubros.id_subrubro = tbl_articulos.id_subrubro', 'left');
        $this->db->join('tbl_rubros', 'tbl_rubros.id_rubro = tbl_subrubros.id_rubro', 'left');
        $this->db->where('codigobarra_articulo', $CB);
        return $this->db->get()->row();
    }
    function getDatosInventario($CB){
        $this->db->select('id_articulo');
        $this->db->select('descripcion_articulo as nombre');
        $this->db->select('codigobarra_articulo as codigobarra');
        $this->db->select('if(cantxbulto_articulo is null, 0, cantxbulto_articulo) as cantidadBulto', false);
        $this->db->select('if( cantxbulto_subrubro is null, 0, cantxbulto_subrubro) as cantidadBultoSub', false);
        $this->db->from($this->getTable());
        $this->db->join('tbl_subrubros', 'tbl_articulos.id_subrubro = tbl_subrubros.id_subrubro', 'left');
        $this->db->where('codigobarra_articulo', $CB);
        return $this->db->get()->row();
    }
    function actualizoCantidadBultos($id,$cantidadXbultos ){
        $this->db->set('CANTXBULTO_ARTICULO', $cantidadXbultos);
        $this->db->where('ID_ARTICULO', $id);
        $this->db->update($this->getTable());
        return true;
    }
}

/* End of file articulos_model.php */
/* Location: ./application/models/articulos_model.php */

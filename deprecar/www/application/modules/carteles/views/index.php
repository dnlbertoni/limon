<?php
/*
 * Vista del index para el controlador de carteles
 */

?>
<?php foreach($Menu as $menu):?>
 <div id="<?php echo $menu['boton'];?>"><?php echo anchor($menu['link'],$menu['nombre'])?></div>
<?php endforeach;?>
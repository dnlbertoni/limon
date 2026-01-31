<?php
/*
 * Vista para la funcionlista de precios que buscar los articulos por rubro, 
 * los lista y despues los envia para que se impriman en php
 */
 
 ?>
 
 <h1>LIsta de Precios</h1>
 
 <?php echo form_open('carteles/listaDePreciosDo','id="codart"')?>
<input type="hidden" name="pagina" value="<?php echo base_url(), 'index.php/carteles/listaDePreciosDo' ?>" id="pagina" />
 <?php echo form_label('Rubro:','rubro');?>
 <?php echo form_dropdown('rubro', $rubrosSel, $rubro,"id='rubro'");?>
 <?php echo form_submit('Agregar', 'Agregar');?>
<?php echo form_close()?>
<div id="bot_checkAll">Seleccionar Todo</div>
<?php echo form_open($accion,'id="Print"');?>
<table id="articulos" width="85%">
</table>
<table>
 <?php echo form_label('Tamano Letra:','tamano')?>
 <?php echo form_dropdown('tamano', array(6=> 6, 11 => 11, 22 => 22 ),22,'id="tamano"')?>
 <tr><td colspan="5"><?php echo form_submit('Imprimir','Imprimir')?></td>
<td colspan="5"><?php echo form_submit('Imprimir','Descargar')?></td></tr>
</table>
<?php echo form_close()?>
<script>
$(document).ready(function(){
  $("#bot_checkAll").button();
  $("#bot_checkAll").click(function(){
    $("input:checkbox").click();
  });
  $("#rubro").focus();
  $("#codart").submit(function(evnt){
    evnt.preventDefault();
    valor  = $("#rubro").val();
    pagina = $("#pagina").val();
    $.ajax({
          url: pagina,
          contentType: "application/x-www-form-urlencoded",
          global: false,
          type: "POST",
          data: ({rubro : valor }),
          dataType: "html",
          async:true,
          success: function(msg){
             $("#articulos").append(msg);
             $("#rubro").val('');
             $("#rubro").focus();
          }
       }
    ).responseText;  
  });
});
</script>
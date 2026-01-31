<?php
/*
 * Vista para la funcion Navidad que buscar los articulos, los lista y despues los envia para que se impriman en php
 */

?>

<h1>Lista de Precios Selectiva</h1>

<?php echo form_open('carteles/buscoDetalles','id="codart"')?>
<input type="hidden" name="pagina" value="<?php echo base_url(), 'index.php/carteles/buscoDetalles' ?>" id="pagina" />
<?php echo form_label('Articulo:','codigobarra');?>
<?php echo form_input('codigobarra','','id="codigobarra"');?>
<?php echo form_submit('Agregar', 'Agregar');?>
<?php echo form_close()?>

<?php echo form_open($accion,'id="Print"');?>
<?php echo form_label('Fecha','fecha');?>
<?php echo form_input('fecha',$fecha,'id="fecha"');?>
<?php if(isset($precio)):?>
    <?php echo form_label('Precio','precio');?>
    <?php echo form_input('precio','','id="precio"');?>
<?php endif;?>
<table id="articulos" width="85%">
</table>
<table>
    <tr>
        <td colspan="5"><?php echo form_submit('Imprimir','Imprimir')?></td>
        <td colspan="5"><?php echo form_submit('Imprimir','Descargar')?></td>
    </tr>
</table>
<?php echo form_close()?>
<script>
    $(document).ready(function(){
        $("#codigobarra").focus();
        $("#fecha").datepicker({
            altField        : "#fecha",
            altFormat       : 'dd/mm/yy',
            appendText      : "dd/mm/aaaa",
            showButtonPanel : true,
            showOn          : "both"
        });
        $("#codart").submit(function(evnt){
            evnt.preventDefault();
            valor  = $("#codigobarra").val();
            pagina = $("#pagina").val()
            $.ajax({
                    url: pagina,
                    contentType: "application/x-www-form-urlencoded",
                    global: false,
                    type: "POST",
                    data: ({codigobarra : valor }),
                    dataType: "html",
                    async:true,
                    success: function(msg){
                        $("#articulos").append(msg);
                        $("#codigobarra").val('');
                        $("#codigobarra").focus();
                    }
                }
            ).responseText;
        });
    });
</script>

<?php echo form_open('', 'id="consultaCuenta"')?>
<?php echo form_input('cuentaTXT','','id="cuentaTXT"')?>
<input type="hidden" id="paginaCuenta" value="<?php echo base_url(),'index.php/cuenta/searchCuentaXDo'?>" />
<input type="hidden" id="filtro" value="<?php echo $filtro?>" />
<?php echo form_submit('Consultar', 'Consultar');?>
<?php echo form_close()?>
<br />
<div id="datosCliente"></div>
<script>
$(document).ready(function(){
  $("input").removeClass('focus');
  $("#cuentaTXT").addClass('focus');
  $("#consultaCuenta > #cuentaTXT").focus();
  $("#cuentaTXT").bind('keyup',function(e){
    var code = e.keyCode;
    if( ( code<90 && code>57 )  || code===13 || code===8 ){
      envioForm();
    }
  });
  $("#consultaCuenta").submit(function(e){
    e.preventDefault();
    envioForm();
  });
});
function envioForm(){
  cuenta  = $("#cuentaTXT").val().trim();
  filtro = $("#filtro").val();
  pagina       = $("#paginaCuenta").val()
  if(cuenta.length > 0){
    $.ajax({
            url: pagina,
            contentType: "application/x-www-form-urlencoded",
            global: false,
            type: "POST",
            data: ({cuentaTXT : cuenta,
                    filtro    : filtro
                  }),
            dataType: "html",
            async:true,
            success: function(msg){
               $("#datosCliente").html(msg);
            }
    }).responseText;
  }
}
</script>
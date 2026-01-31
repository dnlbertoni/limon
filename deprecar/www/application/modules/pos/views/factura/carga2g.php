<h2>Imprimiendo <?php echo $Imprimo?></h2>
<h3>Por Favor espere...</h3>
<div id="carga"><?php echo Assets::image('loading.gif',array('alt'=>"Loading..."));?></div>
<?php print_r($rs)?>
<script language="Javascript">
$(document).ready(function(){
  pagina = $("#paginaAjax").val();
  puesto      = $("#puestoAjax").val();
  id_temporal = $("#idencabAjax").val();
  file        = $("#fileAjax").val();
  cuentaAjax  = $('#cuentaAjax').val();
  importe     = $('#importeAjax').val();
  dnf         = $('#DNF').val();
  tipcom_id   = $('#tipcomAjax').val();
    $.ajax({
            url: pagina,
            contentType: "application/x-www-form-urlencoded",
            global: false,
            type: "POST",
            data: ({file    : file,
                    puesto  : puesto,
                    cuentaAjax  : cuenta,
                    tipcom  : tipcom_id,
                    importe : importe,
                    DNF     : dnf,
                    idencab : id_temporal
                  }),
            dataType: "html",
            async:true,
            beforeSend: function(){$("#carga").fadeIn();},
            success: function(msg){
               $("#carga").append(msg);
               $("#imprimo").dialog('close');
               $("#loading").fadeOut(100);
            }
    }).responseText;
});
</script>

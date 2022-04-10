<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Sistema de Gestion de <?php echo $empresa ?></title>

    <!-- Bootstrap core CSS -->
    <link href="/themes/citrus/screen.css" rel="stylesheet">
    <link href="/themes/citrus/css/bootstrapCitrus.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="/themes/citrus/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <?php echo Assets::css('modern-business') ?> 
    <?php //echo Assets::css('bootstrap') ?>       

    <!-- JavaScript -->
    <script src="/themes/citrus/js/jquery2.js"></script>
    <script src="/themes/citrus/js/bootstrapCitrus.js"></script>
    <script src="/themes/citrus/js/citrus.js"></script>
    
    <?php echo Assets::js() ?>
    <?php //echo Assets::css() ?> 
  </head>

  <body>
    <!-- menu navegacion -->
    <?php echo Template::block('menu', 'barra')?>


    <div class="container">
        <?php echo Template::yield1();  ?>
      <hr>

      <footer>
        <div class="row">
          <div class="col-lg-12">
            <p>Copyright &copy; <?php echo $empresa ?> </p>
          </div>
        </div>
      </footer>

    </div><!-- /.container -->
    <script>
        $(document).ready(function(){
            $("#BACK").click(function (){
                history.back();
            })
        });
    </script>
  </body>
</html>

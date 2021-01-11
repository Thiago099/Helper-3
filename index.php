<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <?php
     include 'bin/sql.php';
     include 'bin/misc.php';
     $config = json_decode(file_dump('config.json'));
     $program         = $config->program;
     sql::$server     = $config->server;
     sql::$user       = $config->user;
     sql::$password   = $config->password;
     ?>
    <link rel="stylesheet" href="style/main.css">
  </head>
  <body>
<div class="container">

    <form class="" action="" method="get">
      <?php
      include 'components/select_databases.php';
      include 'components/select_tables.php';
      include 'components/menu.php';
      ?>
    </form>
    <?php
    $path=[
      'Gerar modelo e controlador'    => 'functionalities/gerar_modelo_e_controlador.php',
      'Adicionar campos de controle'  => 'functionalities/adicionar_campos_de_controle.php',
      'Código insert'                 => 'functionalities/codigo_insert.php',
      'Código controlador'            => 'functionalities/codigo_controlador.php',
      'Código select'                 => 'functionalities/codigo_select.php',
      'Código JSON'                   => 'functionalities/codigo_json.php',
      'Caminhos do controlador'       => 'functionalities/caminhos_do_controlador.php',
    ];
    if(isset($_GET['action']))
    {
      include $path[$_GET['action']];
    }
    ?>
  </body>
  </div>
</html>

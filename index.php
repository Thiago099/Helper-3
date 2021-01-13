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
      'Gerar modelo e controlador'    => 'gerar_modelo_e_controlador.php',
      'Adicionar campos de controle'  => 'adicionar_campos_de_controle.php',
      'Código insert'                 => 'codigo_insert.php',
      'Código controlador'            => 'codigo_controlador.php',
      'Código select'                 => 'codigo_select.php',
      'Código JSON'                   => 'codigo_json.php',
      'Caminhos do controlador'       => 'caminhos_do_controlador.php',
      'Codigo doctrine'               => 'codigo_doctrine.php',
    ];
    if(isset($_GET['action']))
    {
      include 'functionalities/'.$path[$_GET['action']];
    }
    // include 'functionalities/codigo_json.php';
    ?>
  </body>
  </div>
</html>

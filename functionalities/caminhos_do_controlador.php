<?php if(!is_dir("$program/application")):?><div class="error">Falha: Para encontrar os caminhos, o programa deve ser apontado no "config.json".</div>
<?php else:?>
<label>Caminhos do controlador</label>
<textarea name="name" rows="40" cols="80" spellcheck="false"><?php
{
  $path="$program/application/hooks/Verifica_token.php";
  $myfile = fopen($path, "r") or die("Unable to open file!");
  $target = str_split(fread($myfile,filesize($path)));
  fclose($myfile);


  $source=str_split('$mapUrl = array(');
  $end=str_split('),');
  $arrow=str_split('=>');
  // $db=new sql($_GET['database']);
  $source_lenght=count($source);
  $target_lenght=count($target);
  $i=0;

  for (; $i < $target_lenght; $i++)
  {
    if(match($i,$target,$source))
    {
      break;
    }
  }
  for (; $i < $target_lenght; $i++)
  {
    if($target[$i]=='\'')
    {
      $i++;
      $start=$i;
      while ($target[$i]!='\'') {
        $i++;
      }
      $cur=implode(array_slice($target,$start,$i-$start));
      if(strtolower($cur)==$_GET['table'])
      {
        echo ident('Caminho',80).'Privilego'."\n\n";
        $i+=2;
        for (; $i < $target_lenght; $i++)
        {
          if($target[$i]=='\'')
          {
            $i++;
            $start=$i;
            while ($target[$i]!='\'')
            {
              $i++;
            }
            echo ident("public/$cur/".implode(array_slice($target,$start,$i-$start)),80);
            $i++;
            for (; $i < $target_lenght; $i++)
            {
              match($i,$target,$arrow);
              break;
            }
            $i+=3;
            $start=$i;
            while ($target[$i]!="\n")
            {
              $i++;
            }
            $i-=2;
            $priv=str_replace(' ', '',implode(array_slice($target,$start,$i-$start)));
            if($priv!='0')
            {
              // echo ident($db->query("SELECT titulo FROM privilegio WHERE id = $priv")[0]['titulo'],40);
              echo $priv;

            }
            echo "\n";
          }
          else
          if(match($i,$target,$end))
          {
            break;
          }
        }
        break;
      }
      else
      for (; $i < $target_lenght; $i++)
      {
        if(match($i,$target,$end))
        break;
      }
    }
  }
}

?></textarea>
<?php
endif;
 ?>

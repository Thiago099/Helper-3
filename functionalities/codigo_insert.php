<label>Insert</label>
  <textarea name="name" rows="40" cols="200" spellcheck="false"><?php
    if(isset($_GET['database'])&&isset($_GET['table']))
    {
      $database=$_GET['database'];
      $table=$_GET['table'];

      $db=new sql($database);
      $result=$db->query("DESC $_GET[table]");
      $ret="{\n";

        $info=new sql("information_schema");
        $db=new sql($database);
        $fks=$info->query("SELECT K.COLUMN_NAME coluna,k.REFERENCED_TABLE_NAME tabela, k.REFERENCED_COLUMN_NAME chave
        FROM information_schema.TABLE_CONSTRAINTS i
        LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
        WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY'
        AND i.TABLE_SCHEMA = '$database'
        AND i.TABLE_NAME = '$table'
        GROUP BY coluna;");

      $ret="INSERT INTO `$_GET[table]`\n(\n";
      foreach ($result as $i) {
        $ii=$i['Field'];
        $ret.="   `$ii`,\n";
      }
      $ret=substr($ret, 0, -2);
      $ret.="\n)\nVALUES\n(\n";
      foreach ($result as $i)
      {
        $ii=$i['Field'];
        $ij=$i['Type'];
        $str="SQL-$ij";
             if(!(strpos($str, 'varchar')     === false)) $str='""';
        else if(!(strpos($str, 'tinyint(1)')  === false)) $str='false';
        else if(!(strpos($str, 'text')        === false)) $str='""';
        else if(!(strpos($str, 'int')         === false)) $str='0';
        else if(!(strpos($str, 'float')       === false)) $str='0.0';
        else if(!(strpos($str, 'decimal')     === false)) $str='0.0';
        else if(!(strpos($str, 'double')      === false)) $str='0.0';
        else if(!(strpos($str, 'datetime')    === false)) $str='"2020-11-24 00:00:00.000"';
        else if(!(strpos($str, 'date')        === false)) $str='"2020-11-24"';
        foreach ($fks as $j) {
          if($j['coluna']==$ii) $str='null';
        }
        $ret.="   $str,\n";
      }
      $ret=substr($ret, 0, -2);
      $ret.= "\n)\n";
      echo $ret;
    }
    ?></textarea>

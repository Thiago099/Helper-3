<label>Select</label>
<textarea name="name" rows="40" cols="200" spellcheck="false"><?php
  function loop($database,$table,&$join,&$select)
  {
    $info=new sql("information_schema");
    $db=new sql($database);
    $fks=$info->query("SELECT K.COLUMN_NAME coluna,k.REFERENCED_TABLE_NAME tabela, k.REFERENCED_COLUMN_NAME chave
    FROM information_schema.TABLE_CONSTRAINTS i
    LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
    WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY'
    AND i.TABLE_SCHEMA = '$database'
    AND i.TABLE_NAME = '$table'
    GROUP BY coluna;");
    $table=$_GET['table'];
    $fields="";
    foreach ($fks as $i) {
             if($i['coluna']=='created_by') continue;
        else if($i['coluna']=='updated_by') continue;
        $ct=$i['tabela'];
        $fields=$db->query("DESC $ct");
        $fk_name=str_replace("id_","",$i['coluna']);
        foreach ($fields as $j) {
          $jj=$j['Field'];
          if($i['chave']==$jj)continue;
          $select.=ident("`$fk_name`.`$jj`",70)." AS `{$jj}_$fk_name`,\n";
        }

        $join.="LEFT JOIN `$ct`";
        if($ct != $fk_name)$join.=" AS `$fk_name`";
        $join.=" ON `$table`.`$i[coluna]` = `$fk_name`.`$i[chave]`\n";
    }

    // foreach ($fks as $i) {
    //     loop($database,$i['tabela'],$join,$select);
    // }

  }
  $join='';
  $select=",\n";
  loop($_GET['database'],$_GET['table'],$join,$select);
  if($select==",\n")$select='';
  $select=substr($select, 0, -2)."\n";
  echo "SELECT\n`$_GET[table]`.*{$select}FROM `$_GET[table]`\n$join";
?></textarea>

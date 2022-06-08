<label>Select</label>
<textarea name="name" rows="40" cols="200" spellcheck="false"><?php
  $info = new sql("information_schema");
  $siga =  new sql($_GET['database']);
  $bad_fks = $info->query("SELECT
      TABLE_NAME,
      COLUMN_NAME
    FROM 
      COLUMNS
    LEFT JOIN
      (
        SELECT 
          k.TABLE_NAME tabela,
          K.COLUMN_NAME coluna
        FROM 
          information_schema.TABLE_CONSTRAINTS i
        LEFT JOIN 
          information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
        WHERE 
          i.CONSTRAINT_TYPE = 'FOREIGN KEY'
        AND 
          i.TABLE_SCHEMA = '$_GET[database]'
      ) fks ON TABLE_NAME = tabela AND COLUMN_NAME = coluna
    WHERE 
      TABLE_SCHEMA = '$_GET[database]'
    AND 
      COLUMN_NAME LIKE 'id_%'
    AND
      fks.tabela IS NULL ");

  foreach ($bad_fks as $i) 
  {
    $reference = str_replace('id_','',$i['COLUMN_NAME']);
    try
    {
      $violation = $siga->query("SELECT DISTINCT $i[COLUMN_NAME] AS v FROM $i[TABLE_NAME] WHERE $i[COLUMN_NAME] NOT IN (SELECT id FROM $reference)");
    }
    catch (Exception $e)
    {
      $violation = "table not found";
    }
    if(count($info->query("SELECT * FROM tables
    WHERE table_schema = '$_GET[database]'
    AND TABLE_NAME = '$reference'")) == 0) 
    {
      echo "ALTER TABLE `$i[TABLE_NAME]` ADD INDEX `$i[TABLE_NAME]_$i[COLUMN_NAME]` (`$i[COLUMN_NAME]`);\n";
      echo "-- Table not found\n-- ";
    }
    elseif(count($violation) != 0)
    {
      echo "ALTER TABLE `$i[TABLE_NAME]` ADD INDEX `$i[TABLE_NAME]_$i[COLUMN_NAME]` (`$i[COLUMN_NAME]`);\n";
      echo "-- Foreign key violation \"$reference\"\n";
      foreach($violation as $j)
        echo "-- $j[v]\n";
      echo '-- ';
    }
    else
    {
      echo '--';
    }
    echo "ALTER TABLE `$i[TABLE_NAME]` ADD CONSTRAINT `FK_$i[TABLE_NAME]_$i[COLUMN_NAME]` FOREIGN KEY (`$i[COLUMN_NAME]`) REFERENCES `$reference` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;\n\n";
  }
?></textarea>

<label>Select</label>
<textarea name="name" rows="40" cols="200" spellcheck="false"><?php

$database=$_GET['database'];
$table=$_GET['table'];
$db=new sql('information_schema');
$fields = $db->query("SELECT COLUMN_NAME,IS_NULLABLE,COLUMN_TYPE,EXTRA FROM COLUMNS WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='$table'");
$primary = $db->query("SELECT COLUMN_NAME FROM COLUMNS WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='$table' AND COLUMN_KEY='PRI'");
$fks = $db->query("SELECT k.CONSTRAINT_NAME `constraint`, k.CONSTRAINT_SCHEMA `schema`, COLUMN_NAME `column`,k.REFERENCED_TABLE_NAME `table`, k.REFERENCED_COLUMN_NAME `key`
    FROM information_schema.TABLE_CONSTRAINTS i
    LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
    WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY'
    AND i.TABLE_SCHEMA = '$database'
    AND i.TABLE_NAME = '$table'
    GROUP BY K.COLUMN_NAME;");
$more=$db->query("SELECT ENGINE,TABLE_COLLATION FROM TABLES  WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='$table'")[0];
$ret="
public function up()
{
    \$this->addSql('
      CREATE TABLE `$table`
      (\n";
foreach ($fields as $i)
{
  $null=$i['IS_NULLABLE']==='YES'?'NULL':'NOT NULL';
  $extra=$i['EXTRA']!=''?" $i[EXTRA]":'';
  $ret.="          `$i[COLUMN_NAME]` $i[COLUMN_TYPE] $null$extra,\n";
}

foreach ($primary as $i)
{
  $ret.="          PRIMARY KEY (`$i[COLUMN_NAME]`) USING BTREE,\n";
}
foreach ($fks as $i)
{
  $ret.="          INDEX `$i[constraint]` (`$i[column]`) USING BTREE,\n";
}
foreach ($fks as $i)
{
  $ret.="          CONSTRAINT `$i[constraint]` FOREIGN KEY (`$i[column]`) REFERENCES `$i[schema]`.`$i[table]` (`$i[key]`) ON UPDATE NO ACTION ON DELETE NO ACTION,\n";
}
$ret=substr($ret, 0, -2);
$ret.="
      )
      COLLATE=\'$more[TABLE_COLLATION]\'
      ENGINE=$more[ENGINE]
    ');
}
public function down()
{
  \$this->addSql('DROP TABLE `$_GET[table]`');
}";
echo $ret;
?></textarea>

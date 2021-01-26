<label>Select</label>
<textarea name="name" rows="40" cols="200" spellcheck="false"><?php

$database=$_GET['database'];
$table=$_GET['table'];
$db=new sql('information_schema');
$fields = $db->query("SELECT COLUMN_NAME,IS_NULLABLE,COLUMN_TYPE,EXTRA FROM COLUMNS WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='$table'");
$primary = $db->query("SELECT COLUMN_NAME FROM COLUMNS WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='$table' AND COLUMN_KEY='PRI'");
$fks = $db->query("SELECT k.CONSTRAINT_NAME `constraint`,k.CONSTRAINT_SCHEMA `schema`, COLUMN_NAME `column`,k.REFERENCED_TABLE_NAME `table`, k.REFERENCED_COLUMN_NAME `key`, r.UPDATE_RULE, r.DELETE_RULE
    FROM information_schema.TABLE_CONSTRAINTS i
    LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
    LEFT JOIN information_schema.REFERENTIAL_CONSTRAINTS r ON i.CONSTRAINT_NAME = r.CONSTRAINT_NAME
    WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY'
    AND i.TABLE_SCHEMA = '$database'
    AND i.TABLE_NAME = '$table'
    GROUP BY K.COLUMN_NAME;");
$more=$db->query("SELECT ENGINE,TABLE_COLLATION,TABLE_COMMENT FROM TABLES  WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='$table'")[0];
$comment='';
if($more['TABLE_COMMENT']!='')$comment="\n      COMMENT=\'$more[TABLE_COMMENT]\'";
$ret="
".date('YmdHis', time())."_Anexo.php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_".ucfirst($table)." extends AbstractMigration
{
  public function up()
  {
      \$this->db->query('
        CREATE TABLE `$table`
        (\n";
foreach ($fields as $i)
{
  $null=$i['IS_NULLABLE']==='YES'?'NULL':'NOT NULL';
  $extra=$i['EXTRA']!=''?" $i[EXTRA]":'';
  $ret.="           `$i[COLUMN_NAME]` $i[COLUMN_TYPE] $null$extra,\n";
}

foreach ($primary as $i)
{
  $ret.="           PRIMARY KEY (`$i[COLUMN_NAME]`) USING BTREE,\n";
}
foreach ($fks as $i)
{
  $ret.="           INDEX `$i[constraint]` (`$i[column]`) USING BTREE,\n";
}
foreach ($fks as $i)
{
  $ret.="           CONSTRAINT `$i[constraint]` FOREIGN KEY (`$i[column]`) REFERENCES `$i[table]` (`$i[key]`) ON UPDATE $i[UPDATE_RULE] ON DELETE $i[DELETE_RULE],\n";
}
$ret=substr($ret, 0, -2);
$ret.="
        )
        COLLATE=\'$more[TABLE_COLLATION]\'
        ENGINE=$more[ENGINE]$comment
    ');
  }
  public function down()
  {
    \$this->db->query('DROP TABLE `$table`');
  }
}";
echo $ret;
?></textarea>

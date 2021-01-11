<label>Controlador</label>
<textarea name="name" rows="40" cols="200" spellcheck="false"><?php


    $db=new sql($_GET['database']);
    $result=$db->query("DESC $_GET[table]");
    echo "\$dados_insert['$_GET[table]'] = [\n";
    foreach ($result as $i)
    {
      $ii=$i['Field'];
            if($ii=='id')         continue;
       else if($ii=='created_by') continue;
       else if($ii=='created_at') continue;
       else if($ii=='updated_by') continue;
       else if($ii=='updated_at') continue;
      echo ident("   '$ii' ",70)."=> \$dados->$ii,\n";
    }
    echo '];';
    echo "

\$header = (object) \$this->input->request_headers();
\$user = (object) \$this->jwt->decode(isset(\$header->authorization) ? \$header->authorization : \$header->Authorization, CONSUMER_KEY);
\$dados_insert['id_usuario']=\$user->id_usuario;

if ((int)\$dados->id == 0)
{
\$dados_insert['$_GET[table]']['created_by'] = \$user->id_usuario;
\$dados_insert['$_GET[table]']['created_at'] = date('Y-m-d H:i:s', time());
\$result = \$this->$_GET[table]->salvar(\$dados_insert);
}
else
{
\$dados_insert['$_GET[table]']['updated_by'] = \$user->id_usuario;
\$dados_insert['$_GET[table]']['updated_at'] = date('Y-m-d H:i:s', time());
\$result = \$this->$_GET[table]->atualizar(\$dados_insert, \$dados->id);
}

if (\$result)
{
\$response['lista']  = \$this->$_GET[table]->get(\$result);
}
else
{
\$response = [
'status' => 'erro',
'lista'  => [],
];
}
";

  ?></textarea>

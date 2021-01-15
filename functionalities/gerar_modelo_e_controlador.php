<?php
		$database= $_GET['database'];
        $table=$_GET['table'];
        $model = camel(explode('_',$table)).'_Model';
        $controler = ucfirst($table);

        $p_model = "$program/application/models/$model.php";
        $p_controler = "$program/application/controllers/$controler.php";
        if(!is_dir("$program/application")):?><div class="error">Falha: Para gerar os arquivos, o programa deve ser apontado no "config.json".</div><?php
        elseif(file_exists($p_model)||file_exists($p_controler)):
          ?>
          <div class="error">Falha: Arquivo já existente.</div>
          <label>Saida</label>
          <textarea name="name" rows="8" cols="80" spellcheck="false"><?php
          echo "Possíveis rotas:\n";
          echo "public/$controler/get\n";
          echo "public/$controler/salvar\n";
          echo "\nArquivos encontrados:\n";
          echo "$p_model\n";
          echo "$p_controler\n";
          ?>
          </textarea>
          <?php
          else:
          $path="$program/application/hooks/Verifica_token.php";
          $myfile = fopen($path, "r") or die("Unable to open file!");
          $target = str_split(fread($myfile,filesize($path)));
          fclose($myfile);


          $source=str_split('$mapUrl = array(');

          $source_lenght=count($source);
          $target_lenght=count($target);
          for ($i=0; $i < $target_lenght; $i++)
          {
            if(match($i,$target,$source))
            {

              array_splice( $target, $i, 0, "
            '$controler' => array
            (
                'get' => 0,
                'salvar' => $_GET[privilegio],
                'excluir' => $_GET[privilegio],
            ),");
              break;
            }
          }
          $myfile = fopen("$program/application/hooks/Verifica_token.php", "w") or die("Unable to open file!");
          fwrite_long($myfile,implode($target));
          fclose($myfile);

        $controler_str=
"<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header(\"Access-Control-Allow-Origin: *\");
header(\"Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS\");
header(\"Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization\");

class $controler extends CI_Controller
{
    public function __construct()
    {
      // Sobrecarga no costrutor
      parent::__construct();

      // usado somente pra debugar
      //\$this->load->helper('chrome_helper');

      //Carrega os models necessários
      \$this->load->model('$model','$table');
    }
    public function get(\$id=null)
    {
      \$status_code = 200;
      \$response = [
        'status' => 'sucesso',
        'lista'  => [],
      ];

      if(\$query = \$this->{$table}->get(\$id))
      {
        \$response['lista']=\$query;
      }

      return \$this->output
        ->set_content_type('application/json')
        ->set_status_header(\$status_code)
        ->set_output(
          json_encode(\$response)
        );
    }
    public function salvar()
    {
        \$status_code = 200;
        \$response = [
          'status' => 'sucesso',
          'lista'  => [],
        ];

        \$dados_insert = [];
        \$dados = json_decode(\$this->input->post('data'));

  ";
        $db=new sql($_GET['database']);
        $result=$db->query("DESC $_GET[table]");
        $controler_str.= "      \$dados_insert['$_GET[table]'] = [\n";
        foreach ($result as $i)
        {
          $ii=$i['Field'];
                if($ii=='id')         continue;
           else if($ii=='created_by') continue;
           else if($ii=='created_at') continue;
           else if($ii=='updated_by') continue;
           else if($ii=='updated_at') continue;
          $controler_str.= '           '.ident("'$ii' ",70)."=> \$dados->$ii,\n";
        }
        $controler_str.= '        ];';
        $controler_str.=
         "

        \$header = (object) \$this->input->request_headers();
        \$user = (object) \$this->jwt->decode(isset(\$header->authorization) ? \$header->authorization : \$header->Authorization, CONSUMER_KEY);
        \$dados_insert['id_usuario']=\$user->id_usuario;

        if ((int)\$dados->id == 0)
        {
          \$dados_insert['$table']['created_by'] = \$user->id_usuario;
          \$dados_insert['$table']['created_at'] = date('Y-m-d H:i:s', time());
          \$result = \$this->{$table}->salvar(\$dados_insert);
        }
        else
        {
          \$dados_insert['$table']['updated_by'] = \$user->id_usuario;
          \$dados_insert['$table']['updated_at'] = date('Y-m-d H:i:s', time());
          \$result = \$this->{$table}->atualizar(\$dados_insert, \$dados->id);
        }

        if (\$result)
        {
          \$response['lista']  = \$this->{$table}->get(\$result);
        }
        else
        {
          \$response = [
            'status' => 'erro',
            'lista'  => [],
          ];
        }

        return \$this->output
          ->set_content_type('application/json')
          ->set_status_header(\$status_code)
          ->set_output(
            json_encode(\$response)
          );
    }
		public function excluir(\$id)
    {
        \$status_code = 200;
        \$response = [
            'status' => 'sucesso',
            'lista'  => [],
        ];

        \$header = (object) \$this->input->request_headers();
        \$user = (object) \$this->jwt->decode(isset(\$header->authorization) ? \$header->authorization : \$header->Authorization, CONSUMER_KEY);

        \$dados_insert['$table'] = ['excluido'=> 1];

         \$header = (object) \$this->input->request_headers();
         \$user = (object) \$this->jwt->decode(isset(\$header->authorization) ? \$header->authorization : \$header->Authorization, CONSUMER_KEY);
         \$dados_insert['id_usuario']=\$user->id_usuario;


         \$dados_insert['$table']['updated_by'] = \$user->id_usuario;
         \$dados_insert['$table']['updated_at'] = date('Y-m-d H:i:s', time());
         \$result = \$this->{$table}->atualizar(\$dados_insert, \$id);


        return \$this->output
            ->set_content_type('application/json')
            ->set_status_header(\$status_code)
            ->set_output(
                json_encode(\$response)
            );
    }
}
?>";
      $model_str="
<?php

class $model extends CI_Model
{
    public function __construct()
    {
      parent::__construct();
    }
    public function get(\$id = null)
    {
      if(\$id != null)\$id = \"AND `$table`.`id` = \$id\";
      \$query = \$this->db->query(\"";
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
                $select.='        '.ident("`$fk_name`.`$jj`",70)." AS `{$jj}_$fk_name`,\n";
              }

              $join.="        LEFT JOIN `$ct`";
              if($ct != $fk_name)$join.=" AS `$fk_name`";
              $join.=" ON `$table`.`$i[coluna]` = `$fk_name`.`$i[chave]`\n";
          }

          // foreach ($fks as $i) {
          //     loop($database,$i['tabela'],$join,$select);
          // }

        }
        $join='';
        $select=",\n";
        loop($database,$table,$join,$select);
        if($select==",\n")$select='';
        $select=substr($select, 0, -2)."\n";
        $model_str.= "SELECT\n        `$table`.*{$select}        FROM `$table`\n$join        WHERE (`{$table}`.`excluido` != 1 OR `{$table}`.`excluido` is NULL)\n        \$id";

        $model_str.="
      \");
      return \$query->result_object();
    }
    public function salvar(\$dados)
    {
      \$this->db->trans_begin();

      if (\$this->db->insert('$table', \$dados['$table']))
      {
        //INSERINDO
        \$id = \$this->db->insert_id();

        \$dados_log = array(
          'id_registro' => \$id,
          'tabela' => '$table',
          'acao' => 1,
          'sql' => str_replace(\"`\", \"\", \$this->db->last_query()),
          'data_cadastro' => date('Y-m-d H:i:s', time()),
          'id_usuario' => \$dados['id_usuario'],
        );

        \$this->auditoria->salvar(\$dados_log);

        if (\$this->db->trans_status() === false)
        {
          \$this->db->trans_rollback();
          return false;
        }
        else
        {
          \$this->db->trans_commit();
          return \$id;

        }
      }
    }
    public function atualizar(\$dados, \$id)
    {
      if (\$dados != null)
      {
        \$this->db->trans_begin();

        \$this->db->where('id', \$id);
        if (\$this->db->update('$table', \$dados['$table']))
        {
          //Log
          \$dados_log = [
            'id_registro'   => \$id,
            'tabela'        => '$table',
            'acao'          => 2,
            'sql'           => str_replace(\"`\", \"\", \$this->db->last_query()),
            'data_cadastro' => date('Y-m-d H:i:s', time()),
            'id_usuario'    => \$dados['id_usuario']
          ];
          \$this->auditoria->salvar(\$dados_log);
        }
      }
      if (\$this->db->trans_status() === false)
      {
        \$this->db->trans_rollback();
        return false;
      }
      else
      {
        \$this->db->trans_commit();
        return \$id;
      }
    }
}
?>";
    $f_controler = fopen($p_controler,'w');
    fwrite_long($f_controler, $controler_str);
    fclose($f_controler);
    $f_model=fopen($p_model,'w');
    fwrite_long($f_model, $model_str);
    fclose($f_model);
    ?>
    <label>Saida</label>
    <textarea name="name" rows="10" cols="80" spellcheck="false"><?php
    echo "Rotas:\n";
    echo "public/$controler/get\n";
    echo "public/$controler/salvar (privilegio:$_GET[privilegio])\n";
		echo "public/$controler/excluir (privilegio:$_GET[privilegio])\n";
    echo "\nArquivos gerados:\n";
    echo "$p_model\n";
    echo "$p_controler\n";
    echo "\nArquivo editado:\n";
    echo 'application/hooks/Verifica_token.php (Caminhos adicionados no inicio do vetor, necessário colocar em ordem alfabetica.)';
    ?>
    </textarea>
	<?php endif; ?>

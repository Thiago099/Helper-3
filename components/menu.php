<?php
// if(isset($_GET['table'])&& exists($_GET['database'],$_GET['table'])):
if(isset($_GET['database'])):
?>

<label>Opções</label>
</div>
  <div class="button_container">
    <div class="button">
    <input type="submit" value="Selecionar">
    <input type="submit" name="action" value="Gerar modelo e controlador">
    <input type="submit" name="action" value="Adicionar campos de controle">
    <input type="submit" name="action" value="Código insert">
    <input type="submit" name="action" value="Código controlador">
    <input type="submit" name="action" value="Código select">
    <input type="submit" name="action" value="Código JSON">
    <input type="submit" name="action" value="Caminhos do controlador">
    <input type="submit" name="action" value="Codigo migration">
    </div>
  </div>
<div class="container">
  <?php if(exists($_GET['database'],'privilegio')): ?>
<label>Privilégio necessário para salvar</label>
<select class="" name="privilegio">
  <option value="0">Nehum</option>
  <?php
  $db=new sql($_GET['database']);
  $result=$db->query('SELECT * FROM privilegio');
   ?>
   <?php foreach ($result as $a) : ?>
   <option value="<?php echo $a['id']; ?>"  <?php if(isset($_GET['privilegio'])&&$_GET['privilegio']==$a['id'])echo 'selected';?>><?php echo $a['titulo']; ?></option>
   <?php endforeach; ?>
</select>
<?php endif; ?>
<?php else: ?>
<label>Opções</label>
<input class="solo" type="submit" value="Selecionar">
<?php
endif;
?>

<?php require 'config.php';require_admin();$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
 if(isset($_POST['delete'])){$pdo->prepare('DELETE FROM departments WHERE name=?')->execute([$_POST['delete']]);$msg='Department removed. Existing records keep their old name.';}
 elseif(trim($_POST['name']??'')!==''){try{$pdo->prepare('INSERT INTO departments(name) VALUES(?)')->execute([strtoupper(trim($_POST['name']))]);$msg='Department added.';}catch(PDOException $x){$msg='That department is already in the list.';}}}
page_head('Departments');?>
<h1 class="h4 mb-3">Departments</h1><?php if($msg):?><div class="alert alert-info"><?=e($msg)?></div><?php endif;?>
<div class="row g-3"><div class="col-md-4"><div class="card"><div class="card-body"><form method="post"><?=csrf_field()?>
<input class="form-control mb-2" name="name" placeholder="Department name, e.g. MAO" required><button class="btn btn-primary w-100">Add department</button></form></div></div></div>
<div class="col-md-8"><div class="card"><div class="card-body"><table class="table table-sm align-middle"><tbody>
<?php foreach(get_departments($pdo) as $n):?><tr><td><?=e($n)?></td><td class="text-end"><form method="post" onsubmit="return confirm('Remove this department from the list?')"><?=csrf_field()?><button class="btn btn-sm btn-outline-danger" name="delete" value="<?=e($n)?>">Remove</button></form></td></tr><?php endforeach;?></tbody></table></div></div></div></div>
<?php page_foot();

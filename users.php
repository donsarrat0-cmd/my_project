<?php require 'config.php';require_admin();$msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
 if(isset($_POST['delete'])&&(int)$_POST['delete']!==$_SESSION['user']['id']){$pdo->prepare('DELETE FROM users WHERE id=?')->execute([(int)$_POST['delete']]);$msg='User deleted.';}
 elseif(isset($_POST['username'])){$u=trim($_POST['username']);
  if($u===''||strlen($_POST['password'])<6)$msg='Enter a username and a password of at least 6 characters.';
  else try{$pdo->prepare('INSERT INTO users(username,password,role) VALUES(?,?,?)')->execute([$u,password_hash($_POST['password'],PASSWORD_DEFAULT),$_POST['role']==='admin'?'admin':'staff']);$msg='User added.';}
  catch(PDOException $e){$msg='That username is already taken.';}}}
page_head('Users');?>
<h1 class="h4 mb-3">Users</h1><?php if($msg):?><div class="alert alert-info"><?=e($msg)?></div><?php endif;?>
<div class="row g-3"><div class="col-md-4"><div class="card"><div class="card-body"><form method="post"><?=csrf_field()?>
<input class="form-control mb-2" name="username" placeholder="Username" required><input class="form-control mb-2" type="password" name="password" placeholder="Password (6+ characters)" required>
<select class="form-select mb-3" name="role"><option value="staff">Staff</option><option value="admin">Admin</option></select><button class="btn btn-primary w-100">Add user</button></form></div></div></div>
<div class="col-md-8"><div class="card"><div class="card-body"><table class="table align-middle"><thead><tr><th>Username</th><th>Role</th><th></th></tr></thead><tbody>
<?php foreach($pdo->query('SELECT id,username,role FROM users ORDER BY username') as $u):?><tr><td><?=e($u['username'])?></td><td><?=e($u['role'])?></td><td class="text-end">
<?php if($u['id']!=$_SESSION['user']['id']):?><form method="post" onsubmit="return confirm('Delete this user?')"><?=csrf_field()?><button class="btn btn-sm btn-outline-danger" name="delete" value="<?=$u['id']?>">Delete</button></form><?php endif;?></td></tr><?php endforeach;?></tbody></table></div></div></div></div>
<?php page_foot();

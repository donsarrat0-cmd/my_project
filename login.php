<?php require 'config.php';$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
 $s=$pdo->prepare('SELECT * FROM users WHERE username=?');$s->execute([trim($_POST['username'])]);$u=$s->fetch();
 if($u&&password_verify($_POST['password'],$u['password'])){session_regenerate_id(true);$_SESSION['user']=['id'=>$u['id'],'username'=>$u['username'],'role'=>$u['role']];header('Location: index.php');exit;}
 $err='Wrong username or password.';}
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Log in</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body style="background:#1f3a4d"><div class="container" style="max-width:400px;margin-top:12vh"><div class="card shadow"><div class="card-body p-4">
<h1 class="h4 mb-1">Document Logbook</h1><p class="text-muted">Log in to record releases and receipts.</p>
<?php if($err):?><div class="alert alert-danger py-2"><?=e($err)?></div><?php endif;?>
<form method="post"><?=csrf_field()?>
<label class="form-label">Username</label><input class="form-control mb-3" name="username" required autofocus>
<label class="form-label">Password</label><input class="form-control mb-3" type="password" name="password" required>
<button class="btn btn-primary w-100">Log in</button></form></div></div></div></body></html>

<?php
session_start();
const OFFICE_NAME='Document Release & Receiving Logbook';
const HOLDER='This office';
$pdo=new PDO('mysql:host=localhost;dbname=doc_tracker;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function fdt($s){return $s?date('M j, Y g:i A',strtotime($s)):'';}
function is_admin(){return ($_SESSION['user']['role']??'')==='admin';}
function require_login(){if(empty($_SESSION['user'])){header('Location: login.php');exit;}}
function require_admin(){require_login();if(!is_admin()){http_response_code(403);exit('Admins only.');}}
function csrf_field(){$_SESSION['csrf']=$_SESSION['csrf']??bin2hex(random_bytes(16));return '<input type="hidden" name="csrf" value="'.$_SESSION['csrf'].'">';}
function csrf_check(){if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){http_response_code(400);exit('Session expired. Go back and try again.');}}
function log_history($pdo,$id,$action,$by,$remarks=''){$pdo->prepare('INSERT INTO document_history(document_id,action,performed_by,date_time,remarks) VALUES(?,?,?,NOW(),?)')->execute([$id,$action,$by,$remarks]);}
function badge($s){$c=['Forwarded'=>'info text-dark','Released'=>'warning text-dark','Received'=>'success','Completed'=>'secondary'][$s]??'light';return "<span class='badge bg-$c'>".e($s).'</span>';}
function get_doc($pdo,$id){$s=$pdo->prepare('SELECT * FROM documents WHERE id=?');$s->execute([$id]);return $s->fetch()?:exit('Document not found.');}
function get_departments($pdo){return $pdo->query('SELECT name FROM departments ORDER BY name')->fetchAll(PDO::FETCH_COLUMN);}
function doc_query($pdo,$g){
 $w=[];$p=[];$like=fn($v)=>'%'.$v.'%';
 if(($g['q']??'')!==''){$w[]='(d.release_number LIKE ? OR d.document_title LIKE ?)';$p[]=$like($g['q']);$p[]=$like($g['q']);}
 if(($g['receiver']??'')!==''){$w[]='r.received_by LIKE ?';$p[]=$like($g['receiver']);}
 if(($g['office']??'')!==''){$w[]='(d.from_office LIKE ? OR d.released_to LIKE ?)';$p[]=$like($g['office']);$p[]=$like($g['office']);}
 if(($g['dept']??'')!==''){$w[]=(($g['dir']??'')==='from'?'d.from_office':'d.released_to').'=?';$p[]=$g['dept'];}
 if(($g['from']??'')!==''){$w[]='DATE(d.date_released)>=?';$p[]=$g['from'];}
 if(($g['to']??'')!==''){$w[]='DATE(d.date_released)<=?';$p[]=$g['to'];}
 if(($g['status']??'')!==''){$w[]='d.status=?';$p[]=$g['status'];}
 $sql='SELECT d.*,r.received_by,r.date_received,r.time_received FROM documents d LEFT JOIN receiving_records r ON r.id=(SELECT MAX(id) FROM receiving_records WHERE document_id=d.id) AND d.status IN (\'Released\',\'Completed\')'.($w?' WHERE '.implode(' AND ',$w):'').' ORDER BY d.date_released DESC,d.id DESC LIMIT 500';
 $s=$pdo->prepare($sql);$s->execute($p);return $s->fetchAll();
}
function page_head($title){require_login();?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?> | Logbook</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>body{background:#eef1f4}.navbar{background:#1f3a4d}.card{border:0;border-left:4px solid #1f3a4d}.table thead th{background:#e3e9ee;white-space:nowrap}
@media print{.no-print{display:none!important}body{background:#fff}.card{border:0}}</style></head><body>
<nav class="navbar navbar-expand-md navbar-dark no-print"><div class="container">
<a class="navbar-brand" href="index.php">Document Logbook</a>
<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#n"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="n"><ul class="navbar-nav me-auto">
<li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
<li class="nav-item"><a class="nav-link" href="documents.php">Documents</a></li>
<li class="nav-item"><a class="nav-link" href="departments.php">By Department</a></li>
<li class="nav-item"><a class="nav-link" href="release.php">Add Document</a></li>
<?php if(is_admin()):?><li class="nav-item"><a class="nav-link" href="manage_departments.php">Departments</a></li><li class="nav-item"><a class="nav-link" href="users.php">Users</a></li><?php endif;?></ul>
<span class="navbar-text me-3"><?=e($_SESSION['user']['username'])?> (<?=e($_SESSION['user']['role'])?>)</span>
<a class="btn btn-outline-light btn-sm" href="logout.php">Log out</a></div></div></nav>
<div class="container py-4">
<?php }
function page_foot(){?></div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script></body></html><?php }

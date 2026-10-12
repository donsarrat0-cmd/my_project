<?php require 'config.php';require_login();$d=get_doc($pdo,(int)($_GET['id']??0));
if($_SERVER['REQUEST_METHOD']==='POST'&&$d['status']!=='Completed'&&is_admin()){csrf_check();
 $pdo->prepare("UPDATE documents SET status='Completed' WHERE id=?")->execute([$d['id']]);
 log_history($pdo,$d['id'],'Marked Completed. Status: COMPLETED',$_SESSION['user']['username']);header('Location: history.php?id='.$d['id']);exit;}
$h=$pdo->prepare('SELECT * FROM document_history WHERE document_id=? ORDER BY date_time,id');$h->execute([$d['id']]);$h=$h->fetchAll();
$r=$pdo->prepare('SELECT * FROM receiving_records WHERE document_id=? ORDER BY id');$r->execute([$d['id']]);$r=$r->fetchAll();
page_head('History');?>
<?php if(isset($_GET['new'])):?><div class="alert alert-success">Saved as <?=e($d['release_number'])?>.</div><?php endif;?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2"><h1 class="h4 m-0"><?=e($d['release_number'])?> <?=badge($d['status'])?></h1>
<div class="d-flex gap-2 flex-wrap">
<?php if($d['status']!=='Completed'):?><a class="btn btn-primary" href="forward.php?id=<?=$d['id']?>">Release / Forward</a><?php endif;?>
<?php if($d['status']!=='Completed'&&is_admin()):?><form method="post"><?=csrf_field()?><button class="btn btn-secondary">Mark Completed</button></form><?php endif;?>
<a class="btn btn-outline-dark" href="report.php?type=history&id=<?=$d['id']?>">Print History</a>
<?php if($r):?><a class="btn btn-outline-dark" href="report.php?type=ack&id=<?=$d['id']?>">Print Acknowledgment</a><?php endif;?></div></div>
<div class="row g-3"><div class="col-lg-5"><div class="card h-100"><div class="card-body"><h2 class="h6">Document</h2>
<dl class="row mb-0 small"><dt class="col-4">Title</dt><dd class="col-8"><?=e($d['document_title'])?></dd><dt class="col-4">Type</dt><dd class="col-8"><?=e($d['document_type'])?></dd>
<dt class="col-4">From</dt><dd class="col-8"><?=e($d['from_office'])?></dd><dt class="col-4">Released to</dt><dd class="col-8"><?=e($d['released_to'])?></dd>
<dt class="col-4">Released by</dt><dd class="col-8"><?=e($d['released_by'])?></dd><dt class="col-4">Date</dt><dd class="col-8"><?=fdt($d['date_released'])?></dd>
<dt class="col-4">Remarks</dt><dd class="col-8"><?=nl2br(e($d['remarks']))?></dd>
<?php if($d['attachment']):?><dt class="col-4">Attachment</dt><dd class="col-8"><a href="uploads/<?=e($d['attachment'])?>">Open scan</a></dd><?php endif;?></dl></div></div></div>
<div class="col-lg-7"><div class="card h-100"><div class="card-body"><h2 class="h6">History</h2><div class="table-responsive"><table class="table table-sm">
<thead><tr><th>Date &amp; time</th><th>Action</th><th>By</th></tr></thead><tbody>
<?php foreach($h as $x):?><tr><td class="text-nowrap"><?=fdt($x['date_time'])?></td><td><?=e($x['action'])?><?php if($x['remarks']):?><div class="small text-muted"><?=e($x['remarks'])?></div><?php endif;?></td><td><?=e($x['performed_by'])?></td></tr><?php endforeach;?></tbody></table></div>
<?php foreach($r as $i=>$x):?><h2 class="h6 mt-3">Proof of receiving<?=count($r)>1?' #'.($i+1):''?></h2><div class="small"><?=e($x['received_by'])?>, <?=e($x['position'])?> &ndash; <?=e($x['date_received'])?> <?=e(substr($x['time_received'],0,5))?></div>
<img src="<?=e($x['signature'])?>" alt="Signature" class="border rounded bg-white mt-2" style="max-width:100%;height:90px"><?php endforeach;?></div></div></div></div>
<?php page_foot();

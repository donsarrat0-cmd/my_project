<?php require 'config.php';require_login();$t=$_GET['type']??'release';
$id=(int)($_GET['id']??0);$d=$id?get_doc($pdo,$id):null;
if($d){$r=$pdo->prepare('SELECT * FROM receiving_records WHERE document_id=? ORDER BY id DESC LIMIT 1');$r->execute([$id]);$r=$r->fetch();
 $h=$pdo->prepare('SELECT * FROM document_history WHERE document_id=? ORDER BY date_time,id');$h->execute([$id]);$h=$h->fetchAll();}
page_head('Report');?>
<div class="no-print mb-3"><button class="btn btn-primary" onclick="window.print()">Print Report</button> <a class="btn btn-outline-secondary" href="javascript:history.back()">Back</a></div>
<div class="card"><div class="card-body">
<div class="text-center mb-3"><h1 class="h5 m-0"><?=e(OFFICE_NAME)?></h1>
<div><?=['release'=>'Release Report','ack'=>'Receiving Acknowledgment','history'=>'Document History Report'][$t]??''?></div><div class="small text-muted">Printed <?=date('M j, Y g:i A')?></div></div>
<?php if($t==='release'):$rows=doc_query($pdo,$_GET);?>
<div class="table-responsive"><table class="table table-bordered table-sm"><thead><tr><th>Release No.</th><th>Document</th><th>From</th><th>Released To</th><th>Released</th><th>Received By</th><th>Status</th></tr></thead><tbody>
<?php foreach($rows as $x):?><tr><td><?=e($x['release_number'])?></td><td><?=e($x['document_title'])?></td><td><?=e($x['from_office'])?></td><td><?=e($x['released_to'])?></td><td><?=fdt($x['date_released'])?></td><td><?=e($x['received_by'])?></td><td><?=e($x['status'])?></td></tr><?php endforeach;?></tbody></table></div>
<p class="small">Total: <?=count($rows)?> document(s)</p>
<?php elseif($t==='ack'&&$d&&$r):?>
<p>This confirms that the document below was received.</p>
<table class="table table-bordered"><tr><th width="30%">Release No.</th><td><?=e($d['release_number'])?></td></tr><tr><th>Document</th><td><?=e($d['document_title'])?> (<?=e($d['document_type'])?>)</td></tr>
<tr><th>From</th><td><?=e($d['from_office'])?></td></tr><tr><th>Released to</th><td><?=e($d['released_to'])?></td></tr><tr><th>Released by / date</th><td><?=e($d['released_by'])?>, <?=fdt($d['date_released'])?></td></tr>
<tr><th>Received by</th><td><?=e($r['received_by'])?>, <?=e($r['position'])?></td></tr><tr><th>Date &amp; time received</th><td><?=e($r['date_received'])?> <?=e(substr($r['time_received'],0,5))?></td></tr>
<tr><th>Remarks</th><td><?=e($r['remarks'])?></td></tr></table>
<div class="mt-4"><img src="<?=e($r['signature'])?>" alt="Signature" style="height:90px"><hr class="w-50 m-0"><div class="small">Signature of receiver</div></div>
<?php elseif($t==='history'&&$d):?>
<p><strong><?=e($d['release_number'])?></strong> &ndash; <?=e($d['document_title'])?><br><span class="small">From <?=e($d['from_office'])?> to <?=e($d['released_to'])?> | Status: <?=e($d['status'])?></span></p>
<table class="table table-bordered table-sm"><thead><tr><th>Date &amp; time</th><th>Action</th><th>By</th><th>Remarks</th></tr></thead><tbody>
<?php foreach($h as $x):?><tr><td><?=fdt($x['date_time'])?></td><td><?=e($x['action'])?></td><td><?=e($x['performed_by'])?></td><td><?=e($x['remarks'])?></td></tr><?php endforeach;?></tbody></table>
<?php else:?><p class="text-muted">Nothing to show. For an acknowledgment, the document must be received first.</p><?php endif;?>
</div></div><?php page_foot();

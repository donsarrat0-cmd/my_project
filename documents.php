<?php require 'config.php';require_login();$g=$_GET;$rows=doc_query($pdo,$g);
if(($g['export']??'')==='csv'){header('Content-Type: text/csv');header('Content-Disposition: attachment; filename=documents.csv');$o=fopen('php://output','w');
 fputcsv($o,['Release No','Title','Type','From','Released To','Released By','Date Released','Status','Received By','Date Received']);
 foreach($rows as $d)fputcsv($o,[$d['release_number'],$d['document_title'],$d['document_type'],$d['from_office'],$d['released_to'],$d['released_by'],$d['date_released'],$d['status'],$d['received_by'],$d['date_received']]);exit;}
unset($g['export']);$qs=http_build_query($g);page_head('Documents');?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2"><h1 class="h4 m-0">Documents</h1>
<div class="d-flex gap-2"><a class="btn btn-primary" href="release.php">Add Document</a>
<a class="btn btn-outline-dark" href="report.php?type=release&<?=e($qs)?>">Print Report</a>
<a class="btn btn-outline-dark" href="?<?=e($qs)?>&export=csv">Export CSV</a></div></div>
<div class="card mb-3"><div class="card-body"><form class="row g-2" method="get">
<div class="col-md-3"><input class="form-control" name="q" placeholder="Release no. or document" value="<?=e($g['q']??'')?>"></div>
<div class="col-md-3"><input class="form-control" name="receiver" placeholder="Receiver name" value="<?=e($g['receiver']??'')?>"></div>
<div class="col-md-3"><input class="form-control" name="office" placeholder="Office" value="<?=e($g['office']??'')?>"></div>
<div class="col-md-3"><select class="form-select" name="status"><option value="">All statuses</option>
<?php foreach(['Received','Forwarded','Released','Completed'] as $s):?><option <?=($g['status']??'')===$s?'selected':''?>><?=$s?></option><?php endforeach;?></select></div>
<div class="col-6 col-md-3"><input class="form-control" type="date" name="from" value="<?=e($g['from']??'')?>" title="From date"></div>
<div class="col-6 col-md-3"><input class="form-control" type="date" name="to" value="<?=e($g['to']??'')?>" title="To date"></div>
<div class="col-md-6"><button class="btn btn-primary">Search</button> <a class="btn btn-outline-secondary" href="documents.php">Clear</a></div>
</form></div></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>Release No.</th><th>Document</th><th>From</th><th>Released To</th><th>Released</th><th>Received By</th><th>Status</th><th>Action</th></tr></thead><tbody>
<?php foreach($rows as $d):?><tr><td class="text-nowrap"><?=e($d['release_number'])?></td><td><?=e($d['document_title'])?><div class="small text-muted"><?=e($d['document_type'])?></div></td>
<td><?=e($d['from_office'])?></td><td><?=e($d['released_to'])?></td><td><?=fdt($d['date_released'])?></td><td><?=e($d['received_by'])?></td><td><?=badge($d['status'])?></td>
<td><div class="dropdown"><button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">Action</button>
<ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="history.php?id=<?=$d['id']?>">View History</a></li>
<?php if($d['status']!=='Completed'):?><li><a class="dropdown-item" href="forward.php?id=<?=$d['id']?>">Release / Forward</a></li><?php endif;?>
<li><hr class="dropdown-divider"></li><li><a class="dropdown-item" href="report.php?type=history&id=<?=$d['id']?>">Print History</a></li></ul></div></td></tr>
<?php endforeach;if(!$rows):?><tr><td colspan="8" class="text-muted">No documents match. Clear the filters or add a release.</td></tr><?php endif;?></tbody></table></div></div></div>
<script>document.addEventListener('DOMContentLoaded',()=>document.querySelectorAll('.dropdown-toggle').forEach(e=>new bootstrap.Dropdown(e,{popperConfig:{strategy:'fixed'}})));</script>
<?php page_foot();

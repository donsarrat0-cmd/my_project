<?php require 'config.php';require_login();
$dir=($_GET['dir']??'to')==='from'?'from':'to';$col=$dir==='from'?'from_office':'released_to';$name=trim($_GET['name']??'');
page_head('By Department');?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2"><h1 class="h4 m-0">Released per Department</h1>
<div class="btn-group"><a class="btn btn-outline-primary <?=$dir==='to'?'active':''?>" href="?dir=to">Released to</a><a class="btn btn-outline-primary <?=$dir==='from'?'active':''?>" href="?dir=from">Released from</a></div></div>
<?php if($name===''):
$rows=$pdo->query("SELECT $col AS dept,COUNT(*) total,SUM(status='Forwarded') forwarded,SUM(status='Released') released,SUM(status='Received') received,SUM(status='Completed') completed FROM documents WHERE $col<>'This office' GROUP BY $col ORDER BY total DESC,dept")->fetchAll();?>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>Department / office</th><th>Total</th><th>Forwarded</th><th>Released</th><th>Received</th><th>Completed</th><th></th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><?=e($r['dept'])?></td><td><?=$r['total']?></td><td><?=$r['forwarded']?></td><td><?=$r['released']?></td><td><?=$r['received']?></td><td><?=$r['completed']?></td>
<td><a class="btn btn-sm btn-outline-primary" href="?dir=<?=$dir?>&name=<?=urlencode($r['dept'])?>">View All</a></td></tr>
<?php endforeach;if(!$rows):?><tr><td colspan="7" class="text-muted">No releases yet. Add a release to see departments here.</td></tr><?php endif;?></tbody></table></div></div></div>
<?php else:$docs=doc_query($pdo,['dept'=>$name,'dir'=>$dir]);?>
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2"><h2 class="h5 m-0"><?=e($name)?> <span class="text-muted small">(<?=count($docs)?> document<?=count($docs)==1?'':'s'?>, released <?=$dir?>)</span></h2>
<div class="d-flex gap-2"><a class="btn btn-outline-secondary" href="?dir=<?=$dir?>">All departments</a>
<a class="btn btn-outline-dark" href="report.php?type=release&dir=<?=$dir?>&dept=<?=urlencode($name)?>">Print Report</a></div></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>Release No.</th><th>Document</th><th><?=$dir==='to'?'From':'Released To'?></th><th>Forwarded</th><th>Released</th><th>Received By</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($docs as $d):?><tr><td class="text-nowrap"><?=e($d['release_number'])?></td><td><?=e($d['document_title'])?></td><td><?=e($dir==='to'?$d['from_office']:$d['released_to'])?></td><td><?=fdt($d['date_released'])?></td><td><?=e($d['received_by'])?></td><td><?=badge($d['status'])?></td>
<td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="history.php?id=<?=$d['id']?>">View History</a></td></tr>
<?php endforeach;?></tbody></table></div></div></div><?php endif;page_foot();

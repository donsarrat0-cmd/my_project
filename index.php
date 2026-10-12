<?php require 'config.php';page_head('Dashboard');
$c=['Received'=>0,'Forwarded'=>0,'Released'=>0,'Completed'=>0];
foreach($pdo->query('SELECT status,COUNT(*) n FROM documents GROUP BY status') as $r)$c[$r['status']]=$r['n'];
$cards=[['Total Documents',array_sum($c),'primary',''],['Received',$c['Received'],'success','Received'],['Forwarded',$c['Forwarded'],'info','Forwarded'],['Released',$c['Released'],'warning','Released'],['Completed',$c['Completed'],'secondary','Completed']];
$recent=doc_query($pdo,[]);$recent=array_slice($recent,0,8);?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2"><h1 class="h4 m-0">Dashboard</h1>
<a class="btn btn-primary" href="release.php">Add Document</a></div>
<div class="row g-3 mb-4"><?php foreach($cards as [$l,$n,$col,$f]):?>
<div class="col-6 col-lg"><a class="text-decoration-none" href="documents.php?status=<?=urlencode($f)?>"><div class="card h-100"><div class="card-body">
<div class="text-muted small"><?=e($l)?></div><div class="display-6 text-<?=$col?>"><?=$n?></div></div></div></a></div><?php endforeach;?></div>
<div class="card"><div class="card-body"><h2 class="h6">Latest entries</h2><div class="table-responsive">
<table class="table table-sm align-middle"><thead><tr><th>Release No.</th><th>Document</th><th>Currently With</th><th>Date</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach($recent as $d):?><tr><td><?=e($d['release_number'])?></td><td><?=e($d['document_title'])?></td><td><?=e($d['released_to'])?></td><td><?=fdt($d['date_released'])?></td><td><?=badge($d['status'])?></td>
<td><a class="btn btn-sm btn-outline-primary" href="history.php?id=<?=$d['id']?>">View History</a></td></tr>
<?php endforeach;if(!$recent):?><tr><td colspan="6" class="text-muted">No documents yet. Use Add Document to log the first one.</td></tr><?php endif;?></tbody></table></div></div></div>
<?php page_foot();

<?php require 'config.php';require_login();$d=get_doc($pdo,(int)($_GET['id']??0));$err='';
if($d['status']==='Completed'){header('Location: history.php?id='.$d['id']);exit;}
$depts=get_departments($pdo);$act=$_POST['action']??'forward';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$f=array_map('trim',$_POST);$to=$f['to'];$by=$_SESSION['user']['username'];
 if($to==='')$err='Select where the document is going.';
 elseif($act==='release'&&($f['received_by']===''||$f['position']===''||$f['date_received']===''||$f['time_received']===''))$err='Release needs the receiver name, position, date and time.';
 elseif($act==='release'&&(!preg_match('#^data:image/png;base64,[A-Za-z0-9+/=]+$#',$f['signature'])||strlen($f['signature'])>700000))$err='Please have the receiver sign in the signature box.';
 else{$pdo->beginTransaction();
  if($act==='release'){
   $pdo->prepare('INSERT INTO receiving_records(document_id,received_by,position,date_received,time_received,signature,remarks) VALUES(?,?,?,?,?,?,?)')->execute([$d['id'],$f['received_by'],$f['position'],$f['date_received'],$f['time_received'],$f['signature'],$f['remarks']]);
   $pdo->prepare("UPDATE documents SET released_to=?,status='Released' WHERE id=?")->execute([$to,$d['id']]);
   log_history($pdo,$d['id'],"Released to $to, received by {$f['received_by']} ({$f['position']}). Status: RELEASED",$by,$f['remarks']);
  }else{
   $pdo->prepare("UPDATE documents SET released_to=?,status='Forwarded' WHERE id=?")->execute([$to,$d['id']]);
   log_history($pdo,$d['id'],"Forwarded to $to. Status: FORWARDED",$by,$f['remarks']);
  }
  $pdo->commit();header('Location: history.php?id='.$d['id']);exit;}}
$v=fn($k,$x='')=>e($_POST[$k]??$x);page_head('Release / Forward');?>
<h1 class="h4 mb-3">Release / Forward</h1><?php if($err):?><div class="alert alert-danger"><?=e($err)?></div><?php endif;?>
<div class="card mb-3"><div class="card-body"><strong><?=e($d['release_number'])?></strong> &ndash; <?=e($d['document_title'])?>
<div class="text-muted small">Came from <?=e($d['from_office'])?>. Now with <?=e($d['released_to'])?>.</div></div></div>
<div class="card"><div class="card-body"><form method="post" class="row g-3"><?=csrf_field()?>
<div class="col-12"><div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="action" id="af" value="forward" <?=$act==='forward'?'checked':''?>><label class="form-check-label" for="af"><strong>Forward</strong> (department only)</label></div>
<div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="action" id="ar" value="release" <?=$act==='release'?'checked':''?>><label class="form-check-label" for="ar"><strong>Release</strong> (receiver signs)</label></div></div>
<div class="col-md-6"><label class="form-label">Department / office *</label><select class="form-select" name="to" required><option value="">Select department / office</option><?php foreach($depts as $p):?><option <?=($_POST['to']??'')===$p?'selected':''?>><?=e($p)?></option><?php endforeach;?></select></div>
<div id="rel" class="row g-3 m-0 p-0" style="display:none">
<div class="col-md-6"><label class="form-label">Received by (person) *</label><input class="form-control" name="received_by" value="<?=$v('received_by')?>"></div>
<div class="col-md-6"><label class="form-label">Position / office *</label><input class="form-control" name="position" value="<?=$v('position')?>"></div>
<div class="col-6"><label class="form-label">Date received *</label><input class="form-control" type="date" name="date_received" value="<?=$v('date_received',date('Y-m-d'))?>"></div>
<div class="col-6"><label class="form-label">Time received *</label><input class="form-control" type="time" name="time_received" value="<?=$v('time_received',date('H:i'))?>"></div>
<div class="col-12"><label class="form-label">Signature *</label><canvas id="sig" class="border rounded bg-white w-100" height="160" style="touch-action:none"></canvas>
<input type="hidden" name="signature" id="sigdata"><button type="button" class="btn btn-sm btn-outline-secondary mt-1" id="clr">Clear signature</button></div></div>
<div class="col-12"><label class="form-label">Remarks</label><textarea class="form-control" name="remarks" rows="2"><?=$v('remarks')?></textarea></div>
<div class="col-12"><button class="btn btn-primary" id="go">Forward document</button> <a class="btn btn-outline-secondary" href="history.php?id=<?=$d['id']?>">Cancel</a></div></form></div></div>
<script>
const c=document.getElementById('sig'),x=c.getContext('2d'),rel=document.getElementById('rel');let dn=false,dirty=false;
function mode(){const r=document.getElementById('ar').checked;rel.style.display=r?'':'none';document.getElementById('go').textContent=r?'Release document':'Forward document';if(r&&c.width!==c.clientWidth){c.width=c.clientWidth}x.lineWidth=2;x.lineCap='round'}
document.querySelectorAll('[name=action]').forEach(i=>i.onchange=mode);mode();
const pt=e=>{const r=c.getBoundingClientRect();return[e.clientX-r.left,e.clientY-r.top]};
c.onpointerdown=e=>{dn=true;x.beginPath();x.moveTo(...pt(e))};c.onpointermove=e=>{if(dn){x.lineTo(...pt(e));x.stroke();dirty=true}};
c.onpointerup=c.onpointerleave=()=>dn=false;
document.getElementById('clr').onclick=()=>{x.clearRect(0,0,c.width,c.height);dirty=false};
document.querySelector('form').onsubmit=()=>{document.getElementById('sigdata').value=dirty?c.toDataURL('image/png'):''};
</script><?php page_foot();

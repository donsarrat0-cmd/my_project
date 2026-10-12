<?php require 'config.php';require_login();$err='';$depts=get_departments($pdo);
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
 $f=array_map('trim',$_POST);$file=null;
 foreach(['document_title','document_type','from_office','released_by','date_released'] as $k)if($f[$k]==='')$err='Please fill in all required fields.';
 if(!$err&&!empty($_FILES['attachment']['name'])){
  $ext=strtolower(pathinfo($_FILES['attachment']['name'],PATHINFO_EXTENSION));
  if($_FILES['attachment']['error']||!in_array($ext,['pdf','jpg','jpeg','png'])||$_FILES['attachment']['size']>5*1048576)$err='Attachment must be a PDF, JPG or PNG up to 5 MB.';
  else{$file=bin2hex(random_bytes(10)).'.'.$ext;move_uploaded_file($_FILES['attachment']['tmp_name'],__DIR__.'/uploads/'.$file);}}
 if(!$err){
  $y=date('Y',strtotime($f['date_released']));
  $pdo->beginTransaction();
  $n=$pdo->query("SELECT COALESCE(MAX(CAST(SUBSTRING(release_number,10) AS UNSIGNED)),0)+1 FROM documents WHERE release_number LIKE 'REL-$y-%' FOR UPDATE")->fetchColumn();
  $no=sprintf('REL-%s-%04d',$y,$n);
  $pdo->prepare("INSERT INTO documents(release_number,document_title,document_type,from_office,released_to,released_by,date_released,status,remarks,attachment) VALUES(?,?,?,?,?,?,?,'Received',?,?)")
   ->execute([$no,$f['document_title'],$f['document_type'],$f['from_office'],HOLDER,$f['released_by'],$f['date_released'],$f['remarks'],$file]);
  $id=$pdo->lastInsertId();
  log_history($pdo,$id,"Received from {$f['from_office']}. Status: RECEIVED",$f['released_by'],$f['remarks']);
  $pdo->commit();header("Location: history.php?id=$id&new=1");exit;}}
$v=fn($k,$d='')=>e($_POST[$k]??$d);
page_head('Add Document');?>
<h1 class="h4 mb-3">Add Document</h1>
<?php if($err):?><div class="alert alert-danger"><?=e($err)?></div><?php endif;?>
<div class="card"><div class="card-body"><form method="post" enctype="multipart/form-data" class="row g-3"><?=csrf_field()?>
<div class="col-md-8"><label class="form-label">Document title / description *</label><input class="form-control" name="document_title" value="<?=$v('document_title')?>" required></div>
<div class="col-md-4"><label class="form-label">Document type *</label><input class="form-control" name="document_type" list="types" value="<?=$v('document_type')?>" required>
<datalist id="types"><option>Letter</option><option>Memorandum</option><option>Voucher</option><option>Report</option><option>Contract</option><option>Request</option></datalist></div>
<div class="col-md-6"><label class="form-label">Received from (department) *</label><select class="form-select" name="from_office" required><option value="">Select department</option><?php foreach($depts as $dp):?><option <?=($_POST['from_office']??'')===$dp?'selected':''?>><?=e($dp)?></option><?php endforeach;?></select></div>
<div class="col-md-6"><label class="form-label">Date &amp; time received *</label><input class="form-control" type="datetime-local" name="date_released" value="<?=$v('date_released',date('Y-m-d\TH:i'))?>" required></div>
<div class="col-md-6"><label class="form-label">Logged by *</label><input class="form-control" name="released_by" value="<?=$v('released_by',$_SESSION['user']['username'])?>" required></div>
<div class="col-12"><label class="form-label">Purpose / remarks</label><textarea class="form-control" name="remarks" rows="2"><?=$v('remarks')?></textarea></div>
<div class="col-12"><label class="form-label">Attachment (optional scan: PDF, JPG, PNG)</label><input class="form-control" type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png"></div>
<div class="col-12"><button class="btn btn-primary">Save document</button> <a class="btn btn-outline-secondary" href="documents.php">Cancel</a></div>
</form></div></div><?php page_foot();

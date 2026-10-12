<?php // Run once in the browser, then DELETE this file.
$pdo=new PDO('mysql:host=localhost;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec(file_get_contents(__DIR__.'/schema.sql'));
$pdo->exec('USE doc_tracker');
if(!$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn()){
 $s=$pdo->prepare('INSERT INTO users(username,password,role) VALUES(?,?,?)');
 $s->execute(['admin',password_hash('admin123',PASSWORD_DEFAULT),'admin']);
 $s->execute(['staff',password_hash('staff123',PASSWORD_DEFAULT),'staff']);
}
echo 'Installed. Log in with admin/admin123 or staff/staff123, change passwords, then delete install.php.';

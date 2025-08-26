<?php
require_once __DIR__ . '/../config.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: read.php"); exit; }
$stmt = $pdo->prepare("SELECT * FROM leitores WHERE id_leitor = ?");
$stmt->execute([$id]);
$u = $stmt->fetch();
if (!$u) { header("Location: read.php"); exit; }
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    if ($nome==='') $errors[]='Nome obrigatório';
    if ($email!=='' && !filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Email inválido';
    if (!$errors) {
        $pdo->prepare("UPDATE leitores SET nome=:n,email=:e,telefone=:t WHERE id_leitor=:id")
            ->execute([':n'=>$nome,':e'=>$email?:null,':t'=>$telefone?:null,':id'=>$id]);
        header("Location: read.php"); exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Editar Leitor</title></head><body>
<h2>Editar Leitor</h2>
<?php foreach($errors as $err) echo "<p style='color:red'>".htmlspecialchars($err)."</p>"; ?>
<form method="post">
 Nome: <input name="nome" value="<?php echo htmlspecialchars($u['nome']); ?>" required><br>
 Email: <input name="email" value="<?php echo htmlspecialchars($u['email']); ?>"><br>
 Telefone: <input name="telefone" value="<?php echo htmlspecialchars($u['telefone']); ?>"><br>
 <button>Salvar</button>
</form>
<p><a href="read.php">Voltar</a></p>
</body></html>

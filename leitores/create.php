<?php
require_once __DIR__ . '/../config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');

    if ($nome === '') $errors[] = "Nome é obrigatório.";
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";

    if (!$errors) {
        try {
            $pdo->prepare("INSERT INTO leitores (nome,email,telefone) VALUES (:n,:e,:t)")
                ->execute([':n'=>$nome,':e'=>$email ?: null,':t'=>$telefone ?: null]);
            header("Location: read.php"); exit;
        } catch (Exception $e) {
            $errors[] = "Erro: " . $e->getMessage();
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Novo Leitor</title></head><body>
<h2>Cadastrar Leitor</h2>
<?php foreach($errors as $e) echo "<p style='color:red'>".htmlspecialchars($e)."</p>"; ?>
<form method="post">
 Nome: <input name="nome" required><br>
 Email: <input name="email"><br>
 Telefone: <input name="telefone"><br>
 <button>Salvar</button>
</form>
<p><a href="read.php">Voltar</a></p>
</body></html>

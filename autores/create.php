<?php
require_once __DIR__ . '/../config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $nacionalidade = trim($_POST['nacionalidade'] ?? '');
    $ano_nascimento = $_POST['ano_nascimento'] ?? null;
    if ($nome === '') $errors[] = "Nome é obrigatório.";

    if (empty($errors)) {
        $sql = "INSERT INTO autores (nome, nacionalidade, ano_nascimento) VALUES (:nome, :nac, :ano)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nome'=>$nome, ':nac'=>$nacionalidade ?: null, ':ano'=>$ano_nascimento ?: null]);
        header("Location: read.php");
        exit;
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Criar Autor</title></head><body>
<h2>Cadastrar Autor</h2>
<?php if($errors): foreach($errors as $e) echo "<p style='color:red'>".htmlspecialchars($e)."</p>"; endforeach; ?>
<form method="post">
  Nome: <input name="nome" required><br>
  Nacionalidade: <input name="nacionalidade"><br>
  Ano Nascimento: <input name="ano_nascimento" type="number" min="1000" max="<?php echo date('Y'); ?>"><br>
  <button type="submit">Salvar</button>
</form>
<p><a href="read.php">Voltar</a></p>
</body></html>

<?php
require_once __DIR__ . '/../config.php';
$errors = [];

$autStmt = $pdo->query("SELECT id_autor, nome FROM autores ORDER BY nome");
$autores = $autStmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $genero = trim($_POST['genero'] ?? '');
    $ano = intval($_POST['ano_publicacao'] ?? 0);
    $id_autor = intval($_POST['id_autor'] ?? 0);

    if ($titulo === '') $errors[] = "Título é obrigatório.";
    if ($id_autor <= 0) $errors[] = "Selecione um autor.";
    if ($ano <= 1500 || $ano > intval(date('Y'))) $errors[] = "Ano de publicação inválido.";

    if (!$errors) {
        $sql = "INSERT INTO livros (titulo, genero, ano_publicacao, id_autor) VALUES (:titulo,:genero,:ano,:autor)";
        $pdo->prepare($sql)->execute([':titulo'=>$titulo,':genero'=>$genero?:null,':ano'=>$ano,':autor'=>$id_autor]);
        header("Location: read.php");
        exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Novo Livro</title></head><body>
<h2>Cadastrar Livro</h2>
<?php foreach($errors as $e) echo "<p style='color:red'>".htmlspecialchars($e)."</p>"; ?>
<form method="post">
  Título: <input name="titulo" required><br>
  Gênero: <input name="genero"><br>
  Ano de publicação: <input name="year" name="ano_publicacao" type="number" min="1501" max="<?php echo date('Y'); ?>" value="<?php echo date('Y'); ?>"><br>
  Autor:
  <select name="id_autor" required>
    <option value="">-- selecione --</option>
    <?php foreach($autores as $a): ?>
      <option value="<?php echo $a['id_autor']; ?>"><?php echo htmlspecialchars($a['nome']); ?></option>
    <?php endforeach; ?>
  </select><br>
  <button type="submit">Salvar</button>
</form>
<p><a href="read.php">Voltar</a></p>
</body></html>

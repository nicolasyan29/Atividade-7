<?php
require_once __DIR__ . '/../config.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: read.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM livros WHERE id_livro = ?");
$stmt->execute([$id]);
$livro = $stmt->fetch();
if (!$livro) { header("Location: read.php"); exit; }

$autores = $pdo->query("SELECT id_autor, nome FROM autores ORDER BY nome")->fetchAll();
$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $titulo = trim($_POST['titulo']);
    $genero = trim($_POST['genero']);
    $ano = intval($_POST['ano_publicacao']);
    $id_autor = intval($_POST['id_autor']);
    if ($titulo==='') $errors[]='Título obrigatório';
    if ($id_autor<=0) $errors[]='Selecione autor';
    if ($ano<=1500 || $ano>intval(date('Y'))) $errors[]='Ano inválido';
    if (!$errors) {
        $sql = "UPDATE livros SET titulo=:titulo, genero=:genero, ano_publicacao=:ano, id_autor=:autor WHERE id_livro=:id";
        $pdo->prepare($sql)->execute([':titulo'=>$titulo,':genero'=>$genero?:null,':ano'=>$ano,':autor'=>$id_autor,':id'=>$id]);
        header("Location: read.php"); exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Editar Livro</title></head><body>
<h2>Editar Livro</h2>
<?php foreach($errors as $err) echo "<p style='color:red'>".htmlspecialchars($err)."</p>"; ?>
<form method="post">
  Título: <input name="titulo" value="<?php echo htmlspecialchars($livro['titulo']); ?>" required><br>
  Gênero: <input name="genero" value="<?php echo htmlspecialchars($livro['genero']); ?>"><br>
  Ano: <input name="ano_publicacao" type="number" min="1501" max="<?php echo date('Y'); ?>" value="<?php echo $livro['ano_publicacao']; ?>"><br>
  Autor:
  <select name="id_autor" required>
    <?php foreach($autores as $a): ?>
      <option value="<?php echo $a['id_autor']; ?>" <?php if($a['id_autor']==$livro['id_autor']) echo 'selected'; ?>><?php echo htmlspecialchars($a['nome']); ?></option>
    <?php endforeach; ?>
  </select><br>
  <button>Salvar</button>
</form>
<p><a href="read.php">Voltar</a></p>
</body></html>

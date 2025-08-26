<?php
require_once __DIR__ . '/../config.php';
$errors = [];

$livros = $pdo->query("SELECT l.id_livro, l.titulo FROM livros l ORDER BY titulo")->fetchAll();
$leitores = $pdo->query("SELECT id_leitor, nome FROM leitores ORDER BY nome")->fetchAll();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id_livro = intval($_POST['id_livro'] ?? 0);
    $id_leitor = intval($_POST['id_leitor'] ?? 0);
    $data_emprestimo = $_POST['data_emprestimo'] ?? date('Y-m-d');
    $data_devolucao = $_POST['data_devolucao'] ?: null;

    if ($id_livro<=0) $errors[] = "Selecione um livro.";
    if ($id_leitor<=0) $errors[] = "Selecione um leitor.";

    if ($data_devolucao && $data_devolucao < $data_emprestimo) $errors[] = "Data de devolução não pode ser anterior à data de empréstimo.";

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM emprestimos WHERE id_livro = ? AND data_devolucao IS NULL");
        $stmt->execute([$id_livro]);
        if ($stmt->fetchColumn() > 0) $errors[] = "Livro já possui empréstimo ativo.";
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM emprestimos WHERE id_leitor = ? AND data_devolucao IS NULL");
        $stmt->execute([$id_leitor]);
        if ($stmt->fetchColumn() >= 3) $errors[] = "Leitor já possui 3 empréstimos ativos.";
    }

    if (!$errors) {
        $sql = "INSERT INTO emprestimos (id_livro, id_leitor, data_emprestimo, data_devolucao) VALUES (:l,:le,:d1,:d2)";
        $pdo->prepare($sql)->execute([':l'=>$id_livro,':le'=>$id_leitor,':d1'=>$data_emprestimo,':d2'=>$data_devolucao ?: null]);
        header("Location: read.php");
        exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Novo Empréstimo</title></head><body>
<h2>Registrar Empréstimo</h2>
<?php foreach($errors as $e) echo "<p style='color:red'>".htmlspecialchars($e)."</p>"; ?>
<form method="post">
  Livro:
  <select name="id_livro" required>
    <option value="">-- selecione --</option>
    <?php foreach($livros as $l): ?><option value="<?php echo $l['id_livro']; ?>"><?php echo htmlspecialchars($l['titulo']); ?></option><?php endforeach; ?>
  </select><br>
  Leitor:
  <select name="id_leitor" required>
    <option value="">-- selecione --</option>
    <?php foreach($leitores as $le): ?><option value="<?php echo $le['id_leitor']; ?>"><?php echo htmlspecialchars($le['nome']); ?></option><?php endforeach; ?>
  </select><br>
  Data Empréstimo: <input name="data_emprestimo" type="date" value="<?php echo date('Y-m-d'); ?>"><br>
  Data Devolução (opcional): <input name="data_devolucao" type="date"><br>
  <button>Registrar</button>
</form>
<p><a href="read.php">Voltar</a></p>
</body></html>

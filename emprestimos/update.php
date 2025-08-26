<?php
require_once __DIR__ . '/../config.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: read.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM emprestimos WHERE id_emprestimo = ?");
$stmt->execute([$id]);
$e = $stmt->fetch();
if (!$e) { header("Location: read.php"); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_devolucao = $_POST['data_devolucao'] ?: null;
    if ($data_devolucao && $data_devolucao < $e['data_emprestimo']) $errors[] = "Data de devolução não pode ser anterior à data do empréstimo.";

    if (!$errors) {
        $pdo->prepare("UPDATE emprestimos SET data_devolucao = :d WHERE id_emprestimo = :id")
            ->execute([':d'=>$data_devolucao ?: null, ':id'=>$id]);
        header("Location: read.php"); exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Editar Empréstimo</title></head><body>
<h2>Editar Empréstimo (devolução)</h2>
<?php foreach($errors as $err) echo "<p style='color:red'>".htmlspecialchars($err)."</p>"; ?>
<form method="post">
  Livro: <?php echo htmlspecialchars($pdo->query("SELECT titulo FROM livros WHERE id_livro = ".$e['id_livro'])->fetchColumn()); ?><br>
  Leitor: <?php echo htmlspecialchars($pdo->query("SELECT nome FROM leitores WHERE id_leitor = ".$e['id_leitor'])->fetchColumn()); ?><br>
  Data Empréstimo: <?php echo $e['data_emprestimo']; ?><br>
  Data Devolução: <input name="data_devolucao" type="date" value="<?php echo htmlspecialchars($e['data_devolucao']); ?>"><br>
  <button>Salvar</button>
</form>
<p><a href="read.php">Voltar</a></p>
</body></html>

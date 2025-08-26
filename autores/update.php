<?php
require_once __DIR__ . '/../config.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: read.php"); exit; }

$stmt = $pdo->prepare("SELECT * FROM autores WHERE id_autor = ?");
$stmt->execute([$id]);
$autor = $stmt->fetch();
if (!$autor) { header("Location: read.php"); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nome = trim($_POST['nome']);
    $nac = trim($_POST['nacionalidade']);
    $ano = $_POST['ano_nascimento'] ?: null;
    if ($nome==='') $errors[]='Nome obrigatório';
    if (!$errors) {
        $sql = "UPDATE autores SET nome=:nome, nacionalidade=:nac, ano_nascimento=:ano WHERE id_autor=:id";
        $pdo->prepare($sql)->execute([':nome'=>$nome,':nac'=>$nac? $nac:null,':ano'=>$ano,':id'=>$id]);
        header("Location: read.php"); exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Editar Autor</title></head><body>
<h2>Editar Autor</h2>
<?php foreach($errors as $e) echo "<p style='color:red'>".htmlspecialchars($e)."</p>"; ?>
<form method="post">
 Nome: <input name="nome" value="<?php echo htmlspecialchars($autor['nome']); ?>" required><br>
 Nacionalidade: <input name="nacionalidade" value="<?php echo htmlspecialchars($autor['nacionalidade']); ?>"><br>
 Ano Nascimento: <input name="ano_nascimento" type="number" min="1000" max="<?php echo date('Y'); ?>" value="<?php echo $autor['ano_nascimento']; ?>"><br>
 <button>Salvar</button>
</form>
<p><a href="read.php">Voltar</a></p>
</body></html>

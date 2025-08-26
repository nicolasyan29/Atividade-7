<?php
require_once __DIR__ . '/../config.php';

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 8;
$offset = ($page - 1) * $perPage;

$q = trim($_GET['q'] ?? '');
$where = '';
$params = [];
if ($q !== '') {
    $where = 'WHERE nome LIKE :q';
    $params[':q'] = "%$q%";
}

$total = $pdo->prepare("SELECT COUNT(*) FROM autores $where");
$total->execute($params);
$totalRows = $total->fetchColumn();

$stmt = $pdo->prepare("SELECT * FROM autores $where ORDER BY nome LIMIT :lim OFFSET :off");
foreach ($params as $k=>$v) $stmt->bindValue($k,$v);
$stmt->bindValue(':lim',$perPage,PDO::PARAM_INT);
$stmt->bindValue(':off',$offset,PDO::PARAM_INT);
$stmt->execute();
$autores = $stmt->fetchAll();
$lastPage = ceil($totalRows / $perPage);
?>
<!doctype html><html><head><meta charset="utf-8"><title>Autores</title></head><body>
<h2>Autores</h2>
<p><a href="../index.php">Menu</a> | <a href="create.php">Novo Autor</a></p>

<form method="get">
  Buscar por nome: <input name="q" value="<?php echo htmlspecialchars($q); ?>">
  <button>Pesquisar</button>
</form>

<table border="1" cellpadding="6">
  <tr><th>ID</th><th>Nome</th><th>Nacionalidade</th><th>Ano</th><th>Ações</th></tr>
  <?php foreach($autores as $a): ?>
  <tr>
    <td><?php echo $a['id_autor']; ?></td>
    <td><?php echo htmlspecialchars($a['nome']); ?></td>
    <td><?php echo htmlspecialchars($a['nacionalidade']); ?></td>
    <td><?php echo $a['ano_nascimento']; ?></td>
    <td>
      <a href="update.php?id=<?php echo $a['id_autor']; ?>">Editar</a> |
      <a href="delete.php?id=<?php echo $a['id_autor']; ?>" onclick="return confirm('Excluir autor?')">Excluir</a>
    </td>
  </tr>
  <?php endforeach; ?>
</table>

<p>Pagina <?php echo $page; ?> de <?php echo max(1,$lastPage); ?></p>
<?php if($page>1): ?><a href="?page=<?php echo $page-1; ?>&q=<?php echo urlencode($q); ?>">Anterior</a><?php endif; ?>
<?php if($page<$lastPage): ?><a href="?page=<?php echo $page+1; ?>&q=<?php echo urlencode($q); ?>">Próxima</a><?php endif; ?>

</body></html>

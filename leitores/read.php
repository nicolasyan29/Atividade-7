<?php
require_once __DIR__ . '/../config.php';
$page = max(1,intval($_GET['page'] ?? 1));
$perPage = 8;
$offset = ($page-1)*$perPage;
$q = trim($_GET['q'] ?? '');

$where = ' WHERE 1=1 ';
$params = [];
if ($q!=='') { $where .= " AND (nome LIKE :q OR email LIKE :q) "; $params[':q']="%$q%"; }

$total = $pdo->prepare("SELECT COUNT(*) FROM leitores $where");
$total->execute($params);
$totalRows = $total->fetchColumn();

$sql = "SELECT * FROM leitores $where ORDER BY nome LIMIT :lim OFFSET :off";
$stmt = $pdo->prepare($sql);
foreach($params as $k=>$v) $stmt->bindValue($k,$v);
$stmt->bindValue(':lim',$perPage,PDO::PARAM_INT);
$stmt->bindValue(':off',$offset,PDO::PARAM_INT);
$stmt->execute();
$leitores = $stmt->fetchAll();
$lastPage = max(1,ceil($totalRows/$perPage));
?>
<!doctype html><html><head><meta charset="utf-8"><title>Leitores</title></head><body>
<h2>Leitores</h2>
<p><a href="../index.php">Menu</a> | <a href="create.php">Novo Leitor</a></p>
<form method="get">Pesquisar: <input name="q" value="<?php echo htmlspecialchars($q); ?>"><button>OK</button></form>
<table border="1" cellpadding="6">
<tr><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th></tr>
<?php foreach($leitores as $l): ?>
<tr>
 <td><?php echo $l['id_leitor']; ?></td>
 <td><?php echo htmlspecialchars($l['nome']); ?></td>
 <td><?php echo htmlspecialchars($l['email']); ?></td>
 <td><?php echo htmlspecialchars($l['telefone']); ?></td>
 <td><a href="update.php?id=<?php echo $l['id_leitor']; ?>">Editar</a> | <a href="delete.php?id=<?php echo $l['id_leitor']; ?>" onclick="return confirm('Excluir?')">Excluir</a> | <a href="../emprestimos/read.php?leitor=<?php echo $l['id_leitor']; ?>">Ver Empréstimos</a></td>
</tr>
<?php endforeach; ?>
</table>
<p>Pagina <?php echo $page; ?> de <?php echo $lastPage; ?></p>
<?php if($page>1): ?><a href="?page=<?php echo $page-1; ?>&q=<?php echo urlencode($q); ?>">Anterior</a><?php endif; ?>
<?php if($page<$lastPage): ?><a href="?page=<?php echo $page+1; ?>&q=<?php echo urlencode($q); ?>">Próxima</a><?php endif; ?>
</body></html>

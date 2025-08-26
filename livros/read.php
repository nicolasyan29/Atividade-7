<?php
require_once __DIR__ . '/../config.php';

$page = max(1,intval($_GET['page'] ?? 1));
$perPage = 8;
$offset = ($page-1)*$perPage;

$f_genero = trim($_GET['genero'] ?? '');
$f_autor = intval($_GET['autor'] ?? 0);
$f_ano = intval($_GET['ano'] ?? 0);

$autores = $pdo->query("SELECT id_autor, nome FROM autores ORDER BY nome")->fetchAll();

$where = ' WHERE 1=1 ';
$params = [];
if ($f_genero !== '') { $where .= " AND genero LIKE :genero "; $params[':genero'] = "%$f_genero%"; }
if ($f_autor > 0) { $where .= " AND l.id_autor = :autor "; $params[':autor'] = $f_autor; }
if ($f_ano > 0) { $where .= " AND l.ano_publicacao = :ano "; $params[':ano'] = $f_ano; }

$totalQ = $pdo->prepare("SELECT COUNT(*) FROM livros l $where");
$totalQ->execute($params);
$total = $totalQ->fetchColumn();

$sql = "SELECT l.*, a.nome as autor_nome
        FROM livros l
        JOIN autores a ON a.id_autor = l.id_autor
        $where
        ORDER BY l.titulo
        LIMIT :lim OFFSET :off";
$stmt = $pdo->prepare($sql);
foreach($params as $k=>$v) $stmt->bindValue($k,$v);
$stmt->bindValue(':lim',$perPage,PDO::PARAM_INT);
$stmt->bindValue(':off',$offset,PDO::PARAM_INT);
$stmt->execute();
$livros = $stmt->fetchAll();

$lastPage = max(1,ceil($total/$perPage));
?>
<!doctype html><html><head><meta charset="utf-8"><title>Livros</title></head><body>
<h2>Livros</h2>
<p><a href="../index.php">Menu</a> | <a href="create.php">Novo Livro</a></p>

<form method="get">
  Gênero: <input name="genero" value="<?php echo htmlspecialchars($f_genero); ?>">
  Autor:
  <select name="autor">
    <option value="0">Todos</option>
    <?php foreach($autores as $a): ?>
      <option value="<?php echo $a['id_autor']; ?>" <?php if($f_autor==$a['id_autor']) echo 'selected'; ?>><?php echo htmlspecialchars($a['nome']); ?></option>
    <?php endforeach; ?>
  </select>
  Ano: <input name="ano" type="number" value="<?php echo $f_ano?:''; ?>">
  <button>Filtrar</button>
</form>

<table border="1" cellpadding="6">
  <tr><th>ID</th><th>Título</th><th>Gênero</th><th>Ano</th><th>Autor</th><th>Ações</th></tr>
  <?php foreach($livros as $l): ?>
    <tr>
      <td><?php echo $l['id_livro']; ?></td>
      <td><?php echo htmlspecialchars($l['titulo']); ?></td>
      <td><?php echo htmlspecialchars($l['genero']); ?></td>
      <td><?php echo $l['ano_publicacao']; ?></td>
      <td><?php echo htmlspecialchars($l['autor_nome']); ?></td>
      <td>
        <a href="update.php?id=<?php echo $l['id_livro']; ?>">Editar</a> |
        <a href="delete.php?id=<?php echo $l['id_livro']; ?>" onclick="return confirm('Excluir livro?')">Excluir</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<p>Pagina <?php echo $page; ?> de <?php echo $lastPage; ?></p>
<?php if($page>1): ?><a href="?page=<?php echo $page-1; ?>&genero=<?php echo urlencode($f_genero); ?>&autor=<?php echo $f_autor; ?>&ano=<?php echo $f_ano; ?>">Anterior</a><?php endif; ?>
<?php if($page<$lastPage): ?><a href="?page=<?php echo $page+1; ?>&genero=<?php echo urlencode($f_genero); ?>&autor=<?php echo $f_autor; ?>&ano=<?php echo $f_ano; ?>">Próxima</a><?php endif; ?>

</body></html>

<?php
require_once __DIR__ . '/../config.php';

$filter = $_GET['filter'] ?? 'ativos';
$leitorFilter = intval($_GET['leitor'] ?? 0);

$leitores = $pdo->query("SELECT id_leitor, nome FROM leitores ORDER BY nome")->fetchAll();

$where = " WHERE 1=1 ";
$params = [];
if ($filter === 'ativos') $where .= " AND e.data_devolucao IS NULL ";
elseif ($filter === 'concluidos') $where .= " AND e.data_devolucao IS NOT NULL ";

if ($leitorFilter>0) { $where .= " AND e.id_leitor = :leitor "; $params[':leitor'] = $leitorFilter; }

$sql = "SELECT e.*, l.titulo as livro_titulo, le.nome as leitor_nome
        FROM emprestimos e
        JOIN livros l ON l.id_livro = e.id_livro
        JOIN leitores le ON le.id_leitor = e.id_leitor
        $where
        ORDER BY e.data_emprestimo DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$emprestimos = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Empréstimos</title></head><body>
<h2>Empréstimos</h2>
<p><a href="../index.php">Menu</a> | <a href="create.php">Novo Empréstimo</a></p>

<form method="get">
  Mostrar:
  <select name="filter">
    <option value="ativos" <?php if($filter=='ativos') echo 'selected'; ?>>Ativos</option>
    <option value="concluidos" <?php if($filter=='concluidos') echo 'selected'; ?>>Concluídos</option>
    <option value="todos" <?php if($filter=='todos') echo 'selected'; ?>>Todos</option>
  </select>
  Leitor:
  <select name="leitor">
    <option value="0">Todos</option>
    <?php foreach($leitores as $l): ?>
      <option value="<?php echo $l['id_leitor']; ?>" <?php if($leitorFilter==$l['id_leitor']) echo 'selected'; ?>><?php echo htmlspecialchars($l['nome']); ?></option>
    <?php endforeach; ?>
  </select>
  <button>Filtrar</button>
</form>

<table border="1" cellpadding="6">
<tr><th>ID</th><th>Livro</th><th>Leitor</th><th>Data Empréstimo</th><th>Data Devolução</th><th>Ações</th></tr>
<?php foreach($emprestimos as $e): ?>
<tr>
 <td><?php echo $e['id_emprestimo']; ?></td>
 <td><?php echo htmlspecialchars($e['livro_titulo']); ?></td>
 <td><?php echo htmlspecialchars($e['leitor_nome']); ?></td>
 <td><?php echo $e['data_emprestimo']; ?></td>
 <td><?php echo $e['data_devolucao'] ?? '-'; ?></td>
 <td>
   <a href="update.php?id=<?php echo $e['id_emprestimo']; ?>">Editar</a> |
   <a href="delete.php?id=<?php echo $e['id_emprestimo']; ?>" onclick="return confirm('Excluir registro?')">Excluir</a>
 </td>
</tr>
<?php endforeach; ?>
</table>
</body></html>

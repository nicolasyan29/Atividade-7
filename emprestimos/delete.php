<?php
require_once __DIR__ . '/../config.php';
$id = intval($_GET['id'] ?? 0);
if ($id) {
    $pdo->prepare("DELETE FROM emprestimos WHERE id_emprestimo = ?")->execute([$id]);
}
header("Location: read.php");
exit;
?>

<?php
require_once __DIR__ . '/../config.php';
$id = intval($_GET['id'] ?? 0);
if ($id) {
    try {
        $pdo->prepare("DELETE FROM leitores WHERE id_leitor = ?")->execute([$id]);
    } catch (Exception $e) {
        die("Não foi possível excluir o leitor. Verifique empréstimos. Erro: ".htmlspecialchars($e->getMessage()));
    }
}
header("Location: read.php");
exit;
?>

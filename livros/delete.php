<?php
require_once __DIR__ . '/../config.php';
$id = intval($_GET['id'] ?? 0);
if ($id) {
    try {
        $pdo->prepare("DELETE FROM livros WHERE id_livro = ?")->execute([$id]);
    } catch (Exception $e) {
        die("Não foi possível excluir o livro. Verifique relacionamentos. Erro: ".htmlspecialchars($e->getMessage()));
    }
}
header("Location: read.php");
exit;
?>

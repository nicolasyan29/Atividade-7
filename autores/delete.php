<?php
require_once __DIR__ . '/../config.php';
$id = intval($_GET['id'] ?? 0);
if ($id) {
    
    $stmt = $pdo->prepare("DELETE FROM autores WHERE id_autor = ?");
    try {
        $stmt->execute([$id]);
    } catch (Exception $e) {
        
        die("Não foi possível excluir — verifique se autor possui livros. Erro: " . htmlspecialchars($e->getMessage()));
    }
}
header("Location: read.php");
exit;
?>

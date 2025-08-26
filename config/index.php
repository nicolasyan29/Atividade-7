<?php

?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <title>Biblioteca - Menu</title>
  <style>
    body{font-family: Arial; padding:20px; background:#f4f4f4}
    .wrap{max-width:900px;margin:0 auto;background:#fff;padding:20px;border-radius:8px;}
    h1{margin-top:0}
    .grid{display:flex;gap:12px;flex-wrap:wrap}
    .card{flex:1 1 200px;padding:12px;border:1px solid #ddd;border-radius:6px;text-align:center;background:#fafafa}
    a{display:inline-block;margin-top:8px;padding:8px 12px;background:#1976d2;color:#fff;text-decoration:none;border-radius:4px}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Biblioteca — Menu</h1>
    <p>CRUD para Autores, Livros, Leitores e Empréstimos (PHP + MySQL).</p>

    <div class="grid">
      <div class="card">
        <h3>Autores</h3>
        <a href="autores/read.php">Gerenciar Autores</a>
      </div>

      <div class="card">
        <h3>Livros</h3>
        <a href="livros/read.php">Gerenciar Livros</a>
      </div>

      <div class="card">
        <h3>Leitores</h3>
        <a href="leitores/read.php">Gerenciar Leitores</a>
      </div>

      <div class="card">
        <h3>Empréstimos</h3>
        <a href="emprestimos/read.php">Gerenciar Empréstimos</a>
      </div>
    </div>

    <hr>
    <p><small>Projeto escolar — altere o arquivo <code>config.php</code> se necessário.</small></p>
  </div>
</body>
</html>

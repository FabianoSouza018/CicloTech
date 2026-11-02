<?php
session_start();

require_once __DIR__ . '/../includes/config.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($usuario === ADMIN_USUARIO && $senha === ADMIN_SENHA) {

        session_regenerate_id(true);

        $_SESSION['admin_logado'] = true;

        header('Location: index.php');
        exit;
    } else {
        $erro = 'Usuário ou senha incorretos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Administrativo - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-ciclotech">
        <div class="container">
            <a class="navbar-brand" href="../index.php">♻ CicloTech</a>
        </div>
    </nav>

    <main class="container d-flex justify-content-center align-items-center" style="min-height: 80vh;">

        <div class="card shadow-sm p-4" style="width: 100%; max-width: 420px;">

            <div class="text-center mb-4">
                <h3 class="text-success fw-bold">Área administrativa</h3>
                <p class="text-muted">Entre com suas credenciais para continuar.</p>
            </div>

            <?php if ($erro): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Usuário</label>
                    <input type="text" name="usuario" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-success w-100">
                    Entrar
                </button>

            </form>

            <div class="text-center mt-3">
                <a href="../index.php" class="text-decoration-none text-muted">
                    Voltar ao site institucional
                </a>
            </div>

        </div>

    </main>

</body>

</html>
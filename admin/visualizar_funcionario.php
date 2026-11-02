<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$funcionarios = lerJson('funcionarios.json');

$codigo = $_GET['codigo'] ?? '';

$funcionarioEncontrado = null;

// Procura o funcionário pelo código
foreach ($funcionarios as $funcionario) {

    if (($funcionario['codigo'] ?? '') === $codigo) {

        $funcionarioEncontrado = $funcionario;
        break;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Visualizar Funcionário - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-ciclotech">
        <div class="container">

            <a class="navbar-brand" href="index.php">♻ CicloTech</a>

            <a href="listar_funcionarios.php" class="btn btn-outline-light btn-sm">
                Voltar à listagem
            </a>

        </div>
    </nav>

    <main class="container py-5">

        <?php if ($funcionarioEncontrado === null): ?>

            <div class="alert alert-warning">
                Funcionário não encontrado.
            </div>

            <a href="listar_funcionarios.php" class="btn btn-secondary">
                Voltar
            </a>

        <?php else: ?>

            <div class="mb-4">

                <h2 class="fw-bold">Dados do funcionário</h2>

                <p class="text-secondary">
                    Consulte as informações do colaborador cadastrado.
                </p>

            </div>

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <div class="row g-4">

                        <div class="col-md-6">

                            <label class="text-secondary small">
                                Código interno
                            </label>

                            <p class="fw-bold">
                                <?php echo htmlspecialchars($funcionarioEncontrado['codigo'] ?? ''); ?>
                            </p>

                        </div>

                        <div class="col-md-6">

                            <label class="text-secondary small">
                                Nome completo
                            </label>

                            <p class="fw-bold">
                                <?php echo htmlspecialchars($funcionarioEncontrado['nome'] ?? ''); ?>
                            </p>

                        </div>

                        <div class="col-md-6">

                            <label class="text-secondary small">
                                Cargo / função
                            </label>

                            <p>
                                <?php echo htmlspecialchars($funcionarioEncontrado['cargo'] ?? ''); ?>
                            </p>

                        </div>

                        <div class="col-md-6">

                            <label class="text-secondary small">
                                Telefone
                            </label>

                            <p>
                                <?php echo htmlspecialchars($funcionarioEncontrado['telefone'] ?? 'Não informado'); ?>
                            </p>

                        </div>

                        <div class="col-md-6">

                            <label class="text-secondary small">
                                E-mail
                            </label>

                            <p>
                                <?php echo htmlspecialchars($funcionarioEncontrado['email'] ?? 'Não informado'); ?>
                            </p>

                        </div>

                        <div class="col-md-6">

                            <label class="text-secondary small">
                                Data de cadastro
                            </label>

                            <p>
                                <?php echo htmlspecialchars($funcionarioEncontrado['data_cadastro'] ?? 'Não informada'); ?>
                            </p>

                        </div>

                        <div class="col-md-6">

                            <label class="text-secondary small">
                                Estado do cadastro
                            </label>

                            <p>

                                <?php if (($funcionarioEncontrado['ativo'] ?? false) === true): ?>

                                    <span class="badge text-bg-success">
                                        Ativo
                                    </span>

                                <?php else: ?>

                                    <span class="badge text-bg-secondary">
                                        Inativo
                                    </span>

                                <?php endif; ?>

                            </p>

                        </div>

                    </div>

                    <hr>

                    <a href="editar_funcionario.php?codigo=<?php echo urlencode($funcionarioEncontrado['codigo']); ?>"
                        class="btn btn-success">
                        Editar
                    </a>

                    <a href="listar_funcionarios.php"
                        class="btn btn-outline-secondary">
                        Voltar à listagem
                    </a>

                </div>

            </div>

        <?php endif; ?>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
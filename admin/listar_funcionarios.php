<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$funcionarios = lerJson('funcionarios.json');

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Funcionários - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-ciclotech">
        <div class="container">

            <a class="navbar-brand" href="index.php">
                ♻ CicloTech
            </a>

            <a href="index.php" class="btn btn-outline-light btn-sm">
                Voltar ao menu administrativo
            </a>

        </div>
    </nav>

    <main class="container py-5">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

            <div>
                <h2 class="fw-bold">Funcionários cadastrados</h2>

                <p class="text-secondary mb-0">
                    Consulte os colaboradores registrados no sistema.
                </p>
            </div>

            <a href="funcionarios.php" class="btn btn-success">
                + Novo funcionário
            </a>

        </div>

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <?php if (empty($funcionarios)): ?>

                    <div class="text-center py-5">

                        <h5>Nenhum funcionário cadastrado.</h5>

                        <p class="text-secondary">
                            Cadastre o primeiro funcionário para começar.
                        </p>

                        <a href="funcionarios.php" class="btn btn-success">
                            Cadastrar funcionário
                        </a>

                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table table-hover align-middle"
                            style="min-width: 850px;">

                            <thead class="table-light">

                                <tr>
                                    <th>Código</th>
                                    <th>Nome</th>
                                    <th>Cargo / Função</th>
                                    <th>Telefone</th>
                                    <th>E-mail</th>
                                    <th>Estado</th>
                                    <th>Ações</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($funcionarios as $funcionario): ?>

                                    <tr>

                                        <td class="text-nowrap">
                                            <strong>
                                                <?php echo htmlspecialchars($funcionario['codigo'] ?? ''); ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($funcionario['nome'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($funcionario['cargo'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($funcionario['telefone'] ?? ''); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($funcionario['email'] ?? ''); ?>
                                        </td>

                                        <td class="text-nowrap">

                                            <?php if (($funcionario['ativo'] ?? false) === true): ?>

                                                <span class="badge text-bg-success">
                                                    Ativo
                                                </span>

                                            <?php else: ?>

                                                <span class="badge text-bg-secondary">
                                                    Inativo
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td class="text-nowrap">

                                            <a href="visualizar_funcionario.php?codigo=<?php echo urlencode($funcionario['codigo']); ?>"
                                                class="btn btn-success btn-sm btn-visualizar">
                                                Visualizar
                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
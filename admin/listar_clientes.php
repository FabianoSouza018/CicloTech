<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$clientes = lerJson('clientes.json');

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Clientes - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .tabela-clientes {
            min-width: 950px;
        }

        .tabela-clientes th {
            white-space: nowrap;
        }

        .btn-padrao {
            min-width: 100px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-ciclotech">
        <div class="container">

            <a class="navbar-brand" href="index.php">CicloTech</a>

            <a href="index.php" class="btn btn-outline-light btn-sm">
                Voltar ao menu administrativo
            </a>

        </div>
    </nav>

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h2>Clientes cadastrados</h2>
                <p class="text-muted mb-0">
                    Consulte e gerencie os clientes da CicloTech.
                </p>
            </div>

            <a href="clientes.php" class="btn btn-success">
                Cadastrar cliente
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle tabela-clientes">

                        <thead class="table-light">

                            <tr>
                                <th>Código</th>
                                <th>Nome / Razão social</th>
                                <th>CPF / CNPJ</th>
                                <th>Telefone</th>
                                <th>E-mail</th>
                                <th>Situação</th>
                                <th>Ações</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (empty($clientes)): ?>

                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Nenhum cliente cadastrado.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($clientes as $cliente): ?>

                                    <tr>

                                        <td>
                                            <?php echo htmlspecialchars($cliente['codigo']); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($cliente['nome']); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($cliente['documento']); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($cliente['telefone']); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($cliente['email']); ?>
                                        </td>

                                        <td>

                                            <?php if ($cliente['ativo']): ?>

                                                <span class="badge bg-success">
                                                    Ativo
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-secondary">
                                                    Inativo
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <a
                                                href="visualizar_cliente.php?codigo=<?php echo urlencode($cliente['codigo']); ?>"
                                                class="btn btn-success btn-sm btn-visualizar">
                                                Visualizar
                                            </a>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
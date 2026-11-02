<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$manutencoes = lerJson('manutencoes.json');
$clientes = lerJson('clientes.json');
$equipamentos = lerJson('equipamentos.json');
$funcionarios = lerJson('funcionarios.json');

function buscarRegistro($registros, $codigo)
{
    foreach ($registros as $registro) {
        if (($registro['codigo'] ?? '') === $codigo) {
            return $registro;
        }
    }

    return null;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manutenções - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .tabela-manutencoes {
            min-width: 850px;
        }

        .tabela-manutencoes th {
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
                <h2>Manutenções cadastradas</h2>
                <p class="text-muted mb-0">
                    Consulte os serviços registrados na CicloTech.
                </p>
            </div>

            <a href="manutencao.php" class="btn btn-success">
                Registrar manutenção
            </a>

        </div>

        <div class="card shadow-sm">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle tabela-manutencoes">

                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Cliente</th>
                                <th>Equipamento</th>
                                <th>Técnico</th>
                                <th>Abertura</th>
                                <th>Situação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (empty($manutencoes)): ?>

                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Nenhuma manutenção cadastrada.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($manutencoes as $manutencao): ?>

                                    <?php
                                    $cliente = !empty($manutencao['codigo_cliente'])
                                        ? buscarRegistro($clientes, $manutencao['codigo_cliente'])
                                        : null;
                                    $equipamento = buscarRegistro($equipamentos, $manutencao['codigo_equipamento']);
                                    $tecnico = buscarRegistro($funcionarios, $manutencao['codigo_funcionario']);

                                    $status = $manutencao['status'] ?? 'Aguardando';

                                    $cores = [
                                        'Aguardando' => 'secondary',
                                        'Em análise' => 'info',
                                        'Em manutenção' => 'warning',
                                        'Concluído' => 'success'
                                    ];

                                    $corStatus = $cores[$status] ?? 'secondary';
                                    ?>

                                    <tr>

                                        <td>
                                            <?php echo htmlspecialchars($manutencao['codigo']); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($cliente['nome'] ?? 'Não se aplica (equipamento doado)'); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($manutencao['codigo_equipamento']); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($tecnico['nome'] ?? 'Não encontrado'); ?>
                                        </td>

                                        <td>
                                            <?php
                                            echo date(
                                                'd/m/Y H:i',
                                                strtotime($manutencao['data_abertura'])
                                            );
                                            ?>
                                        </td>

                                        <td>
                                            <span class="badge bg-<?php echo $corStatus; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <a
                                                href="visualizar_manutencao.php?codigo=<?php echo urlencode($manutencao['codigo']); ?>"
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
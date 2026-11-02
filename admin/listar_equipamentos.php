<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$equipamentos = lerJson('equipamentos.json');
$clientes = lerJson('clientes.json');

$resultado = $_GET['resultado'] ?? '';

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Equipamentos - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-ciclotech">
        <div class="container">

            <a class="navbar-brand" href="index.php">♻ CicloTech</a>

            <a href="index.php" class="btn btn-outline-light btn-sm">
                Voltar ao menu administrativo
            </a>

        </div>
    </nav>

    <main class="container py-5">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

            <div>
                <h2 class="fw-bold">Equipamentos cadastrados</h2>

                <p class="text-secondary mb-0">
                    Consulte os equipamentos registrados.
                </p>
            </div>

            <a href="equipamentos.php" class="btn btn-success">
                + Novo equipamento
            </a>

        </div>

        <?php if ($resultado === 'desativado'): ?>

            <div class="alert alert-success">
                Equipamento desativado com sucesso.
            </div>

        <?php elseif ($resultado === 'erro'): ?>

            <div class="alert alert-danger">
                Não foi possível salvar a desativação.
            </div>

        <?php elseif ($resultado === 'nao_encontrado'): ?>

            <div class="alert alert-warning">
                Equipamento não encontrado.
            </div>

        <?php elseif ($resultado === 'ja_inativo'): ?>

            <div class="alert alert-info">
                Esse equipamento já estava desativado.
            </div>

        <?php endif; ?>

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <!-- Legenda dos códigos -->
                <div class="legenda-equipamentos mb-4">

                    <h6 class="fw-bold mb-3">Legenda dos códigos:</h6>

                    <div class="d-flex flex-wrap gap-3">

                        <div class="item-legenda">
                            <span class="codigo-prefixo prefixo-ed">ED</span>
                            <span>Equipamento recebido/doado</span>
                        </div>

                        <div class="item-legenda">
                            <span class="codigo-prefixo prefixo-cm">CM</span>
                            <span>Equipamento de cliente (manutenção)</span>
                        </div>

                        <div class="item-legenda">
                            <span class="codigo-prefixo prefixo-ci">CI</span>
                            <span>Uso interno da CicloTech</span>
                        </div>

                    </div>

                </div>

                <?php if (empty($equipamentos)): ?>

                    <div class="text-center py-5">

                        <h5>Nenhum equipamento cadastrado.</h5>

                        <p class="text-secondary">
                            Cadastre o primeiro equipamento para começar.
                        </p>

                        <a href="equipamentos.php" class="btn btn-success">
                            Cadastrar equipamento
                        </a>

                    </div>

                <?php else: ?>

                    <div class="table-responsive">

                        <table class="table table-hover align-middle"
                            style="min-width: 1000px;">

                            <thead class="table-light">

                                <tr>
                                    <th class="text-nowrap">Código</th>
                                    <th class="text-nowrap">Equipamento</th>
                                    <th class="text-nowrap">Marca / Modelo</th>
                                    <th class="text-nowrap">Cliente</th>
                                    <th class="text-nowrap">Responsável</th>
                                    <th class="text-nowrap">Situação</th>
                                    <th class="text-nowrap">Estado</th>
                                    <th class="text-nowrap">Ações</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($equipamentos as $equipamento): ?>

                                    <?php

                                    // Procura o cliente vinculado ao equipamento
                                    $nomeCliente = '—';

                                    if (!empty($equipamento['codigo_cliente'])) {

                                        foreach ($clientes as $cliente) {

                                            if (
                                                ($cliente['codigo'] ?? '') === $equipamento['codigo_cliente']
                                            ) {

                                                $nomeCliente = $cliente['nome'] ?? '—';
                                                break;
                                            }
                                        }
                                    }

                                    // Identifica o prefixo do código
                                    $codigoEquipamento = $equipamento['codigo'] ?? '';
                                    $prefixo = strtoupper(explode('-', $codigoEquipamento)[0]);

                                    $classesPrefixos = [
                                        'ED' => 'prefixo-ed',
                                        'CM' => 'prefixo-cm',
                                        'CI' => 'prefixo-ci'
                                    ];

                                    $classePrefixo = $classesPrefixos[$prefixo] ?? 'prefixo-outro';

                                    ?>

                                    <tr>

                                        <td class="text-nowrap">
                                            <span class="codigo-prefixo <?php echo $classePrefixo; ?>">
                                                <?php echo htmlspecialchars($codigoEquipamento); ?>
                                            </span>
                                        </td>

                                        <td class="text-nowrap">
                                            <?php echo htmlspecialchars($equipamento['tipo'] ?? ''); ?>
                                        </td>

                                        <td class="text-nowrap">

                                            <?php
                                            echo htmlspecialchars(
                                                ($equipamento['marca'] ?? '') . ' ' .
                                                    ($equipamento['modelo'] ?? '')
                                            );
                                            ?>

                                        </td>

                                        <td class="text-nowrap">
                                            <?php echo htmlspecialchars($nomeCliente); ?>
                                        </td>

                                        <td class="text-nowrap">
                                            <?php echo htmlspecialchars($equipamento['responsavel'] ?? ''); ?>
                                        </td>

                                        <td class="text-nowrap">
                                            <?php echo htmlspecialchars($equipamento['status'] ?? ''); ?>
                                        </td>

                                        <td class="text-nowrap">

                                            <?php if (($equipamento['ativo'] ?? false) === true): ?>

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

                                            <a href="visualizar_equipamento.php?codigo=<?php echo urlencode($codigoEquipamento); ?>"
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
<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$equipamentos = lerJson('equipamentos.json');
$clientes = lerJson('clientes.json');

$codigo = $_GET['codigo'] ?? '';
$resultado = $_GET['resultado'] ?? '';

$equipamentoEncontrado = null;
$clienteEncontrado = null;

// Procura o equipamento pelo código
foreach ($equipamentos as $equipamento) {

    if (($equipamento['codigo'] ?? '') === $codigo) {
        $equipamentoEncontrado = $equipamento;
        break;
    }
}

// Procura o cliente vinculado
if (
    $equipamentoEncontrado !== null &&
    ($equipamentoEncontrado['prefixo'] ?? '') === 'CM' &&
    !empty($equipamentoEncontrado['codigo_cliente'])
) {

    foreach ($clientes as $cliente) {

        if (($cliente['codigo'] ?? '') === $equipamentoEncontrado['codigo_cliente']) {
            $clienteEncontrado = $cliente;
            break;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detalhes do Equipamento - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-ciclotech">
        <div class="container">

            <a class="navbar-brand" href="index.php">♻ CicloTech</a>

            <a href="listar_equipamentos.php" class="btn btn-outline-light btn-sm">
                Voltar à listagem
            </a>

        </div>
    </nav>

    <main class="container py-5">

        <?php if ($equipamentoEncontrado === null): ?>

            <div class="alert alert-warning">
                Equipamento não encontrado.
            </div>

            <a href="listar_equipamentos.php" class="btn btn-secondary">
                Voltar
            </a>

        <?php else: ?>

            <div class="mb-4">

                <h2 class="fw-bold">Detalhes do equipamento</h2>

                <p class="text-secondary">
                    Consulte as informações completas do registro.
                </p>

            </div>

            <?php if ($resultado === 'alterado'): ?>

                <div class="alert alert-success">
                    Situação do equipamento alterada com sucesso.
                </div>

            <?php elseif ($resultado === 'erro'): ?>

                <div class="alert alert-danger">
                    Não foi possível alterar a situação do equipamento.
                </div>

            <?php elseif ($resultado === 'ja_alterado'): ?>

                <div class="alert alert-info">
                    O equipamento já estava nessa situação.
                </div>

            <?php endif; ?>

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h4 class="fw-bold mb-1">
                                <?php echo htmlspecialchars($equipamentoEncontrado['codigo'] ?? ''); ?>
                            </h4>

                            <span class="text-secondary">
                                <?php echo htmlspecialchars($equipamentoEncontrado['tipo'] ?? ''); ?>
                            </span>

                        </div>

                        <?php if (($equipamentoEncontrado['ativo'] ?? false) === true): ?>

                            <span class="badge text-bg-success">Ativo</span>

                        <?php else: ?>

                            <span class="badge text-bg-secondary">Inativo</span>

                        <?php endif; ?>

                    </div>

                    <hr>

                    <div class="row g-4">

                        <div class="col-md-6">
                            <label class="text-secondary small">Categoria</label>
                            <p class="fw-semibold">
                                <?php echo htmlspecialchars($equipamentoEncontrado['prefixo'] ?? ''); ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-secondary small">Tipo</label>
                            <p class="fw-semibold">
                                <?php echo htmlspecialchars($equipamentoEncontrado['tipo'] ?? ''); ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-secondary small">Marca</label>
                            <p class="fw-semibold">
                                <?php echo htmlspecialchars($equipamentoEncontrado['marca'] ?? ''); ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-secondary small">Modelo</label>
                            <p class="fw-semibold">
                                <?php echo htmlspecialchars($equipamentoEncontrado['modelo'] ?? ''); ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-secondary small">Número de série / patrimônio</label>
                            <p class="fw-semibold">
                                <?php echo htmlspecialchars($equipamentoEncontrado['identificacao'] ?? 'Não informado'); ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-secondary small">Responsável</label>
                            <p class="fw-semibold">
                                <?php echo htmlspecialchars($equipamentoEncontrado['responsavel'] ?? ''); ?>
                            </p>
                        </div>

                        <?php if (($equipamentoEncontrado['prefixo'] ?? '') === 'CM'): ?>

                            <div class="col-md-6">
                                <label class="text-secondary small">Código do cliente</label>
                                <p class="fw-semibold">
                                    <?php echo htmlspecialchars($equipamentoEncontrado['codigo_cliente'] ?? 'Não vinculado'); ?>
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="text-secondary small">Cliente vinculado</label>
                                <p class="fw-semibold">

                                    <?php if ($clienteEncontrado !== null): ?>

                                        <?php echo htmlspecialchars($clienteEncontrado['nome'] ?? ''); ?>

                                    <?php else: ?>

                                        Cadastro de cliente não localizado.

                                    <?php endif; ?>

                                </p>
                            </div>

                        <?php endif; ?>

                        <div class="col-md-6">
                            <label class="text-secondary small">Situação</label>
                            <p class="fw-semibold">
                                <?php echo htmlspecialchars($equipamentoEncontrado['status'] ?? ''); ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <label class="text-secondary small">Data do cadastro</label>
                            <p class="fw-semibold">
                                <?php echo htmlspecialchars($equipamentoEncontrado['data_cadastro'] ?? ''); ?>
                            </p>
                        </div>

                    </div>

                    <hr>

                    <div class="mt-4">

                        <h5 class="fw-bold mb-3">Gerenciamento do equipamento</h5>

                        <?php if (($equipamentoEncontrado['ativo'] ?? false) === true): ?>

                            <div class="d-flex flex-wrap gap-2">

                                <a href="editar_equipamento.php?codigo=<?php echo urlencode($codigo); ?>"
                                    class="btn btn-outline-success">
                                    Editar equipamento
                                </a>

                                <form method="POST"
                                    action="alterar_status_equipamento.php"
                                    onsubmit="return confirm('Deseja realmente desativar este equipamento?');">

                                    <input type="hidden" name="codigo"
                                        value="<?php echo htmlspecialchars($codigo); ?>">

                                    <input type="hidden" name="acao" value="desativar">

                                    <button type="submit" class="btn btn-outline-danger">
                                        Desativar equipamento
                                    </button>

                                </form>

                            </div>

                        <?php else: ?>

                            <p class="text-secondary">
                                Este equipamento está inativo. Para voltar a utilizá-lo no sistema, reative o cadastro.
                            </p>

                            <form method="POST"
                                action="alterar_status_equipamento.php"
                                onsubmit="return confirm('Deseja realmente reativar este equipamento?');">

                                <input type="hidden" name="codigo"
                                    value="<?php echo htmlspecialchars($codigo); ?>">

                                <input type="hidden" name="acao" value="reativar">

                                <button type="submit" class="btn btn-success">
                                    Reativar equipamento
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                    <hr>

                    <a href="listar_equipamentos.php" class="btn btn-outline-secondary">
                        Voltar à listagem
                    </a>

                </div>

            </div>

        <?php endif; ?>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
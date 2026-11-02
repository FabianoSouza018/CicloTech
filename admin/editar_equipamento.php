<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$equipamentos = lerJson('equipamentos.json');
$clientes = lerJson('clientes.json');
$funcionarios = lerJson('funcionarios.json');

$codigo = $_GET['codigo'] ?? $_POST['codigo'] ?? '';

$indiceEncontrado = null;
$mensagem = '';
$tipoMensagem = '';

// Localiza o equipamento
foreach ($equipamentos as $indice => $equipamento) {
    if (($equipamento['codigo'] ?? '') === $codigo) {
        $indiceEncontrado = $indice;
        break;
    }
}

// Salva as alterações
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $indiceEncontrado !== null) {

    $tipo = trim($_POST['tipo'] ?? '');
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $identificacao = trim($_POST['identificacao'] ?? '');
    $semIdentificacao = isset($_POST['sem_identificacao']);

    $codigoFuncionario = $_POST['codigo_funcionario'] ?? '';
    $codigoCliente = $_POST['codigo_cliente'] ?? '';
    $status = $_POST['status'] ?? '';

    $statusPermitidos = [
        'Recebido',
        'Em avaliação',
        'Aguardando manutenção',
        'Em manutenção',
        'Em uso'
    ];

    $prefixoAtual = $equipamentos[$indiceEncontrado]['prefixo'] ?? '';

    $clienteSelecionado = null;
    $funcionarioSelecionado = null;

    // Procura o funcionário selecionado
    foreach ($funcionarios as $funcionario) {
        if (
            ($funcionario['codigo'] ?? '') === $codigoFuncionario &&
            ($funcionario['ativo'] ?? false) === true
        ) {
            $funcionarioSelecionado = $funcionario;
            break;
        }
    }

    // Procura o cliente selecionado
    if ($prefixoAtual === 'CM') {

        foreach ($clientes as $cliente) {
            if (
                ($cliente['codigo'] ?? '') === $codigoCliente &&
                ($cliente['ativo'] ?? false) === true
            ) {
                $clienteSelecionado = $cliente;
                break;
            }
        }
    }

    // Valida os campos
    if (
        $tipo === '' ||
        $marca === '' ||
        $modelo === '' ||
        $funcionarioSelecionado === null ||
        !in_array($status, $statusPermitidos, true) ||
        (!$semIdentificacao && $identificacao === '') ||
        ($prefixoAtual === 'CM' && $clienteSelecionado === null)
    ) {

        $mensagem = 'Preencha os campos obrigatórios corretamente.';
        $tipoMensagem = 'danger';
    } else {

        // Atualiza os campos editáveis
        $equipamentos[$indiceEncontrado]['tipo'] = $tipo;
        $equipamentos[$indiceEncontrado]['marca'] = $marca;
        $equipamentos[$indiceEncontrado]['modelo'] = $modelo;

        $equipamentos[$indiceEncontrado]['identificacao'] =
            $semIdentificacao ? null : $identificacao;

        // Atualiza responsável e código do funcionário
        $equipamentos[$indiceEncontrado]['responsavel'] =
            $funcionarioSelecionado['nome'];

        $equipamentos[$indiceEncontrado]['codigo_funcionario'] =
            $funcionarioSelecionado['codigo'];

        $equipamentos[$indiceEncontrado]['status'] = $status;

        // Atualiza o vínculo do cliente somente para equipamentos CM
        if ($prefixoAtual === 'CM') {
            $equipamentos[$indiceEncontrado]['codigo_cliente'] = $codigoCliente;
        }

        // Sincroniza a manutenção mais recente quando o equipamento é de cliente (CM).
        // Situações sem correspondência direta não alteram a manutenção.
        $manutencoes = [];
        $sincronizarManutencao = false;

        if ($prefixoAtual === 'CM') {
            $manutencoes = lerJson('manutencoes.json');

            $indiceManutencao = null;

            // Percorre do fim para o início para localizar a manutenção mais recente.
            for ($i = count($manutencoes) - 1; $i >= 0; $i--) {
                if (($manutencoes[$i]['codigo_equipamento'] ?? '') === $codigo) {
                    $indiceManutencao = $i;
                    break;
                }
            }

            if ($indiceManutencao !== null) {
                $statusManutencao = [
                    'Aguardando manutenção' => 'Aguardando',
                    'Em manutenção' => 'Em manutenção',
                    'Disponível' => 'Concluído'
                ];

                if (isset($statusManutencao[$status])) {
                    $manutencoes[$indiceManutencao]['status'] = $statusManutencao[$status];
                    $sincronizarManutencao = true;
                }
            }
        }

        $salvouEquipamento = salvarJson('equipamentos.json', $equipamentos);
        $salvouManutencao = true;

        if ($salvouEquipamento && $sincronizarManutencao) {
            $salvouManutencao = salvarJson('manutencoes.json', $manutencoes);
        }

        if ($salvouEquipamento && $salvouManutencao) {

            header('Location: visualizar_equipamento.php?codigo=' . urlencode($codigo) . '&editado=1');
            exit;
        } else {

            $mensagem = 'Não foi possível salvar todas as alterações. Verifique os arquivos JSON.';
            $tipoMensagem = 'danger';
        }
    }
}

$equipamentoAtual = $indiceEncontrado !== null
    ? $equipamentos[$indiceEncontrado]
    : null;

$codigoClienteAtual = $_POST['codigo_cliente']
    ?? ($equipamentoAtual['codigo_cliente'] ?? '');

// Identifica o funcionário atual
$codigoFuncionarioAtual = $_POST['codigo_funcionario']
    ?? ($equipamentoAtual['codigo_funcionario'] ?? '');

// Compatibilidade com equipamentos cadastrados antes da inclusão
// do código do funcionário
if ($codigoFuncionarioAtual === '' && $equipamentoAtual !== null) {

    foreach ($funcionarios as $funcionario) {

        if (
            ($funcionario['nome'] ?? '') === ($equipamentoAtual['responsavel'] ?? '') &&
            ($funcionario['ativo'] ?? false) === true
        ) {
            $codigoFuncionarioAtual = $funcionario['codigo'];
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

    <title>Editar Equipamento - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-ciclotech">

        <div class="container">

            <a class="navbar-brand" href="index.php">♻ CicloTech</a>

            <a href="visualizar_equipamento.php?codigo=<?php echo urlencode($codigo); ?>" class="btn btn-outline-light btn-sm">
                Voltar ao registro
            </a>

        </div>

    </nav>

    <main class="container py-5">

        <?php if ($equipamentoAtual === null): ?>

            <div class="alert alert-warning">
                Equipamento não encontrado.
            </div>

            <a href="listar_equipamentos.php" class="btn btn-secondary">
                Voltar
            </a>

        <?php else: ?>

            <div class="mb-4">

                <h2 class="fw-bold">Editar equipamento</h2>

                <p class="text-secondary">
                    Alterando o registro <?php echo htmlspecialchars($codigo); ?>
                </p>

            </div>

            <?php if ($mensagem !== ''): ?>

                <div class="alert alert-<?php echo $tipoMensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>

            <?php endif; ?>

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <form method="POST">

                        <input type="hidden" name="codigo"
                            value="<?php echo htmlspecialchars($codigo); ?>">

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label">Código interno</label>

                                <input type="text" class="form-control"
                                    value="<?php echo htmlspecialchars($codigo); ?>"
                                    disabled>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Tipo de equipamento</label>

                                <select name="tipo" class="form-select" required>

                                    <?php
                                    $tipos = ['Computador', 'Notebook', 'Monitor', 'Impressora', 'Outro'];

                                    foreach ($tipos as $tipo):
                                    ?>

                                        <option value="<?php echo htmlspecialchars($tipo); ?>"
                                            <?php echo ($equipamentoAtual['tipo'] ?? '') === $tipo ? 'selected' : ''; ?>>

                                            <?php echo htmlspecialchars($tipo); ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Marca</label>

                                <input type="text" name="marca" class="form-control"
                                    value="<?php echo htmlspecialchars($equipamentoAtual['marca'] ?? ''); ?>"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Modelo</label>

                                <input type="text" name="modelo" class="form-control"
                                    value="<?php echo htmlspecialchars($equipamentoAtual['modelo'] ?? ''); ?>"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Número de série / patrimônio</label>

                                <input type="text" name="identificacao" class="form-control"
                                    value="<?php echo htmlspecialchars($equipamentoAtual['identificacao'] ?? ''); ?>">

                                <div class="form-check mt-2">

                                    <input type="checkbox" name="sem_identificacao"
                                        value="1" class="form-check-input"
                                        id="semIdentificacao"
                                        <?php echo empty($equipamentoAtual['identificacao']) ? 'checked' : ''; ?>>

                                    <label class="form-check-label" for="semIdentificacao">
                                        Equipamento sem identificação externa
                                    </label>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Responsável pelo equipamento</label>

                                <select name="codigo_funcionario" class="form-select" required>

                                    <option value="">Selecione um funcionário</option>

                                    <?php foreach ($funcionarios as $funcionario): ?>

                                        <?php if (($funcionario['ativo'] ?? false) === true): ?>

                                            <option
                                                value="<?php echo htmlspecialchars($funcionario['codigo']); ?>"
                                                <?php echo $codigoFuncionarioAtual === $funcionario['codigo'] ? 'selected' : ''; ?>>

                                                <?php echo htmlspecialchars(
                                                    $funcionario['codigo'] . ' - ' . $funcionario['nome']
                                                ); ?>

                                            </option>

                                        <?php endif; ?>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <?php if (($equipamentoAtual['prefixo'] ?? '') === 'CM'): ?>

                                <div class="col-md-6">

                                    <label class="form-label">Cliente vinculado</label>

                                    <select name="codigo_cliente" id="codigoCliente" class="form-select" required>

                                        <option value="">Selecione um cliente</option>

                                        <?php foreach ($clientes as $cliente): ?>

                                            <?php if (($cliente['ativo'] ?? false) === true): ?>

                                                <option
                                                    value="<?php echo htmlspecialchars($cliente['codigo']); ?>"
                                                    <?php echo $codigoClienteAtual === $cliente['codigo'] ? 'selected' : ''; ?>>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $cliente['codigo'] . ' - ' . $cliente['nome']
                                                    );
                                                    ?>

                                                </option>

                                            <?php endif; ?>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                            <?php endif; ?>

                            <div class="col-md-6">

                                <label class="form-label">Situação</label>

                                <select name="status" class="form-select" required>

                                    <?php
                                    $statusOpcoes = [
                                        'Recebido',
                                        'Em avaliação',
                                        'Aguardando manutenção',
                                        'Em manutenção',
                                        'Em uso'
                                    ];

                                    foreach ($statusOpcoes as $opcao):
                                    ?>

                                        <option value="<?php echo htmlspecialchars($opcao); ?>"
                                            <?php echo ($equipamentoAtual['status'] ?? '') === $opcao ? 'selected' : ''; ?>>

                                            <?php echo htmlspecialchars($opcao); ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </div>

                        <div class="mt-4">

                            <button type="submit" class="btn btn-success">
                                Salvar alterações
                            </button>

                            <a href="visualizar_equipamento.php?codigo=<?php echo urlencode($codigo); ?>"
                                class="btn btn-outline-secondary ms-2">
                                Voltar à visualização
                            </a>

                        </div>

                    </form>

                </div>

            </div>

        <?php endif; ?>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
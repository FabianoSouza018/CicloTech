<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$clientes = lerJson('clientes.json');
$equipamentos = lerJson('equipamentos.json');
$funcionarios = lerJson('funcionarios.json');
$manutencoes = lerJson('manutencoes.json');

$mensagem = '';
$tipoMensagem = '';

function gerarCodigoManutencao($manutencoes)
{
    $maiorNumero = 0;

    foreach ($manutencoes as $manutencao) {
        if (preg_match('/^MAN-(\d+)$/', $manutencao['codigo'], $resultado)) {
            $numero = (int) $resultado[1];

            if ($numero > $maiorNumero) {
                $maiorNumero = $numero;
            }
        }
    }

    return 'MAN-' . str_pad($maiorNumero + 1, 4, '0', STR_PAD_LEFT);
}

$clientesAtivos = array_filter($clientes, function ($cliente) {
    return ($cliente['ativo'] ?? false) === true;
});

$funcionariosAtivos = array_filter($funcionarios, function ($funcionario) {
    return ($funcionario['ativo'] ?? false) === true;
});

$equipamentosElegiveis = array_filter($equipamentos, function ($equipamento) {
    $ativo = ($equipamento['ativo'] ?? false) === true;
    $prefixo = $equipamento['prefixo'] ?? '';

    // Equipamentos de clientes continuam disponíveis para manutenção.
    $equipamentoCliente = $prefixo === 'CM'
        && $ativo
        && !empty($equipamento['codigo_cliente']);

    // Equipamentos doados entram na manutenção quando aguardam atendimento.
    $equipamentoDoado = $prefixo === 'ED'
        && $ativo
        && ($equipamento['status'] ?? '') === 'Aguardando manutenção';

    return $equipamentoCliente || $equipamentoDoado;
});

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigoCliente = $_POST['codigo_cliente'] ?? '';
    $codigoEquipamento = $_POST['codigo_equipamento'] ?? '';
    $codigoFuncionario = $_POST['codigo_funcionario'] ?? '';
    $defeito = trim($_POST['defeito'] ?? '');

    $clienteValido = ($codigoCliente === '');
    $equipamentoSelecionado = null;
    $equipamentoValido = false;
    $funcionarioValido = false;

    if ($codigoCliente !== '') {
        $clienteValido = false;
        foreach ($clientesAtivos as $cliente) {
            if (($cliente['codigo'] ?? '') === $codigoCliente) {
                $clienteValido = true;
                break;
            }
        }
    }

    foreach ($equipamentosElegiveis as $equipamento) {
        if (($equipamento['codigo'] ?? '') === $codigoEquipamento) {
            $equipamentoSelecionado = $equipamento;
            break;
        }
    }

    if ($equipamentoSelecionado !== null) {
        $prefixo = $equipamentoSelecionado['prefixo'] ?? '';

        if ($prefixo === 'ED') {
            $equipamentoValido = $codigoCliente === ''
                && ($equipamentoSelecionado['status'] ?? '') === 'Aguardando manutenção';
        } elseif ($prefixo === 'CM') {
            $equipamentoValido = $codigoCliente !== ''
                && ($equipamentoSelecionado['codigo_cliente'] ?? '') === $codigoCliente;
        }
    }

    foreach ($funcionariosAtivos as $funcionario) {
        if (($funcionario['codigo'] ?? '') === $codigoFuncionario) {
            $funcionarioValido = true;
            break;
        }
    }

    if (!$clienteValido || !$equipamentoValido || !$funcionarioValido || $defeito === '') {

        $mensagem = 'Confira os dados selecionados e informe o defeito relatado.';
        $tipoMensagem = 'danger';
    } else {

        $nomeTecnico = '';

        foreach ($funcionariosAtivos as $funcionario) {
            if ($funcionario['codigo'] === $codigoFuncionario) {
                $nomeTecnico = $funcionario['nome'];
                break;
            }
        }

        $novaManutencao = [
            'codigo' => gerarCodigoManutencao($manutencoes),
            'codigo_cliente' => $codigoCliente, // Vazio para equipamentos doados (ED).
            'codigo_equipamento' => $codigoEquipamento,
            'codigo_funcionario' => $codigoFuncionario,
            'nome_tecnico' => $nomeTecnico,
            'defeito' => $defeito,
            'servico_realizado' => '',
            'status' => 'Aguardando',
            'data_abertura' => date('Y-m-d H:i:s')
        ];

        $manutencoes[] = $novaManutencao;

        if (salvarJson('manutencoes.json', $manutencoes)) {
            header('Location: listar_manutencoes.php');
            exit;
        } else {
            $mensagem = 'Não foi possível salvar a manutenção.';
            $tipoMensagem = 'danger';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registrar Manutenção - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-ciclotech">
        <div class="container">

            <a class="navbar-brand" href="index.php">CicloTech</a>

            <a href="listar_manutencoes.php" class="btn btn-outline-light btn-sm">
                Voltar à listagem
            </a>

        </div>
    </nav>

    <div class="container py-5">

        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card shadow-sm">
                    <div class="card-body p-4">

                        <h2 class="mb-4">Registrar manutenção</h2>

                        <p class="text-muted">
                            Preencha os dados do equipamento e do serviço solicitado.
                        </p>

                        <?php if ($mensagem !== ''): ?>
                            <div class="alert alert-<?php echo $tipoMensagem; ?>">
                                <?php echo htmlspecialchars($mensagem); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($equipamentosElegiveis) || empty($funcionariosAtivos)): ?>

                            <div class="alert alert-warning">
                                Para registrar uma manutenção, é necessário ter equipamentos elegíveis
                                e funcionários ativos cadastrados.
                            </div>

                        <?php else: ?>

                            <form method="POST">

                                <div class="mb-3">

                                    <label class="form-label">Cliente</label>

                                    <select name="codigo_cliente" id="cliente" class="form-select">
                                        <option value="">Não se aplica (equipamento doado)</option>

                                        <?php foreach ($clientesAtivos as $cliente): ?>
                                            <option
                                                value="<?php echo htmlspecialchars($cliente['codigo']); ?>"
                                                <?php echo (($_POST['codigo_cliente'] ?? '') === $cliente['codigo']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cliente['nome']); ?>
                                            </option>
                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">Equipamento</label>

                                    <select name="codigo_equipamento" id="equipamento" class="form-select" required>
                                        <option value="">Selecione o equipamento</option>

                                        <?php foreach ($equipamentosElegiveis as $equipamento): ?>

                                            <option
                                                value="<?php echo htmlspecialchars($equipamento['codigo']); ?>"
                                                data-cliente="<?php echo htmlspecialchars($equipamento['codigo_cliente'] ?? ''); ?>"
                                                data-prefixo="<?php echo htmlspecialchars($equipamento['prefixo'] ?? ''); ?>"
                                                <?php echo (($_POST['codigo_equipamento'] ?? '') === $equipamento['codigo']) ? 'selected' : ''; ?>>

                                                <?php
                                                echo htmlspecialchars(
                                                    $equipamento['codigo'] . ' - ' .
                                                        $equipamento['tipo'] . ' - ' .
                                                        $equipamento['marca'] . ' ' .
                                                        $equipamento['modelo']
                                                );
                                                ?>

                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label">Técnico responsável</label>

                                    <select name="codigo_funcionario" class="form-select" required>
                                        <option value="">Selecione o técnico</option>

                                        <?php foreach ($funcionariosAtivos as $funcionario): ?>
                                            <option
                                                value="<?php echo htmlspecialchars($funcionario['codigo']); ?>"
                                                <?php echo (($_POST['codigo_funcionario'] ?? '') === $funcionario['codigo']) ? 'selected' : ''; ?>>

                                                <?php echo htmlspecialchars($funcionario['nome']); ?>

                                            </option>
                                        <?php endforeach; ?>

                                    </select>

                                </div>

                                <div class="mb-4">

                                    <label class="form-label">Defeito relatado</label>

                                    <textarea
                                        name="defeito"
                                        class="form-control"
                                        rows="4"
                                        required><?php echo htmlspecialchars($_POST['defeito'] ?? ''); ?></textarea>

                                </div>

                                <div class="d-flex gap-2">

                                    <button type="submit" class="btn btn-success">
                                        Registrar manutenção
                                    </button>

                                    <a href="listar_manutencoes.php" class="btn btn-secondary">
                                        Voltar à listagem
                                    </a>

                                </div>

                            </form>

                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        const cliente = document.getElementById('cliente');
        const equipamento = document.getElementById('equipamento');

        function filtrarEquipamentos() {
            const codigoCliente = cliente.value;
            let equipamentoSelecionadoDisponivel = false;

            Array.from(equipamento.options).forEach(function(opcao, indice) {
                if (indice === 0) {
                    opcao.hidden = false;
                    return;
                }

                const prefixo = opcao.dataset.prefixo;
                const pertenceAoCliente = opcao.dataset.cliente === codigoCliente;
                const equipamentoDoado = prefixo === 'ED' && codigoCliente === '';
                const equipamentoCliente = prefixo === 'CM' && codigoCliente !== '' && pertenceAoCliente;
                const disponivel = equipamentoDoado || equipamentoCliente;

                opcao.hidden = !disponivel;

                if (opcao.selected && disponivel) {
                    equipamentoSelecionadoDisponivel = true;
                }
            });

            if (!equipamentoSelecionadoDisponivel) {
                equipamento.value = '';
            }
        }

        cliente.addEventListener('change', filtrarEquipamentos);

        filtrarEquipamentos();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
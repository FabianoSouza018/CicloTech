<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$mensagem = '';
$tipoMensagem = '';

$equipamentos = lerJson('equipamentos.json');
$clientes = lerJson('clientes.json');
$funcionarios = lerJson('funcionarios.json');

$prefixo = $_POST['prefixo'] ?? '';
$codigoCliente = $_POST['codigo_cliente'] ?? '';
$codigoFuncionario = $_POST['codigo_funcionario'] ?? '';


// Gera o próximo código de acordo com o prefixo
function gerarCodigo($prefixo, $equipamentos)
{
    $maiorNumero = 0;

    foreach ($equipamentos as $equipamento) {

        if (
            isset($equipamento['codigo']) &&
            preg_match('/^' . $prefixo . '-(\d+)$/', $equipamento['codigo'], $resultado)
        ) {

            $numero = (int) $resultado[1];

            if ($numero > $maiorNumero) {
                $maiorNumero = $numero;
            }
        }
    }

    return $prefixo . '-' . str_pad($maiorNumero + 1, 4, '0', STR_PAD_LEFT);
}


// Recebe os dados do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipo = trim($_POST['tipo'] ?? '');
    $marca = trim($_POST['marca'] ?? '');
    $modelo = trim($_POST['modelo'] ?? '');
    $identificacao = trim($_POST['identificacao'] ?? '');
    $semIdentificacao = isset($_POST['sem_identificacao']);
    $status = $_POST['status'] ?? '';

    $prefixosPermitidos = ['ED', 'CM', 'CI'];

    $statusPermitidos = [
        'Recebido',
        'Aguardando manutenção',
        'Em manutenção',
        'Em uso'
    ];

    $clienteSelecionado = null;
    $funcionarioSelecionado = null;


    // Procura o cliente selecionado
    if ($prefixo === 'CM') {

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


    // Validação dos campos
    if (
        !in_array($prefixo, $prefixosPermitidos, true) ||
        $tipo === '' ||
        $marca === '' ||
        $modelo === '' ||
        $funcionarioSelecionado === null ||
        !in_array($status, $statusPermitidos, true) ||
        (!$semIdentificacao && $identificacao === '') ||
        ($prefixo === 'CM' && $clienteSelecionado === null)
    ) {

        $mensagem = 'Preencha os campos obrigatórios corretamente.';
        $tipoMensagem = 'danger';
    } else {

        $codigo = gerarCodigo($prefixo, $equipamentos);

        $novoEquipamento = [
            'codigo' => $codigo,
            'prefixo' => $prefixo,
            'tipo' => $tipo,
            'marca' => $marca,
            'modelo' => $modelo,
            'identificacao' => $semIdentificacao ? null : $identificacao,
            'responsavel' => $funcionarioSelecionado['nome'],
            'codigo_funcionario' => $funcionarioSelecionado['codigo'],
            'codigo_cliente' => $prefixo === 'CM' ? $codigoCliente : null,
            'status' => $status,
            'ativo' => true,
            'data_cadastro' => date('Y-m-d H:i:s')
        ];

        $equipamentos[] = $novoEquipamento;

        if (salvarJson('equipamentos.json', $equipamentos)) {

            $mensagem = "Equipamento cadastrado com sucesso! Código: " . $codigo;
            $tipoMensagem = 'success';

            $prefixo = '';
            $codigoCliente = '';
            $codigoFuncionario = '';
        } else {

            $mensagem = 'Não foi possível salvar o equipamento.';
            $tipoMensagem = 'danger';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastro de Equipamentos - CicloTech</title>

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

        <div class="mb-4">

            <h2 class="fw-bold">Cadastro de equipamentos</h2>

            <p class="text-secondary">
                Registre os equipamentos recebidos, de clientes ou utilizados pela CicloTech.
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

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">Categoria do equipamento</label>

                            <select name="prefixo" id="prefixo" class="form-select" required>

                                <option value="">Selecione</option>

                                <option value="ED" <?php echo $prefixo === 'ED' ? 'selected' : ''; ?>>
                                    ED - Equipamento recebido/doado
                                </option>

                                <option value="CM" <?php echo $prefixo === 'CM' ? 'selected' : ''; ?>>
                                    CM - Equipamento de cliente
                                </option>

                                <option value="CI" <?php echo $prefixo === 'CI' ? 'selected' : ''; ?>>
                                    CI - Equipamento de uso interno
                                </option>

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Tipo de equipamento</label>

                            <select name="tipo" class="form-select" required>

                                <option value="">Selecione</option>
                                <option value="Computador">Computador</option>
                                <option value="Notebook">Notebook</option>
                                <option value="Monitor">Monitor</option>
                                <option value="Impressora">Impressora</option>
                                <option value="Outro">Outro</option>

                            </select>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Marca</label>
                            <input type="text" name="marca" class="form-control" required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Modelo</label>
                            <input type="text" name="modelo" class="form-control" required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Número de série / patrimônio</label>

                            <input type="text" name="identificacao" class="form-control">

                            <div class="form-text">
                                Informe uma identificação externa, se disponível.
                            </div>

                            <div class="form-check mt-2">

                                <input
                                    type="checkbox"
                                    name="sem_identificacao"
                                    value="1"
                                    class="form-check-input"
                                    id="semIdentificacao">

                                <label class="form-check-label" for="semIdentificacao">
                                    Equipamento sem identificação externa
                                </label>

                            </div>

                        </div>

                        <!-- Funcionário responsável -->
                        <div class="col-md-6">

                            <label class="form-label">Funcionário responsável</label>

                            <select name="codigo_funcionario" class="form-select" required>

                                <option value="">Selecione um funcionário</option>

                                <?php foreach ($funcionarios as $funcionario): ?>

                                    <?php if (($funcionario['ativo'] ?? false) === true): ?>

                                        <option
                                            value="<?php echo htmlspecialchars($funcionario['codigo']); ?>"
                                            <?php echo $codigoFuncionario === $funcionario['codigo'] ? 'selected' : ''; ?>>

                                            <?php
                                            echo htmlspecialchars(
                                                $funcionario['codigo'] . ' - ' . $funcionario['nome']
                                            );
                                            ?>

                                        </option>

                                    <?php endif; ?>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <!-- Cliente vinculado -->
                        <div class="col-md-6" id="campoCliente">

                            <label class="form-label">Cliente vinculado</label>

                            <select
                                name="codigo_cliente"
                                id="codigo_cliente"
                                class="form-select">

                                <option value="">Selecione um cliente</option>

                                <?php foreach ($clientes as $cliente): ?>

                                    <?php if (($cliente['ativo'] ?? false) === true): ?>

                                        <option
                                            value="<?php echo htmlspecialchars($cliente['codigo']); ?>"
                                            <?php echo $codigoCliente === $cliente['codigo'] ? 'selected' : ''; ?>>

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

                        <div class="col-md-6">

                            <label class="form-label">Situação inicial</label>

                            <select name="status" class="form-select" required>

                                <option value="">Selecione</option>
                                <option value="Recebido">Recebido</option>
                                <option value="Aguardando manutenção">Aguardando manutenção</option>
                                <option value="Em manutenção">Em manutenção</option>
                                <option value="Em uso">Em uso</option>

                            </select>

                        </div>

                    </div>

                    <div class="mt-4">

                        <button type="submit" class="btn btn-success">
                            Cadastrar equipamento
                        </button>

                        <a href="listar_equipamentos.php" class="btn btn-outline-secondary ms-2">
                            Voltar à listagem
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </main>

    <script>
        const prefixo = document.getElementById('prefixo');
        const campoCliente = document.getElementById('campoCliente');
        const codigoCliente = document.getElementById('codigo_cliente');

        function atualizarCampos() {

            if (prefixo.value === 'CM') {

                campoCliente.style.display = 'block';
                codigoCliente.required = true;
                codigoCliente.disabled = false;

            } else {

                campoCliente.style.display = 'none';
                codigoCliente.required = false;
                codigoCliente.disabled = true;
            }
        }

        prefixo.addEventListener('change', atualizarCampos);

        atualizarCampos();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
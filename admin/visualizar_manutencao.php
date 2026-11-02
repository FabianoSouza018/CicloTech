<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$manutencoes = lerJson('manutencoes.json');
$clientes = lerJson('clientes.json');
$equipamentos = lerJson('equipamentos.json');
$funcionarios = lerJson('funcionarios.json');

$codigo = $_GET['codigo'] ?? $_POST['codigo'] ?? '';
$resultado = $_GET['resultado'] ?? '';

$indiceManutencao = null;

foreach ($manutencoes as $indice => $manutencao) {
    if ($manutencao['codigo'] === $codigo) {
        $indiceManutencao = $indice;
        break;
    }
}

if ($indiceManutencao === null) {
    header('Location: listar_manutencoes.php');
    exit;
}

$manutencao = $manutencoes[$indiceManutencao];

$cliente = null;
$equipamento = null;
$tecnico = null;

foreach ($clientes as $registro) {
    if (!empty($manutencao['codigo_cliente']) && $registro['codigo'] === $manutencao['codigo_cliente']) {
        $cliente = $registro;
        break;
    }
}

foreach ($equipamentos as $registro) {
    if ($registro['codigo'] === $manutencao['codigo_equipamento']) {
        $equipamento = $registro;
        break;
    }
}

foreach ($funcionarios as $registro) {
    if ($registro['codigo'] === $manutencao['codigo_funcionario']) {
        $tecnico = $registro;
        break;
    }
}

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $servicoRealizado = trim($_POST['servico_realizado'] ?? '');
    $status = $_POST['status'] ?? '';

    $statusPermitidos = [
        'Aguardando',
        'Em manutenção',
        'Concluído'
    ];

    if (!in_array($status, $statusPermitidos, true)) {

        $mensagem = 'Situação inválida.';
        $tipoMensagem = 'danger';
    } elseif ($status === 'Concluído' && $servicoRealizado === '') {

        $mensagem = 'Informe o serviço realizado antes de concluir a manutenção.';
        $tipoMensagem = 'danger';
    } else {

        // Localiza o equipamento vinculado à manutenção.
        $indiceEquipamento = null;

        foreach ($equipamentos as $indice => $registro) {
            if (($registro['codigo'] ?? '') === ($manutencao['codigo_equipamento'] ?? '')) {
                $indiceEquipamento = $indice;
                break;
            }
        }

        if ($indiceEquipamento === null) {

            $mensagem = 'O equipamento vinculado a esta manutenção não foi encontrado.';
            $tipoMensagem = 'danger';

        } else {

            $situacoesEquipamento = [
                'Aguardando' => 'Aguardando manutenção',
                'Em manutenção' => 'Em manutenção',
                'Concluído' => 'Disponível'
            ];

            $manutencoes[$indiceManutencao]['servico_realizado'] = $servicoRealizado;
            $manutencoes[$indiceManutencao]['status'] = $status;
            $equipamentos[$indiceEquipamento]['status'] = $situacoesEquipamento[$status];

            $salvouManutencoes = salvarJson('manutencoes.json', $manutencoes);
            $salvouEquipamentos = salvarJson('equipamentos.json', $equipamentos);

            if ($salvouManutencoes && $salvouEquipamentos) {

                header('Location: visualizar_manutencao.php?codigo=' . urlencode($codigo) . '&resultado=alterado');
                exit;

            } else {

                $mensagem = 'Não foi possível salvar todas as alterações. Verifique os arquivos de dados.';
                $tipoMensagem = 'danger';
            }
        }
    }

    $manutencao['servico_realizado'] = $servicoRealizado;
    $manutencao['status'] = $status;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Visualizar Manutenção - CicloTech</title>

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

                <div class="card shadow-sm mb-4">

                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-center mb-4">

                            <h2 class="mb-0">Dados da manutenção</h2>

                            <span class="badge bg-secondary">
                                <?php echo htmlspecialchars($manutencao['status']); ?>
                            </span>

                        </div>

                        <?php if ($resultado === 'alterado'): ?>

                            <div class="alert alert-success">
                                Manutenção atualizada com sucesso.
                            </div>

                        <?php endif; ?>

                        <?php if ($mensagem !== ''): ?>

                            <div class="alert alert-<?php echo $tipoMensagem; ?>">
                                <?php echo htmlspecialchars($mensagem); ?>
                            </div>

                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label text-muted">Código da manutenção</label>
                            <p class="form-control">
                                <?php echo htmlspecialchars($manutencao['codigo']); ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Cliente</label>
                            <p class="form-control">
                                <?php echo htmlspecialchars($cliente['nome'] ?? 'Não se aplica (equipamento doado)'); ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Equipamento</label>
                            <p class="form-control">
                                <?php
                                echo htmlspecialchars(
                                    ($equipamento['codigo'] ?? '') . ' - ' .
                                        ($equipamento['tipo'] ?? 'Não encontrado') . ' - ' .
                                        ($equipamento['marca'] ?? '') . ' ' .
                                        ($equipamento['modelo'] ?? '')
                                );
                                ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Técnico responsável</label>
                            <p class="form-control">
                                <?php echo htmlspecialchars($tecnico['nome'] ?? $manutencao['nome_tecnico']); ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Data de abertura</label>
                            <p class="form-control">
                                <?php
                                echo date(
                                    'd/m/Y H:i',
                                    strtotime($manutencao['data_abertura'])
                                );
                                ?>
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted">Defeito relatado</label>
                            <textarea class="form-control" rows="3" disabled><?php echo htmlspecialchars($manutencao['defeito']); ?></textarea>
                        </div>

                    </div>

                </div>

                <div class="card shadow-sm">

                    <div class="card-body p-4">

                        <h4 class="mb-3">Atualizar manutenção</h4>

                        <form method="POST">

                            <input
                                type="hidden"
                                name="codigo"
                                value="<?php echo htmlspecialchars($manutencao['codigo']); ?>">

                            <div class="mb-3">

                                <label class="form-label">Serviço realizado</label>

                                <textarea
                                    name="servico_realizado"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Descreva o serviço realizado..."><?php echo htmlspecialchars($manutencao['servico_realizado']); ?></textarea>

                            </div>

                            <div class="mb-4">

                                <label class="form-label">Situação</label>

                                <select name="status" class="form-select" required>

                                    <?php
                                    $statusPermitidos = [
                                        'Aguardando',
                                        'Em manutenção',
                                        'Concluído'
                                    ];

                                    foreach ($statusPermitidos as $status):
                                    ?>

                                        <option
                                            value="<?php echo htmlspecialchars($status); ?>"
                                            <?php echo ($manutencao['status'] === $status) ? 'selected' : ''; ?>>

                                            <?php echo htmlspecialchars($status); ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                            <div class="d-flex gap-2 flex-wrap">

                                <button type="submit" class="btn btn-success">
                                    Salvar alterações
                                </button>

                                <a href="listar_manutencoes.php" class="btn btn-secondary">
                                    Voltar à listagem
                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
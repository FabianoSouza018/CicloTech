<?php
require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$equipamentos = lerJson('equipamentos.json');
$manutencoes = lerJson('manutencoes.json');

// Identifica equipamentos doados que ainda não possuem manutenção em andamento.
$equipamentosPendentes = array_filter($equipamentos, function ($equipamento) use ($manutencoes) {
    $elegivel = ($equipamento['ativo'] ?? false) === true
        && ($equipamento['prefixo'] ?? '') === 'ED'
        && ($equipamento['status'] ?? '') === 'Aguardando manutenção';

    if (!$elegivel) {
        return false;
    }

    foreach ($manutencoes as $manutencao) {
        $mesmoEquipamento = ($manutencao['codigo_equipamento'] ?? '') === ($equipamento['codigo'] ?? '');
        $statusManutencao = $manutencao['status'] ?? '';

        if ($mesmoEquipamento && $statusManutencao !== 'Concluído') {
            return false;
        }
    }

    return true;
});
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Área Administrativa - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .card-modulo {
            height: 100%;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-modulo:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.12);
            color: inherit;
        }

        .icone-modulo {
            font-size: 2rem;
            color: #35A879;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-ciclotech">
        <div class="container">
            <a class="navbar-brand" href="index.php">♻ CicloTech</a>

            <a href="logout.php" class="btn btn-outline-light btn-sm">Sair</a>
        </div>
    </nav>

    <main class="container py-5">

        <div class="mb-5">
            <h2 class="fw-bold">Área administrativa</h2>
            <p class="text-muted">Selecione um módulo para acessar suas funcionalidades.</p>
        </div>

        <div class="row g-4">

            <!-- Clientes -->
            <div class="col-md-6 col-lg-3">
                <a href="listar_clientes.php" class="card card-modulo shadow-sm">
                    <div class="card-body p-4">
                        <div class="icone-modulo"><i class="bi bi-people-fill"></i></div>
                        <h5 class="text-success">Clientes</h5>
                        <p class="text-muted mb-0">Gerenciamento de clientes cadastrados.</p>
                        <div class="mt-4 text-success fw-semibold">Acessar módulo →</div>
                    </div>
                </a>
            </div>

            <!-- Funcionários -->
            <div class="col-md-6 col-lg-3">
                <a href="listar_funcionarios.php" class="card card-modulo shadow-sm">
                    <div class="card-body p-4">
                        <div class="icone-modulo"><i class="bi bi-person-badge-fill"></i></div>
                        <h5 class="text-success">Funcionários</h5>
                        <p class="text-muted mb-0">Gerenciamento da equipe da CicloTech.</p>
                        <div class="mt-4 text-success fw-semibold">Acessar módulo →</div>
                    </div>
                </a>
            </div>

            <!-- Equipamentos -->
            <div class="col-md-6 col-lg-3">
                <a href="listar_equipamentos.php" class="card card-modulo shadow-sm">
                    <div class="card-body p-4">
                        <div class="icone-modulo"><i class="bi bi-laptop"></i></div>
                        <h5 class="text-success">Equipamentos</h5>
                        <p class="text-muted mb-0">Controle e consulta dos equipamentos.</p>
                        <div class="mt-4 text-success fw-semibold">Acessar módulo →</div>
                    </div>
                </a>
            </div>

            <!-- Manutenções -->
            <div class="col-md-6 col-lg-3">
                <a href="listar_manutencoes.php" class="card card-modulo shadow-sm">
                    <div class="card-body p-4">
                        <div class="icone-modulo"><i class="bi bi-tools"></i></div>
                        <h5 class="text-success">Manutenções</h5>
                        <p class="text-muted mb-0">Acompanhamento dos serviços registrados.</p>
                        <div class="mt-4 text-success fw-semibold">Acessar módulo →</div>
                    </div>
                </a>
            </div>

        </div>

        <!-- Fila automática de equipamentos doados pendentes -->
        <section class="mt-5">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <h4 class="fw-bold mb-1">Equipamentos aguardando manutenção</h4>
                    <p class="text-muted mb-0">Equipamentos doados que ainda precisam ter uma manutenção registrada.</p>
                </div>
                <span class="badge text-bg-warning fs-6">
                    <?php echo count($equipamentosPendentes); ?> pendente(s)
                </span>
            </div>

            <?php if (empty($equipamentosPendentes)): ?>
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle me-2"></i>
                    Não há equipamentos doados aguardando abertura de manutenção.
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Equipamento</th>
                                    <th>Marca / Modelo</th>
                                    <th class="text-end">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($equipamentosPendentes as $equipamento): ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($equipamento['codigo'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($equipamento['tipo'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars(trim(($equipamento['marca'] ?? '') . ' ' . ($equipamento['modelo'] ?? ''))); ?></td>
                                        <td class="text-end">
                                            <a href="manutencao.php" class="btn btn-success btn-sm">
                                                <i class="bi bi-tools me-1"></i> Registrar manutenção
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

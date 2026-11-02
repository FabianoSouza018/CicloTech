<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$clientes = lerJson('clientes.json');

$codigo = $_GET['codigo'] ?? '';
$resultado = $_GET['resultado'] ?? '';

$clienteEncontrado = null;

foreach ($clientes as $cliente) {
    if ($cliente['codigo'] === $codigo) {
        $clienteEncontrado = $cliente;
        break;
    }
}

if ($clienteEncontrado === null) {
    header('Location: listar_clientes.php');
    exit;
}

$cliente = $clienteEncontrado;

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Visualizar Cliente - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-ciclotech">
        <div class="container">

            <a class="navbar-brand" href="index.php">CicloTech</a>

            <a href="listar_clientes.php" class="btn btn-outline-light btn-sm">
                Voltar à listagem
            </a>

        </div>
    </nav>

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="card shadow-sm">

                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between align-items-center mb-4">

                            <h2 class="mb-0">Dados do cliente</h2>

                            <?php if ($cliente['ativo']): ?>
                                <span class="badge bg-success">Ativo</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inativo</span>
                            <?php endif; ?>

                        </div>

                        <?php if ($resultado === 'alterado'): ?>
                            <div class="alert alert-success">
                                Situação do cliente alterada com sucesso.
                            </div>
                        <?php elseif ($resultado === 'erro'): ?>
                            <div class="alert alert-danger">
                                Não foi possível salvar a alteração.
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label text-muted">Código</label>
                            <p class="form-control">
                                <?php echo htmlspecialchars($cliente['codigo']); ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Nome / Razão social</label>
                            <p class="form-control">
                                <?php echo htmlspecialchars($cliente['nome']); ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">CPF / CNPJ</label>
                            <p class="form-control">
                                <?php echo htmlspecialchars($cliente['documento']); ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">Telefone</label>
                            <p class="form-control">
                                <?php echo htmlspecialchars($cliente['telefone']); ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted">E-mail</label>
                            <p class="form-control">
                                <?php echo htmlspecialchars($cliente['email']); ?>
                            </p>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted">Data do cadastro</label>
                            <p class="form-control">
                                <?php
                                echo date(
                                    'd/m/Y H:i',
                                    strtotime($cliente['data_cadastro'])
                                );
                                ?>
                            </p>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">

                            <a
                                href="editar_cliente.php?codigo=<?php echo urlencode($cliente['codigo']); ?>"
                                class="btn btn-success">
                                Editar cliente
                            </a>

                            <a href="listar_clientes.php" class="btn btn-secondary">
                                Voltar à listagem
                            </a>

                        </div>

                    </div>

                </div>

                <div class="card shadow-sm mt-4">

                    <div class="card-body p-4">

                        <h5 class="mb-3">Gerenciamento do cliente</h5>

                        <?php if ($cliente['ativo']): ?>

                            <p class="text-muted">
                                Este cliente está ativo no cadastro.
                            </p>

                            <form
                                method="POST"
                                action="alterar_status_cliente.php"
                                onsubmit="return confirm('Deseja realmente inativar este cliente?');">

                                <input
                                    type="hidden"
                                    name="codigo"
                                    value="<?php echo htmlspecialchars($cliente['codigo']); ?>">

                                <input type="hidden" name="acao" value="inativar">

                                <button type="submit" class="btn btn-danger">
                                    Inativar cliente
                                </button>

                            </form>

                        <?php else: ?>

                            <p class="text-muted">
                                Este cliente está inativo no cadastro.
                            </p>

                            <form
                                method="POST"
                                action="alterar_status_cliente.php"
                                onsubmit="return confirm('Deseja realmente reativar este cliente?');">

                                <input
                                    type="hidden"
                                    name="codigo"
                                    value="<?php echo htmlspecialchars($cliente['codigo']); ?>">

                                <input type="hidden" name="acao" value="reativar">

                                <button type="submit" class="btn btn-success">
                                    Reativar cliente
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
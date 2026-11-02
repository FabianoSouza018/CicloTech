<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$clientes = lerJson('clientes.json');

$codigo = $_GET['codigo'] ?? $_POST['codigo'] ?? '';

$indiceCliente = null;

foreach ($clientes as $indice => $cliente) {

    if ($cliente['codigo'] === $codigo) {
        $indiceCliente = $indice;
        break;
    }
}

if ($indiceCliente === null) {
    header('Location: listar_clientes.php');
    exit;
}

$cliente = $clientes[$indiceCliente];

$mensagem = '';
$tipoMensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $documento = trim($_POST['documento'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nome === '' || $documento === '') {

        $mensagem = 'Preencha o nome e o CPF/CNPJ do cliente.';
        $tipoMensagem = 'danger';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = 'Informe um endereço de e-mail válido.';
        $tipoMensagem = 'danger';
    } else {

        $documentoExistente = false;

        foreach ($clientes as $indice => $outroCliente) {

            if (
                $indice !== $indiceCliente &&
                $outroCliente['documento'] === $documento
            ) {
                $documentoExistente = true;
                break;
            }
        }

        if ($documentoExistente) {

            $mensagem = 'Já existe outro cliente cadastrado com esse CPF/CNPJ.';
            $tipoMensagem = 'danger';
        } else {

            $clientes[$indiceCliente]['nome'] = $nome;
            $clientes[$indiceCliente]['documento'] = $documento;
            $clientes[$indiceCliente]['telefone'] = $telefone;
            $clientes[$indiceCliente]['email'] = $email;

            if (salvarJson('clientes.json', $clientes)) {

                header('Location: visualizar_cliente.php?codigo=' . urlencode($codigo));
                exit;
            } else {

                $mensagem = 'Não foi possível salvar as alterações.';
                $tipoMensagem = 'danger';
            }
        }
    }

    $cliente = $clientes[$indiceCliente];

    $cliente['nome'] = $nome;
    $cliente['documento'] = $documento;
    $cliente['telefone'] = $telefone;
    $cliente['email'] = $email;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Cliente - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .btn-padrao {
            width: 180px;
            min-width: 180px;
            box-sizing: border-box;
        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-ciclotech">
        <div class="container">

            <a class="navbar-brand" href="index.php">CicloTech</a>

            <a href="visualizar_cliente.php?codigo=<?php echo urlencode($codigo); ?>" class="btn btn-outline-light btn-sm">
                Voltar ao registro
            </a>

        </div>
    </nav>

    <div class="container py-5">

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="card shadow-sm mb-4">

                    <div class="card-body p-4">

                        <h2 class="mb-4">Editar cliente</h2>

                        <p class="text-muted">
                            Altere os dados do cliente cadastrado.
                        </p>

                        <?php if ($mensagem !== ''): ?>

                            <div class="alert alert-<?php echo $tipoMensagem; ?>">
                                <?php echo htmlspecialchars($mensagem); ?>
                            </div>

                        <?php endif; ?>

                        <form method="POST">

                            <input
                                type="hidden"
                                name="codigo"
                                value="<?php echo htmlspecialchars($cliente['codigo']); ?>">

                            <div class="mb-3">

                                <label class="form-label">Código do cliente</label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($cliente['codigo']); ?>"
                                    disabled>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Nome / Razão social</label>

                                <input
                                    type="text"
                                    name="nome"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($cliente['nome']); ?>"
                                    required>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">CPF / CNPJ</label>

                                <input
                                    type="text"
                                    name="documento"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($cliente['documento']); ?>"
                                    required>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Telefone</label>

                                <input
                                    type="text"
                                    name="telefone"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($cliente['telefone']); ?>">

                            </div>

                            <div class="mb-4">

                                <label class="form-label">E-mail</label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($cliente['email']); ?>">

                            </div>

                            <div class="d-flex gap-2">

                                <button type="submit" class="btn btn-success">
                                    Salvar alterações
                                </button>

                                <a
                                    href="visualizar_cliente.php?codigo=<?php echo urlencode($codigo); ?>"
                                    class="btn btn-secondary">
                                    Voltar à visualização
                                </a>

                            </div>

                        </form>

                    </div>

                </div>

                <div class="card shadow-sm">

                    <div class="card-body p-4">

                        <h5 class="mb-3">Situação do cadastro</h5>

                        <?php if ($cliente['ativo']): ?>

                            <span class="btn btn-success btn-padrao d-block mb-2">
                                Cliente ativo
                            </span>

                            <form
                                method="POST"
                                action="alterar_status_cliente.php"
                                onsubmit="return confirm('Deseja realmente inativar este cliente?');">

                                <input
                                    type="hidden"
                                    name="codigo"
                                    value="<?php echo htmlspecialchars($cliente['codigo']); ?>">

                                <input type="hidden" name="acao" value="inativar">

                                <button type="submit" class="btn btn-danger btn-padrao">
                                    Inativar cliente
                                </button>

                            </form>

                        <?php else: ?>

                            <span class="btn btn-secondary btn-padrao d-block mb-2">
                                Cliente inativo
                            </span>

                            <form
                                method="POST"
                                action="alterar_status_cliente.php"
                                onsubmit="return confirm('Deseja realmente reativar este cliente?');">

                                <input
                                    type="hidden"
                                    name="codigo"
                                    value="<?php echo htmlspecialchars($cliente['codigo']); ?>">

                                <input type="hidden" name="acao" value="reativar">

                                <button type="submit" class="btn btn-success btn-padrao">
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
<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$clientes = lerJson('clientes.json');

$mensagem = '';
$tipoMensagem = '';

function gerarCodigoCliente($clientes)
{

    $maiorNumero = 0;

    foreach ($clientes as $cliente) {

        if (preg_match('/^CLI-(\d+)$/', $cliente['codigo'], $resultado)) {

            $numero = (int) $resultado[1];

            if ($numero > $maiorNumero) {
                $maiorNumero = $numero;
            }
        }
    }

    return 'CLI-' . str_pad($maiorNumero + 1, 4, '0', STR_PAD_LEFT);
}

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

        foreach ($clientes as $cliente) {

            if ($cliente['documento'] === $documento) {
                $documentoExistente = true;
                break;
            }
        }

        if ($documentoExistente) {

            $mensagem = 'Já existe um cliente cadastrado com esse CPF/CNPJ.';
            $tipoMensagem = 'danger';
        } else {

            $novoCliente = [
                'codigo' => gerarCodigoCliente($clientes),
                'nome' => $nome,
                'documento' => $documento,
                'telefone' => $telefone,
                'email' => $email,
                'ativo' => true,
                'data_cadastro' => date('Y-m-d H:i:s')
            ];

            $clientes[] = $novoCliente;

            if (salvarJson('clientes.json', $clientes)) {

                header('Location: listar_clientes.php');
                exit;
            } else {

                $mensagem = 'Não foi possível salvar o cadastro.';
                $tipoMensagem = 'danger';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cadastrar Cliente - CicloTech</title>

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

                        <h2 class="mb-4">Cadastrar cliente</h2>

                        <p class="text-muted">
                            Preencha os dados para registrar um novo cliente.
                        </p>

                        <?php if ($mensagem !== ''): ?>

                            <div class="alert alert-<?php echo $tipoMensagem; ?>">
                                <?php echo htmlspecialchars($mensagem); ?>
                            </div>

                        <?php endif; ?>

                        <form method="POST">

                            <div class="mb-3">

                                <label class="form-label">Nome completo / Razão social</label>

                                <input
                                    type="text"
                                    name="nome"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                                    required>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">CPF / CNPJ</label>

                                <input
                                    type="text"
                                    name="documento"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($_POST['documento'] ?? ''); ?>"
                                    required>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">Telefone</label>

                                <input
                                    type="text"
                                    name="telefone"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>">

                            </div>

                            <div class="mb-4">

                                <label class="form-label">E-mail</label>

                                <input
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

                            </div>

                            <div class="d-flex gap-2">

                                <button type="submit" class="btn btn-success">
                                    Cadastrar cliente
                                </button>

                                <a href="listar_clientes.php" class="btn btn-secondary">
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
<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$mensagem = '';
$tipoMensagem = '';

function gerarCodigoFuncionario($funcionarios)
{

    $maiorNumero = 0;

    foreach ($funcionarios as $funcionario) {

        if (
            isset($funcionario['codigo']) &&
            preg_match('/^FUNC-(\d+)$/', $funcionario['codigo'], $resultado)
        ) {

            $numero = (int) $resultado[1];

            if ($numero > $maiorNumero) {
                $maiorNumero = $numero;
            }
        }
    }

    return 'FUNC-' . str_pad($maiorNumero + 1, 4, '0', STR_PAD_LEFT);
}


// Recebe o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $funcionarios = lerJson('funcionarios.json');

    $nome = trim($_POST['nome'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nome === '' || $cargo === '') {

        $mensagem = 'Preencha o nome e o cargo do funcionário.';
        $tipoMensagem = 'danger';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = 'Informe um endereço de e-mail válido.';
        $tipoMensagem = 'danger';
    } else {

        $novoFuncionario = [
            'codigo' => gerarCodigoFuncionario($funcionarios),
            'nome' => $nome,
            'cargo' => $cargo,
            'telefone' => $telefone,
            'email' => $email,
            'ativo' => true,
            'data_cadastro' => date('Y-m-d H:i:s')
        ];

        $funcionarios[] = $novoFuncionario;

        if (salvarJson('funcionarios.json', $funcionarios)) {

            $mensagem = 'Funcionário cadastrado com sucesso! Código: ' .
                $novoFuncionario['codigo'];

            $tipoMensagem = 'success';
        } else {

            $mensagem = 'Não foi possível salvar o funcionário.';
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

    <title>Cadastro de Funcionários - CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-ciclotech">
        <div class="container">

            <a class="navbar-brand" href="index.php">♻ CicloTech</a>

            <a href="listar_funcionarios.php" class="btn btn-outline-light btn-sm">
                Voltar à listagem
            </a>

        </div>
    </nav>

    <main class="container py-5">

        <div class="mb-4">

            <h2 class="fw-bold">Cadastro de funcionários</h2>

            <p class="text-secondary">
                Registre os colaboradores responsáveis pelas atividades da CicloTech.
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

                            <label class="form-label">Nome completo</label>

                            <input type="text"
                                name="nome"
                                class="form-control"
                                required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Cargo / função</label>

                            <input type="text"
                                name="cargo"
                                class="form-control"
                                placeholder="Ex.: Técnico de informática"
                                required>

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">Telefone</label>

                            <input type="text"
                                name="telefone"
                                class="form-control"
                                placeholder="(00) 00000-0000">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">E-mail</label>

                            <input type="email"
                                name="email"
                                class="form-control"
                                placeholder="funcionario@email.com">

                        </div>

                    </div>

                    <div class="mt-4">

                        <button type="submit" class="btn btn-success">
                            Cadastrar funcionário
                        </button>

                        <a href="listar_funcionarios.php" class="btn btn-outline-secondary ms-2">
                            Cancelar
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
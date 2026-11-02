<?php

// Carrega as funções utilizadas para trabalhar com os arquivos JSON
require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

// Carrega os funcionários cadastrados
$funcionarios = lerJson('funcionarios.json');

// Recebe o código pela URL ou pelo formulário
$codigo = $_GET['codigo'] ?? $_POST['codigo'] ?? '';

$indiceFuncionario = null;

// Localiza o funcionário pelo código
foreach ($funcionarios as $indice => $funcionario) {

    if (($funcionario['codigo'] ?? '') === $codigo) {
        $indiceFuncionario = $indice;
        break;
    }
}

// Variáveis utilizadas para apresentar mensagens na tela
$mensagem = '';
$tipoMensagem = '';

// Verifica se o formulário de edição foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $indiceFuncionario !== null) {

    // Recebe os dados enviados e remove espaços desnecessários
    $nome = trim($_POST['nome'] ?? '');
    $cargo = trim($_POST['cargo'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Verifica se os campos obrigatórios foram preenchidos
    if ($nome === '' || $cargo === '') {

        $mensagem = 'Preencha o nome e o cargo do funcionário.';
        $tipoMensagem = 'danger';

        // Valida o formato do e-mail, caso tenha sido informado
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = 'Informe um endereço de e-mail válido.';
        $tipoMensagem = 'danger';
    } else {

        // Atualiza somente os dados que podem ser editados
        // O código, a situação e a data de cadastro são preservados
        $funcionarios[$indiceFuncionario]['nome'] = $nome;
        $funcionarios[$indiceFuncionario]['cargo'] = $cargo;
        $funcionarios[$indiceFuncionario]['telefone'] = $telefone;
        $funcionarios[$indiceFuncionario]['email'] = $email;

        // Salva as alterações no arquivo JSON
        if (salvarJson('funcionarios.json', $funcionarios)) {

            $mensagem = 'Funcionário atualizado com sucesso!';
            $tipoMensagem = 'success';
        } else {

            $mensagem = 'Não foi possível salvar as alterações.';
            $tipoMensagem = 'danger';
        }
    }
}

// Recupera os dados atuais do funcionário
if ($indiceFuncionario !== null) {
    $funcionarioAtual = $funcionarios[$indiceFuncionario];
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Funcionário - CicloTech</title>

    <!-- Carrega o Bootstrap para estilização da página -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Carrega o CSS personalizado do projeto -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Padroniza a largura dos botões -->
    <style>
        .btn-padrao {
            width: 180px;
            min-width: 180px;
            box-sizing: border-box;
        }
    </style>

</head>

<body class="bg-light">

    <!-- Barra superior de navegação -->
    <nav class="navbar navbar-ciclotech">
        <div class="container">

            <a class="navbar-brand" href="index.php">
                ♻ CicloTech
            </a>

            <a href="visualizar_funcionario.php?codigo=<?php echo urlencode($codigo); ?>" class="btn btn-outline-light btn-sm">
                Voltar ao registro
            </a>

        </div>
    </nav>

    <main class="container py-5">

        <?php if ($indiceFuncionario === null): ?>

            <!-- Mensagem exibida quando o funcionário não é encontrado -->
            <div class="alert alert-warning">
                Funcionário não encontrado.
            </div>

            <a href="listar_funcionarios.php" class="btn btn-secondary">
                Voltar
            </a>

        <?php else: ?>

            <div class="mb-4">

                <h2 class="fw-bold">Editar funcionário</h2>

                <p class="text-secondary">
                    Atualize as informações do colaborador.
                </p>

            </div>

            <!-- Exibe mensagens de sucesso ou erro -->
            <?php if ($mensagem !== ''): ?>

                <div class="alert alert-<?php echo $tipoMensagem; ?>">
                    <?php echo htmlspecialchars($mensagem); ?>
                </div>

            <?php endif; ?>

            <!-- Cartão com os dados editáveis do funcionário -->
            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <form method="POST">

                        <!-- Mantém o código do funcionário durante o envio -->
                        <input type="hidden" name="codigo"
                            value="<?php echo htmlspecialchars($codigo); ?>">

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label">Código interno</label>

                                <input type="text"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($funcionarioAtual['codigo']); ?>"
                                    disabled>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Nome completo</label>

                                <input type="text"
                                    name="nome"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($funcionarioAtual['nome'] ?? ''); ?>"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Cargo / função</label>

                                <input type="text"
                                    name="cargo"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($funcionarioAtual['cargo'] ?? ''); ?>"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">Telefone</label>

                                <input type="text"
                                    name="telefone"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($funcionarioAtual['telefone'] ?? ''); ?>">

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">E-mail</label>

                                <input type="email"
                                    name="email"
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($funcionarioAtual['email'] ?? ''); ?>">

                            </div>

                        </div>

                        <!-- Botões do formulário de edição -->
                        <div class="mt-4">

                            <button type="submit" class="btn btn-success btn-padrao">
                                Salvar alterações
                            </button>

                            <a href="visualizar_funcionario.php?codigo=<?php echo urlencode($codigo); ?>"
                                class="btn btn-outline-secondary btn-padrao ms-2">
                                Voltar à visualização
                            </a>

                        </div>

                    </form>

                </div>

            </div>

            <!-- Cartão para controlar a situação do funcionário -->
            <div class="card shadow-sm border-0 mt-4">

                <div class="card-body p-4">

                    <h5 class="fw-bold">Situação do cadastro</h5>

                    <p class="text-secondary">
                        Controle a disponibilidade deste funcionário no sistema.
                    </p>

                    <!-- Verifica se o funcionário está ativo -->
                    <?php if (($funcionarioAtual['ativo'] ?? false) === true): ?>

                        <!-- Indicador verde de funcionário ativo -->
                        <span class="btn btn-success btn-padrao d-block mb-2">
                            Funcionário ativo
                        </span>

                        <!-- Formulário responsável pela inativação -->
                        <form method="POST"
                            action="alterar_status_funcionario.php"
                            onsubmit="return confirm('Deseja realmente inativar este funcionário?');">

                            <input type="hidden" name="codigo"
                                value="<?php echo htmlspecialchars($codigo); ?>">

                            <input type="hidden" name="acao" value="inativar">

                            <button type="submit" class="btn btn-danger btn-padrao">
                                Inativar funcionário
                            </button>

                        </form>

                    <?php else: ?>

                        <!-- Indicador cinza de funcionário inativo -->
                        <span class="btn btn-secondary btn-padrao d-block mb-2">
                            Funcionário inativo
                        </span>

                        <!-- Formulário responsável pela reativação -->
                        <form method="POST"
                            action="alterar_status_funcionario.php"
                            onsubmit="return confirm('Deseja realmente reativar este funcionário?');">

                            <input type="hidden" name="codigo"
                                value="<?php echo htmlspecialchars($codigo); ?>">

                            <input type="hidden" name="acao" value="reativar">

                            <button type="submit" class="btn btn-success btn-padrao">
                                Reativar funcionário
                            </button>

                        </form>

                    <?php endif; ?>

                </div>

            </div>

        <?php endif; ?>

    </main>

    <!-- Carrega os recursos JavaScript do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
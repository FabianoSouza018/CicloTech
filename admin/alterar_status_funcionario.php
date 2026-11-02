<?php

require_once __DIR__ . '/../includes/proteger.php';
// Carrega as funções responsáveis pela leitura e gravação dos arquivos JSON
require_once __DIR__ . '/../includes/funcoes.php';


// Recebe o código do funcionário enviado pelo formulário
// O operador ?? define um valor vazio caso não exista informação
$codigo = $_POST['codigo'] ?? '';


// Recebe a ação solicitada: inativar ou reativar
$acao = $_POST['acao'] ?? '';


// Carrega todos os funcionários armazenados no arquivo JSON
$funcionarios = lerJson('funcionarios.json');


// Percorre a lista de funcionários
// $indice representa a posição do registro no array
// $funcionario representa os dados de cada funcionário
foreach ($funcionarios as $indice => $funcionario) {

    // Verifica se o código do funcionário corresponde ao código recebido
    if ($funcionario['codigo'] === $codigo) {

        // Se a ação for inativar, altera o campo ativo para false
        if ($acao === 'inativar') {

            $funcionarios[$indice]['ativo'] = false;
        }

        // Caso a ação seja reativar, altera o campo ativo para true
        elseif ($acao === 'reativar') {

            $funcionarios[$indice]['ativo'] = true;
        }

        // Se receber uma ação diferente das permitidas, interrompe o processo
        else {

            break;
        }

        // Grava a lista atualizada no arquivo JSON
        salvarJson('funcionarios.json', $funcionarios);


        // Redireciona para a edição do funcionário após a alteração
        // urlencode prepara o código para ser utilizado na URL
        header('Location: editar_funcionario.php?codigo=' . urlencode($codigo));

        // Encerra a execução do PHP após o redirecionamento
        exit;
    }
}


// Caso nenhum funcionário seja encontrado, retorna para a listagem
header('Location: listar_funcionarios.php');

exit;

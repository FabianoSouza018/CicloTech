
<?php

date_default_timezone_set('America/Sao_Paulo');

// Caminho da pasta onde ficam os arquivos JSON
define('CAMINHO_DADOS', __DIR__ . '/../dados/');

// Função para ler os dados de um arquivo JSON
function lerJson($arquivo)
{

    $caminho = CAMINHO_DADOS . $arquivo;

    if (!file_exists($caminho)) {
        return [];
    }

    $conteudo = file_get_contents($caminho);

    $dados = json_decode($conteudo, true);

    return is_array($dados) ? $dados : [];
}


// Função para salvar os dados em um arquivo JSON
function salvarJson($arquivo, $dados)
{

    $caminho = CAMINHO_DADOS . $arquivo;

    $conteudo = json_encode(
        $dados,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
    );

    return file_put_contents($caminho, $conteudo, LOCK_EX) !== false;
}


<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

// A desativação só pode ocorrer por envio POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_equipamentos.php');
    exit;
}

$codigo = $_POST['codigo'] ?? '';

$equipamentos = lerJson('equipamentos.json');

$encontrado = false;

foreach ($equipamentos as $indice => $equipamento) {

    if (($equipamento['codigo'] ?? '') === $codigo) {

        $encontrado = true;

        // Verifica se já está desativado
        if (($equipamento['ativo'] ?? false) === true) {

            $equipamentos[$indice]['ativo'] = false;

            if (salvarJson('equipamentos.json', $equipamentos)) {
                header('Location: listar_equipamentos.php?resultado=desativado');
                exit;
            }

            header('Location: listar_equipamentos.php?resultado=erro');
            exit;
        }

        break;
    }
}

if (!$encontrado) {
    header('Location: listar_equipamentos.php?resultado=nao_encontrado');
    exit;
}

header('Location: listar_equipamentos.php?resultado=ja_inativo');
exit;

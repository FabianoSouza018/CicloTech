<?php

require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$codigo = $_POST['codigo'] ?? '';
$acao = $_POST['acao'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_equipamentos.php');
    exit;
}

$equipamentos = lerJson('equipamentos.json');

foreach ($equipamentos as $indice => $equipamento) {

    if (($equipamento['codigo'] ?? '') === $codigo) {

        $ativoAtual = ($equipamento['ativo'] ?? false) === true;

        if ($acao === 'desativar') {

            if (!$ativoAtual) {
                header('Location: visualizar_equipamento.php?codigo=' . urlencode($codigo) . '&resultado=ja_alterado');
                exit;
            }

            $equipamentos[$indice]['ativo'] = false;
        } elseif ($acao === 'reativar') {

            if ($ativoAtual) {
                header('Location: visualizar_equipamento.php?codigo=' . urlencode($codigo) . '&resultado=ja_alterado');
                exit;
            }

            $equipamentos[$indice]['ativo'] = true;
        } else {

            header('Location: visualizar_equipamento.php?codigo=' . urlencode($codigo));
            exit;
        }

        if (salvarJson('equipamentos.json', $equipamentos)) {

            header('Location: visualizar_equipamento.php?codigo=' . urlencode($codigo) . '&resultado=alterado');
            exit;
        } else {

            header('Location: visualizar_equipamento.php?codigo=' . urlencode($codigo) . '&resultado=erro');
            exit;
        }
    }
}

header('Location: listar_equipamentos.php?resultado=nao_encontrado');
exit;

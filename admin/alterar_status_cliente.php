<?php
require_once __DIR__ . '/../includes/proteger.php';
require_once __DIR__ . '/../includes/funcoes.php';

$codigo = $_POST['codigo'] ?? '';
$acao = $_POST['acao'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listar_clientes.php');
    exit;
}

$clientes = lerJson('clientes.json');

foreach ($clientes as $indice => $cliente) {

    if ($cliente['codigo'] === $codigo) {

        if ($acao === 'inativar') {
            $clientes[$indice]['ativo'] = false;
        } elseif ($acao === 'reativar') {
            $clientes[$indice]['ativo'] = true;
        } else {
            header('Location: visualizar_cliente.php?codigo=' . urlencode($codigo));
            exit;
        }

        if (salvarJson('clientes.json', $clientes)) {
            header('Location: visualizar_cliente.php?codigo=' . urlencode($codigo) . '&resultado=alterado');
            exit;
        }

        header('Location: visualizar_cliente.php?codigo=' . urlencode($codigo) . '&resultado=erro');
        exit;
    }
}

header('Location: listar_clientes.php');
exit;

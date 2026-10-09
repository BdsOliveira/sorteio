<?php

/**
 * Converte a planilha exportada de participantes em data/participantes.php (array PHP),
 * para que o site não precise ler o .xlsx a cada acesso.
 *
 * Uso: composer lista [caminho/da/planilha.xlsx]
 */

require __DIR__ . '/../vendor/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

const CABECALHO_LISTA = 'Ordem de inscrição';
const ARQUIVO_SAIDA = __DIR__ . '/../data/participantes.php';

// Somente os campos usados no sorteio; e-mail, telefone etc. ficam fora do arquivo gerado
const CAMPOS = ['no_ingresso', 'nome', 'sobrenome', 'data_compra', 'check_in'];

function createSlug($str, $delimiter = '_')
{
    return strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
}

$arquivo = $argv[1] ?? __DIR__ . '/../Lista de participantes - Serto_Tech (3549344).xlsx';

$reader = ReaderEntityFactory::createReaderFromFile($arquivo);
$reader->open($arquivo);

$lista = [];
$headers = [];
foreach ($reader->getSheetIterator() as $sheet) {
    foreach ($sheet->getRowIterator() as $row) {
        $valores = array_map(fn ($cell) => is_string($cell->getValue()) ? trim($cell->getValue()) : $cell->getValue(), $row->getCells());

        if (!$headers) {
            if (($valores[0] ?? null) === CABECALHO_LISTA) {
                $headers = array_map('createSlug', $valores);
            }
            continue;
        }

        // A lista de participantes termina na primeira linha sem "Ordem de inscrição" numérica
        // (seção de produtos/camisetas ou rodapé "Exportado em ...")
        if (!is_numeric($valores[0] ?? null)) {
            break 2;
        }

        $valores = array_pad(array_slice($valores, 0, count($headers)), count($headers), '');
        $participante = array_combine($headers, $valores);

        if ($participante['estado_de_pagamento'] !== 'Aprovado') {
            continue;
        }

        $lista[] = array_intersect_key($participante, array_flip(CAMPOS));
    }
}
$reader->close();

if (!$headers) {
    fwrite(STDERR, "Cabeçalho \"" . CABECALHO_LISTA . "\" não encontrado em $arquivo\n");
    exit(1);
}

file_put_contents(
    ARQUIVO_SAIDA,
    "<?php\n\n// Gerado por bin/gerar-lista.php a partir de " . basename($arquivo) . ". Não editar à mão.\n\nreturn " . var_export($lista, true) . ";\n"
);

echo count($lista) . " participantes gravados em data/participantes.php\n";

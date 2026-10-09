<?php

namespace PhpPiaui\Sorteio;

const SOMENTE_CHECKIN = false;

// Lista gerada a partir da planilha por bin/gerar-lista.php (composer lista)
$lista = require __DIR__ . '/../data/participantes.php';

if (SOMENTE_CHECKIN) {
    $lista = array_values(array_filter($lista, fn ($participante) => $participante['check_in'] === 'Sim'));
}

$deve_fazer_sorteio = false;

if (!empty($_GET['sorteio']) && $lista) {
    $deve_fazer_sorteio = true;

    $id_sorteado = array_rand($lista);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sorteio PHPi</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- Demo styles -->
    <style>
        body {
            position: relative;
        }

        body {
            font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
        }

        .swiper {
            width: 100%;
            height: 65%;
        }

        .swiper-slide {
            text-align: center;
            font-size: 18px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .swiper-slide img {
            display: block;
            width: 100%;
            height: 65%;
        }
    </style>
</head>

<body class="bg-base-100 h-screen">
    <div class="flex justify-center flex-col">
        <div class="flex flex-col text-center m-4">
            <h1 class="text-4xl">Sorteio PHPi</h1>
            <h2 class="text-2xl"><?php echo count($lista) ?> participantes</h2>
        </div>
        <div class="flex flex-col text-center">
            <div class="flex justify-center flex-col">
                <div class="m-2">
                    <a href="?sorteio=true" class="btn btn-primary">
                        <?php echo $deve_fazer_sorteio ? "Sortear Novamente" : "Iniciar Sorteio" ?>
                    </a>
                </div>
                <div class="">
                    <?php 
                        echo !$deve_fazer_sorteio 
                        ? "" 
                        : "<a href='./' class='btn btn-outline btn-sm'>Voltar</a>"
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="<?php echo $deve_fazer_sorteio ? "" : "hidden" ?> swiper mySwiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <?php if ($deve_fazer_sorteio) {
                    echo 'Ingresso nº: ' . $lista[$id_sorteado]['no_ingresso'] . ' <br> ';
                    echo 'Data da compra: ' . $lista[$id_sorteado]['data_compra'] . ' <br> ';
                } ?>
            </div>
            <div class="swiper-slide">
                <?php if ($deve_fazer_sorteio) {
                    echo $lista[$id_sorteado]['nome'] . ' ' . $lista[$id_sorteado]['sobrenome'];
                } ?>
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Initialize Swiper -->
    <script>
        var swiper = new Swiper(".mySwiper", {
            direction: "vertical",
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });
    </script>
</body>

</html>
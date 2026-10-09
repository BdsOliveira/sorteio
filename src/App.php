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
<html lang="pt-BR" data-theme="sertao">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sorteio Sertão Tech</title>
    <link rel="icon" type="image/png" href="assets/favicon-48.png">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.9.4/dist/full.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@700;800&family=Instrument+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Tema DaisyUI com a paleta do design system do Sertão Tech (sertaotech/ds/tokens/colors.css) */
        [data-theme="sertao"] {
            color-scheme: light;
            --p: 23 73% 38%;   /* terra-600 #A8511A */
            --pf: 22 74% 31%;  /* terra-700 #8A4015 */
            --pc: 38 100% 96%; /* text-on-brand #FFF8EC */
            --s: 33 88% 45%;   /* amber-500 #D87D0E */
            --sf: 33 88% 37%;  /* amber-600 #B4670B */
            --sc: 30 68% 10%;  /* espresso-900 #2A1908 */
            --a: 83 42% 34%;   /* cactus-500 #5E7A32 */
            --af: 84 41% 27%;  /* cactus-600 #4C6329 */
            --ac: 42 68% 95%;  /* sand-50 #FBF6EA */
            --n: 27 69% 14%;   /* espresso-800 #3C210B */
            --nf: 30 68% 10%;  /* espresso-900 #2A1908 */
            --nc: 42 65% 89%;  /* sand-100 #F5EAD0 */
            --b1: 42 65% 89%;  /* sand-100 #F5EAD0 */
            --b2: 42 64% 82%;  /* sand-200 #EEDCB2 */
            --b3: 42 60% 72%;  /* sand-300 #E2C88C */
            --bc: 27 69% 14%;  /* espresso-800 #3C210B */
            --swiper-theme-color: #A8511A;
        }

        body {
            position: relative;
        }

        h1 {
            font-family: "League Spartan", system-ui, sans-serif;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        body {
            font-family: "Instrument Sans", system-ui, -apple-system, "Segoe UI", Helvetica, sans-serif;
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
            <img src="assets/logo-360.webp" alt="Sertão Tech" width="160" height="160" class="mx-auto mb-2">
            <h1 class="text-4xl text-primary">Sorteio Sertão Tech</h1>
            <h2 class="text-2xl text-neutral"><?php echo count($lista) ?> participantes</h2>
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
    <script src="https://cdn.jsdelivr.net/npm/@hiseb/confetti@2.2.0/dist/confetti.min.js"></script>

    <!-- Initialize Swiper -->
    <script>
        var swiper = new Swiper(".mySwiper", {
            direction: "vertical",
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            on: {
                // Confete ao revelar o nome sorteado (segundo slide)
                slideChange: function () {
                    if (this.activeIndex === 1) {
                        comemorar();
                    }
                },
            },
        });

        function comemorar() {
            if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
                return;
            }

            // Cores do design system do Sertão Tech: terra, amber, honey, cactus, brick
            var cores = ["#A8511A", "#D87D0E", "#F6C56C", "#5E7A32", "#B23A20"];
            var posicoes = [
                { x: window.innerWidth * 0.50, y: window.innerHeight * 0.60 },
                { x: window.innerWidth * 0.25, y: window.innerHeight * 0.45 },
                { x: window.innerWidth * 0.75, y: window.innerHeight * 0.45 },
            ];
            posicoes.forEach(function (posicao, i) {
                setTimeout(function () {
                    confetti({ position: posicao, count: 150, color: cores });
                }, i * 250);
            });
        }
    </script>
</body>

</html>
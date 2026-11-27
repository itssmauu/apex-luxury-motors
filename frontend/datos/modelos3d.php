<?php
// Créditos de los modelos 3D (img/vehiculos/modelos/ID.glb), descargados de Sketchfab.
// Las licencias Creative Commons piden decir el nombre del modelo, quién lo hizo,
// de dónde viene y qué licencia tiene. La página del auto lo muestra debajo del visor.
// "NC" (NonCommercial) = solo uso no comercial, como este proyecto universitario.
// "angulo": desde dónde se ve el auto al abrir la página (cada modelo viene mirando
// hacia un lado distinto; con este ángulo se ve de frente y un poco de lado).

$modelos3d = [
     1 => ["titulo" => "Nissan Skyline R34 GT-R", "autor" => "Lexyc16", "enlace" => "https://sketchfab.com/3d-models/nissan-skyline-r34-gt-r-ff8fb2251dfa4bb9979e7022c5a6666c", "licencia" => "CC BY 4.0", "angulo" => "-40deg"],
     2 => ["titulo" => "2017 Nissan GT-R (R35)", "autor" => "Ddiaz Design", "enlace" => "https://sketchfab.com/3d-models/2017-nissan-gt-r-r35-341010423e1e455db4da893c052d1cc6", "licencia" => "CC BY-NC-SA 4.0", "angulo" => "-40deg"],
     3 => ["titulo" => "Toyota Supra mk4", "autor" => "Black Snow", "enlace" => "https://sketchfab.com/3d-models/toyota-supra-mk4-8bebdee25b774067929bb33e648533ac", "licencia" => "CC BY 4.0", "angulo" => "-40deg"],
     4 => ["titulo" => "Mazda RX-7 FD", "autor" => "Lexyc16", "enlace" => "https://sketchfab.com/3d-models/mazda-rx-7-fd-d35ff630df614771b82e7b2f59035b1e", "licencia" => "CC BY 4.0", "angulo" => "-40deg"],
     5 => ["titulo" => "Porsche 911 Carrera S (991)", "autor" => "Mona x Supercars", "enlace" => "https://sketchfab.com/3d-models/porsche-911-carrera-s-991-97785983d3a949cf87e9ee6bf49f4032", "licencia" => "CC BY 4.0", "angulo" => "-40deg"],
     6 => ["titulo" => "2015 BMW M4 Coupe (F82)", "autor" => "Ddiaz Design", "enlace" => "https://sketchfab.com/3d-models/2015-bmw-m4-coupe-f82-08030fc6db9b4167b1cddcb04a5d70c1", "licencia" => "CC BY-NC-SA 4.0", "angulo" => "-40deg"],
     7 => ["titulo" => "2008 Audi R8 4.2 FSI Quattro", "autor" => "OUTPISTON", "enlace" => "https://sketchfab.com/3d-models/2008-audi-r8-42-fsi-quattro-ef280285e7a04610ade2052496d98160", "licencia" => "CC BY-NC-SA 4.0", "angulo" => "-40deg"],
     8 => ["titulo" => "2013 Ferrari 458 Spider", "autor" => "Ddiaz Design", "enlace" => "https://sketchfab.com/3d-models/2013-ferrari-458-spider-d12eab5e5c6d421da468e1c348a860a6", "licencia" => "CC BY 4.0", "angulo" => "140deg"],
     9 => ["titulo" => "Lamborghini Aventador LP750-4 SV", "autor" => "Hari Prasath R", "enlace" => "https://sketchfab.com/3d-models/lamborghini-aventador-lp750-4-sv-fec3fec276d240f6b8a660e2a7874435", "licencia" => "CC BY 4.0", "angulo" => "140deg"],
    10 => ["titulo" => "2015 McLaren 650S", "autor" => "Ddiaz Design", "enlace" => "https://sketchfab.com/3d-models/2015-mclaren-650s-45b4de427dd74c0a9a41efacf5cca9ac", "licencia" => "CC BY 4.0", "angulo" => "-40deg"],
    11 => ["titulo" => "Chevrolet Corvette C7 Stingray", "autor" => "Randomness", "enlace" => "https://sketchfab.com/3d-models/chevrolet-corvette-c7-stingray-a06bc587c6594269ac92be854c5bc126", "licencia" => "CC BY 4.0", "angulo" => "140deg"],
    12 => ["titulo" => "2016 Ford Mustang Shelby GT350R", "autor" => "Ddiaz Design", "enlace" => "https://sketchfab.com/3d-models/2016-ford-mustang-shelby-gt350r-1d03bb121b6e4371b0978dc584b788d8", "licencia" => "CC BY-NC-SA 4.0", "angulo" => "-40deg"],
    13 => ["titulo" => "2020 Ford Shelby GT500", "autor" => "OUTPISTON", "enlace" => "https://sketchfab.com/3d-models/2020-ford-shelby-gt500-6c41306ae69b47a495d744ef9c5e70d1", "licencia" => "CC BY-NC-SA 4.0", "angulo" => "-40deg"],
    14 => ["titulo" => "2018 McLaren 570S Spider", "autor" => "Ddiaz Design", "enlace" => "https://sketchfab.com/3d-models/2018-mclaren-570s-spider-a504090e516f44608e35160953b61fcd", "licencia" => "CC BY 4.0", "angulo" => "-40deg"],
];

// Color de la pintura de cada modelo 3D. Los modelos de Sketchfab vienen pintados
// del color que eligió su autor; aquí se cambia para que coincida con las fotos del
// auto (img/vehiculos/*.jpg). js/modelo3d.js lo aplica cuando el modelo termina de cargar.
// "materiales": nombres de los materiales de la carrocería dentro del archivo .glb.
// "color": color de la pintura en las fotos.
// "metalico" (opcional): 0 = pintura sólida, 1 = metal. Para blancos y grises de fábrica.
// "quitarTextura" (opcional): la carrocería traía una imagen con franjas o calcomanías
// de otro color; se quita para dejar solo el color de las fotos.
// "vidrio" (opcional): vidrios que venían teñidos de otro color; se les pone uno oscuro
// y se mantiene su transparencia.

$pinturas3d = [
     1 => ["materiales" => ["Material.001"], "color" => "#6f7762", "metalico" => 0.5],
     2 => ["materiales" => ["NNissan_GTRTNR0_2017PaintTNR_Material1"], "color" => "#b9bdc1", "metalico" => 0.6],
     3 => ["materiales" => ["esta80", ".002"], "color" => "#b3101b",
           "vidrio" => ["materiales" => ["esta80_glass"], "color" => "#1c1c20"]],
     4 => ["materiales" => ["Material.007"], "color" => "#bd0f1c"],
     5 => ["materiales" => ["11Mtl"], "color" => "#3b3b3d", "metalico" => 0.5],
     6 => ["materiales" => ["carpaint"], "color" => "#e6c018"],
     7 => ["materiales" => ["body"], "color" => "#eeeeec", "metalico" => 0.1],
     8 => ["materiales" => ["Fphong1SG1"], "color" => "#c40f19"],
     9 => ["materiales" => ["Body_Colour", "Body_Colour_sv_colour.png", "Interior_Body_Colour", "Body_Colour_sv_interior.png"], "color" => "#f0b405"],
    10 => ["materiales" => ["McLaren_650S_2015Paint_Material"], "color" => "#9a3410"],
    11 => ["materiales" => ["H1Mtl", "Paint2Mtl", "Paint7Mtl", "Rleft1Mtl"], "color" => "#f0b600"],
    12 => ["materiales" => ["shFord_ShelbyGT350R_2016PaintA_Material1"], "color" => "#ececea", "metalico" => 0.1, "quitarTextura" => true],
    13 => ["materiales" => ["2020_ford_shelby_gt500_ext_dod1.etc.png"], "color" => "#123f78", "quitarTextura" => true],
    14 => ["materiales" => ["carpaint"], "color" => "#e8690c"],
];

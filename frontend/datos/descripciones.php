<?php
// Descripciones de los vehículos en los otros idiomas (la base solo las tiene en español).
// "es" es el texto original, copiado tal cual de bases_de_datos/datos_apex_luxury_motors.sql.
// Si el administrador cambia la descripción en la base, ya no coincide con "es"
// y se muestra la de la base (en español) para no enseñar un texto viejo.
// Las traducciones no agregan ni quitan datos: los números son los mismos.
require_once __DIR__ . "/../includes/idioma.php";

$descripciones = [
    1 => [
        "es" => "Leyenda del deportivo japonés con motor de seis cilindros en línea biturbo de 2.6 L y tracción integral. Un clásico del tuning y de los videojuegos.",
        "en" => "A Japanese sports car legend with a 2.6 L twin-turbo inline six-cylinder engine and all-wheel drive. A classic of tuning and video games.",
        "pt" => "Lenda dos esportivos japoneses, com motor de seis cilindros em linha biturbo de 2.6 L e tração integral. Um clássico do tuning e dos videogames.",
        "fr" => "Légende de la sportive japonaise, avec un moteur six cylindres en ligne biturbo de 2.6 L et une transmission intégrale. Un classique du tuning et des jeux vidéo.",
    ],
    2 => [
        "es" => "Conocido como Godzilla: motor V6 biturbo de 3.8 L con más de 550 hp y tracción integral. Aceleración de superdeportivo con uso diario.",
        "en" => "Known as Godzilla: a 3.8 L twin-turbo V6 engine with more than 550 hp and all-wheel drive. Supercar acceleration for everyday use.",
        "pt" => "Conhecido como Godzilla: motor V6 biturbo de 3.8 L com mais de 550 hp e tração integral. Aceleração de superesportivo para o uso diário.",
        "fr" => "Surnommée Godzilla\u{a0}: moteur V6 biturbo de 3.8 L de plus de 550 hp et transmission intégrale. Des accélérations de supercar au quotidien.",
    ],
    3 => [
        "es" => "Motor de seis cilindros en línea biturbo de 3.0 L y 320 hp. Famoso por su resistencia y su potencial de potencia.",
        "en" => "A 3.0 L twin-turbo inline six-cylinder engine with 320 hp. Famous for its durability and its power potential.",
        "pt" => "Motor de seis cilindros em linha biturbo de 3.0 L e 320 hp. Famoso pela resistência e pelo potencial de potência.",
        "fr" => "Moteur six cylindres en ligne biturbo de 3.0 L et 320 hp. Réputé pour sa robustesse et son potentiel de puissance.",
    ],
    4 => [
        "es" => "Deportivo ligero con motor rotativo biturbo de 1.3 L y unos 255 hp. Su diseño y su equilibrio lo hacen único.",
        "en" => "A lightweight sports car with a 1.3 L twin-turbo rotary engine and about 255 hp. Its design and balance make it unique.",
        "pt" => "Esportivo leve com motor rotativo biturbo de 1.3 L e cerca de 255 hp. O design e o equilíbrio o tornam único.",
        "fr" => "Sportive légère avec un moteur rotatif biturbo de 1.3 L et environ 255 hp. Son design et son équilibre la rendent unique.",
    ],
    5 => [
        "es" => "Motor bóxer de seis cilindros y 3.4 L con 350 hp, tracción trasera y el diseño inconfundible de la marca.",
        "en" => "A 3.4 L six-cylinder boxer engine with 350 hp, rear-wheel drive and the brand's unmistakable design.",
        "pt" => "Motor boxer de seis cilindros e 3.4 L com 350 hp, tração traseira e o design inconfundível da marca.",
        "fr" => "Moteur boxer six cylindres de 3.4 L et 350 hp, propulsion et le design inimitable de la marque.",
    ],
    6 => [
        "es" => "Coupé con motor de seis cilindros en línea biturbo de 3.0 L y 425 hp, pensado para conducir con deportividad y comodidad.",
        "en" => "A coupe with a 3.0 L twin-turbo inline six-cylinder engine and 425 hp, designed for sporty yet comfortable driving.",
        "pt" => "Cupê com motor de seis cilindros em linha biturbo de 3.0 L e 425 hp, pensado para uma condução esportiva e confortável.",
        "fr" => "Coupé doté d'un moteur six cylindres en ligne biturbo de 3.0 L et 425 hp, conçu pour une conduite sportive et confortable.",
    ],
    7 => [
        "es" => "Motor V8 central de 4.2 L con 420 hp y tracción integral quattro. Un superdeportivo fácil de manejar todos los días.",
        "en" => "A mid-mounted 4.2 L V8 engine with 420 hp and quattro all-wheel drive. A supercar that is easy to drive every day.",
        "pt" => "Motor V8 central de 4.2 L com 420 hp e tração integral quattro. Um superesportivo fácil de dirigir todos os dias.",
        "fr" => "Moteur V8 central de 4.2 L de 420 hp et transmission intégrale quattro. Une supercar facile à conduire au quotidien.",
    ],
    8 => [
        "es" => "Motor V8 central de 4.5 L con 570 hp y sonido inconfundible. Uno de los Ferrari de motor atmosférico más queridos.",
        "en" => "A mid-mounted 4.5 L V8 engine with 570 hp and an unmistakable sound. One of the most loved naturally aspirated Ferraris.",
        "pt" => "Motor V8 central de 4.5 L com 570 hp e som inconfundível. Uma das Ferrari de motor aspirado mais queridas.",
        "fr" => "Moteur V8 central de 4.5 L de 570 hp au son inimitable. L'une des Ferrari à moteur atmosphérique les plus appréciées.",
    ],
    9 => [
        "es" => "Motor V12 de 6.5 L con 750 hp y tracción integral. Versión Superveloce, enfocada en el máximo rendimiento.",
        "en" => "A 6.5 L V12 engine with 750 hp and all-wheel drive. The Superveloce version, focused on maximum performance.",
        "pt" => "Motor V12 de 6.5 L com 750 hp e tração integral. Versão Superveloce, focada no máximo desempenho.",
        "fr" => "Moteur V12 de 6.5 L de 750 hp et transmission intégrale. Version Superveloce, axée sur la performance maximale.",
    ],
    10 => [
        "es" => "Motor V8 biturbo de 3.8 L con unos 650 CV, techo retráctil y chasis de fibra de carbono. Rapidez con sensación de pista.",
        "en" => "A 3.8 L twin-turbo V8 engine with about 650 CV, a retractable roof and a carbon fiber chassis. Speed with a track feel.",
        "pt" => "Motor V8 biturbo de 3.8 L com cerca de 650 CV, teto retrátil e chassi de fibra de carbono. Velocidade com sensação de pista.",
        "fr" => "Moteur V8 biturbo de 3.8 L d'environ 650 CV, toit rétractable et châssis en fibre de carbone. La vitesse avec des sensations de circuit.",
    ],
    11 => [
        "es" => "Motor V8 de 6.2 L con 455 hp y motor delantero. Rendimiento de superdeportivo a un precio accesible.",
        "en" => "A front-mounted 6.2 L V8 engine with 455 hp. Supercar performance at an affordable price.",
        "pt" => "Motor V8 de 6.2 L com 455 hp, montado na dianteira. Desempenho de superesportivo a um preço acessível.",
        "fr" => "Moteur V8 de 6.2 L de 455 hp placé à l'avant. Des performances de supercar à un prix accessible.",
    ],
    12 => [
        "es" => "Motor V8 de 5.2 L con 526 hp que gira hasta altas revoluciones. Versión de pista del Mustang, con carácter propio.",
        "en" => "A high-revving 5.2 L V8 engine with 526 hp. The track version of the Mustang, with a character of its own.",
        "pt" => "Motor V8 de 5.2 L com 526 hp que gira até altas rotações. Versão de pista do Mustang, com personalidade própria.",
        "fr" => "Moteur V8 de 5.2 L de 526 hp qui monte haut dans les tours. La version piste de la Mustang, avec un caractère bien à elle.",
    ],
    13 => [
        "es" => "Motor V8 sobrealimentado, la versión más potente del Mustang. Pensado para quienes buscan fuerza bruta y carácter.",
        "en" => "A supercharged V8 engine, the most powerful version of the Mustang. Made for those who want raw power and character.",
        "pt" => "Motor V8 sobrealimentado, a versão mais potente do Mustang. Pensado para quem busca força bruta e personalidade.",
        "fr" => "Moteur V8 suralimenté, la version la plus puissante de la Mustang. Pensée pour ceux qui recherchent la force brute et le caractère.",
    ],
    14 => [
        "es" => "Motor V8 biturbo de 3.8 L con unos 570 CV, techo retráctil y chasis de fibra de carbono. La puerta de entrada al mundo McLaren.",
        "en" => "A 3.8 L twin-turbo V8 engine with about 570 CV, a retractable roof and a carbon fiber chassis. The gateway to the world of McLaren.",
        "pt" => "Motor V8 biturbo de 3.8 L com cerca de 570 CV, teto retrátil e chassi de fibra de carbono. A porta de entrada para o mundo McLaren.",
        "fr" => "Moteur V8 biturbo de 3.8 L d'environ 570 CV, toit rétractable et châssis en fibre de carbone. La porte d'entrée dans l'univers McLaren.",
    ],
];

// descripción en el idioma actual. Devuelve el texto de la base si el idioma es español,
// si el vehículo no está en la lista o si la base ya no tiene el texto original
function descripcionVehiculo($id, $textoDeLaBase)
{
    global $descripciones;
    $idioma = idiomaActual();
    $id = (int) $id;

    if ($idioma === "es" || !isset($descripciones[$id][$idioma])) {
        return $textoDeLaBase;
    }
    if ($textoDeLaBase !== $descripciones[$id]["es"]) {
        return $textoDeLaBase;
    }
    return $descripciones[$id][$idioma];
}

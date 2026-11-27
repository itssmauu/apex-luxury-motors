<?php
// Especificaciones técnicas de los vehículos, para mostrarlas en el detalle (vehiculo.php).
// Estos datos NO están en la base: se guardan aquí aparte y se pueden quitar sin afectar nada.
// Los ids son los del SQL de Mauro (bases_de_datos/datos_apex_luxury_motors.sql), del 1 al 14.
// Si un vehículo no está en esta lista, la página no muestra la sección de especificaciones.
//
// Los valores se copiaron de ESPECIFICACIONES_VEHICULOS.md con estos cambios acordados:
// - "No puedo confirmar" se muestra como "No disponible".
// - Las estimaciones dicen "estimación; cifra oficial no disponible".
// - En el 650S y el 570S Spider la potencia va sin rpm.
// - Se quitaron "dato de tercero" y "las fuentes difieren" (hay una nota al pie en la página).

$especificaciones = [
    // 1. Nissan Skyline GT-R (R34)
    1 => [
        "Motor" => "RB26DETT, 6 cilindros en línea, biturbo, DOHC de 4 válvulas por cilindro",
        "Cilindrada" => "2,568 cc",
        "Potencia" => "280 PS a 6,800 rpm (≈206 kW; ≈276 hp)",
        "Torque" => "392 N·m (40.0 kgm) a 4,400 rpm",
        "Aceleración" => "No disponible",
        "Velocidad máxima" => "250 km/h (155 mph)",
        "Tracción" => "Integral",
        "Peso" => "1,540–1,580 kg según versión",
    ],

    // 2. Nissan GT-R (R35)
    2 => [
        "Motor" => "VR38DETT, V6 biturbo",
        "Cilindrada" => "3,799 cc",
        "Potencia" => "550 PS a 6,400 rpm (≈542 hp; ≈404 kW)",
        "Torque" => "632 N·m (466 lb-ft) a 5,800 rpm",
        "Aceleración" => "≈2.8–3.0 s (0–100 km/h)",
        "Velocidad máxima" => "315 km/h (196 mph)",
        "Tracción" => "Integral",
        "Peso" => "1,736–1,783 kg",
    ],

    // 3. Toyota Supra
    3 => [
        "Motor" => "2JZ-GTE, 6 cilindros en línea, biturbo",
        "Cilindrada" => "2,997 cc",
        "Potencia" => "321 hp a 5,600 rpm (≈239 kW), versión EE. UU. y Canadá",
        "Torque" => "315 lb-ft (≈427 N·m) a 4,000 rpm, versión EE. UU. y Canadá",
        "Aceleración" => "0–60 mph: 4.6 s",
        "Velocidad máxima" => "250 km/h (155 mph)",
        "Tracción" => "Trasera",
        "Peso" => "1,615 kg (3,560 lb)",
    ],

    // 4. Mazda RX-7 (FD)
    4 => [
        "Motor" => "13B-REW, rotativo de 2 rotores, biturbo secuencial",
        "Cilindrada" => "1,308 cc (2 × 654 cc)",
        "Potencia" => "≈188 kW a 6,500 rpm (≈255 PS; ≈252 hp)",
        "Torque" => "294 N·m a 5,000 rpm",
        "Aceleración" => "≈5.2–5.4 s (0–62 mph, que son ≈100 km/h)",
        "Velocidad máxima" => "155 mph (≈250 km/h)",
        "Tracción" => "Trasera",
        "Peso" => "1,284 kg",
    ],

    // 5. Porsche 911 Carrera
    5 => [
        "Motor" => "Bóxer de 6 cilindros, atmosférico",
        "Cilindrada" => "3,436 cc (3.4 L)",
        "Potencia" => "350 hp a 7,400 rpm (≈257 kW)",
        "Torque" => "287 lb-ft (≈390 N·m) a 5,600 rpm",
        "Aceleración" => "0–60 mph: 4.6 s en la versión base (las configuraciones más equipadas llegan a 4.2 s)",
        "Velocidad máxima" => "178–179 mph (≈287–288 km/h)",
        "Tracción" => "Trasera",
        "Peso" => "3,042 lb (≈1,380 kg) en la versión base; 3,086 lb (≈1,400 kg) en otra configuración",
    ],

    // 6. BMW M4
    6 => [
        "Motor" => "S55B30, 6 cilindros en línea, biturbo (M TwinPower Turbo)",
        "Cilindrada" => "2,979 cc",
        "Potencia" => "425 hp a 5,500–7,300 rpm (versión EE. UU.; en Europa se declara como 431 PS)",
        "Torque" => "406 lb-ft (≈550 N·m) a 1,850–5,500 rpm",
        "Aceleración" => "≈4.1 s (0–100 km/h)",
        "Velocidad máxima" => "250 km/h (155 mph)",
        "Tracción" => "Trasera",
        "Peso" => "1,537 kg (3,389 lb)",
    ],

    // 7. Audi R8 (primera generación)
    7 => [
        "Motor" => "V8 central 4.2 FSI, atmosférico",
        "Cilindrada" => "4,163 cc",
        "Potencia" => "420 PS a 7,800 rpm (≈414 hp; 309 kW)",
        "Torque" => "430 N·m (≈317 lb-ft) a 4,500–6,000 rpm",
        "Aceleración" => "0–100 km/h: 4.6 s",
        "Velocidad máxima" => "301 km/h (187 mph)",
        "Tracción" => "Integral (quattro)",
        "Peso" => "1,635 kg (3,605 lb)",
    ],

    // 8. Ferrari 458
    8 => [
        "Motor" => "V8 de 90°, 4.5 L, atmosférico, inyección directa, central",
        "Cilindrada" => "≈4,497–4,499 cc",
        "Potencia" => "570 CV a 9,000 rpm (≈562 hp; 425 kW)",
        "Torque" => "540 N·m (≈398 lb-ft) a 6,000 rpm",
        "Aceleración" => "Menos de 3.4 s (0–100 km/h), cifra de Ferrari",
        "Velocidad máxima" => "Más de 325 km/h (202 mph)",
        "Tracción" => "Trasera",
        "Peso" => "1,380 kg en seco",
    ],

    // 9. Lamborghini Aventador LP750-4 SV
    9 => [
        "Motor" => "V12 de 60°, 6.5 L, atmosférico",
        "Cilindrada" => "6,498 cc",
        "Potencia" => "552 kW (≈750 PS, calculado: 552 × 1.35962) a 8,400 rpm; ≈740 hp",
        "Torque" => "690 N·m (≈509 lb-ft) a 5,500 rpm",
        "Aceleración" => "0–100 km/h: 2.8 s",
        "Velocidad máxima" => "Más de 350 km/h",
        "Tracción" => "Integral",
        "Peso" => "1,525 kg (la fuente no aclara si es en seco o en orden de marcha)",
    ],

    // 10. McLaren 650S Spider
    10 => [
        "Motor" => "V8 biturbo de 3.8 L, central, cárter seco",
        "Cilindrada" => "3,799 cc",
        "Potencia" => "650 PS (641 bhp)",
        "Torque" => "678 N·m (500 lb-ft) a 6,000 rpm",
        "Aceleración" => "0–100 km/h: 3.0 s",
        "Velocidad máxima" => "329 km/h (204 mph)",
        "Tracción" => "Trasera",
        "Peso" => "≈1,468 kg",
    ],

    // 11. Chevrolet Corvette Stingray (C7)
    11 => [
        "Motor" => "LT1, V8 de 6.2 L, atmosférico, motor delantero",
        "Cilindrada" => "6.2 L (376 pulgadas cúbicas)",
        "Potencia" => "455 hp a 6,000 rpm (≈339 kW); 460 hp con escape de rendimiento opcional",
        "Torque" => "460 lb-ft (≈624 N·m) a 4,600 rpm; 465 lb-ft (≈630 N·m) con escape opcional",
        "Aceleración" => "0–60 mph: 3.8 s (con paquete Z51)",
        "Velocidad máxima" => "No disponible",
        "Tracción" => "Trasera",
        "Peso" => "3,298 lb (≈1,496 kg) en el coupé; la fuente indica 1,499 kg",
    ],

    // 12. Ford Mustang Shelby GT350 (S550)
    12 => [
        "Motor" => "5.2 L V8 atmosférico con cigüeñal plano",
        "Cilindrada" => "5,163 cc (315 pulgadas cúbicas)",
        "Potencia" => "526 hp a 7,500 rpm (≈392 kW)",
        "Torque" => "429 lb-ft (≈582 N·m) a 4,750 rpm",
        "Aceleración" => "≈4.3 s (0–60 mph), estimación; cifra oficial no disponible",
        "Velocidad máxima" => "≈175–180 mph (≈282–290 km/h), estimación; cifra oficial no disponible",
        "Tracción" => "Trasera, con diferencial Torsen",
        "Peso" => "3,760 lb (≈1,705 kg)",
    ],

    // 13. Ford Mustang Shelby GT500
    13 => [
        "Motor" => "5.2 L V8 sobrealimentado (supercharged)",
        "Cilindrada" => "5,163 cc",
        "Potencia" => "760 hp a 7,300 rpm (≈567 kW)",
        "Torque" => "625 lb-ft (≈847 N·m) a 5,000 rpm",
        "Aceleración" => "0–60 mph: ≈3.5–3.8 s, estimación; cifra oficial no disponible",
        "Velocidad máxima" => "180 mph (≈290 km/h)",
        "Tracción" => "Trasera",
        "Peso" => "4,182 lb (≈1,897 kg)",
    ],

    // 14. McLaren 570S Spider
    14 => [
        "Motor" => "V8 biturbo de 3.8 L, central, cárter seco",
        "Cilindrada" => "3,799 cc",
        "Potencia" => "570 PS (562 bhp)",
        "Torque" => "600 N·m (443 lb-ft) a 5,000–6,500 rpm",
        "Aceleración" => "0–100 km/h: 3.2 s",
        "Velocidad máxima" => "328 km/h (204 mph) con el techo cerrado; 315 km/h con el techo abierto",
        "Tracción" => "Trasera",
        "Peso" => "1,503 kg en orden de marcha; 1,359 kg en seco (folleto de McLaren reproducido por un tercero)",
    ],
];

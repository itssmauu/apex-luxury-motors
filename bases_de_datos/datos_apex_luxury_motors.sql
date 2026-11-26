-- Datos de prueba de Apex Luxury Motors (demo)
-- Correr despues de apex_luxury_motors.sql
USE apex_luxury_motors;

-- Usuarios de prueba (contrasenas ya cifradas)
-- administrador: admin@apexluxury.com / Admin123*
-- cliente: cliente@apexluxury.com / Cliente123*
INSERT INTO usuarios (nombre, correo, contrasena, telefono, rol) VALUES
('Administrador Apex', 'admin@apexluxury.com',   '$2y$10$CzE.g0Xv/dUJyrq9o0ftDeBg8UOPt2oSZJPCDNqUjuKxOG6bQWa02', '6000-0001', 'administrador'),
('Cliente de Prueba',  'cliente@apexluxury.com', '$2y$10$FlMGh.s.58sBwffv/dpG8uIYnaAt607/lIHkwYp.xSQ1mu0LH1SE.', '6000-0002', 'cliente');

-- Los 12 vehiculos del catalogo (precios en USD, de demostracion)
INSERT INTO vehiculos (marca, modelo, precio, categoria, descripcion) VALUES
('Nissan',    'Skyline GT-R (R34)',          89900.00,  'Deportivo japonés',          'Leyenda del deportivo japonés con motor de seis cilindros en línea biturbo de 2.6 L y tracción integral. Un clásico del tuning y de los videojuegos.'),
('Nissan',    'GT-R (R35)',                  124900.00, 'Deportivo japonés',          'Conocido como Godzilla: motor V6 biturbo de 3.8 L con más de 550 hp y tracción integral. Aceleración de superdeportivo con uso diario.'),
('Toyota',    'Supra',                       59900.00,  'Deportivo japonés',          'Motor de seis cilindros en línea biturbo de 3.0 L y 320 hp. Famoso por su resistencia y su potencial de potencia.'),
('Mazda',     'RX-7 (FD)',                   49900.00,  'Deportivo japonés',          'Deportivo ligero con motor rotativo biturbo de 1.3 L y unos 255 hp. Su diseño y su equilibrio lo hacen único.'),
('Porsche',   '911 Carrera',                 119900.00, 'Deportivo alemán',           'Motor bóxer de seis cilindros y 3.4 L con 350 hp, tracción trasera y el diseño inconfundible de la marca.'),
('BMW',       'M4',                          84900.00,  'Deportivo alemán',           'Coupé con motor de seis cilindros en línea biturbo de 3.0 L y 425 hp, pensado para conducir con deportividad y comodidad.'),
('Audi',      'R8 (primera generación)',     159900.00, 'Deportivo alemán',           'Motor V8 central de 4.2 L con 420 hp y tracción integral quattro. Un superdeportivo fácil de manejar todos los días.'),
('Ferrari',   '458',                         239900.00, 'Superdeportivo',             'Motor V8 central de 4.5 L con 570 hp y sonido inconfundible. Uno de los Ferrari de motor atmosférico más queridos.'),
('Lamborghini', 'Aventador LP750-4 SV',      449900.00, 'Superdeportivo',             'Motor V12 de 6.5 L con 750 hp y tracción integral. Versión Superveloce, enfocada en el máximo rendimiento.'),
('McLaren',   '650S Spider',                 279900.00, 'Superdeportivo',             'Motor V8 biturbo de 3.8 L con unos 650 CV, techo retráctil y chasis de fibra de carbono. Rapidez con sensación de pista.'),
('Chevrolet', 'Corvette Stingray (C7)',      62900.00,  'Deportivo estadounidense',   'Motor V8 de 6.2 L con 455 hp y motor delantero. Rendimiento de superdeportivo a un precio accesible.'),
('Ford',      'Mustang Shelby GT350 (S550)', 59900.00,  'Deportivo estadounidense',   'Motor V8 de 5.2 L con 526 hp que gira hasta altas revoluciones. Versión de pista del Mustang, con carácter propio.');

-- Una foto por vehiculo, en el mismo orden de arriba
INSERT INTO fotos (vehiculo_id, ruta, orden) VALUES
(1,  'img/Nissan_Skyline_R34_GT-R.jpg', 1),
(2,  'img/Nissan_R35_GT_R.jpg', 1),
(3,  'img/1994_Toyota_Supra.jpg', 1),
(4,  'img/Mazda_RX-7_FD3S,_front_right,_05-05-2024.jpg', 1),
(5,  'img/2013_Porsche_911_Carrera.jpg', 1),
(6,  'img/2016_BMW_M4.jpg', 1),
(7,  'img/Audi_R8_Front-view.jpg', 1),
(8,  'img/Ferrari_458.jpg', 1),
(9,  'img/Lamborghini_Aventador_LP750-4_SV.jpg', 1),
(10, 'img/McLaren_Spider_3799cc_registered_June_2016.jpg', 1),
(11, 'img/C7_Corvette_Stingray_Z51_2014.jpg', 1),
(12, 'img/Ford_MUSTANG_SHELBY_GT350_(S550).jpg', 1);

-- Dos vehiculos mas para tener mas variedad
INSERT INTO vehiculos (marca, modelo, precio, categoria, descripcion) VALUES
('Ford', 'Mustang Shelby GT500', 79900.00, 'Deportivo estadounidense',
 'Motor V8 sobrealimentado, la versión más potente del Mustang. Pensado para quienes buscan fuerza bruta y carácter.'),
('McLaren', '570S Spider', 209900.00, 'Superdeportivo',
 'Motor V8 biturbo de 3.8 L con unos 570 CV, techo retráctil y chasis de fibra de carbono. La puerta de entrada al mundo McLaren.');

INSERT INTO fotos (vehiculo_id, ruta, orden) VALUES
((SELECT id FROM vehiculos WHERE marca = 'Ford' AND modelo = 'Mustang Shelby GT500'),
 'img/Ford_Mustang_Shelby_GT_500.jpg', 1),
((SELECT id FROM vehiculos WHERE marca = 'McLaren' AND modelo = '570S Spider'),
 'img/McLaren_570S_Spider_IMG_9945.jpg', 1);

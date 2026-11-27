-- Fotos extra de cada vehículo: perfil, trasera, interior y puertas abiertas
-- Correr después de datos_apex_luxury_motors.sql (se puede volver a correr sin duplicar)
-- Los archivos están en img/vehiculos/. La palabra final del nombre (lateral, trasera,
-- interior, puertas, cabina) es la que la página usa para nombrar cada vista.
USE apex_luxury_motors;

-- si ya se había corrido, primero se quitan las de la vez anterior
DELETE FROM fotos WHERE ruta LIKE 'img/vehiculos/%';

-- Nissan Skyline GT-R (R34)
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/nissan-skyline-r34-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Nissan' AND v.modelo = 'Skyline GT-R (R34)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/nissan-skyline-r34-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Nissan' AND v.modelo = 'Skyline GT-R (R34)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/nissan-skyline-r34-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Nissan' AND v.modelo = 'Skyline GT-R (R34)';

-- Nissan GT-R (R35)
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/nissan-gtr-r35-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Nissan' AND v.modelo = 'GT-R (R35)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/nissan-gtr-r35-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Nissan' AND v.modelo = 'GT-R (R35)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/nissan-gtr-r35-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Nissan' AND v.modelo = 'GT-R (R35)';

-- Toyota Supra
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/toyota-supra-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Toyota' AND v.modelo = 'Supra';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/toyota-supra-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Toyota' AND v.modelo = 'Supra';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/toyota-supra-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Toyota' AND v.modelo = 'Supra';

-- Mazda RX-7 (FD)
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/mazda-rx7-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Mazda' AND v.modelo = 'RX-7 (FD)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/mazda-rx7-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Mazda' AND v.modelo = 'RX-7 (FD)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/mazda-rx7-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Mazda' AND v.modelo = 'RX-7 (FD)';

-- Porsche 911 Carrera
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/porsche-911-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Porsche' AND v.modelo = '911 Carrera';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/porsche-911-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Porsche' AND v.modelo = '911 Carrera';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/porsche-911-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Porsche' AND v.modelo = '911 Carrera';

-- BMW M4
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/bmw-m4-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'BMW' AND v.modelo = 'M4';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/bmw-m4-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'BMW' AND v.modelo = 'M4';

-- Audi R8 (primera generación)
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/audi-r8-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Audi' AND v.modelo = 'R8 (primera generación)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/audi-r8-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Audi' AND v.modelo = 'R8 (primera generación)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/audi-r8-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Audi' AND v.modelo = 'R8 (primera generación)';

-- Ferrari 458
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/ferrari-458-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Ferrari' AND v.modelo = '458';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/ferrari-458-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Ferrari' AND v.modelo = '458';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/ferrari-458-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Ferrari' AND v.modelo = '458';

-- Lamborghini Aventador LP750-4 SV
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/lamborghini-aventador-sv-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Lamborghini' AND v.modelo = 'Aventador LP750-4 SV';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/lamborghini-aventador-sv-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Lamborghini' AND v.modelo = 'Aventador LP750-4 SV';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/lamborghini-aventador-sv-puertas.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Lamborghini' AND v.modelo = 'Aventador LP750-4 SV';

-- McLaren 650S Spider
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/mclaren-650s-spider-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'McLaren' AND v.modelo = '650S Spider';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/mclaren-650s-spider-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'McLaren' AND v.modelo = '650S Spider';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/mclaren-650s-spider-puertas.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'McLaren' AND v.modelo = '650S Spider';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/mclaren-650s-spider-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'McLaren' AND v.modelo = '650S Spider';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/mclaren-650s-spider-cabina.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'McLaren' AND v.modelo = '650S Spider';

-- Chevrolet Corvette Stingray (C7)
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/chevrolet-corvette-c7-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Chevrolet' AND v.modelo = 'Corvette Stingray (C7)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/chevrolet-corvette-c7-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Chevrolet' AND v.modelo = 'Corvette Stingray (C7)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/chevrolet-corvette-c7-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Chevrolet' AND v.modelo = 'Corvette Stingray (C7)';

-- Ford Mustang Shelby GT350 (S550)
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/ford-shelby-gt350-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Ford' AND v.modelo = 'Mustang Shelby GT350 (S550)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/ford-shelby-gt350-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Ford' AND v.modelo = 'Mustang Shelby GT350 (S550)';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/ford-shelby-gt350-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Ford' AND v.modelo = 'Mustang Shelby GT350 (S550)';

-- Ford Mustang Shelby GT500
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/ford-shelby-gt500-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Ford' AND v.modelo = 'Mustang Shelby GT500';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/ford-shelby-gt500-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Ford' AND v.modelo = 'Mustang Shelby GT500';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/ford-shelby-gt500-interior.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'Ford' AND v.modelo = 'Mustang Shelby GT500';

-- McLaren 570S Spider
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/mclaren-570s-spider-puertas.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'McLaren' AND v.modelo = '570S Spider';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/mclaren-570s-spider-lateral.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'McLaren' AND v.modelo = '570S Spider';
INSERT INTO fotos (vehiculo_id, ruta, orden)
SELECT v.id, 'img/vehiculos/mclaren-570s-spider-trasera.jpg',
       (SELECT COALESCE(MAX(f.orden), 0) + 1 FROM fotos f WHERE f.vehiculo_id = v.id)
FROM vehiculos v WHERE v.marca = 'McLaren' AND v.modelo = '570S Spider';

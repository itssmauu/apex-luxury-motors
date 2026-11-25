-- Base de datos del proyecto Apex Luxury Motors (demo)
-- Motor: MySQL 8 o MariaDB 10.2 en adelante

DROP DATABASE IF EXISTS apex_luxury_motors;
CREATE DATABASE apex_luxury_motors CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE apex_luxury_motors;

-- Personas que usan el sitio (clientes y administradores)
CREATE TABLE usuarios (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre      VARCHAR(100) NOT NULL,
  correo      VARCHAR(120) NOT NULL,
  contrasena  VARCHAR(255) NOT NULL,   -- se guarda cifrada, nunca en texto plano
  telefono    VARCHAR(20)  NOT NULL,
  rol         ENUM('cliente','administrador') NOT NULL DEFAULT 'cliente',
  activo      TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_usuarios_correo (correo)   -- no se repiten correos
) ENGINE=InnoDB;

-- Autos del catalogo (en dolares)
CREATE TABLE vehiculos (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  marca        VARCHAR(50)  NOT NULL,
  modelo       VARCHAR(80)  NOT NULL,
  precio       DECIMAL(12,2) NOT NULL,
  categoria    VARCHAR(50)  NOT NULL,
  descripcion  TEXT         NOT NULL,
  estado       ENUM('disponible','vendido') NOT NULL DEFAULT 'disponible',
  activo       TINYINT(1)   NOT NULL DEFAULT 1,   -- desactivar en vez de borrar
  PRIMARY KEY (id),
  CONSTRAINT ck_vehiculos_precio CHECK (precio > 0),
  KEY ix_vehiculos_marca (marca),
  KEY ix_vehiculos_categoria (categoria),
  KEY ix_vehiculos_precio (precio)    -- ayudan a los filtros del catalogo
) ENGINE=InnoDB;

-- Fotos de cada vehiculo (por ahora una sola por auto)
CREATE TABLE fotos (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  vehiculo_id  INT UNSIGNED NOT NULL,
  ruta         VARCHAR(200) NOT NULL,   -- archivo dentro de la carpeta img/
  orden        INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_fotos_orden (vehiculo_id, orden),
  CONSTRAINT fk_fotos_vehiculo FOREIGN KEY (vehiculo_id)
    REFERENCES vehiculos (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Solicitudes de compra de los clientes
CREATE TABLE solicitudes (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id   INT UNSIGNED NOT NULL,
  vehiculo_id  INT UNSIGNED NOT NULL,
  fecha        DATE NOT NULL DEFAULT (CURRENT_DATE),
  estado       ENUM('pendiente','aprobada','rechazada','cancelada') NOT NULL DEFAULT 'pendiente',
  comentario   VARCHAR(255) NULL,   -- lo escribe el administrador, es opcional
  -- truco para no permitir dos pendientes iguales: vale 1 solo si esta pendiente
  es_pendiente TINYINT GENERATED ALWAYS AS (IF(estado = 'pendiente', 1, NULL)) STORED,
  PRIMARY KEY (id),
  UNIQUE KEY uq_una_pendiente (usuario_id, vehiculo_id, es_pendiente),
  KEY ix_solicitudes_vehiculo (vehiculo_id),
  KEY ix_solicitudes_estado (estado),
  CONSTRAINT fk_solicitudes_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios (id),
  CONSTRAINT fk_solicitudes_vehiculo FOREIGN KEY (vehiculo_id)
    REFERENCES vehiculos (id)
) ENGINE=InnoDB;

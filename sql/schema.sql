-- Script de creación de base de datos para el sistema de diseños curriculares
-- Crear base de datos
CREATE DATABASE IF NOT EXISTS disenos_curriculares 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE disenos_curriculares;

-- Tabla de diseños
CREATE TABLE `diseños` (
    `codigoDiseño` VARCHAR(255) NOT NULL COMMENT 'Formato: codigoPrograma-versionPrograma (ej: 124101-1)',
    `codigoPrograma` VARCHAR(255) NOT NULL,
    `versionPograma` VARCHAR(255) NOT NULL,
    `lineaTecnologica` VARCHAR(255) NOT NULL,
    `redTecnologica` VARCHAR(255) NOT NULL,
    `redConocimiento` VARCHAR(255) NOT NULL,
    `horasDesarrolloDiseño` DECIMAL(10,2) NOT NULL,
    `mesesDesarrolloDiseño` DECIMAL(10,2) NOT NULL,
    `nivelAcademicoIngreso` VARCHAR(255) NOT NULL,
    `gradoNivelAcademico` VARCHAR(255),
    `formacionTrabajoDesarrolloHumano` ENUM('Si', 'No') NOT NULL,
    `edadMinima` INT NOT NULL,
    `requisitosAdicionales` TEXT,
    `fechaCreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `fechaActualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(`codigoDiseño`),
    INDEX idx_codigo_programa (`codigoPrograma`),
    INDEX idx_linea_tecnologica (`lineaTecnologica`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de competencias
CREATE TABLE `competencias` (
    `codigoDiseñoCompetencia` VARCHAR(255) NOT NULL COMMENT 'Formato: codigoDiseño-codigoCompetencia (ej: 124101-1-220201501)',
    `codigoCompetencia` VARCHAR(255) NOT NULL,
    `nombreCompetencia` VARCHAR(255) NOT NULL,
    `normaUnidadCompetencia` TEXT,
    `horasDesarrolloCompetencia` DECIMAL(10,2) NOT NULL,
    `requisitosAcademicosInstructor` TEXT,
    `experienciaLaboralInstructor` TEXT,
    `fechaCreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `fechaActualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(`codigoDiseñoCompetencia`),
    INDEX idx_codigo_competencia (`codigoCompetencia`),
    INDEX idx_nombre_competencia (`nombreCompetencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de RAPs (Resultados de Aprendizaje)
CREATE TABLE `raps` (
    `codigoDiseñoCompetenciaRap` VARCHAR(255) NOT NULL COMMENT 'Formato: codigoDiseño-codigoCompetencia-codigoRap (ej: 124101-1-220201501-RA1)',
    `codigoRap` VARCHAR(55) NOT NULL,
    `nombreRap` TEXT NOT NULL,
    `horasDesarrolloRap` DECIMAL(10,2) NOT NULL,
    `fechaCreacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `fechaActualizacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY(`codigoDiseñoCompetenciaRap`),
    INDEX idx_codigo_rap (`codigoRap`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo para testing
INSERT INTO `diseños` (
    `codigoDiseño`, `codigoPrograma`, `versionPograma`, `lineaTecnologica`, 
    `redTecnologica`, `redConocimiento`, `horasDesarrolloDiseño`, `mesesDesarrolloDiseño`,
    `nivelAcademicoIngreso`, `gradoNivelAcademico`, `formacionTrabajoDesarrolloHumano`, 
    `edadMinima`, `requisitosAdicionales`
) VALUES 
(
    '124101-1', '124101', '1', 'Gestión de la Información',
    'Red de Tecnologías de Información y Comunicaciones', 'Mercadeo y Publicidad',
    2640.00, 18.00, 'Bachillerato', '11',
    'Si', 16, 'Conocimientos básicos en matemáticas y habilidades comunicativas'
),
(
    '228106-2', '228106', '2', 'Producción y Transformación',
    'Red de Tecnologías Agrícolas', 'Producción Agropecuaria',
    1980.00, 12.00, 'Bachillerato', '11',
    'Si', 17, 'Disponibilidad para trabajo de campo'
);

INSERT INTO `competencias` (
    `codigoDiseñoCompetencia`, `codigoCompetencia`, `nombreCompetencia`,
    `normaUnidadCompetencia`, `horasDesarrolloCompetencia`,
    `requisitosAcademicosInstructor`, `experienciaLaboralInstructor`
) VALUES 
(
    '124101-1-220201501', '220201501', 'Desarrollar procesos de mercadeo',
    'NCL 220201501: Desarrollar procesos de mercadeo según el tipo de producto o servicio y características del consumidor',
    440.00, 'Profesional en Mercadeo, Administración o áreas afines',
    '24 meses de experiencia en mercadeo y ventas'
),
(
    '124101-1-220201502', '220201502', 'Realizar investigación de mercados',
    'NCL 220201502: Realizar investigación de mercados de acuerdo con el tipo de producto o servicio',
    360.00, 'Profesional en Mercadeo, Estadística o áreas afines',
    '18 meses de experiencia en investigación de mercados'
);

INSERT INTO `raps` (
    `codigoDiseñoCompetenciaRap`, `codigoRap`, `nombreRap`, `horasDesarrolloRap`
) VALUES 
(
    '124101-1-220201501-RA1', 'RA1', 'Identificar las necesidades del cliente de acuerdo con los objetivos de la empresa',
    80.00
),
(
    '124101-1-220201501-RA2', 'RA2', 'Definir el producto o servicio según las características del mercado objetivo',
    120.00
),
(
    '124101-1-220201502-RA1', 'RA1', 'Diseñar instrumentos de recolección de información según el tipo de investigación',
    90.00
);

-- Comentarios sobre el funcionamiento de las llaves:
-- 
-- diseños: codigoDiseño = codigoPrograma + "-" + versionPograma
-- Ejemplo: codigoPrograma=124101, versionPograma=1 → codigoDiseño=124101-1
--
-- competencias: codigoDiseñoCompetencia = codigoDiseño + "-" + codigoCompetencia  
-- Ejemplo: codigoDiseño=124101-1, codigoCompetencia=220201501 → codigoDiseñoCompetencia=124101-1-220201501
--
-- raps: codigoDiseñoCompetenciaRap = codigoDiseño + "-" + codigoCompetencia + "-" + codigoRap
-- Ejemplo: codigoDiseño=124101-1, codigoCompetencia=220201501, codigoRap=RA1 → codigoDiseñoCompetenciaRap=124101-1-220201501-RA1

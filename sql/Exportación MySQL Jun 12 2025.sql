CREATE TABLE `diseños`(
    `codigoDiseño` VARCHAR(255), -- en el registro se debe concatenear: "codigoPrograma{tabla:diseños}-versionPograma{tabla:diseños}" 
    -- ejemplo final: codigoPrograma=124101
    -- versionPograma=1
    -- codigoDiseño=124101-1
    `codigoPrograma` VARCHAR(255),
    `versionPograma` VARCHAR(255),
    --"nuevo campo:
    `nombrePrograma` VARCHAR(255),
    --"
    `lineaTecnologica` VARCHAR(255),
    `redTecnologica` VARCHAR(255),
    `redConocimiento` VARCHAR(255),
    --"nuevo campo:
    `horasDesarrolloLectiva` DECIMAL(10,2),
    `horasDesarrolloProductiva` DECIMAL(10,2),
    `mesesDesarrolloLectiva` DECIMAL(10,2),
    `mesesDesarrolloProductiva` DECIMAL(10,2),
    --"
    `horasDesarrolloDiseño` DECIMAL(10,2), -- es la suma entre: horasDesarrolloLectiva+horasDesarrolloProductiva
    `mesesDesarrolloDiseño` DECIMAL(10,2), -- es la suma entre: mesesDesarrolloLectiva+mesesDesarrolloProductiva
    `nivelAcademicoIngreso` VARCHAR(255),
    `gradoNivelAcademico` INT, -- antes estaba en VARCHAR(255) pero ahora se cambio a INT
    `formacionTrabajoDesarrolloHumano` ENUM('Si', 'No'),
    `edadMinima` INT,
    `requisitosAdicionales` TEXT,
    PRIMARY KEY(`codigoDiseño`)
);
CREATE TABLE `competencias`(
    `codigoDiseñoCompetencia` VARCHAR(255), -- en el registro se debe concatenear: "codigoDiseño{tabla:diseños}-codigoCompetencia{tabla:competencias}"
    -- ejemplo final: codigoDiseño=124101-1
    -- codigoCompetencia=220201501
    -- codigoDiseñoCompetencia=124101-1-220201501
    `codigoCompetencia` VARCHAR(255),
    `nombreCompetencia` VARCHAR(255),
    `normaUnidadCompetencia` TEXT,
    `horasDesarrolloCompetencia` DECIMAL(10,2),
    `requisitosAcademicosInstructor` TEXT,
    `experienciaLaboralInstructor` TEXT,
    PRIMARY KEY(`codigoDiseñoCompetencia`)
);
CREATE TABLE `raps`(
    `codigoDiseñoCompetenciaRap` VARCHAR(255), -- en el registro se debe concatenear: "codigoDiseño{tabla:diseños}-codigoCompetencia{tabla:competencias}-codigoRap{tabla:raps}"
    -- ejemplo final: codigoDiseño=124101-1
    -- codigoCompetencia=220201501
    -- codigoRap=RA1
    -- codigoDiseñoCompetenciaRap=124101-1-220201501-RA1
    `codigoRap` VARCHAR(55),
    `nombreRap` TEXT,
    `horasDesarrolloRap` DECIMAL(10,2),
    PRIMARY KEY(`codigoDiseñoCompetenciaRap`)
);

-- en los ejemplo se refleja como deben funsionar las llaves..


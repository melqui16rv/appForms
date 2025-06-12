const express = require('express');
const router = express.Router();
const Database = require('../conf/database');

// Obtener todos los diseños
router.get('/', async (req, res) => {
    try {
        const disenos = await Database.query('SELECT * FROM diseños ORDER BY codigoDiseño');
        res.json(disenos);
    } catch (error) {
        res.status(500).json({ error: 'Error al obtener los diseños', details: error.message });
    }
});

// Obtener un diseño por código
router.get('/:codigo', async (req, res) => {
    try {
        const [diseno] = await Database.query(
            'SELECT * FROM diseños WHERE codigoDiseño = ?',
            [req.params.codigo]
        );
        
        if (diseno) {
            res.json(diseno);
        } else {
            res.status(404).json({ error: 'Diseño no encontrado' });
        }
    } catch (error) {
        res.status(500).json({ error: 'Error al obtener el diseño', details: error.message });
    }
});

// Validar si existe un diseño
router.get('/validate/:codigoPrograma/:versionPrograma', async (req, res) => {
    try {
        const codigoDiseño = `${req.params.codigoPrograma}-${req.params.versionPrograma}`;
        const [diseno] = await Database.query(
            'SELECT * FROM diseños WHERE codigoDiseño = ?',
            [codigoDiseño]
        );
        
        res.json({ 
            exists: !!diseno, 
            diseno: diseno || null,
            codigoDiseño: codigoDiseño
        });
    } catch (error) {
        res.status(500).json({ error: 'Error al validar el diseño', details: error.message });
    }
});

// Crear un nuevo diseño
router.post('/', async (req, res) => {
    try {
        const {
            codigoPrograma,
            versionPograma,
            lineaTecnologica,
            redTecnologica,
            redConocimiento,
            horasDesarrolloDiseño,
            mesesDesarrolloDiseño,
            nivelAcademicoIngreso,
            gradoNivelAcademico,
            formacionTrabajoDesarrolloHumano,
            edadMinima,
            requisitosAdicionales
        } = req.body;

        // Generar el código del diseño
        const codigoDiseño = `${codigoPrograma}-${versionPograma}`;

        // Verificar si ya existe
        const [existingDiseno] = await Database.query(
            'SELECT codigoDiseño FROM diseños WHERE codigoDiseño = ?',
            [codigoDiseño]
        );

        if (existingDiseno) {
            return res.status(400).json({ 
                error: 'El diseño ya existe', 
                codigoDiseño: codigoDiseño 
            });
        }

        // Insertar nuevo diseño
        await Database.query(
            `INSERT INTO diseños (
                codigoDiseño, codigoPrograma, versionPograma, lineaTecnologica,
                redTecnologica, redConocimiento, horasDesarrolloDiseño,
                mesesDesarrolloDiseño, nivelAcademicoIngreso, gradoNivelAcademico,
                formacionTrabajoDesarrolloHumano, edadMinima, requisitosAdicionales
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
            [
                codigoDiseño, codigoPrograma, versionPograma, lineaTecnologica,
                redTecnologica, redConocimiento, horasDesarrolloDiseño,
                mesesDesarrolloDiseño, nivelAcademicoIngreso, gradoNivelAcademico,
                formacionTrabajoDesarrolloHumano, edadMinima, requisitosAdicionales
            ]
        );

        res.status(201).json({ 
            message: 'Diseño creado exitosamente', 
            codigoDiseño: codigoDiseño 
        });
    } catch (error) {
        res.status(500).json({ error: 'Error al crear el diseño', details: error.message });
    }
});

// Actualizar un diseño
router.put('/:codigo', async (req, res) => {
    try {
        const {
            lineaTecnologica,
            redTecnologica,
            redConocimiento,
            horasDesarrolloDiseño,
            mesesDesarrolloDiseño,
            nivelAcademicoIngreso,
            gradoNivelAcademico,
            formacionTrabajoDesarrolloHumano,
            edadMinima,
            requisitosAdicionales
        } = req.body;

        const result = await Database.query(
            `UPDATE diseños SET 
                lineaTecnologica = ?, redTecnologica = ?, redConocimiento = ?,
                horasDesarrolloDiseño = ?, mesesDesarrolloDiseño = ?,
                nivelAcademicoIngreso = ?, gradoNivelAcademico = ?,
                formacionTrabajoDesarrolloHumano = ?, edadMinima = ?,
                requisitosAdicionales = ?
            WHERE codigoDiseño = ?`,
            [
                lineaTecnologica, redTecnologica, redConocimiento,
                horasDesarrolloDiseño, mesesDesarrolloDiseño,
                nivelAcademicoIngreso, gradoNivelAcademico,
                formacionTrabajoDesarrolloHumano, edadMinima,
                requisitosAdicionales, req.params.codigo
            ]
        );

        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'Diseño no encontrado' });
        }

        res.json({ message: 'Diseño actualizado exitosamente' });
    } catch (error) {
        res.status(500).json({ error: 'Error al actualizar el diseño', details: error.message });
    }
});

module.exports = router;

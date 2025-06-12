const express = require('express');
const router = express.Router();
const Database = require('../conf/database');

// Obtener todos los RAPs
router.get('/', async (req, res) => {
    try {
        const raps = await Database.query('SELECT * FROM raps ORDER BY codigoDiseñoCompetenciaRap');
        res.json(raps);
    } catch (error) {
        res.status(500).json({ error: 'Error al obtener los RAPs', details: error.message });
    }
});

// Obtener RAPs por competencia
router.get('/competencia/:codigoDiseñoCompetencia', async (req, res) => {
    try {
        const raps = await Database.query(
            'SELECT * FROM raps WHERE codigoDiseñoCompetenciaRap LIKE ? ORDER BY codigoDiseñoCompetenciaRap',
            [`${req.params.codigoDiseñoCompetencia}-%`]
        );
        res.json(raps);
    } catch (error) {
        res.status(500).json({ error: 'Error al obtener los RAPs de la competencia', details: error.message });
    }
});

// Validar si existe un RAP
router.get('/validate/:codigoDiseño/:codigoCompetencia/:codigoRap', async (req, res) => {
    try {
        const codigoDiseñoCompetenciaRap = `${req.params.codigoDiseño}-${req.params.codigoCompetencia}-${req.params.codigoRap}`;
        const [rap] = await Database.query(
            'SELECT * FROM raps WHERE codigoDiseñoCompetenciaRap = ?',
            [codigoDiseñoCompetenciaRap]
        );
        
        res.json({ 
            exists: !!rap, 
            rap: rap || null,
            codigoDiseñoCompetenciaRap: codigoDiseñoCompetenciaRap
        });
    } catch (error) {
        res.status(500).json({ error: 'Error al validar el RAP', details: error.message });
    }
});

// Crear un nuevo RAP
router.post('/', async (req, res) => {
    try {
        const {
            codigoDiseño,
            codigoCompetencia,
            codigoRap,
            nombreRap,
            horasDesarrolloRap
        } = req.body;

        // Verificar que la competencia existe
        const codigoDiseñoCompetencia = `${codigoDiseño}-${codigoCompetencia}`;
        const [competencia] = await Database.query(
            'SELECT codigoDiseñoCompetencia FROM competencias WHERE codigoDiseñoCompetencia = ?',
            [codigoDiseñoCompetencia]
        );

        if (!competencia) {
            return res.status(400).json({ 
                error: 'La competencia especificada no existe',
                codigoDiseñoCompetencia: codigoDiseñoCompetencia
            });
        }

        // Generar el código del RAP
        const codigoDiseñoCompetenciaRap = `${codigoDiseño}-${codigoCompetencia}-${codigoRap}`;

        // Verificar si ya existe
        const [existingRap] = await Database.query(
            'SELECT codigoDiseñoCompetenciaRap FROM raps WHERE codigoDiseñoCompetenciaRap = ?',
            [codigoDiseñoCompetenciaRap]
        );

        if (existingRap) {
            return res.status(400).json({ 
                error: 'El RAP ya existe', 
                codigoDiseñoCompetenciaRap: codigoDiseñoCompetenciaRap 
            });
        }

        // Insertar nuevo RAP
        await Database.query(
            `INSERT INTO raps (
                codigoDiseñoCompetenciaRap, codigoRap, nombreRap, horasDesarrolloRap
            ) VALUES (?, ?, ?, ?)`,
            [codigoDiseñoCompetenciaRap, codigoRap, nombreRap, horasDesarrolloRap]
        );

        res.status(201).json({ 
            message: 'RAP creado exitosamente', 
            codigoDiseñoCompetenciaRap: codigoDiseñoCompetenciaRap 
        });
    } catch (error) {
        res.status(500).json({ error: 'Error al crear el RAP', details: error.message });
    }
});

// Actualizar un RAP
router.put('/:codigo', async (req, res) => {
    try {
        const { nombreRap, horasDesarrolloRap } = req.body;

        const result = await Database.query(
            `UPDATE raps SET nombreRap = ?, horasDesarrolloRap = ? WHERE codigoDiseñoCompetenciaRap = ?`,
            [nombreRap, horasDesarrolloRap, req.params.codigo]
        );

        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'RAP no encontrado' });
        }

        res.json({ message: 'RAP actualizado exitosamente' });
    } catch (error) {
        res.status(500).json({ error: 'Error al actualizar el RAP', details: error.message });
    }
});

module.exports = router;

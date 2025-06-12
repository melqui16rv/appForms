const express = require('express');
const router = express.Router();
const Database = require('../conf/database');

// Obtener todas las competencias
router.get('/', async (req, res) => {
    try {
        const competencias = await Database.query('SELECT * FROM competencias ORDER BY codigoDiseñoCompetencia');
        res.json(competencias);
    } catch (error) {
        res.status(500).json({ error: 'Error al obtener las competencias', details: error.message });
    }
});

// Obtener competencias por diseño
router.get('/diseno/:codigoDiseño', async (req, res) => {
    try {
        const competencias = await Database.query(
            'SELECT * FROM competencias WHERE codigoDiseñoCompetencia LIKE ? ORDER BY codigoDiseñoCompetencia',
            [`${req.params.codigoDiseño}-%`]
        );
        res.json(competencias);
    } catch (error) {
        res.status(500).json({ error: 'Error al obtener las competencias del diseño', details: error.message });
    }
});

// Validar si existe una competencia
router.get('/validate/:codigoDiseño/:codigoCompetencia', async (req, res) => {
    try {
        const codigoDiseñoCompetencia = `${req.params.codigoDiseño}-${req.params.codigoCompetencia}`;
        const [competencia] = await Database.query(
            'SELECT * FROM competencias WHERE codigoDiseñoCompetencia = ?',
            [codigoDiseñoCompetencia]
        );
        
        res.json({ 
            exists: !!competencia, 
            competencia: competencia || null,
            codigoDiseñoCompetencia: codigoDiseñoCompetencia
        });
    } catch (error) {
        res.status(500).json({ error: 'Error al validar la competencia', details: error.message });
    }
});

// Crear una nueva competencia
router.post('/', async (req, res) => {
    try {
        const {
            codigoDiseño,
            codigoCompetencia,
            nombreCompetencia,
            normaUnidadCompetencia,
            horasDesarrolloCompetencia,
            requisitosAcademicosInstructor,
            experienciaLaboralInstructor
        } = req.body;

        // Verificar que el diseño existe
        const [diseno] = await Database.query(
            'SELECT codigoDiseño FROM diseños WHERE codigoDiseño = ?',
            [codigoDiseño]
        );

        if (!diseno) {
            return res.status(400).json({ 
                error: 'El diseño especificado no existe',
                codigoDiseño: codigoDiseño
            });
        }

        // Generar el código de la competencia
        const codigoDiseñoCompetencia = `${codigoDiseño}-${codigoCompetencia}`;

        // Verificar si ya existe
        const [existingCompetencia] = await Database.query(
            'SELECT codigoDiseñoCompetencia FROM competencias WHERE codigoDiseñoCompetencia = ?',
            [codigoDiseñoCompetencia]
        );

        if (existingCompetencia) {
            return res.status(400).json({ 
                error: 'La competencia ya existe', 
                codigoDiseñoCompetencia: codigoDiseñoCompetencia 
            });
        }

        // Insertar nueva competencia
        await Database.query(
            `INSERT INTO competencias (
                codigoDiseñoCompetencia, codigoCompetencia, nombreCompetencia,
                normaUnidadCompetencia, horasDesarrolloCompetencia,
                requisitosAcademicosInstructor, experienciaLaboralInstructor
            ) VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [
                codigoDiseñoCompetencia, codigoCompetencia, nombreCompetencia,
                normaUnidadCompetencia, horasDesarrolloCompetencia,
                requisitosAcademicosInstructor, experienciaLaboralInstructor
            ]
        );

        res.status(201).json({ 
            message: 'Competencia creada exitosamente', 
            codigoDiseñoCompetencia: codigoDiseñoCompetencia 
        });
    } catch (error) {
        res.status(500).json({ error: 'Error al crear la competencia', details: error.message });
    }
});

// Actualizar una competencia
router.put('/:codigo', async (req, res) => {
    try {
        const {
            nombreCompetencia,
            normaUnidadCompetencia,
            horasDesarrolloCompetencia,
            requisitosAcademicosInstructor,
            experienciaLaboralInstructor
        } = req.body;

        const result = await Database.query(
            `UPDATE competencias SET 
                nombreCompetencia = ?, normaUnidadCompetencia = ?,
                horasDesarrolloCompetencia = ?, requisitosAcademicosInstructor = ?,
                experienciaLaboralInstructor = ?
            WHERE codigoDiseñoCompetencia = ?`,
            [
                nombreCompetencia, normaUnidadCompetencia,
                horasDesarrolloCompetencia, requisitosAcademicosInstructor,
                experienciaLaboralInstructor, req.params.codigo
            ]
        );

        if (result.affectedRows === 0) {
            return res.status(404).json({ error: 'Competencia no encontrada' });
        }

        res.json({ message: 'Competencia actualizada exitosamente' });
    } catch (error) {
        res.status(500).json({ error: 'Error al actualizar la competencia', details: error.message });
    }
});

module.exports = router;

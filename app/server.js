const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// Middlewares
app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static('public'));

// Importar rutas
const disenosRoutes = require('../routes/disenos');
const competenciasRoutes = require('../routes/competencias');
const rapsRoutes = require('../routes/raps');

// Usar rutas
app.use('/api/disenos', disenosRoutes);
app.use('/api/competencias', competenciasRoutes);
app.use('/api/raps', rapsRoutes);

// Ruta principal
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, '../public/index.html'));
});

// Iniciar servidor
app.listen(PORT, () => {
    console.log(`Servidor ejecutándose en http://localhost:${PORT}`);
});

module.exports = app;

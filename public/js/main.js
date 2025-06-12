// API Base URL - Usando servidor PHP
const API_BASE_URL = '/api';

// Variables globales
let editingDiseno = null;
let editingCompetencia = null;
let editingRap = null;

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    setupEventListeners();
    loadInitialData();
    showSection('disenos'); // Mostrar sección de diseños por defecto
}

function setupEventListeners() {
    // Formulario de diseños
    document.getElementById('disenoForm').addEventListener('submit', handleDisenoSubmit);
    document.getElementById('codigoPrograma').addEventListener('input', generateCodigoDiseno);
    document.getElementById('versionPrograma').addEventListener('input', generateCodigoDiseno);

    // Formulario de competencias
    document.getElementById('competenciaForm').addEventListener('submit', handleCompetenciaSubmit);
    document.getElementById('codigoDiseñoComp').addEventListener('change', generateCodigoCompetencia);
    document.getElementById('codigoCompetencia').addEventListener('input', generateCodigoCompetencia);

    // Formulario de RAPs
    document.getElementById('rapForm').addEventListener('submit', handleRapSubmit);
    document.getElementById('codigoDiseñoRap').addEventListener('change', function() {
        loadCompetenciasForRap();
        generateCodigoRap();
    });
    document.getElementById('codigoCompetenciaRap').addEventListener('change', generateCodigoRap);
    document.getElementById('codigoRap').addEventListener('input', generateCodigoRap);
}

// Navegación entre secciones
function showSection(sectionName) {
    // Ocultar todas las secciones
    const sections = document.querySelectorAll('.section');
    sections.forEach(section => {
        section.classList.remove('active');
    });

    // Mostrar la sección seleccionada
    document.getElementById(sectionName + 'Section').classList.add('active');

    // Actualizar navegación
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.classList.remove('active');
    });

    // Cargar datos específicos de la sección
    switch(sectionName) {
        case 'disenos':
            loadDisenos();
            break;
        case 'competencias':
            loadCompetencias();
            loadDisenosForSelect();
            break;
        case 'raps':
            loadRaps();
            loadDisenosForRapSelect();
            break;
    }
}

// Cargar datos iniciales
async function loadInitialData() {
    try {
        await loadDisenos();
    } catch (error) {
        console.error('Error cargando datos iniciales:', error);
    }
}

// ==================== DISEÑOS ====================

// Generar código de diseño automáticamente
function generateCodigoDiseno() {
    const codigoPrograma = document.getElementById('codigoPrograma').value.trim();
    const versionPrograma = document.getElementById('versionPrograma').value.trim();
    
    if (codigoPrograma && versionPrograma) {
        const codigoDiseno = `${codigoPrograma}-${versionPrograma}`;
        document.getElementById('codigoDiseñoGenerado').value = codigoDiseno;
        
        // Validar si existe
        validateDisenoExists(codigoPrograma, versionPrograma);
    } else {
        document.getElementById('codigoDiseñoGenerado').value = '';
        document.getElementById('disenoExistente').style.display = 'none';
    }
}

// Validar si el diseño existe
async function validateDisenoExists(codigoPrograma, versionPrograma) {
    try {
        const response = await fetch(`${API_BASE_URL}/disenos/validate/${codigoPrograma}/${versionPrograma}`);
        const data = await response.json();
        
        const alertDiv = document.getElementById('disenoExistente');
        const msgSpan = document.getElementById('disenoExistenteMsg');
        
        if (data.exists) {
            msgSpan.textContent = `El diseño con código ${data.codigoDiseño} ya existe. Se cargarán los datos para edición.`;
            alertDiv.className = 'alert alert-warning';
            alertDiv.style.display = 'block';
            
            // Cargar datos del diseño existente
            loadDisenoForEdit(data.diseno);
            editingDiseno = data.codigoDiseño;
            document.getElementById('btnSubmitDiseno').textContent = 'Actualizar Diseño';
        } else {
            msgSpan.textContent = `El código ${data.codigoDiseño} está disponible para un nuevo diseño.`;
            alertDiv.className = 'alert alert-success';
            alertDiv.style.display = 'block';
            
            editingDiseno = null;
            document.getElementById('btnSubmitDiseno').textContent = 'Guardar Diseño';
        }
    } catch (error) {
        console.error('Error validando diseño:', error);
        showAlert('Error al validar el diseño', 'danger');
    }
}

// Cargar datos del diseño para edición
function loadDisenoForEdit(diseno) {
    document.getElementById('lineaTecnologica').value = diseno.lineaTecnologica || '';
    document.getElementById('redTecnologica').value = diseno.redTecnologica || '';
    document.getElementById('redConocimiento').value = diseno.redConocimiento || '';
    document.getElementById('horasDesarrollo').value = diseno.horasDesarrolloDiseño || '';
    document.getElementById('mesesDesarrollo').value = diseno.mesesDesarrolloDiseño || '';
    document.getElementById('nivelAcademico').value = diseno.nivelAcademicoIngreso || '';
    document.getElementById('gradoNivel').value = diseno.gradoNivelAcademico || '';
    document.getElementById('formacionTrabajo').value = diseno.formacionTrabajoDesarrolloHumano || '';
    document.getElementById('edadMinima').value = diseno.edadMinima || '';
    document.getElementById('requisitosAdicionales').value = diseno.requisitosAdicionales || '';
}

// Manejar envío del formulario de diseño
async function handleDisenoSubmit(event) {
    event.preventDefault();
    
    // Función para obtener valor numérico o null
    const getNumericValue = (id) => {
        const value = document.getElementById(id).value.trim();
        return value ? parseFloat(value) : null;
    };
    
    const formData = {
        codigoPrograma: document.getElementById('codigoPrograma').value.trim(),
        versionPograma: document.getElementById('versionPrograma').value.trim(),
        lineaTecnologica: document.getElementById('lineaTecnologica').value.trim(),
        redTecnologica: document.getElementById('redTecnologica').value.trim(),
        redConocimiento: document.getElementById('redConocimiento').value.trim(),
        horasDesarrolloDiseño: getNumericValue('horasDesarrollo'),
        mesesDesarrolloDiseño: getNumericValue('mesesDesarrollo'),
        nivelAcademicoIngreso: document.getElementById('nivelAcademico').value,
        gradoNivelAcademico: document.getElementById('gradoNivel').value.trim() || null,
        formacionTrabajoDesarrolloHumano: document.getElementById('formacionTrabajo').value,
        edadMinima: parseInt(document.getElementById('edadMinima').value),
        requisitosAdicionales: document.getElementById('requisitosAdicionales').value.trim() || null
    };

    try {
        let response;
        if (editingDiseno) {
            // Actualizar diseño existente
            response = await fetch(`${API_BASE_URL}/disenos/${editingDiseno}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
        } else {
            // Crear nuevo diseño
            response = await fetch(`${API_BASE_URL}/disenos`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
        }

        const result = await response.json();

        if (response.ok) {
            showAlert(result.message, 'success');
            limpiarFormularioDiseno();
            loadDisenos();
        } else {
            showAlert(result.error, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al procesar la solicitud: ' + error.message, 'danger');
    }
}

// Cargar lista de diseños
async function loadDisenos() {
    try {
        const response = await fetch(`${API_BASE_URL}/disenos`);
        const disenos = await response.json();
        
        const tbody = document.getElementById('disenosTable');
        tbody.innerHTML = '';
        
        disenos.forEach(diseno => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="text-truncate">${diseno.codigoDiseño}</td>
                <td class="text-truncate">${diseno.lineaTecnologica}</td>
                <td>${diseno.horasDesarrolloDiseño}</td>
                <td>
                    <button class="btn btn-sm btn-warning me-1" onclick="editDiseno('${diseno.codigoDiseño}')">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    } catch (error) {
        console.error('Error cargando diseños:', error);
        showAlert('Error al cargar los diseños', 'danger');
    }
}

// Editar diseño
async function editDiseno(codigoDiseno) {
    try {
        const response = await fetch(`${API_BASE_URL}/disenos/${codigoDiseno}`);
        const diseno = await response.json();
        
        if (response.ok) {
            // Separar el código del diseño
            const [codigoPrograma, versionPrograma] = diseno.codigoDiseño.split('-');
            
            document.getElementById('codigoPrograma').value = codigoPrograma;
            document.getElementById('versionPrograma').value = versionPrograma;
            
            loadDisenoForEdit(diseno);
            generateCodigoDiseno();
            
            // Scroll al formulario
            document.getElementById('disenoForm').scrollIntoView({ behavior: 'smooth' });
        } else {
            showAlert('Error al cargar el diseño', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al cargar el diseño', 'danger');
    }
}

// Limpiar formulario de diseño
function limpiarFormularioDiseno() {
    document.getElementById('disenoForm').reset();
    document.getElementById('codigoDiseñoGenerado').value = '';
    document.getElementById('disenoExistente').style.display = 'none';
    editingDiseno = null;
    document.getElementById('btnSubmitDiseno').textContent = 'Guardar Diseño';
}

// ==================== COMPETENCIAS ====================

// Cargar diseños para select de competencias
async function loadDisenosForSelect() {
    try {
        const response = await fetch(`${API_BASE_URL}/disenos`);
        const disenos = await response.json();
        
        const select = document.getElementById('codigoDiseñoComp');
        select.innerHTML = '<option value="">Seleccionar diseño...</option>';
        
        disenos.forEach(diseno => {
            const option = document.createElement('option');
            option.value = diseno.codigoDiseño;
            option.textContent = `${diseno.codigoDiseño} - ${diseno.lineaTecnologica}`;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error cargando diseños para select:', error);
    }
}

// Generar código de competencia
function generateCodigoCompetencia() {
    const codigoDiseno = document.getElementById('codigoDiseñoComp').value;
    const codigoCompetencia = document.getElementById('codigoCompetencia').value.trim();
    
    if (codigoDiseno && codigoCompetencia) {
        const codigoCompleto = `${codigoDiseno}-${codigoCompetencia}`;
        document.getElementById('codigoCompetenciaGenerado').value = codigoCompleto;
        
        // Validar si existe
        validateCompetenciaExists(codigoDiseno, codigoCompetencia);
    } else {
        document.getElementById('codigoCompetenciaGenerado').value = '';
        document.getElementById('competenciaExistente').style.display = 'none';
    }
}

// Validar si la competencia existe
async function validateCompetenciaExists(codigoDiseno, codigoCompetencia) {
    try {
        const response = await fetch(`${API_BASE_URL}/competencias/validate/${codigoDiseno}/${codigoCompetencia}`);
        const data = await response.json();
        
        const alertDiv = document.getElementById('competenciaExistente');
        const msgSpan = document.getElementById('competenciaExistenteMsg');
        
        if (data.exists) {
            msgSpan.textContent = `La competencia ${data.codigoDiseñoCompetencia} ya existe. Se cargarán los datos para edición.`;
            alertDiv.className = 'alert alert-warning';
            alertDiv.style.display = 'block';
            
            loadCompetenciaForEdit(data.competencia);
            editingCompetencia = data.codigoDiseñoCompetencia;
            document.getElementById('btnSubmitCompetencia').textContent = 'Actualizar Competencia';
        } else {
            msgSpan.textContent = `El código ${data.codigoDiseñoCompetencia} está disponible para una nueva competencia.`;
            alertDiv.className = 'alert alert-success';
            alertDiv.style.display = 'block';
            
            editingCompetencia = null;
            document.getElementById('btnSubmitCompetencia').textContent = 'Guardar Competencia';
        }
    } catch (error) {
        console.error('Error validando competencia:', error);
        showAlert('Error al validar la competencia', 'danger');
    }
}

// Cargar datos de competencia para edición
function loadCompetenciaForEdit(competencia) {
    document.getElementById('nombreCompetencia').value = competencia.nombreCompetencia || '';
    document.getElementById('normaUnidad').value = competencia.normaUnidadCompetencia || '';
    document.getElementById('horasCompetencia').value = competencia.horasDesarrolloCompetencia || '';
    document.getElementById('requisitosInstructor').value = competencia.requisitosAcademicosInstructor || '';
    document.getElementById('experienciaInstructor').value = competencia.experienciaLaboralInstructor || '';
}

// Manejar envío del formulario de competencia
async function handleCompetenciaSubmit(event) {
    event.preventDefault();
    
    const formData = {
        codigoDiseño: document.getElementById('codigoDiseñoComp').value,
        codigoCompetencia: document.getElementById('codigoCompetencia').value.trim(),
        nombreCompetencia: document.getElementById('nombreCompetencia').value.trim(),
        normaUnidadCompetencia: document.getElementById('normaUnidad').value.trim(),
        horasDesarrolloCompetencia: parseFloat(document.getElementById('horasCompetencia').value),
        requisitosAcademicosInstructor: document.getElementById('requisitosInstructor').value.trim(),
        experienciaLaboralInstructor: document.getElementById('experienciaInstructor').value.trim()
    };

    try {
        let response;
        if (editingCompetencia) {
            response = await fetch(`${API_BASE_URL}/competencias/${editingCompetencia}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
        } else {
            response = await fetch(`${API_BASE_URL}/competencias`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
        }

        const result = await response.json();

        if (response.ok) {
            showAlert(result.message, 'success');
            limpiarFormularioCompetencia();
            loadCompetencias();
        } else {
            showAlert(result.error, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al procesar la solicitud', 'danger');
    }
}

// Cargar lista de competencias
async function loadCompetencias() {
    try {
        const response = await fetch(`${API_BASE_URL}/competencias`);
        const competencias = await response.json();
        
        const tbody = document.getElementById('competenciasTable');
        tbody.innerHTML = '';
        
        competencias.forEach(competencia => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="text-truncate">${competencia.codigoDiseñoCompetencia}</td>
                <td class="text-truncate">${competencia.nombreCompetencia}</td>
                <td>${competencia.horasDesarrolloCompetencia}</td>
                <td>
                    <button class="btn btn-sm btn-warning me-1" onclick="editCompetencia('${competencia.codigoDiseñoCompetencia}')">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    } catch (error) {
        console.error('Error cargando competencias:', error);
        showAlert('Error al cargar las competencias', 'danger');
    }
}

// Editar competencia
async function editCompetencia(codigoCompetencia) {
    try {
        const response = await fetch(`${API_BASE_URL}/competencias/${codigoCompetencia}`);
        const competencia = await response.json();
        
        if (response.ok) {
            // Separar el código de la competencia
            const parts = competencia.codigoDiseñoCompetencia.split('-');
            const codigoDiseno = parts.slice(0, 2).join('-'); // primeras dos partes
            const codCompetencia = parts.slice(2).join('-'); // resto
            
            document.getElementById('codigoDiseñoComp').value = codigoDiseno;
            document.getElementById('codigoCompetencia').value = codCompetencia;
            
            loadCompetenciaForEdit(competencia);
            generateCodigoCompetencia();
            
            // Scroll al formulario
            document.getElementById('competenciaForm').scrollIntoView({ behavior: 'smooth' });
        } else {
            showAlert('Error al cargar la competencia', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al cargar la competencia', 'danger');
    }
}

// Limpiar formulario de competencia
function limpiarFormularioCompetencia() {
    document.getElementById('competenciaForm').reset();
    document.getElementById('codigoCompetenciaGenerado').value = '';
    document.getElementById('competenciaExistente').style.display = 'none';
    editingCompetencia = null;
    document.getElementById('btnSubmitCompetencia').textContent = 'Guardar Competencia';
}

// ==================== RAPs ====================

// Cargar diseños para select de RAPs
async function loadDisenosForRapSelect() {
    try {
        const response = await fetch(`${API_BASE_URL}/disenos`);
        const disenos = await response.json();
        
        const select = document.getElementById('codigoDiseñoRap');
        select.innerHTML = '<option value="">Seleccionar diseño...</option>';
        
        disenos.forEach(diseno => {
            const option = document.createElement('option');
            option.value = diseno.codigoDiseño;
            option.textContent = `${diseno.codigoDiseño} - ${diseno.lineaTecnologica}`;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error cargando diseños para RAPs:', error);
    }
}

// Cargar competencias para RAPs según el diseño seleccionado
async function loadCompetenciasForRap() {
    const codigoDiseno = document.getElementById('codigoDiseñoRap').value;
    const select = document.getElementById('codigoCompetenciaRap');
    
    select.innerHTML = '<option value="">Seleccionar competencia...</option>';
    
    if (!codigoDiseno) return;
    
    try {
        const response = await fetch(`${API_BASE_URL}/competencias/diseno/${codigoDiseno}`);
        const competencias = await response.json();
        
        competencias.forEach(competencia => {
            const option = document.createElement('option');
            option.value = competencia.codigoCompetencia;
            option.textContent = `${competencia.codigoCompetencia} - ${competencia.nombreCompetencia}`;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error cargando competencias para RAPs:', error);
    }
}

// Generar código de RAP
function generateCodigoRap() {
    const codigoDiseno = document.getElementById('codigoDiseñoRap').value;
    const codigoCompetencia = document.getElementById('codigoCompetenciaRap').value;
    const codigoRap = document.getElementById('codigoRap').value.trim();
    
    if (codigoDiseno && codigoCompetencia && codigoRap) {
        const codigoCompleto = `${codigoDiseno}-${codigoCompetencia}-${codigoRap}`;
        document.getElementById('codigoRapGenerado').value = codigoCompleto;
        
        // Validar si existe
        validateRapExists(codigoDiseno, codigoCompetencia, codigoRap);
    } else {
        document.getElementById('codigoRapGenerado').value = '';
        document.getElementById('rapExistente').style.display = 'none';
    }
}

// Validar si el RAP existe
async function validateRapExists(codigoDiseno, codigoCompetencia, codigoRap) {
    try {
        const response = await fetch(`${API_BASE_URL}/raps/validate/${codigoDiseno}/${codigoCompetencia}/${codigoRap}`);
        const data = await response.json();
        
        const alertDiv = document.getElementById('rapExistente');
        const msgSpan = document.getElementById('rapExistenteMsg');
        
        if (data.exists) {
            msgSpan.textContent = `El RAP ${data.codigoDiseñoCompetenciaRap} ya existe. Se cargarán los datos para edición.`;
            alertDiv.className = 'alert alert-warning';
            alertDiv.style.display = 'block';
            
            loadRapForEdit(data.rap);
            editingRap = data.codigoDiseñoCompetenciaRap;
            document.getElementById('btnSubmitRap').textContent = 'Actualizar RAP';
        } else {
            msgSpan.textContent = `El código ${data.codigoDiseñoCompetenciaRap} está disponible para un nuevo RAP.`;
            alertDiv.className = 'alert alert-success';
            alertDiv.style.display = 'block';
            
            editingRap = null;
            document.getElementById('btnSubmitRap').textContent = 'Guardar RAP';
        }
    } catch (error) {
        console.error('Error validando RAP:', error);
        showAlert('Error al validar el RAP', 'danger');
    }
}

// Cargar datos de RAP para edición
function loadRapForEdit(rap) {
    document.getElementById('nombreRap').value = rap.nombreRap || '';
    document.getElementById('horasRap').value = rap.horasDesarrolloRap || '';
}

// Manejar envío del formulario de RAP
async function handleRapSubmit(event) {
    event.preventDefault();
    
    const formData = {
        codigoDiseño: document.getElementById('codigoDiseñoRap').value,
        codigoCompetencia: document.getElementById('codigoCompetenciaRap').value,
        codigoRap: document.getElementById('codigoRap').value.trim(),
        nombreRap: document.getElementById('nombreRap').value.trim(),
        horasDesarrolloRap: parseFloat(document.getElementById('horasRap').value)
    };

    try {
        let response;
        if (editingRap) {
            response = await fetch(`${API_BASE_URL}/raps/${editingRap}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
        } else {
            response = await fetch(`${API_BASE_URL}/raps`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
        }

        const result = await response.json();

        if (response.ok) {
            showAlert(result.message, 'success');
            limpiarFormularioRap();
            loadRaps();
        } else {
            showAlert(result.error, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al procesar la solicitud', 'danger');
    }
}

// Cargar lista de RAPs
async function loadRaps() {
    try {
        const response = await fetch(`${API_BASE_URL}/raps`);
        const raps = await response.json();
        
        const tbody = document.getElementById('rapsTable');
        tbody.innerHTML = '';
        
        raps.forEach(rap => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="text-truncate">${rap.codigoDiseñoCompetenciaRap}</td>
                <td class="text-truncate">${rap.nombreRap}</td>
                <td>${rap.horasDesarrolloRap}</td>
                <td>
                    <button class="btn btn-sm btn-warning me-1" onclick="editRap('${rap.codigoDiseñoCompetenciaRap}')">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    } catch (error) {
        console.error('Error cargando RAPs:', error);
        showAlert('Error al cargar los RAPs', 'danger');
    }
}

// Editar RAP
async function editRap(codigoRap) {
    try {
        const response = await fetch(`${API_BASE_URL}/raps/${codigoRap}`);
        const rap = await response.json();
        
        if (response.ok) {
            // Separar el código del RAP
            const parts = rap.codigoDiseñoCompetenciaRap.split('-');
            const codigoDiseno = parts.slice(0, 2).join('-'); // primeras dos partes
            const codigoCompetencia = parts[2]; // tercera parte
            const codRap = parts.slice(3).join('-'); // resto
            
            document.getElementById('codigoDiseñoRap').value = codigoDiseno;
            await loadCompetenciasForRap(); // Cargar competencias del diseño
            document.getElementById('codigoCompetenciaRap').value = codigoCompetencia;
            document.getElementById('codigoRap').value = codRap;
            
            loadRapForEdit(rap);
            generateCodigoRap();
            
            // Scroll al formulario
            document.getElementById('rapForm').scrollIntoView({ behavior: 'smooth' });
        } else {
            showAlert('Error al cargar el RAP', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error al cargar el RAP', 'danger');
    }
}

// Limpiar formulario de RAP
function limpiarFormularioRap() {
    document.getElementById('rapForm').reset();
    document.getElementById('codigoRapGenerado').value = '';
    document.getElementById('rapExistente').style.display = 'none';
    document.getElementById('codigoCompetenciaRap').innerHTML = '<option value="">Seleccionar competencia...</option>';
    editingRap = null;
    document.getElementById('btnSubmitRap').textContent = 'Guardar RAP';
}

// ==================== UTILIDADES ====================

// Mostrar alertas
function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${getIconForType(type)} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    // Auto-dismiss después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Obtener icono según el tipo de alerta
function getIconForType(type) {
    switch(type) {
        case 'success': return 'check-circle';
        case 'danger': return 'exclamation-triangle';
        case 'warning': return 'exclamation-circle';
        case 'info': return 'info-circle';
        default: return 'info-circle';
    }
}

// API Base URL - Usando servidor PHP
const API_BASE_URL = 'api'; // Siempre relativo a la raíz de la app

// Variables globales
let editingDiseno = null;
let editingCompetencia = null;
let editingRap = null;

// Mostrar solo una sección a la vez
function showSection(sectionName) {
    const sections = document.querySelectorAll('.section');
    sections.forEach(section => section.classList.remove('active'));
    document.getElementById(sectionName + 'Section').classList.add('active');
    
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

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    showSection('disenos');
});

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

// ============= FUNCIONES DE DISEÑOS =============
async function loadDisenos() {
    try {
        const response = await fetch(`${API_BASE_URL}/disenos.php`);
        const disenos = await response.json();
        displayDisenos(disenos);
    } catch (error) {
        console.error('Error cargando diseños:', error);
        showAlert('Error al cargar diseños', 'danger');
    }
}

function displayDisenos(disenos) {
    const tableBody = document.getElementById('disenosTable');
    tableBody.innerHTML = '';
    
    disenos.forEach(diseno => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${diseno.codigoDiseño}</td>
            <td>${diseno.lineaTecnologica}</td>
            <td>${diseno.horasDesarrolloDiseño || 'N/A'}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editDiseno('${diseno.codigoDiseño}')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteDiseno('${diseno.codigoDiseño}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

function generateCodigoDiseno() {
    const codigoPrograma = document.getElementById('codigoPrograma').value;
    const versionPrograma = document.getElementById('versionPrograma').value;
    
    if (codigoPrograma && versionPrograma) {
        const codigoGenerado = `${codigoPrograma}-${versionPrograma}`;
        document.getElementById('codigoDiseñoGenerado').value = codigoGenerado;
        
        // Verificar si existe solo cuando ambos campos tienen longitud mínima
        if (codigoPrograma.length >= 3 && versionPrograma.length >= 1) {
            validateDisenoExists(codigoPrograma, versionPrograma);
        }
    } else {
        document.getElementById('codigoDiseñoGenerado').value = '';
        hideAlert('disenoExistente');
        // NO limpiar el formulario aquí - el usuario está escribiendo
    }
}

async function validateDisenoExists(codigoPrograma, versionPrograma) {
    try {
        const response = await fetch(`${API_BASE_URL}/disenos.php?validate=${codigoPrograma}&version=${versionPrograma}`);
        const result = await response.json();
        
        if (result.exists && result.diseno) {
            // Auto-cargar los datos del diseño existente
            fillDisenoForm(result.diseno);
            editingDiseno = result.diseno.codigoDiseño;
            
            showExistingAlert('disenoExistente', 'disenoExistenteMsg', 
                `El diseño ${result.codigoDiseño} ya existe. Los datos se han cargado automáticamente. Puede modificarlos y guardar, o hacer clic en Limpiar para crear uno nuevo.`);
        } else {
            hideAlert('disenoExistente');
            // Solo resetear el modo de edición, no borrar el formulario
            // El usuario está escribiendo, no debemos borrar sus datos
            if (editingDiseno) {
                editingDiseno = null;
            }
        }
    } catch (error) {
        console.error('Error validando diseño:', error);
    }
}

async function handleDisenoSubmit(e) {
    e.preventDefault();
    
    const formData = {
        codigoPrograma: document.getElementById('codigoPrograma').value,
        versionPrograma: document.getElementById('versionPrograma').value,
        lineaTecnologica: document.getElementById('lineaTecnologica').value,
        redTecnologica: document.getElementById('redTecnologica').value,
        redConocimiento: document.getElementById('redConocimiento').value,
        horasDesarrolloDiseño: document.getElementById('horasDesarrolloDiseño').value || null,
        mesesDesarrolloDiseño: document.getElementById('mesesDesarrolloDiseño').value || null,
        nivelAcademicoIngreso: document.getElementById('nivelAcademicoIngreso').value,
        gradoNivelAcademico: document.getElementById('gradoNivelAcademico').value || null,
        formacionTrabajoDesarrolloHumano: document.getElementById('formacionTrabajoDesarrolloHumano').value,
        edadMinima: document.getElementById('edadMinima').value,
        requisitosAdicionales: document.getElementById('requisitosAdicionales').value || null
    };
    
    try {
        const method = editingDiseno ? 'PUT' : 'POST';
        const url = editingDiseno ? `${API_BASE_URL}/disenos.php?codigo=${editingDiseno}` : `${API_BASE_URL}/disenos.php`;
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (response.ok && (result.success || result.message)) {
            showAlert(result.message || 'Diseño guardado exitosamente', 'success');
            clearDisenoForm(); // Solo limpiar después de guardar exitosamente
            loadDisenos();
        } else {
            showAlert(result.error || 'Error al guardar diseño', 'danger');
            // NO limpiar el formulario en caso de error - preservar los datos del usuario
        }
    } catch (error) {
        console.error('Error enviando diseño:', error);
        showAlert('Error de conexión. Verifique su conexión e intente nuevamente.', 'danger');
        // NO limpiar el formulario en caso de error de conexión
    }
}

function limpiarFormularioDiseno() {
    clearDisenoForm();
    showAlert('Formulario limpiado. Puede crear un nuevo diseño.', 'info');
}

function clearDisenoForm() {
    document.getElementById('codigoPrograma').value = '';
    document.getElementById('versionPrograma').value = '';
    document.getElementById('lineaTecnologica').value = '';
    document.getElementById('redTecnologica').value = '';
    document.getElementById('redConocimiento').value = '';
    document.getElementById('horasDesarrolloDiseño').value = '';
    document.getElementById('mesesDesarrolloDiseño').value = '';
    document.getElementById('nivelAcademicoIngreso').value = '';
    document.getElementById('gradoNivelAcademico').value = '';
    document.getElementById('formacionTrabajoDesarrolloHumano').value = '';
    document.getElementById('edadMinima').value = '';
    document.getElementById('requisitosAdicionales').value = '';
    document.getElementById('codigoDiseñoGenerado').value = '';
    editingDiseno = null;
    hideAlert('disenoExistente');
}

function clearDisenoCodesOnly() {
    // Solo limpiar los códigos, preservar el resto de datos
    document.getElementById('codigoPrograma').value = '';
    document.getElementById('versionPrograma').value = '';
    document.getElementById('codigoDiseñoGenerado').value = '';
    editingDiseno = null;
    hideAlert('disenoExistente');
}

// ============= FUNCIONES DE COMPETENCIAS =============
async function loadCompetencias() {
    try {
        const response = await fetch(`${API_BASE_URL}/competencias.php`);
        const competencias = await response.json();
        displayCompetencias(competencias);
    } catch (error) {
        console.error('Error cargando competencias:', error);
        showAlert('Error al cargar competencias', 'danger');
    }
}

function displayCompetencias(competencias) {
    const tableBody = document.getElementById('competenciasTable');
    tableBody.innerHTML = '';
    
    competencias.forEach(competencia => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${competencia.codigoDiseñoCompetencia}</td>
            <td>${competencia.nombreCompetencia}</td>
            <td>${competencia.horasDesarrolloCompetencia}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editCompetencia('${competencia.codigoDiseñoCompetencia}')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteCompetencia('${competencia.codigoDiseñoCompetencia}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

async function loadDisenosForSelect() {
    try {
        const response = await fetch(`${API_BASE_URL}/disenos.php`);
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

function generateCodigoCompetencia() {
    const codigoDiseno = document.getElementById('codigoDiseñoComp').value;
    const codigoCompetencia = document.getElementById('codigoCompetencia').value;
    
    if (codigoDiseno && codigoCompetencia) {
        const codigoGenerado = `${codigoDiseno}-${codigoCompetencia}`;
        document.getElementById('codigoCompetenciaGenerado').value = codigoGenerado;
        
        // Verificar si existe
        if (codigoDiseno.length >= 3 && codigoCompetencia.length >= 3) {
            validateCompetenciaExists(codigoDiseno, codigoCompetencia);
        }
    } else {
        document.getElementById('codigoCompetenciaGenerado').value = '';
        hideAlert('competenciaExistente');
    }
}

async function validateCompetenciaExists(codigoDiseno, codigoCompetencia) {
    try {
        const response = await fetch(`${API_BASE_URL}/competencias.php?action=validate&codigoDiseno=${codigoDiseno}&codigoCompetencia=${codigoCompetencia}`);
        const result = await response.json();
        
        if (result.exists && result.competencia) {
            // Auto-cargar los datos de la competencia existente
            fillCompetenciaForm(result.competencia);
            editingCompetencia = result.competencia.codigoDiseñoCompetencia;
            
            showExistingAlert('competenciaExistente', 'competenciaExistenteMsg', 
                `La competencia ${result.codigoDisenoCompetencia} ya existe. Los datos se han cargado automáticamente. Puede modificarlos y guardar, o hacer clic en Limpiar para crear una nueva.`);
        } else {
            hideAlert('competenciaExistente');
            // Solo resetear el modo de edición, no borrar los datos del usuario
            if (editingCompetencia) {
                editingCompetencia = null;
            }
        }
    } catch (error) {
        console.error('Error validando competencia:', error);
    }
}

async function handleCompetenciaSubmit(e) {
    e.preventDefault();
    
    const formData = {
        codigoDiseno: document.getElementById('codigoDiseñoComp').value,
        codigoCompetencia: document.getElementById('codigoCompetencia').value,
        nombreCompetencia: document.getElementById('nombreCompetencia').value,
        normaUnidadCompetencia: document.getElementById('normaUnidad').value || null,
        horasDesarrolloCompetencia: document.getElementById('horasCompetencia').value,
        requisitosAcademicosInstructor: document.getElementById('requisitosInstructor').value || null,
        experienciaLaboralInstructor: document.getElementById('experienciaInstructor').value || null
    };
    
    try {
        const method = editingCompetencia ? 'PUT' : 'POST';
        const url = editingCompetencia ? `${API_BASE_URL}/competencias.php?codigo=${editingCompetencia}` : `${API_BASE_URL}/competencias.php`;
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (response.ok && (result.success || result.message)) {
            showAlert(result.message || 'Competencia guardada exitosamente', 'success');
            clearCompetenciaForm(); // Solo limpiar después de guardar exitosamente
            loadCompetencias();
        } else {
            showAlert(result.error || 'Error al guardar competencia', 'danger');
            // NO limpiar el formulario en caso de error - preservar los datos del usuario
        }
    } catch (error) {
        console.error('Error enviando competencia:', error);
        showAlert('Error de conexión. Verifique su conexión e intente nuevamente.', 'danger');
        // NO limpiar el formulario en caso de error de conexión
    }
}

function limpiarFormularioCompetencia() {
    clearCompetenciaForm();
    showAlert('Formulario limpiado. Puede crear una nueva competencia.', 'info');
}

async function editCompetencia(codigoCompetencia) {
    try {
        const response = await fetch(`${API_BASE_URL}/competencias.php?codigo=${codigoCompetencia}`);
        const competencia = await response.json();
        
        if (response.ok && competencia) {
            fillCompetenciaForm(competencia);
            editingCompetencia = codigoCompetencia;
            showSection('competencias');
            showAlert('Modo edición activado para competencia. Modifique los campos y guarde los cambios.', 'info');
        } else {
            showAlert('Error al cargar la competencia para editar', 'danger');
        }
    } catch (error) {
        console.error('Error editando competencia:', error);
        showAlert('Error al cargar la competencia', 'danger');
    }
}

function fillCompetenciaForm(competencia) {
    // Extraer código de diseño y competencia del código completo
    const parts = competencia.codigoDiseñoCompetencia.split('-');
    const codigoDiseno = parts.slice(0, -1).join('-'); // Todo excepto el último elemento
    const codigoComp = parts[parts.length - 1]; // Último elemento
    
    document.getElementById('codigoDiseñoComp').value = codigoDiseno;
    document.getElementById('codigoCompetencia').value = codigoComp;
    document.getElementById('nombreCompetencia').value = competencia.nombreCompetencia || '';
    document.getElementById('normaUnidad').value = competencia.normaUnidadCompetencia || '';
    document.getElementById('horasCompetencia').value = competencia.horasDesarrolloCompetencia || '';
    document.getElementById('requisitosInstructor').value = competencia.requisitosAcademicosInstructor || '';
    document.getElementById('experienciaInstructor').value = competencia.experienciaLaboralInstructor || '';
    document.getElementById('codigoCompetenciaGenerado').value = competencia.codigoDiseñoCompetencia;
}

function clearCompetenciaForm() {
    document.getElementById('codigoDiseñoComp').value = '';
    document.getElementById('codigoCompetencia').value = '';
    document.getElementById('nombreCompetencia').value = '';
    document.getElementById('normaUnidad').value = '';
    document.getElementById('horasCompetencia').value = '';
    document.getElementById('requisitosInstructor').value = '';
    document.getElementById('experienciaInstructor').value = '';
    document.getElementById('codigoCompetenciaGenerado').value = '';
    editingCompetencia = null;
    hideAlert('competenciaExistente');
}

async function deleteCompetencia(codigoCompetencia) {
    const canDelete = await checkCompetenciaCanBeDeleted(codigoCompetencia);
    
    if (!canDelete.canDelete) {
        showAlert(`No se puede eliminar la competencia. ${canDelete.reason}`, 'warning');
        return;
    }
    
    if (confirm(`¿Está seguro de que desea eliminar la competencia ${codigoCompetencia}?`)) {
        try {
            const response = await fetch(`${API_BASE_URL}/competencias.php?codigo=${codigoCompetencia}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (response.ok && result.success) {
                showAlert(result.message || 'Competencia eliminada exitosamente', 'success');
                loadCompetencias();
            } else {
                showAlert(result.error || 'Error al eliminar competencia', 'danger');
            }
        } catch (error) {
            console.error('Error eliminando competencia:', error);
            showAlert('Error de conexión al eliminar la competencia', 'danger');
        }
    }
}

async function checkCompetenciaCanBeDeleted(codigoCompetencia) {
    try {
        const response = await fetch(`${API_BASE_URL}/raps.php?action=by-competencia&codigoCompetencia=${codigoCompetencia}`);
        const raps = await response.json();
        
        if (raps.length > 0) {
            return {
                canDelete: false,
                reason: `Tiene ${raps.length} RAP(s) asociado(s). Elimine primero los RAPs.`
            };
        }
        
        return { canDelete: true };
    } catch (error) {
        console.error('Error verificando dependencias:', error);
        return {
            canDelete: false,
            reason: 'Error al verificar dependencias'
        };
    }
}

// ============= FUNCIONES DE RAPs =============
async function loadRaps() {
    try {
        const response = await fetch(`${API_BASE_URL}/raps.php`);
        const raps = await response.json();
        displayRaps(raps);
    } catch (error) {
        console.error('Error cargando RAPs:', error);
        showAlert('Error al cargar RAPs', 'danger');
    }
}

function displayRaps(raps) {
    const tableBody = document.getElementById('rapsTable');
    tableBody.innerHTML = '';
    
    raps.forEach(rap => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${rap.codigoRapCompleto || rap.codigoDiseñoCompetenciaRap}</td>
            <td>${rap.nombreRap}</td>
            <td>${rap.horasDesarrolloRap}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editRap('${rap.codigoRapCompleto || rap.codigoDiseñoCompetenciaRap}')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteRap('${rap.codigoRapCompleto || rap.codigoDiseñoCompetenciaRap}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

async function loadDisenosForRapSelect() {
    try {
        const response = await fetch(`${API_BASE_URL}/disenos.php`);
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

async function loadCompetenciasForRap() {
    const codigoDiseno = document.getElementById('codigoDiseñoRap').value;
    const select = document.getElementById('codigoCompetenciaRap');
    
    select.innerHTML = '<option value="">Seleccionar competencia...</option>';
    
    if (!codigoDiseno) return;
    
    try {
        const response = await fetch(`${API_BASE_URL}/competencias.php?diseno=${codigoDiseno}`);
        const competencias = await response.json();
        
        competencias.forEach(competencia => {
            const option = document.createElement('option');
            option.value = competencia.codigoDiseñoCompetencia;
            option.textContent = `${competencia.codigoCompetencia} - ${competencia.nombreCompetencia}`;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Error cargando competencias:', error);
    }
}

function generateCodigoRap() {
    const codigoDiseno = document.getElementById('codigoDiseñoRap').value;
    const codigoCompetencia = document.getElementById('codigoCompetenciaRap').value;
    const codigoRap = document.getElementById('codigoRap').value;
    
    if (codigoDiseno && codigoCompetencia && codigoRap) {
        const codigoGenerado = `${codigoDiseno}-${codigoCompetencia}-${codigoRap}`;
        document.getElementById('codigoRapGenerado').value = codigoGenerado;
        
        // Verificar si existe
        if (codigoDiseno.length >= 3 && codigoCompetencia.length >= 3 && codigoRap.length >= 2) {
            validateRapExists(codigoDiseno, codigoCompetencia, codigoRap);
        }
    } else {
        document.getElementById('codigoRapGenerado').value = '';
        hideAlert('rapExistente');
    }
}

async function validateRapExists(codigoDiseno, codigoCompetencia, codigoRap) {
    try {
        const response = await fetch(`${API_BASE_URL}/raps.php?action=validate&codigoDiseno=${codigoDiseno}&codigoCompetencia=${codigoCompetencia}&codigoRap=${codigoRap}`);
        const result = await response.json();
        
        if (result.exists && result.rap) {
            // Auto-cargar los datos del RAP existente
            fillRapForm(result.rap);
            editingRap = result.rap.codigoDiseñoCompetenciaRap;
            
            showExistingAlert('rapExistente', 'rapExistenteMsg', 
                `El RAP ${result.codigoDisenoCompetenciaRap} ya existe. Los datos se han cargado automáticamente. Puede modificarlos y guardar, o hacer clic en Limpiar para crear uno nuevo.`);
        } else {
            hideAlert('rapExistente');
            // Solo resetear el modo de edición, no borrar los datos del usuario
            if (editingRap) {
                editingRap = null;
            }
        }
    } catch (error) {
        console.error('Error validando RAP:', error);
    }
}

async function handleRapSubmit(e) {
    e.preventDefault();
    
    const formData = {
        codigoDiseno: document.getElementById('codigoDiseñoRap').value,
        codigoCompetencia: document.getElementById('codigoCompetenciaRap').value,
        codigoRap: document.getElementById('codigoRap').value,
        nombreRap: document.getElementById('nombreRap').value,
        horasDesarrolloRap: document.getElementById('horasRap').value
    };
    
    try {
        const method = editingRap ? 'PUT' : 'POST';
        const url = editingRap ? `${API_BASE_URL}/raps.php?codigo=${editingRap}` : `${API_BASE_URL}/raps.php`;
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        
        if (response.ok && (result.success || result.message)) {
            showAlert(result.message || 'RAP guardado exitosamente', 'success');
            clearRapForm(); // Solo limpiar después de guardar exitosamente
            loadRaps();
        } else {
            showAlert(result.error || 'Error al guardar RAP', 'danger');
            // NO limpiar el formulario en caso de error - preservar los datos del usuario
        }
    } catch (error) {
        console.error('Error enviando RAP:', error);
        showAlert('Error de conexión. Verifique su conexión e intente nuevamente.', 'danger');
        // NO limpiar el formulario en caso de error de conexión
    }
}

function limpiarFormularioRap() {
    clearRapForm();
    showAlert('Formulario limpiado. Puede crear un nuevo RAP.', 'info');
}

function clearRapForm() {
    document.getElementById('codigoDiseñoRap').value = '';
    document.getElementById('codigoCompetenciaRap').value = '';
    document.getElementById('codigoRap').value = '';
    document.getElementById('nombreRap').value = '';
    document.getElementById('horasRap').value = '';
    document.getElementById('codigoRapGenerado').value = '';
    editingRap = null;
    hideAlert('rapExistente');
}

// ============= FUNCIONES DE EDICIÓN Y ELIMINACIÓN =============

async function editDiseno(codigoDiseno) {
    try {
        const response = await fetch(`${API_BASE_URL}/disenos.php?codigo=${codigoDiseno}`);
        const diseno = await response.json();
        
        if (response.ok && diseno) {
            // Llenar el formulario con los datos del diseño
            fillDisenoForm(diseno);
            editingDiseno = codigoDiseno;
            
            // Cambiar al tab de diseños y mostrar modo edición
            showSection('disenos');
            showAlert('Modo edición activado. Modifique los campos y guarde los cambios.', 'info');
        } else {
            showAlert('Error al cargar el diseño para editar', 'danger');
        }
    } catch (error) {
        console.error('Error editando diseño:', error);
        showAlert('Error al cargar el diseño', 'danger');
    }
}

function fillDisenoForm(diseno) {
    document.getElementById('codigoPrograma').value = diseno.codigoPrograma || '';
    document.getElementById('versionPrograma').value = diseno.versionPrograma || '';
    document.getElementById('lineaTecnologica').value = diseno.lineaTecnologica || '';
    document.getElementById('redTecnologica').value = diseno.redTecnologica || '';
    document.getElementById('redConocimiento').value = diseno.redConocimiento || '';
    document.getElementById('horasDesarrolloDiseño').value = diseno.horasDesarrolloDiseño || '';
    document.getElementById('mesesDesarrolloDiseño').value = diseno.mesesDesarrolloDiseño || '';
    document.getElementById('nivelAcademicoIngreso').value = diseno.nivelAcademicoIngreso || '';
    document.getElementById('gradoNivelAcademico').value = diseno.gradoNivelAcademico || '';
    document.getElementById('formacionTrabajoDesarrolloHumano').value = diseno.formacionTrabajoDesarrolloHumano || '';
    document.getElementById('edadMinima').value = diseno.edadMinima || '';
    document.getElementById('requisitosAdicionales').value = diseno.requisitosAdicionales || '';
    document.getElementById('codigoDiseñoGenerado').value = diseno.codigoDiseño || '';
}

async function deleteDiseno(codigoDiseno) {
    // Verificar dependencias antes de eliminar
    const canDelete = await checkDisenoCanBeDeleted(codigoDiseno);
    
    if (!canDelete.canDelete) {
        showAlert(`No se puede eliminar el diseño. ${canDelete.reason}`, 'warning');
        return;
    }
    
    if (confirm(`¿Está seguro de que desea eliminar el diseño ${codigoDiseno}?`)) {
        try {
            const response = await fetch(`${API_BASE_URL}/disenos.php?codigo=${codigoDiseno}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (response.ok && result.success) {
                showAlert(result.message || 'Diseño eliminado exitosamente', 'success');
                loadDisenos();
            } else {
                showAlert(result.error || 'Error al eliminar diseño', 'danger');
            }
        } catch (error) {
            console.error('Error eliminando diseño:', error);
            showAlert('Error de conexión al eliminar el diseño', 'danger');
        }
    }
}

async function checkDisenoCanBeDeleted(codigoDiseno) {
    try {
        // Verificar si tiene competencias asociadas
        const compResponse = await fetch(`${API_BASE_URL}/competencias.php?action=by-diseno&codigoDiseno=${codigoDiseno}`);
        const competencias = await compResponse.json();
        
        if (competencias.length > 0) {
            return {
                canDelete: false,
                reason: `Tiene ${competencias.length} competencia(s) asociada(s). Elimine primero las competencias y sus RAPs.`
            };
        }
        
        return { canDelete: true };
    } catch (error) {
        console.error('Error verificando dependencias:', error);
        return {
            canDelete: false,
            reason: 'Error al verificar dependencias'
        };
    }
}

// ============= FUNCIONES AUXILIARES =============
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    alertContainer.appendChild(alertDiv);
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function showExistingAlert(alertId, msgId, message) {
    document.getElementById(alertId).style.display = 'block';
    document.getElementById(msgId).textContent = message;
}

function hideAlert(alertId) {
    document.getElementById(alertId).style.display = 'none';
}

// ============= FUNCIONES DE RAPS =============

async function editRap(codigoRap) {
    try {
        const response = await fetch(`${API_BASE_URL}/raps.php?codigo=${codigoRap}`);
        const rap = await response.json();
        
        if (response.ok && rap) {
            fillRapForm(rap);
            editingRap = codigoRap;
            showSection('raps');
            showAlert('Modo edición activado para RAP. Modifique los campos y guarde los cambios.', 'info');
        } else {
            showAlert('Error al cargar el RAP para editar', 'danger');
        }
    } catch (error) {
        console.error('Error editando RAP:', error);
        showAlert('Error al cargar el RAP', 'danger');
    }
}

function fillRapForm(rap) {
    // Extraer códigos del código completo (formato: diseño-competencia-rap)
    const parts = rap.codigoDiseñoCompetenciaRap.split('-');
    const codigoRapSolo = parts[parts.length - 1]; // Último elemento
    const codigoCompetencia = parts[parts.length - 2]; // Penúltimo elemento
    const codigoDiseno = parts.slice(0, -2).join('-'); // Todo excepto los últimos 2 elementos
    
    document.getElementById('codigoDiseñoRap').value = codigoDiseno;
    document.getElementById('codigoCompetenciaRap').value = codigoCompetencia;
    document.getElementById('codigoRap').value = codigoRapSolo;
    document.getElementById('nombreRap').value = rap.nombreRap || '';
    document.getElementById('horasRap').value = rap.horasDesarrolloRap || '';
    document.getElementById('codigoRapGenerado').value = rap.codigoDiseñoCompetenciaRap;
}

async function deleteRap(codigoRap) {
    if (confirm(`¿Está seguro de que desea eliminar el RAP ${codigoRap}?`)) {
        try {
            const response = await fetch(`${API_BASE_URL}/raps.php?codigo=${codigoRap}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (response.ok && result.success) {
                showAlert(result.message || 'RAP eliminado exitosamente', 'success');
                loadRaps();
            } else {
                showAlert(result.error || 'Error al eliminar RAP', 'danger');
            }
        } catch (error) {
            console.error('Error eliminando RAP:', error);
            showAlert('Error de conexión al eliminar el RAP', 'danger');
        }
    }
}
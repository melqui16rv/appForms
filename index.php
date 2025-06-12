<?php // Puedes usar este bloque para configuración global futura ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Diseños Curriculares</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-graduation-cap me-2"></i>Diseños Curriculares</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('disenos')">Diseños</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('competencias')">Competencias</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showSection('raps')">RAPs</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Alertas -->
        <div id="alertContainer"></div>

        <!-- Sección Diseños -->
        <div id="disenosSection" class="section active">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-plus-circle me-2"></i>Crear/Editar Diseño</h5>
                        </div>
                        <div class="card-body">
                            <form id="disenoForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="codigoPrograma" class="form-label">Código Programa *</label>
                                            <input type="text" class="form-control" id="codigoPrograma" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="versionPrograma" class="form-label">Versión Programa *</label>
                                            <input type="text" class="form-control" id="versionPrograma" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="codigoDiseñoGenerado" class="form-label">Código Diseño (Generado)</label>
                                    <input type="text" class="form-control" id="codigoDiseñoGenerado" readonly>
                                </div>

                                <div class="alert alert-info" id="disenoExistente" style="display: none;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="disenoExistenteMsg"></span>
                                </div>

                                <div class="mb-3">
                                    <label for="lineaTecnologica" class="form-label">Línea Tecnológica *</label>
                                    <input type="text" class="form-control" id="lineaTecnologica" required>
                                </div>

                                <div class="mb-3">
                                    <label for="redTecnologica" class="form-label">Red Tecnológica *</label>
                                    <input type="text" class="form-control" id="redTecnologica" required>
                                </div>

                                <div class="mb-3">
                                    <label for="redConocimiento" class="form-label">Red de Conocimiento *</label>
                                    <input type="text" class="form-control" id="redConocimiento" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="horasDesarrolloDiseño" class="form-label">Horas de Desarrollo</label>
                                            <input type="number" step="0.01" class="form-control" id="horasDesarrolloDiseño" placeholder="Opcional">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mesesDesarrolloDiseño" class="form-label">Meses de Desarrollo</label>
                                            <input type="number" step="0.01" class="form-control" id="mesesDesarrolloDiseño" placeholder="Opcional">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nivelAcademicoIngreso" class="form-label">Nivel Académico de Ingreso *</label>
                                            <select class="form-select" id="nivelAcademicoIngreso" required>
                                                <option value="">Seleccionar...</option>
                                                <option value="Primaria">Primaria</option>
                                                <option value="Bachillerato">Bachillerato</option>
                                                <option value="Tecnólogo">Tecnólogo</option>
                                                <option value="Técnico">Técnico</option>
                                                <option value="Universitario">Universitario</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gradoNivelAcademico" class="form-label">Grado del Nivel Académico</label>
                                            <input type="text" class="form-control" id="gradoNivelAcademico">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="formacionTrabajoDesarrolloHumano" class="form-label">Formación para el Trabajo *</label>
                                            <select class="form-select" id="formacionTrabajoDesarrolloHumano" required>
                                                <option value="">Seleccionar...</option>
                                                <option value="Si">Sí</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edadMinima" class="form-label">Edad Mínima *</label>
                                            <input type="number" class="form-control" id="edadMinima" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="requisitosAdicionales" class="form-label">Requisitos Adicionales</label>
                                    <textarea class="form-control" id="requisitosAdicionales" rows="3"></textarea>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-secondary me-md-2" onclick="limpiarFormularioDiseno()">Limpiar</button>
                                    <button type="submit" class="btn btn-primary" id="btnSubmitDiseno">Guardar Diseño</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-list me-2"></i>Diseños Registrados</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Línea Tecnológica</th>
                                            <th>Horas</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="disenosTable">
                                        <!-- Datos cargados dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección Competencias -->
        <div id="competenciasSection" class="section">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-plus-circle me-2"></i>Crear/Editar Competencia</h5>
                        </div>
                        <div class="card-body">
                            <form id="competenciaForm">
                                <div class="mb-3">
                                    <label for="codigoDiseñoComp" class="form-label">Código Diseño *</label>
                                    <select class="form-select" id="codigoDiseñoComp" required>
                                        <option value="">Seleccionar diseño...</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="codigoCompetencia" class="form-label">Código Competencia *</label>
                                    <input type="text" class="form-control" id="codigoCompetencia" required>
                                </div>

                                <div class="mb-3">
                                    <label for="codigoCompetenciaGenerado" class="form-label">Código Diseño-Competencia (Generado)</label>
                                    <input type="text" class="form-control" id="codigoCompetenciaGenerado" readonly>
                                </div>

                                <div class="alert alert-info" id="competenciaExistente" style="display: none;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="competenciaExistenteMsg"></span>
                                </div>

                                <div class="mb-3">
                                    <label for="nombreCompetencia" class="form-label">Nombre de la Competencia *</label>
                                    <input type="text" class="form-control" id="nombreCompetencia" required>
                                </div>

                                <div class="mb-3">
                                    <label for="normaUnidad" class="form-label">Norma de Unidad de Competencia</label>
                                    <textarea class="form-control" id="normaUnidad" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="horasCompetencia" class="form-label">Horas de Desarrollo *</label>
                                    <input type="number" step="0.01" class="form-control" id="horasCompetencia" required>
                                </div>

                                <div class="mb-3">
                                    <label for="requisitosInstructor" class="form-label">Requisitos Académicos del Instructor</label>
                                    <textarea class="form-control" id="requisitosInstructor" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="experienciaInstructor" class="form-label">Experiencia Laboral del Instructor</label>
                                    <textarea class="form-control" id="experienciaInstructor" rows="3"></textarea>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-secondary me-md-2" onclick="limpiarFormularioCompetencia()">Limpiar</button>
                                    <button type="submit" class="btn btn-primary" id="btnSubmitCompetencia">Guardar Competencia</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-list me-2"></i>Competencias Registradas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Horas</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="competenciasTable">
                                        <!-- Datos cargados dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección RAPs -->
        <div id="rapsSection" class="section">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-plus-circle me-2"></i>Crear/Editar RAP</h5>
                        </div>
                        <div class="card-body">
                            <form id="rapForm">
                                <div class="mb-3">
                                    <label for="codigoDiseñoRap" class="form-label">Código Diseño *</label>
                                    <select class="form-select" id="codigoDiseñoRap" required>
                                        <option value="">Seleccionar diseño...</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="codigoCompetenciaRap" class="form-label">Código Competencia *</label>
                                    <select class="form-select" id="codigoCompetenciaRap" required>
                                        <option value="">Seleccionar competencia...</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="codigoRap" class="form-label">Código RAP *</label>
                                    <input type="text" class="form-control" id="codigoRap" required placeholder="ej: RA1, RA2, etc.">
                                </div>

                                <div class="mb-3">
                                    <label for="codigoRapGenerado" class="form-label">Código Completo (Generado)</label>
                                    <input type="text" class="form-control" id="codigoRapGenerado" readonly>
                                </div>

                                <div class="alert alert-info" id="rapExistente" style="display: none;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span id="rapExistenteMsg"></span>
                                </div>

                                <div class="mb-3">
                                    <label for="nombreRap" class="form-label">Nombre del RAP *</label>
                                    <input type="text" class="form-control" id="nombreRap" required>
                                </div>

                                <div class="mb-3">
                                    <label for="horasRap" class="form-label">Horas de Desarrollo *</label>
                                    <input type="number" step="0.01" class="form-control" id="horasRap" required>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-secondary me-md-2" onclick="limpiarFormularioRap()">Limpiar</button>
                                    <button type="submit" class="btn btn-primary" id="btnSubmitRap">Guardar RAP</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-list me-2"></i>RAPs Registrados</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>Horas</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rapsTable">
                                        <!-- Datos cargados dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>

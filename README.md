# Sistema de Gestión de Diseños Curriculares

Una aplicación web completa para la gestión de diseños curriculares del SENA, que permite administrar diseños, competencias y RAPs (Resultados de Aprendizaje) con validaciones automáticas y generación de códigos.

## Características

- **Gestión de Diseños**: Crear y editar diseños curriculares con validación automática de códigos
- **Gestión de Competencias**: Administrar competencias asociadas a cada diseño
- **Gestión de RAPs**: Crear y editar resultados de aprendizaje vinculados a competencias
- **Validaciones en tiempo real**: El sistema valida automáticamente si los registros ya existen
- **Generación automática de códigos**: Los códigos primarios se generan siguiendo la lógica establecida
- **Interfaz moderna**: Diseño responsivo con Bootstrap 5 y iconos Font Awesome

## Estructura de Códigos

El sistema maneja una estructura jerárquica de códigos:

### Diseños
- **Formato**: `codigoPrograma-versionPrograma`
- **Ejemplo**: `124101-1`

### Competencias  
- **Formato**: `codigoDiseño-codigoCompetencia`
- **Ejemplo**: `124101-1-220201501`

### RAPs
- **Formato**: `codigoDiseño-codigoCompetencia-codigoRap`
- **Ejemplo**: `124101-1-220201501-RA1`

## Requisitos del Sistema

- Node.js >= 14.0.0
- MySQL >= 8.0
- npm >= 6.0.0

## Instalación

### 1. Clonar el repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd appForms
```

### 2. Instalar dependencias
```bash
npm install
```

### 3. Configurar la base de datos

#### Crear la base de datos
```bash
mysql -u root -p < sql/schema.sql
```

#### Configurar variables de entorno
Copiar el archivo `.env` y ajustar las credenciales de la base de datos:

```env
# Variables de base de datos
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=tu_password
DB_NAME=disenos_curriculares
DB_PORT=3306

# Puerto del servidor
PORT=3000
```

### 4. Iniciar la aplicación

#### Modo desarrollo
```bash
npm run dev
```

#### Modo producción
```bash
npm start
```

### 5. Acceder a la aplicación
Abrir el navegador en: `http://localhost:3000`

## Estructura del Proyecto

```
appForms/
├── app/
│   └── server.js          # Servidor principal de Express
├── conf/
│   └── database.js        # Configuración de base de datos
├── public/
│   ├── index.html         # Página principal
│   ├── styles/
│   │   └── main.css       # Estilos personalizados
│   └── js/
│       └── main.js        # Lógica del frontend
├── routes/
│   ├── disenos.js         # API endpoints para diseños
│   ├── competencias.js    # API endpoints para competencias
│   └── raps.js            # API endpoints para RAPs
├── sql/
│   ├── schema.sql         # Script de creación de BD
│   └── Exportación MySQL Jun 12 2025.sql  # Script original
├── package.json
├── .env                   # Variables de entorno
└── README.md
```

## API Endpoints

### Diseños
- `GET /api/disenos` - Obtener todos los diseños
- `GET /api/disenos/:codigo` - Obtener diseño por código
- `GET /api/disenos/validate/:codigoPrograma/:versionPrograma` - Validar existencia
- `POST /api/disenos` - Crear nuevo diseño
- `PUT /api/disenos/:codigo` - Actualizar diseño

### Competencias
- `GET /api/competencias` - Obtener todas las competencias
- `GET /api/competencias/diseno/:codigoDiseño` - Competencias por diseño
- `GET /api/competencias/validate/:codigoDiseño/:codigoCompetencia` - Validar existencia
- `POST /api/competencias` - Crear nueva competencia
- `PUT /api/competencias/:codigo` - Actualizar competencia

### RAPs
- `GET /api/raps` - Obtener todos los RAPs
- `GET /api/raps/competencia/:codigoDiseñoCompetencia` - RAPs por competencia
- `GET /api/raps/validate/:codigoDiseño/:codigoCompetencia/:codigoRap` - Validar existencia
- `POST /api/raps` - Crear nuevo RAP
- `PUT /api/raps/:codigo` - Actualizar RAP

## Uso de la Aplicación

### Crear un Diseño
1. Ir a la sección "Diseños"
2. Llenar los campos obligatorios:
   - Código de Programa
   - Versión de Programa
   - Línea Tecnológica
   - Red Tecnológica
   - Red de Conocimiento
   - Horas y meses de desarrollo
   - Nivel académico de ingreso
   - Formación para el trabajo
   - Edad mínima
3. El sistema genera automáticamente el código del diseño
4. Si el código ya existe, se mostrarán los datos para edición
5. Hacer clic en "Guardar Diseño"

### Crear una Competencia
1. Ir a la sección "Competencias"
2. Seleccionar un diseño existente
3. Ingresar el código de la competencia
4. Llenar los demás campos obligatorios
5. El sistema valida automáticamente la existencia
6. Hacer clic en "Guardar Competencia"

### Crear un RAP
1. Ir a la sección "RAPs"
2. Seleccionar un diseño existente
3. Seleccionar una competencia del diseño
4. Ingresar el código del RAP (ej: RA1, RA2)
5. Llenar el nombre y horas del RAP
6. Hacer clic en "Guardar RAP"

## Validaciones del Sistema

- **Códigos únicos**: El sistema previene duplicados verificando las claves primarias
- **Dependencias**: No se pueden crear competencias sin diseños, ni RAPs sin competencias
- **Campos obligatorios**: Validación tanto en frontend como backend
- **Tipos de datos**: Validación de números, decimales y selecciones

## Datos de Ejemplo

El sistema incluye datos de ejemplo para testing:
- Diseño: "124101-1" - Desarrollo de Procesos de Mercadeo
- Competencias relacionadas con mercadeo e investigación de mercados
- RAPs asociados a cada competencia

## Tecnologías Utilizadas

### Backend
- **Node.js**: Runtime de JavaScript
- **Express.js**: Framework web
- **MySQL2**: Driver para MySQL
- **dotenv**: Gestión de variables de entorno
- **body-parser**: Parseo de datos HTTP
- **cors**: Configuración de CORS

### Frontend
- **HTML5**: Estructura semántica
- **CSS3**: Estilos personalizados
- **JavaScript ES6+**: Lógica del cliente
- **Bootstrap 5**: Framework CSS
- **Font Awesome**: Iconografía

### Base de Datos
- **MySQL 8.0**: Sistema de gestión de base de datos
- **InnoDB**: Motor de almacenamiento
- **UTF8MB4**: Codificación de caracteres

## Desarrollo

### Instalar dependencias de desarrollo
```bash
npm install --save-dev nodemon
```

### Ejecutar en modo desarrollo
```bash
npm run dev
```

El servidor se reiniciará automáticamente al detectar cambios en los archivos.

## Contribución

1. Fork del proyecto
2. Crear una rama para la nueva característica
3. Commit de los cambios
4. Push a la rama
5. Abrir un Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT.

## Soporte

Para soporte técnico o consultas sobre el sistema, contactar al equipo de desarrollo.

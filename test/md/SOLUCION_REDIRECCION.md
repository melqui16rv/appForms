# 🚨 GUÍA DE RESOLUCIÓN - PROBLEMA DE REDIRECCIÓN EN PRODUCCIÓN

## PROBLEMA IDENTIFICADO
En appscide.com/appForms la URL se mantiene correcta pero muestra contenido incorrecto, sugiriendo una redirección silenciosa o configuración problemática del servidor.

## ARCHIVOS CREADOS PARA DIAGNÓSTICO

### 1. `diagnostico.php` - Diagnóstico Completo
**Propósito:** Identificar la causa exacta del problema de redirección
**Uso:** https://appscide.com/appForms/diagnostico.php

**Qué verifica:**
- Información del servidor y headers
- Detección de redirecciones silenciosas
- Verificación de archivos .htaccess
- Estado de archivos críticos
- Configuración de base de datos
- Test de APIs

### 2. `test_redireccion.php` - Test Específico de URLs
**Propósito:** Probar URLs específicas y detectar redirecciones
**Uso:** https://appscide.com/appForms/test_redireccion.php

**Funcionalidades:**
- Test de headers HTTP
- Detección de redirecciones 301/302
- Verificación de URLs individuales
- Información detallada del navegador

### 3. `.htaccess` Actualizado
**Propósito:** Prevenir redirecciones heredadas del dominio raíz
**Cambios:** Agregadas reglas anti-redirección específicas

### 4. `htaccess_raiz_corregido.txt`
**Propósito:** Archivo de ejemplo para el .htaccess del dominio raíz
**Uso:** Copiar contenido al .htaccess de public_html/

### 5. `config_produccion.php`
**Propósito:** Configuración específica para producción con instrucciones detalladas
**Uso:** Reemplazar config.php después de configurar la base de datos

## PASOS PARA RESOLVER EL PROBLEMA

### PASO 1: DIAGNÓSTICO INICIAL
1. Subir el archivo `diagnostico.php` a appscide.com/appForms/
2. Acceder a https://appscide.com/appForms/diagnostico.php
3. Revisar la sección "🔄 Detección de Redirecciones"
4. Si hay headers REDIRECT_*, el problema está confirmado

### PASO 2: VERIFICAR .HTACCESS DEL DOMINIO RAÍZ
1. Acceder a cPanel > File Manager
2. Ir a public_html/
3. Buscar archivo .htaccess (mostrar archivos ocultos)
4. **CRÍTICO:** Buscar reglas que puedan afectar /appForms/

**Reglas problemáticas comunes:**
```apache
RewriteRule ^(.*)$ /otra-carpeta/$1 [L]
RewriteRule .* index.php [L]
RewriteRule ^appForms/(.*)$ /redireccion/$1 [L]
```

### PASO 3: APLICAR CORRECCIÓN DE .HTACCESS
1. **Opción A - Modificar .htaccess raíz:**
   - Editar public_html/.htaccess
   - Agregar al INICIO del archivo:
   ```apache
   # EXCLUIR appForms de redirecciones
   RewriteRule ^appForms(/.*)?$ - [L]
   ```

2. **Opción B - Usar archivo corregido:**
   - Usar el contenido de `htaccess_raiz_corregido.txt`
   - Reemplazar completamente public_html/.htaccess

### PASO 4: VERIFICAR CONFIGURACIÓN DE CPANEL
1. **Revisar Subdominios:**
   - cPanel > Subdominios
   - Verificar que no haya redirecciones automáticas

2. **Revisar Redirects:**
   - cPanel > Redirects
   - Verificar que no haya reglas que afecten /appForms

3. **Revisar Addon Domains:**
   - Verificar configuración de dominios adicionales

### PASO 5: CONFIGURAR BASE DE DATOS
1. cPanel > MySQL Databases
2. Crear base de datos: "disenos" (será: cuenta_disenos)
3. Crear usuario: "appuser" (será: cuenta_appuser)
4. Asignar privilegios completos
5. Actualizar `config.php` con los datos reales

### PASO 6: VERIFICACIÓN FINAL
1. Acceder a https://appscide.com/appForms/diagnostico.php
2. Verificar que no hay redirecciones detectadas
3. Test de conexión de base de datos exitoso
4. Probar la aplicación principal

## SEÑALES DE ÉXITO

### ✅ PROBLEMA RESUELTO:
- diagnostico.php muestra "No se detectaron redirecciones"
- URL se mantiene como appscide.com/appForms/
- Contenido correcto (formularios de diseños curriculares)
- APIs responden correctamente
- Base de datos conecta sin errores

### ❌ PROBLEMA PERSISTE:
- Headers REDIRECT_* presentes
- Contenido diferente al esperado
- URLs cambian automáticamente
- Errores 404 en recursos estáticos

## TROUBLESHOOTING ADICIONAL

### Si el problema persiste después de los pasos anteriores:

1. **Verificar configuración de Apache/LiteSpeed:**
   - Contactar soporte de hosting
   - Solicitar verificación de configuración virtual host

2. **Verificar logs del servidor:**
   - cPanel > Error Logs
   - Buscar entradas relacionadas con appForms

3. **Test desde diferentes ubicaciones:**
   - Usar herramientas online como: whatsmydns.net
   - Verificar desde diferentes dispositivos/redes

4. **Verificar DNS:**
   - Confirmar que el dominio apunta correctamente
   - Verificar registros A y CNAME

## CONTACTO PARA SOPORTE

Si después de seguir todos los pasos el problema persiste:

1. Ejecutar `diagnostico.php` y capturar pantalla completa
2. Ejecutar `test_redireccion.php` y documentar resultados
3. Copiar contenido completo del .htaccess del dominio raíz
4. Proporcionar nombre de la cuenta de cPanel
5. Incluir esta información en la solicitud de ayuda

## ARCHIVOS DE RESPALDO

Antes de realizar cambios:
1. Respaldar public_html/.htaccess
2. Exportar configuración actual de cPanel
3. Documentar configuración de subdominios/redirects existente

---

**Última actualización:** 2025-01-26
**Versión:** 1.0
**Archivos incluidos:** diagnostico.php, test_redireccion.php, .htaccess actualizado, config_produccion.php

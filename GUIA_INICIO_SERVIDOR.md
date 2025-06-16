# 🚀 GUÍA PASO A PASO - INICIAR SERVIDOR LOCAL

## 📋 INFORMACIÓN ACTUAL DEL SERVIDOR
- **Estado**: ✅ FUNCIONANDO
- **IP**: 192.168.55.77:8080
- **URL Local**: http://localhost:8080
- **URL Red**: http://192.168.55.77:8080
- **Fecha**: 16 de junio de 2025
- **Configuración**: ✅ OPTIMIZADA PARA RED LOCAL

---

## 🛠️ OPCIÓN 1: INICIO AUTOMÁTICO (RECOMENDADO)

### Paso 1: Abrir Terminal
```bash
# Abrir Terminal en macOS (Cmd + Space, escribir "Terminal")
```

### Paso 2: Navegar al proyecto
```bash
cd /Users/melquiromero/Documents/GitHub/appForms
```

### Paso 3: Ejecutar script automático
```bash
./test/iniciar_servidor.sh
```

**¡Listo!** El servidor se iniciará automáticamente y verás:
```
🚀 INICIANDO SERVIDOR DE DISEÑOS CURRICULARES
=============================================
🗄️  Verificando MySQL...
🌐 Iniciando servidor PHP en [TU_IP]:8080...
🔗 URL: http://[TU_IP]:8080
```

---

## ⚙️ OPCIÓN 2: INICIO MANUAL (PASO A PASO)

### Paso 1: Verificar MySQL
```bash
# Verificar si MySQL está corriendo
brew services list | grep mysql

# Si no está iniciado, iniciarlo:
brew services start mysql
```

### Paso 2: Navegar al directorio del proyecto
```bash
cd /Users/melquiromero/Documents/GitHub/appForms
```

### Paso 3: Obtener tu IP local
```bash
# Ver tu IP actual
ifconfig | grep "inet " | grep -v 127.0.0.1

# O usar este comando más específico:
ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1
```

### Paso 4: Verificar que el puerto esté libre
```bash
# Verificar si el puerto 8080 está libre
lsof -i :8080

# Si está ocupado, terminar el proceso:
sudo lsof -t -i :8080 | xargs kill -9
```

### Paso 5: Iniciar el servidor PHP
```bash
# Iniciar servidor en todas las interfaces (0.0.0.0) puerto 8080
php -S 0.0.0.0:8080
```

---

## 🌐 ACCESO AL SISTEMA

### 🖥️ Desde la misma computadora:
- **URL local**: http://localhost:8080
- **URL IP**: http://192.168.55.77:8080

### 📱 Desde otros dispositivos en la red:
- **URL**: http://192.168.55.77:8080
- **Requisito**: Ambos dispositivos en la misma red WiFi
- **Dispositivos soportados**: Teléfonos, tablets, otras computadoras

### ✅ Verificación de acceso:
```bash
# Ejecutar script de verificación
./test/verificar_acceso_red.sh
```

---

## 🛑 DETENER EL SERVIDOR

### En Terminal donde corre el servidor:
```bash
# Presionar Ctrl + C para detener
```

### Si el proceso se quedó en background:
```bash
# Encontrar y terminar proceso en puerto 8080
sudo lsof -t -i :8080 | xargs kill -9
```

---

## 🔧 SOLUCIÓN DE PROBLEMAS

### ❌ Error: "Port already in use"
```bash
# Terminar proceso existente
sudo lsof -t -i :8080 | xargs kill -9
# Reintentar
./test/iniciar_servidor.sh
```

### ❌ Error: "MySQL connection failed"
```bash
# Verificar y reiniciar MySQL
brew services restart mysql
# Esperar 5 segundos y reintentar
```

### ❌ Error: "Permission denied"
```bash
# Dar permisos al script
chmod +x test/iniciar_servidor.sh
```

### ❌ No se ve desde otros dispositivos
```bash
# Verificar firewall de macOS
# Sistema > Seguridad y Privacidad > Firewall
# Permitir conexiones entrantes para PHP

# O deshabilitar temporalmente el firewall para pruebas
sudo /usr/libexec/ApplicationFirewall/socketfilterfw --setglobalstate off

# Verificar que el servidor escuche en todas las interfaces
netstat -an | grep :8080
```

### ❌ Error de conexión desde dispositivos remotos
```bash
# Verificar que ambos dispositivos estén en la misma red
# En el dispositivo remoto, verificar conectividad:
ping 192.168.55.77

# Si no hay respuesta, revisar configuración de red
```

---

## 📱 VERIFICACIÓN DE FUNCIONAMIENTO

### 1. Abrir navegador y ir a:
```
http://192.168.55.77:8080
```

### 2. Deberías ver:
- ✅ Interfaz de Diseños Curriculares
- ✅ Tabs: Diseños, Competencias, RAPs
- ✅ Sin errores de conexión

### 3. Probar funcionalidad:
- ✅ Crear un diseño de prueba
- ✅ Agregar una competencia
- ✅ Verificar que los datos se guardan

---

## 🎯 COMANDOS RÁPIDOS DE REFERENCIA

```bash
# Ir al proyecto
cd /Users/melquiromero/Documents/GitHub/appForms

# Iniciar todo automáticamente
./test/iniciar_servidor.sh

# Solo iniciar PHP (si MySQL ya está corriendo)
php -S 0.0.0.0:8080

# Verificar estado del servidor
curl -s http://localhost:8080 | head -10

# Detener proceso en puerto 8080
sudo lsof -t -i :8080 | xargs kill -9
```

---

## 📞 ESTADO ACTUAL
- **Servidor**: ✅ CORRIENDO
- **URL Actual**: http://192.168.55.77:8080
- **MySQL**: ✅ FUNCIONANDO
- **APIs**: ✅ OPERATIVAS

**¡El sistema está listo para usar!** 🎉

---

## 🔄 CAMBIO DE RED WiFi O IP

### 📡 **Cuando cambies de red WiFi:**

#### **Paso 1: Detectar nueva IP**
```bash
# Ver tu nueva IP
ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1
```

#### **Paso 2: Actualizar configuración automáticamente**
```bash
# Usar script automático para actualizar IP
cd /Users/melquiromero/Documents/GitHub/appForms
./test/actualizar_ip_red.sh
```

#### **Paso 3: Reiniciar servidor**
```bash
# Detener servidor actual
sudo lsof -t -i :8080 | xargs kill -9

# Iniciar con nueva configuración
./test/iniciar_servidor.sh
```

### 🛠️ **Actualización manual de IP:**

#### **Si necesitas cambiar manualmente la IP:**
1. **Editar config.php:**
   ```bash
   nano config.php
   ```
   
2. **Buscar alrededor de la línea 37:**
   ```php
   // Archivo: config.php - Configuración de URLs
   $config['base_url'] = 'http://192.168.55.77:8080'; // ← CAMBIAR ESTA IP
   ```

3. **Reemplazar por tu nueva IP:**
   ```php
   $config['base_url'] = 'http://TU_NUEVA_IP:8080'; // ← Nueva IP aquí
   ```

4. **Guardar (Ctrl+O) y salir (Ctrl+X)**
5. **Reiniciar servidor**

### 📱 **Comunicar nueva URL:**
Después del cambio, compartir la nueva URL:
```
http://TU_NUEVA_IP:8080
```

### 🧪 **Verificar cambio:**
```bash
# Verificar que funciona en nueva red
./test/verificar_acceso_red.sh
```

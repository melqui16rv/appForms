# 🔄 GUÍA RÁPIDA - CAMBIO DE RED WiFi

## 🚨 CUANDO CAMBIES DE RED WiFi O LA IP CAMBIE

### 🎯 **MÉTODO AUTOMÁTICO (RECOMENDADO)**

```bash
# 1. Ir al directorio del proyecto
cd /Users/melquiromero/Documents/GitHub/appForms

# 2. Ejecutar script de actualización automática
./test/actualizar_ip_red.sh

# 3. Reiniciar servidor
./test/iniciar_servidor.sh
```

**¡Listo!** La nueva IP se detectará y configurará automáticamente.

---

### ⚙️ **MÉTODO MANUAL**

#### **Paso 1: Detectar nueva IP**
```bash
ifconfig | grep "inet " | grep -v 127.0.0.1 | awk '{print $2}' | head -1
```

#### **Paso 2: Editar configuración**
```bash
nano config.php
```

#### **Paso 3: Buscar y modificar (línea ~45)**
```php
// ENCONTRAR esta sección:
// ========================================================
// 🌐 CONFIGURACIÓN DE RED - CAMBIAR AQUÍ SI ES NECESARIO
// ========================================================

// COMENTAR la detección automática:
// $current_ip = $_SERVER['HTTP_HOST'] ?? 'localhost';
// if (strpos($current_ip, ':') !== false) {
//     $config['base_url'] = 'http://' . $current_ip;
// } else {
//     $config['base_url'] = 'http://' . $current_ip . ':8080';
// }

// DESCOMENTAR y MODIFICAR la línea manual:
$config['base_url'] = 'http://TU_NUEVA_IP:8080';
// Ejemplo: $config['base_url'] = 'http://192.168.1.100:8080';
```

#### **Paso 4: Guardar y salir**
```bash
# En nano:
Ctrl + O  (guardar)
Ctrl + X  (salir)
```

#### **Paso 5: Reiniciar servidor**
```bash
# Detener servidor actual
sudo lsof -t -i :8080 | xargs kill -9

# Iniciar servidor
./test/iniciar_servidor.sh
```

---

## 📱 **COMPARTIR NUEVA URL**

### Después del cambio, informar a usuarios:
```
Nueva URL: http://TU_NUEVA_IP:8080
```

### URLs de ejemplo según red:
- **Casa**: `http://192.168.1.100:8080`
- **Oficina**: `http://10.0.0.50:8080`
- **Hotel**: `http://172.16.1.25:8080`

---

## 🧪 **VERIFICAR QUE FUNCIONA**

```bash
# Verificar acceso en nueva red
./test/verificar_acceso_red.sh

# Probar desde otro dispositivo (sustituir IP)
curl http://TU_NUEVA_IP:8080
```

---

## 🛠️ **ARCHIVOS QUE SE MODIFICAN**

### **Configuración principal:**
- `config.php` (línea ~45-55)

### **Scripts automáticos:**
- `test/actualizar_ip_red.sh` - Actualiza automáticamente
- `test/verificar_acceso_red.sh` - Verifica nueva configuración

---

## ⚡ **COMANDOS RÁPIDOS DE EMERGENCIA**

```bash
# Ver IP actual
ifconfig | grep "inet " | grep -v 127.0.0.1

# Actualizar automáticamente
./test/actualizar_ip_red.sh

# Reiniciar servidor
./test/iniciar_servidor.sh

# Verificar funcionamiento
curl http://localhost:8080

# Ver qué IP está configurada
grep "base_url" config.php
```

---

## 🔧 **PROBLEMAS COMUNES**

### ❌ **No detecta la nueva IP**
```bash
# Verificar conexión WiFi
networksetup -getairportnetwork en0

# Reiniciar WiFi
sudo ifconfig en0 down && sudo ifconfig en0 up
```

### ❌ **Script no encuentra archivo**
```bash
# Asegurarse de estar en directorio correcto
pwd
# Debe mostrar: /Users/melquiromero/Documents/GitHub/appForms

# Si no, navegar al directorio
cd /Users/melquiromero/Documents/GitHub/appForms
```

### ❌ **Permisos negados**
```bash
# Dar permisos a scripts
chmod +x test/*.sh
```

---

**💡 TIP**: Guarda esta guía en tu teléfono para acceso rápido cuando cambies de red.

**📂 Ubicación**: `/Users/melquiromero/Documents/GitHub/appForms/CAMBIO_IP_WIFI.md`

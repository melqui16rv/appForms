# Instrucciones de configuración para producción

## 1. Configurar la base de datos en config.php

Abre el archivo `config.php` y actualiza la sección de producción:

```php
} else {
    // Configuración de producción - ACTUALIZAR ESTOS VALORES
    $config['db'] = [
        'host' => 'localhost', // Generalmente localhost en cPanel
        'dbname' => 'appscide_disenos', // Cambiar por el nombre real de tu BD
        'username' => 'appscide_user', // Cambiar por tu usuario de BD
        'password' => 'tu_password_real', // Cambiar por tu contraseña real
        'charset' => 'utf8mb4'
    ];
    $config['base_url'] = 'https://appscide.com/appForms';
}
```

## 2. Crear la base de datos en cPanel

1. Ve a cPanel > Bases de datos MySQL
2. Crea una nueva base de datos llamada algo como `appscide_disenos`
3. Crea un usuario para la base de datos
4. Asigna permisos completos al usuario
5. Importa el archivo `sql/schema.sql` usando phpMyAdmin

## 3. Modificar .htaccess raíz

En el .htaccess de public_html, ANTES de las reglas de redirección existentes, agregar:

```apache
# Excluir appForms de las redirecciones automáticas
RewriteCond %{REQUEST_URI} !^/appForms/
```

Esto debe ir antes de cada RewriteRule que redirija a viaticosApp.

## 4. Subir archivos

Sube todos los archivos de la carpeta appForms a public_html/appForms/

## 5. Verificar permisos

Asegúrate de que los archivos tengan los permisos correctos:
- Archivos PHP: 644
- Carpetas: 755
- .htaccess: 644

## 6. Prueba

Después de estos cambios, deberías poder acceder a:
https://appscide.com/appForms/index.php

Y ver la aplicación de diseños curriculares funcionando correctamente.

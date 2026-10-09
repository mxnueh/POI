# POI

Portal web institucional con inicio de sesión y paneles diferenciados para **administradores** y **usuarios**, desarrollado en PHP y MySQL.

## 1. Descripción

POI es un portal de gestión de contenido y usuarios con identidad salesiana. Tras iniciar sesión, cada persona accede al panel que corresponde a su cargo: el lado de **cliente** (usuario) o el lado de **administración**.

## 2. Características

- Login con validación de credenciales mediante consultas preparadas (`mysqli`).
- Sesiones PHP y redirección según el cargo del usuario.
- Panel de usuario con las secciones: Dashboard, Misión, Formación, Consagración, Familia Salesiana y Desarrollo Institucional.
- Perfil del usuario con pestañas de Seguridad, Notificaciones y Preferencias.
- Panel de administración con las mismas secciones para gestionar el contenido.
- Barra lateral colapsable (`app.js`) y ventanas emergentes (`openPopup.js`).

## 3. Tecnologías

- PHP · MySQL (`mysqli`)
- HTML · CSS · JavaScript

## 4. Requisitos

- PHP 7.4 o superior
- MySQL/MariaDB
- XAMPP, WAMP o similar

## 5. Configuración

1. Crea la base de datos `poi_db` con, al menos, las tablas `usuarios` y `cargo`.
2. Revisa los datos de conexión en `Pages/db.php` y `Pages/validar.php` (servidor, usuario, clave y base de datos).

## 6. Uso

Copia el proyecto en `htdocs` y abre:

```
http://localhost/POI/Pages/Login.php
```

## 7. Estructura

```
POI/
├── Pages/
│   ├── Login.php · LoginAdmin.css   # Inicio de sesión
│   ├── validar.php                  # Validación de credenciales y redirección
│   ├── db.php                       # Conexión a MySQL
│   ├── client_side/                 # Panel de usuario (PHP)
│   │   └── profile-pages/           # Seguridad, Notificaciones, Preferencias
│   └── admin_side/                  # Panel de administración (HTML)
└── sin usar/                        # Archivos descartados de versiones anteriores
```

## 8. Mejoras pendientes

- Guardar las contraseñas con hash (`password_hash` / `password_verify`) en lugar de compararlas en texto plano.
- Usar consultas preparadas también en `client_side/Dashboard.php`.
- Sacar las credenciales de conexión del código.

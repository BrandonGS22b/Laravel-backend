<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel
# 🧾 Sistema de Gestión de Contribuyentes

Aplicación web desarrollada con **Laravel**, utilizando el **stack Breeze + Vite + TailwindCSS**, que permite la **gestión de contribuyentes** con un panel administrativo dinámico basado en **DataTables** y peticiones **AJAX**.  
Implementa una arquitectura limpia con **patrón repositorio**, **validaciones personalizadas** y **componentes Blade reutilizables**.

---

## 🚀 Tecnologías principales

- **Laravel 12+**
- **PHP 8.2+**
- **Laravel Breeze (autenticación con Blade)**
- **Vite (compilador frontend)**
- **Tailwind CSS**
- **jQuery + DataTables**
- **MySQL / MariaDB**
- **Eloquent ORM**
- **AJAX / Fetch API**

---

## ⚙️ Instalación y configuración

### 1️⃣ Clonar el repositorio

```bash
git clone https://github.com/tuusuario/gestion-contribuyentes.git
cd gestion-contribuyentes


2️⃣ Instalar dependencias
composer install
npm install

3️⃣ Crear el archivo de entorno
cp .env.example .env / se puede manual tambien


Edita el archivo .env con tus credenciales si usas postgres deberas cambiarle el db_connection por el nombre correspondiente:

APP_NAME="Gestión Contribuyentes"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=contribuyentes_db
DB_USERNAME=root
DB_PASSWORD=


Genera la clave de aplicación:

php artisan key:generate

🧩 Migraciones y datos iniciales

Ejecuta las migraciones y, si tienes seeders:

php artisan migrate --seed

🧑‍💻 Iniciar el servidor
Backend (Laravel)
php artisan serve

Frontend (Vite)
npm run dev


Luego abre 👉 http://127.0.0.1:8000

📂 Estructura destacada del proyecto
app/
 ├── Helpers/
 │    ├── ContarLetrasHelper.php  # Lógica para análisis de texto (Frecuencia de letras)
 │    └── ValidationHelper.php    # Validaciones personalizadas (ej: correo válido)
 ├── Http/
 │    ├── Controllers/
 │    │    ├── Auth/             # Controladores de Autenticación
 │    │    └── ContribuyenteController.php
 │    └── Requests/
 │         └── Auth/             # Requests de Autenticación (ej: ProfileUpdateRequest.php)
 ├── Providers/
 │    └── AppServiceProvider.php  # Binding de interfaces a implementaciones (Repositorios)
 ├── Repositories/
 │    ├── Interfaces/
 │    │    └── ContribuyenteRepositoryInterface.php
 │    └── ContribuyenteRepository.php # Implementación del patrón Repositorio
 ├── Service/                     # Capa de Servicio para lógica de negocio compleja
 └── Models/                      # Modelos de Eloquent (ej: Contribuyente.php)
resources/
 ├── views/
 │    ├── contribuyentes/
 │    │    ├── index.blade.php    # (Listado principal DataTables)
 │    │    └── ... otros blade de gestión
 │    └── ... otras vistas (layouts, auth)
 ├── css/                         # Estilos Tailwind (app.css, dashboard.css)
 └── js/                          # Lógica DataTables + AJAX (app.js, bootstrap.js)
📊 Funcionalidades principales

✅ Autenticación completa (login, registro, logout, restablecer contraseña)
✅ Gestión de contribuyentes (crear, listar, editar, eliminar)
✅ Búsqueda dinámica con DataTables + AJAX
✅ Validaciones en frontend y backend
✅ Arquitectura limpia con Repositorios y Helpers
✅ Interfaz responsive con Tailwind CSS

🧠 Validaciones personalizadas

Archivo: app/Helpers/ValidationHelper.php

namespace App\Helpers;

class ValidationHelper
{
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

🧰 Comandos útiles
Comando	Descripción
php artisan serve
npm run dev
php artisan serve	Inicia el servidor backend
npm run dev	Inicia el compilador de Vite
php artisan migrate:fresh --seed	Reinicia la base de datos
php artisan route:list	Lista todas las rutas registradas
php artisan make:model Nombre -mcr	Crea modelo con migración, controlador y recurso
🧑‍🏫 Autor

Brandon Suárez
💼 Desarrollador Backend / Fullstack
📧 brandondulian36@gmail.com

🌐 GitHub



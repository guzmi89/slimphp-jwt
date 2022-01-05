# API con PHP SLIM Y JWT 🚀
Framwework **PHP Slim** que hace usp de **JWT** (tuupola/slim-jwt-auth) :lock:

## Instalación 🔧
Primero clonamos el repositorio

```
git clone https://github.com/guzmi89/slimphp-jwt.git
```

Segundo instalamos las dependencias con composer

```
composer update
```

## Configuración ⚙️
Sólo debemos **renombrar el archivo .env.example a .env** y dentro ajustar el valor de la variables.

## Uso ⌨️
La raíz del API es la carpeta public, en index.php ya disponemos del endpoint /films como endpoint de ejemplo haciendo una consulta a la base de datos mysql. :closed_book:

## Construido con 🛠️
* [SlimPhp](https://github.com/slimphp/Slim) - El framework usado
* [Middleware JWT](https://github.com/tuupola/slim-jwt-auth) - PSR-7 and PSR-15 JWT Authentication Middleware
* [Middleware CORS](https://github.com/tuupola/cors-middleware) - PSR-7 and PSR-15 CORS Middleware
* [phpdotenv](https://github.com/vlucas/phpdotenv) - Librería para el manejo de variables globales de PHP
<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"');

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

//Añado el archivo de funciones
require __DIR__ . '/../functions/functions.php';

//Cargo las variables de entorno desde el .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

$app = AppFactory::create();

//$app->setBasePath('/apimarvel/public');

//Sin esto inferior por alguna razón da error de CORS pese a tenerlas arriba las peticiones con JWT dan fallo de CORS
$app->add(new Tuupola\Middleware\CorsMiddleware([
    "origin" => ["*"],
    "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
    "headers.allow" => ["Authorization", "If-Match", "If-Unmodified-Since"],
    "headers.expose" => ["Etag"],
    "credentials" => true,
    "cache" => 86400
]));

//Parte para la autenticación por JWT
$app->add(new Tuupola\Middleware\JwtAuthentication([
    'algorithm' => 'HS256',
    "secret" => $_ENV['SECRET_JWT'],
    "ignore" => ["/endpointaignorar", "/endpointaignorar2"],

    "error" => function ($response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));


//Rutas
$app->get('/', function (Request $request, Response $response, $args) {
    $token = $request->getAttribute("token");
    if (in_array("read", $token["scope"])) {
        $response->getBody()->write("Hello world!");
    } else {
        /* No scope so respond with 401 Unauthorized */
        $response->getBody()->write("NO AUTORIZADO!");
    }
    return $response;
});

$app->get('/films', function (Request $request, Response $response, $args) {

    $token = $request->getAttribute("token");
    if (in_array("read", $token["scope"])) {
        
        $sql = "SELECT * FROM films";
		$resultado = getConnection($sql);
		foreach($resultado as $row){

			$fila = [
				"id"=>$row['id'],
				"title"=>$row['title'],
				"year"=>$row['year'],
                "description"=>$row['description'],
				"image"=>$row['image']
			];
			$myArray[] = $fila;
		}
		$datos = json_encode($myArray);
        $response->getBody()->write($datos);
    } else {
        /* No scope so respond with 401 Unauthorized */
        $response->getBody()->write("NO AUTORIZADO!");
    }
    return $response;
});

$app->get('/film/{id}', function (Request $request, Response $response, $args) {
	$sql = "SELECT * FROM films WHERE id=$args[id]";
	$resultado = getConnection($sql);
	foreach($resultado as $row){

		$fila = [
			"id"=>$row['id'],
			"title"=>$row['title'],
			"year"=>$row['year'],
            "description"=>$row['description'],
            "image"=>$row['image']
		];
		$myArray[] = $fila;
	}
	$datos = json_encode($myArray);
	$response->getBody()->write($datos);
    return $response;
});

$app->run();

?>
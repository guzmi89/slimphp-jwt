<?php

require __DIR__ . '/../vendor/autoload.php';

//Cargo las variables de entorno desde el .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__. '/../');
$dotenv->load();

class Db
{
  private $link;
  private $engine;
  private $host;
  private $name;
  private $user;
  private $pass;
  private $charset;
  private $options;
  
  /**
   * Constructor para nuestra clase
   */
  public function __construct()
  {
    $this->engine  = 'mysql';
    $this->name    = $_ENV['NAME_DB'];
    $this->user    = $_ENV['USER_DB'];
    $this->pass    = $_ENV['PASSWORD_DB'];
    $this->charset = 'utf8';
    $this->host    = $_ENV['HOST_DB'];
    
    $this->options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
    return $this;    
  }

  /**
   * Método para abrir una conexión a la base de datos
   *
   * @return mixed
   */
  private function connect() 
  {
    try {
      $this->link = new PDO($this->engine.':host='.$this->host.';dbname='.$this->name.';charset='.$this->charset, $this->user, $this->pass, $this->options);
      return $this->link;
    } catch (PDOException $e) {
      //die(sprintf('No  hay conexión a la base de datos, hubo un error: %s', $e->getMessage()));
      die(sprintf('No hay conexión a la base de datos, hubo un error.'));
    }
  }

  /**
   * Método para hacer un query a la base de datos
   *
   * @param string $sql
   * @param array $params
   * @return void
   */
  public static function query($sql, $params = [])
  {
    $db = new self();
    $link = $db->connect(); // nuestra conexión a la db
    $link->beginTransaction(); // por cualquier error, checkpoint
    $query = $link->prepare($sql);

    // Manejando errores en el query o la petición
    // SELECT * FROM usuarios WHERE id=:cualquier AND name = :name;
    if(!$query->execute($params)) {

      $link->rollBack();
      $error = $query->errorInfo();
      // index 0 es el tipo de error
      // index 1 es el código de error
      // index 2 es el mensaje de error al usuario
      throw new Exception($error[2]);
    }

    // SELECT | INSERT | UPDATE | DELETE | ALTER TABLE
    // Manejando el tipo de query
    // SELECT * FROM usuarios;
    if(strpos($sql, 'SELECT') !== false) {
      
      return $query->rowCount() > 0 ? $query->fetchAll() : false; // no hay resultados

    } elseif(strpos($sql, 'INSERT INTO ida_vuelta') !== false) { //TODO: para que al hacer insert no envíe el lastid porque no  lo tiene ni hace falta en esta tabla

      $link->commit();
      return true;

    }elseif(strpos($sql, 'INSERT') !== false) {

      $link->commit();
      return true;

    } 
    
    elseif(strpos($sql, 'UPDATE') !== false) {

      $link->commit();
      return true;

    } elseif(strpos($sql, 'DELETE') !== false) {

      if($query->rowCount() > 0) {
        $link->commit();
        return true;
      }
      
      $link->rollBack();
      return false; // Nada ha sido borrado

    } else {

      // ALTER TABLE | DROP TABLE 
      $link->commit();
      return true;
      
    }
  }
  
  // Método destructor
  public function __destruct(){
            
  }
}
?>
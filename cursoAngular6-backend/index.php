<?php
require_once 'vendor/autoload.php';

$app = new \Slim\Slim();

$db = new mysqli("localhost","root","root","curso_angular6");

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if($method == "OPTIONS") {
    die();
}

// TEST
$app->get("/pruebas",function() use($app, $db){
  echo "Hola Mundo desde SLIM PHP <br>";
  //var_dump($db);
  if ($db->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
  echo "Connected successfully";
});

// LISTAR TODOS LOS PRODUCTOS
$app->get('/productos', function() use($db, $app){
  $sql = 'SELECT * FROM productos ORDER BY id DESC;';
  $query = $db->query($sql);

  //var_dump($query->fetch_all());
  $productos = array();
  while ($producto = $query->fetch_assoc()){
    $productos[] = $producto;
  }

  $result = array (
    'status' => 'success',
    'code' => 200,
    'data' => $productos
  );

  echo json_encode($result);

});

// DEVOLVER UN SOLO PRODUCTO
$app->get('/producto/:id', function($id) use($app, $db){
  $sql = 'SELECT * FROM productos WHERE id = '.$id.';';
  $query = $db->query($sql);

  $result = array (
    'status' => 'error',
    'code' => 404,
    'message' => 'Producto no encontrado'
  );

  if($query->num_rows == 1){
    $producto = $query->fetch_assoc();
    $result = array (
      'status' => 'success',
      'code' => 200,
      'message' => 'Producto encontrado',
      'data' => $producto
    );
  }
  echo json_encode($result);
});

// ELIMINAR UN PRODUCTO
$app->get('/delete-producto/:id', function($id) use($app, $db){
  $sql = 'DELETE FROM productos WHERE id = '.$id.';';
  $query = $db->query($sql);



  if(mysqli_affected_rows($db)>0){
    $result = array (
      'status' => 'success',
      'code' => 200,
      'message' => 'Producto eliminado correctamente',
      'rows' => mysqli_affected_rows($db). ' productos eliminados'
    );
  }else{
    $result = array (
      'status' => 'error',
      'code' => 404,
      'message' => 'Producto no eliminado'
    );
  }

  echo json_encode($result);

});

// ACTUALIZAR UN PRODUCTO
$app->post('/update-producto/:id', function($id) use($app, $db){
  $json = $app->request->post('json');
  $data = json_decode($json, true);

  $sql = "UPDATE productos SET ".
          "nombre = '{$data["nombre"]}',".
          "descripcion = '{$data["descripcion"]}',";

  if(isset($data['imagen'])){
      $sql .= "imagen = '{$data["imagen"]}',";
  }

  $sql .= "precio = '{$data["precio"]}' WHERE id = ".$id.";";

  $query = $db->query($sql);

  //var_dump($sql);

  if($query){
    $result = array (
      'status' => 'success',
      'code' => 200,
      'message' => 'Producto se ha actualizado'
    );
  } else {
    $result = array (
      'status' => 'error',
      'code' => 404,
      'message' => 'Producto no se ha actualizado'
    );
  }

  echo json_encode($result);

});

// SUBIR UNA IMAGEN A UN Producto
$app->post('/upload-file', function() use($app, $db){
  $result = array (
    'status' => 'error',
    'code' => 404,
    'message' => 'El archivo no ha podido subirse'
  );

  if(isset($_FILES['uploads'])){
    $piramideUploader = new PiramideUploader();

    //'Nombre del fichero (prefijo)', 'name del fichero', 'directorio donde se guarda', 'ficheros permitidos'
    $upload = $piramideUploader->upload('image','uploads','uploads',array('image/jpeg','image/png', 'image/gif'));
    $file = $piramideUploader->getInfoFile();
    $file_name = $file['complete_name'];

    //var_dump($file);
    if(isset($upload) && $upload['uploaded'] == false){
      $result = array (
        'status' => 'error',
        'code' => 404,
        'message' => 'El archivo no se ha subido correctamente'
      );
    } else {
      $result = array (
        'status' => 'succes',
        'code' => 200,
        'message' => 'El archivo se ha subido correctamente',
        'data' => $file,
        'filename' => $file_name
      );
    }
  }

echo json_encode($result);

});

// GUARDAR PRODUCTOS | SAVE PRODUCTS
$app->post('/productos', function() use($app, $db){
  $json = $app->request->post('json');
  $data = json_decode($json, true);

// CHECKING
  if(!isset($data['nombre'])){
    $data['nombre'] = null;
  }

  if(!isset($data['descripcion'])){
    $data['descripcion'] = null;
  }

  if(!isset($data['precio'])){
    $data['precio'] = null;
  }

  if(!isset($data['imagen'])){
    $data['imagen'] = null;
  }

//Insert to BBDD
  $query = "INSERT INTO productos VALUES (NULL,".
            "'{$data['nombre']}',".
            "'{$data['descripcion']}',".
            "'{$data['precio']}',".
            "'{$data['imagen']}'".
            ");";

  $insert = $db->query($query);

//Missatge resultant del insert | Result Message from the insert done
  $result = array (
    'status' => 'error',
    'code' => 404,
    'message' => 'Producto no se ha creado correctamente'
  );

  if($insert){
    $result = array (
      'status' => 'success',
      'code' => 200,
      'message' => 'Producto creado correctamente'
    );
  }

  echo json_encode($result);

// CHECKINGS
  //var_dump($query);
  //var_dump($json);
  //var_dump($data);
});

$app->run();
 ?>

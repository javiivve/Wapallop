<?php
session_start();
require_once 'modelos/ConnexionDB.php';
require_once 'modelos/fotos.php';
require_once 'modelos/fotosDAO.php';
require_once 'modelos/Anuncios.php';
require_once 'modelos/AnunciosDAO.php';
require_once 'modelos/config.php';

$connexionDB = new ConnexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
$conn = $connexionDB->getConnexion();
// Si no existe una variable de sesión sacar al usuario
if (!isset($_SESSION['email'])) {
  header("location: index.php");
  $_SESSION['error'] = "No puedes crear anuncios si no inicias sesión";
  die();
}

$AnunciosDAO = new AnunciosDAO($conn);
$idanun = htmlspecialchars($_GET['id']);
$anuncio = $AnunciosDAO->getById($idanun);



$error = '';
$foto = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Conectamos con la BD

  
  // Limpiamos los datos
  $nombre = htmlspecialchars($_POST['nombre']);
  $descripcion = htmlspecialchars($_POST['descripcion']);
  $precio = htmlspecialchars($_POST['precio']);
  $array_fotos = array();
  $array_fotosTMP = array();
  $array_fotosINS = array();

  // Validamos los datos
  if (empty($nombre) || empty($descripcion) || empty($precio)) {
      $error = "Es obligatorio rellenar todos los campos";
  }else{
    $anuncio->setNombre($nombre);
    $anuncio->setDescripcion($descripcion);
    $anuncio->setPrecio($precio);
    $anuncio->setId($idanun);

        if($AnunciosDAO->update($anuncio)) {
            header('location: index.php');
            die();
          }
}

}



?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="estilos/estilosCrear.css">
  <title>Modificar anuncio</title>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="//cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
</head>
<body>


  <div class="registro-container">
    <button id="back-button" onclick="goBack()">Volver</button>

    <h1>Modificar anuncio</h1> <br>
    <?= $error ?>
    <form action="modificarAnuncio.php?id=<?= $idanun ?>" method="post" enctype="multipart/form-data" id="nanun">
      <label>Producto: </label><input type="text" value="<?= $anuncio->getNombre() ?>" name="nombre" placeholder="Nombre del producto" required maxlength="200"><br>
          <label>Descripción: </label>
          <div id="desc">
              <p>Nueva descripción de tu producto</p>
          </div><br>
          <input type="hidden" value="<?= $anuncio->getDescripcion() ?>" id="descripcion" name="descripcion">
          <label>Precio: </label>
              <input type="number" name="precio" value="<?= $anuncio->getPrecio() ?>" placeholder="Precio para tu anuncio" required min=1 max="9999999"><br>
          <br>
          <input type="submit" value="Subir anuncio"><br>
        </form>
  </div>






  <script>
    function goBack() {
      window.history.back();
    }

        var quill = new Quill('#desc', {
            theme: 'snow'
        });

        var form = document.getElementById("hola");
        form.onsubmit = function () {
            var texto = quill.getText();
            var name = document.querySelector('input[name=descripcion]');
            name.value = texto.trim();
            return true;
       }


  </script>

</body>
</html>
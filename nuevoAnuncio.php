<?php
session_start();
require_once 'modelos/ConnexionDB.php';
require_once 'modelos/fotos.php';
require_once 'modelos/fotosDAO.php';
require_once 'modelos/Anuncios.php';
require_once 'modelos/AnunciosDAO.php';
require_once 'modelos/config.php';


// Si no existe una variable de sesión sacar al usuario
if (!isset($_SESSION['email'])) {
  header("location: index.php");
  $_SESSION['error'] = "No puedes crear anuncios si no inicias sesión";
  die();
}

$error = '';
$foto = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Conectamos con la BD
  $connexionDB = new ConnexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
  $conn = $connexionDB->getConnexion();
  
  // Limpiamos los datos
  $nombre = htmlspecialchars($_POST['nombre']);
  $descripcion = htmlspecialchars($_POST['descripcion']);
  $precio = htmlspecialchars($_POST['precio']);
  $array_fotos = array();
  $array_fotosTMP = array();
  $array_fotosINS = array();

  // Validamos los datos
  if (empty($nombre) || empty($descripcion) || empty($precio)) {
      $error = "Rellena los campos vacíos";
  }

  if ($_FILES['fotos']['error'][0] == UPLOAD_ERR_NO_FILE) {
      $error = "El anuncio debe tener minimo una foto";
  } elseif (count($_FILES['fotos']['name']) > 6) {
      $error = "El anuncio no puede contener más de 6 fotos";
  } else {
      $num_files = count($_FILES['fotos']['name']);

      for ($i = 0; $i < $num_files; $i++) {
          $array_fotos[] = $_FILES['fotos']['name'][$i];
          $array_fotosTMP[] = $_FILES['fotos']['tmp_name'][$i];
      }
  }

  foreach ($array_fotos as $i => $foto) {
      // Comprobamos que la extensión de los archivos introducidos son válidas
      $extension = pathinfo($foto, PATHINFO_EXTENSION);
      if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'webp' && $extension != 'png') {
          $error = "Formato de imagen no admitido, debe ser .jpg, .jpeg, .png o .webp";
      } else {
          // Copiamos la foto al disco
          // Calculamos un hash para el nombre del archivo
          $foto = uniqid(true) . '.' . $extension;

          // Si existe un archivo con ese nombre volvemos a calcular el hash
          while (file_exists("fotosAnuncios/$foto")) {
              $foto = uniqid(true) . '.' . $extension;
          }

          foreach ($array_fotosTMP as $j => $fotoTMP) {
              if ($i == $j && $error == '') {
                  if (!move_uploaded_file($fotoTMP, "fotosAnuncios/$foto")) {
                      die("Error al copiar la foto a la carpeta de fotosAnuncios");
                  }
              }
          }

          $array_fotosINS[] = $foto;
      }
  }

  if ($error == '') {
      $AnunciosDAO = new AnunciosDAO($conn);
      $anuncio = new Anuncio();
      $anuncio->setId_usuario($_SESSION['id']);
      $anuncio->setNombre($nombre);
      $anuncio->setDescripcion($descripcion);
      $anuncio->setPrecio($precio);
      if ($idA = $AnunciosDAO->insert($anuncio)) {
          $fotosDAO = new fotosDAO($conn);
          $fotosDAO->insert($idA, $array_fotosINS);
          header('location: index.php');
          die();
      } else {
          $error = "No se ha podido insertar el anuncio";
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
  <title>Subir Anuncio</title>
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="//cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
</head>
<body>


  <div class="registro-container">
    <button id="back-button" onclick="goBack()">Volver</button>

    <h1>Subir anuncio</h1> <br>
    <?= $error ?>
      <form action="nuevoAnuncio.php" method="post" enctype="multipart/form-data" id="nanun">
      <label>Fotos: </label><input type="file" name="fotos[]" accept="image/jpeg, image/webp, image/png" multiple required><br>    
      <label>Producto: </label><input type="text" name="nombre" placeholder="Nombre del producto" required maxlength="200"><br>
          <label>Descripción: </label>
          <div id="desc">
              <p>Descripción de tu producto</p>
          </div><br>
          <input type="hidden" id="descripcion" name="descripcion">
          <label>Precio: </label>
              <input type="number" name="precio" placeholder="Precio para tu anuncio" required min=1 max="9999999"><br>
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

        var form = document.getElementById("nanun");
        form.onsubmit = function () {
            var texto = quill.getText();
            var name = document.querySelector('input[name=descripcion]');
            name.value = texto.trim();
            return true;
       }


  </script>

</body>
</html>

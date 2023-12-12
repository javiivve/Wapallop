<?php 

require_once 'modelos/ConnexionDB.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/UsuariosDAO.php';
require_once 'modelos/config.php';


function generarNombreArchivo(string $nombreOriginal):string {
  $nuevoNombre = md5(time()+rand());
  $partes = explode('.',$nombreOriginal);
  $extension = $partes[count($partes)-1];
  return $nuevoNombre.'.'.$extension;
}

function obExtension(string $foto):string {
  $partes = explode('.',$foto);
  return $partes[count($partes)-1];
}

$error='';
$foto='';

if($_SERVER['REQUEST_METHOD']=='POST'){

    //Limpiamos los datos
    $foto = $_FILES['foto']['name'];
    $nombre = htmlentities($_POST['nombre']);
    $email = htmlentities($_POST['email']);
    $password = htmlentities($_POST['password']);
    $telefono = htmlentities($_POST['telefono']);
    $poblacion = htmlentities($_POST['poblacion']);

    //Validación 

    //Conectamos con la BD
    $connexionDB = new ConnexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
    $conn = $connexionDB->getConnexion();

    //Compruebo que no haya un usuario registrado con el mismo email
    $usuariosDAO = new UsuariosDAO($conn);
    if($usuariosDAO->getByEmail($email) != null){
        $error = "Ya hay un usuario con ese email";
    }
    else{

               //Copiamos la foto al disco
                if(obExtension($foto) != "jpg" && 
                    obExtension($foto) != "png" &&
                    obExtension($foto) != "webp")
                {
                    $error="la foto no tiene el formato admitido, debe ser jpg, png o webp";
                }
                else{
                    //Calculamos un hash para el nombre del archivo
                    $foto = generarNombreArchivo($_FILES['foto']['name']);

                    //Si existe un archivo con ese nombre volvemos a calcular el hash
                    while(file_exists("fotosUsuarios/$foto")){
                        $foto = generarNombreArchivo($_FILES['foto']['name']);
                    }
                    
                    if(!move_uploaded_file($_FILES['foto']['tmp_name'], "fotosUsuarios/$foto")){
                        die("Error al copiar la foto a la carpeta fotosUsuarios");
                    }
                }
               

        if($error == '')    //Si no hay error
        {
            //Insertamos en la BD
            
          
            $usuario = new Usuario();
            $usuario->setFoto($foto);
            $usuario->setNombre($nombre);  
            $usuario->setEmail($email);
            //encriptamos el password
            $passwordCifrado = password_hash($password,PASSWORD_DEFAULT);
            $usuario->setPassword($passwordCifrado);
            $usuario->setTelefono($telefono);
            $usuario->setPoblacion($poblacion);
            
            $usuario->setSid(sha1(rand()+time()));

            if($usuariosDAO->insert($usuario)){
                header("location: index.php");
                die();
            }else{
                $error = "No se ha podido insertar el usuario";
            }
        }
    }
    
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro</title>
  <link rel="stylesheet" href="estilos/estilosCrear.css">
</head>
<body>


  <div class="registro-container">
    <button id="back-button" onclick="goBack()">Volver</button>

    <h1>Registro</h1> <br>
    <?= $error ?>
    <form action="registro.php" method="post" enctype="multipart/form-data">
      <label for="foto">Foto de Perfil:</label>
      <input type="file" id="foto" name="foto" accept="image/*">

      <label for="nombre">Nombre:</label>
      <input type="text" id="nombre" name="nombre" required maxlength="15">

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required maxlength="40">

      <label for="contrasena">Contraseña:</label>
      <input type="password" id="password" name="password" required minlength="4" maxlength="20">

      <label for="telefono">Teléfono:</label>
      <input type="number" id="telefono" name="telefono" min="000000001" max="999999999">

      <label for="poblacion">Población:</label>
      <input type="text" id="poblacion" name="poblacion" maxlength="20"> <br>

      <button type="submit">Registrar</button>
    </form> 
  </div>






  <script>
    function goBack() {
      window.history.back();
    }
  </script>

</body>
</html>

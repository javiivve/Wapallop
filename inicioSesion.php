<?php 
session_start();
require_once 'modelos/ConnexionDB.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/UsuariosDAO.php';
require_once 'modelos/config.php';

//Creamos la conexión utilizando la clase que hemos creado
if($_SERVER['REQUEST_METHOD']=='POST'){

$connexionDB = new ConnexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
$conn = $connexionDB->getConnexion();

//limpiamos los datos que vienen del usuario
$email = htmlspecialchars($_POST['email']);
$password = htmlspecialchars($_POST['password']);

//Validamos el usuario
$usuariosDAO = new UsuariosDAO($conn);
if($usuario = $usuariosDAO->getByEmail($email)){
    if(password_verify($password, $usuario->getPassword()))
    {
        //email y password correctos. Inciamos sesión
        $_SESSION['email'] = $usuario->getEmail();
        $_SESSION['foto'] = $usuario->getFoto();
        $_SESSION['id'] = $usuario->getId();
        
        //Creamos la cookie para que nos recuerde 1 semana
        setcookie('sid',$usuario->getSid(),time()+24*60*60,'/');
        //Redirigimos a index.php
        header('location: index.php');
        die();
    }
}

}
function guardarMensaje($mensaje){
  $_SESSION['error']=$mensaje;
}

//email o password incorrectos, redirigir a index.php
guardarMensaje("Email o password incorrectos");
//header('location: index.php');

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="estilos/estilosCrear.css">
</head>
<body>

  <div class="login-container">
    <h2>Iniciar Sesión</h2>
    <button id="back-button" onclick="goBack()">Volver</button>

    <form action="inicioSesion.php" method="post">
      <label for="usuario">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="contrasena">Contraseña:</label>
      <input type="password" id="password" name="password" required maxlength="20">

      <button type="submit">Iniciar Sesión</button>
    </form>
    <br>
    <p>¿No tienes una cuenta? <a href="registro.php">Registrarse</a></p>
  </div>
  <script>
    function goBack() {
      window.history.back();
    }
  </script>

</body>
</html>

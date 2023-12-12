<?php 
session_start();
require_once 'modelos/ConnexionDB.php';
require_once 'modelos/Usuario.php';
require_once 'modelos/UsuariosDAO.php';
require_once 'modelos/Anuncios.php';
require_once 'modelos/AnunciosDAO.php';
require_once 'modelos/config.php';
require_once 'modelos/fotosDAO.php';
require_once 'modelos/fotos.php';

//Creamos la conexión utilizando la clase que hemos creado
$connexionDB = new ConnexionDB('root','','localhost','Wapallop');
$conn = $connexionDB->getConnexion();

$AnunciosDAO= new AnunciosDAO($conn);
$anuncios=$AnunciosDAO->getAll();

$fotosDAO= new fotosDAO($conn);



if( !isset($_SESSION['email']) && isset($_COOKIE['sid'])){
    //Nos conectamos para obtener el id y la foto del usuario
    $UsuariosDAO = new UsuariosDAO($conn);
    //$usuario = $usuariosDAO->getByEmail($_COOKIE['email']);
    if($usuario = $UsuariosDAO->getBySid($_COOKIE['sid'])){
        //Inicio sesión
        $_SESSION['email']=$usuario->getEmail();
        $_SESSION['id']=$usuario->getId();
        $_SESSION['foto']=$usuario->getFoto();
    }
    
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    $titu = htmlspecialchars($_POST['buscador']);

    $arrayfiltrado = Array();
$arrayfiltrado = $AnunciosDAO->filtrarAnuncio($titu);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wapallop</title>
    <link rel="stylesheet" href="estilos/styles.css">
    <link rel="shortcut icon" href="images/logo.ico" />
</head>
<body>

<header>
    
    <h1 id="titu">WAPALL<img id="logo" src="images/logo.png" alt="">P</h1>
    <nav>
       <div id="padre">   
            <div id="user">
                <?php if(isset($_SESSION['email'])): ?>
                    <img src="fotosUsuarios/<?= $_SESSION['foto']?>" class="fotoUsuario">
                    <span class="emailUsuario"><?= $_SESSION['email'] ?></span>  
                <?php endif; ?>
            </div>
       
       <div id="menu">
                <ul>
                    <li><a href="index.php">Anuncios</a></li>
                    <li><a href="misAnuncios.php">Mis anuncios</a></li>

                    <?php if(isset($_SESSION['email'])): ?>
                    
                    <li><a href="logout.php">Cerrar sesión</a></li>
                    <?php else: ?>
                        <li><a href="inicioSesion.php">Inicio Sesión/Registro</a></li>
                    <?php endif; ?>
                </ul>
        </div>

        <div id="buscador">
            <form action="index.php" method="POST">     
             <input type="search" name="buscador" class="search-input">
                <input type="submit" name="Buscar" class="search-button">
            </form>
        </div>
            
        </div>
    </nav>
    
    
</header>

    <div id="productos">   
        <?php if (empty($arrayfiltrado)):?>         
            <?php foreach ($anuncios as $anuncio): ?>
                <?php $mainfoto = $fotosDAO->selectMainById($anuncio->getId());?>

                <a href="infoAnuncio.php?id=<?=$anuncio->getId()?>">
                    <article class="producto">
                        <img id="fotAnun" src="fotosAnuncios/<?= $mainfoto ?>">
                        <p class="precio"><?= $anuncio->getPrecio() ?>€</p>
                        <h2><?= $anuncio->getNombre() ?></h2>
                    </article>
                </a>
            <?php  endforeach; ?>

            <?php else: ?>
            <?php foreach ($arrayfiltrado as $anuncio): ?>
                <?php $mainfoto = $fotosDAO->selectMainById($anuncio->getId());?>

                <a href="infoAnuncio.php?id=<?=$anuncio->getId()?>">
                    <article class="producto">
                        <img id="fotAnun" src="fotosAnuncios/<?= $mainfoto ?>">
                        <p class="precio"><?= $anuncio->getPrecio() ?>€</p>
                        <h2><?= $anuncio->getNombre() ?></h2>
                    </article>
                </a>
            <?php  endforeach; ?>
                <?php endif; ?>
    </div>


<footer>
    <p>&copy; 2023 WAPALLOP <img id="logo" src="images/logo.png" alt="">. Todos los derechos reservados.</p>
</footer>

</body>
</html>


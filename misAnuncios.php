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
$connexionDB = new ConnexionDB(MYSQL_USER,MYSQL_PASS,MYSQL_HOST,MYSQL_DB);
$conn = $connexionDB->getConnexion();

$fotosDAO= new fotosDAO($conn);
$UsuariosDAO= new UsuariosDAO($conn);

$AnunciosDAO= new AnunciosDAO($conn);
$anuncios = array();

if (isset($_SESSION['id'])) {       
    $anuncios = $AnunciosDAO->getByIdUsuario($_SESSION['id']);
}else {
    $error='Error :/';
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
    
    <h1 id="titu">MIS ANUNCI<img id="logo" src="images/logo.png" alt="">S</h1>
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
            <form action="misAnuncios.php" method="POST">     
                <input type="search" name="buscador" class="search-input">
                <input type="submit" name="Buscar" class="search-button">
            </form>
        </div>
            
        </div>
    </nav>
    
    
</header>

<?php if(isset($_SESSION['email'])): ?>

 <?php   $usuario = $UsuariosDAO->getById('id'); ?>
<a href="nuevoAnuncio.php" class="upload-btn">✚ㅤSubir Anuncio</a>


<div id="productos"> 
<?php if (empty($arrayfiltrado)):?>                
<?php foreach ($anuncios as $anun): ?>
            <?php $mainfoto = $fotosDAO->selectMainById($anun->getId());?>

            <a href="infoAnuncio.php?id=<?=$anun->getId()?>">
                <article class="producto">
                    <img id="fotAnun" src="fotosAnuncios/<?= $mainfoto ?>">
                    <p class="precio"><?= $anun->getPrecio() ?>€</p>
                    <h2><?= $anun->getNombre() ?></h2>
                </article>
            </a>
        <?php  endforeach; ?>
        <?php else: ?>
                <?php foreach ($arrayfiltrado as $anun): ?>
            <?php $mainfoto = $fotosDAO->selectMainById($anun->getId());?>

            <a href="infoAnuncio.php?id=<?=$anun->getId()?>">
                <article class="producto">
                    <img id="fotAnun" src="fotosAnuncios/<?= $mainfoto ?>">
                    <p class="precio"><?= $anun->getPrecio() ?>€</p>
                    <h2><?= $anun->getNombre() ?></h2>
                </article>
            </a>
        <?php  endforeach; ?>
        <?php endif; ?>
</div>

<?php else: ?>

<h1>PARA VER TUS ANUNCIOS PRIMERO DEBES <a href="inicioSesion.php">INICIAR SESIÓN</a></h1>

<?php endif; ?>

<footer>
    <p>&copy; 2023 WAPALLOP <img id="logo" src="images/logo.png" alt="">. Todos los derechos reservados.</p>
</footer>



</body>
</html>


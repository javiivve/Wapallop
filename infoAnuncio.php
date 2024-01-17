<?php

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
$AnunciosDAO= new AnunciosDAO($conn);
$UsuariosDAO= new UsuariosDAO($conn);

$idanun = htmlspecialchars($_GET['id']);
$anuncio=$AnunciosDAO->getById($idanun);
$anuncios = $AnunciosDAO->getByIdUsuario($idanun);
$usuanuncio = $UsuariosDAO->getById($anuncio->getId_usuario());

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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wapallop</title>
    <link rel="stylesheet" href="estilos/styles.css">
    <link rel="stylesheet" href="estilos/estilosInfo.css">
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
        <!--    <form action="index.php" method="POST">     
                <input type="search" name="buscador" class="search-input">
                <input type="submit" name="Buscar" value="Buscar" class="search-button">
         </form>-->   
        </div> 
            
        </div>
    </nav>
    
    
</header>

<?php if(isset($_SESSION['email'])): ?>


<?php if($usuanuncio->getId() == $_SESSION['id']): ?>
    
    <div id="botones">
<a href="borrarAnuncio.php" class="borrar-btn">Borrar Anuncio</a>

<a href="modificarAnuncio.php?id= <?= $anuncio->getId() ?> " class="edit-btn">Editar Anuncio</a>

</div>
<section>
    <?php $mainfoto = $fotosDAO->selectMainById($anuncio->getId());?>

    <div class="product">
        <div id="datosusu">
            <h2>Vendedor: <?= $usuanuncio->getNombre() ?></h2>
            <p><?= $usuanuncio->getEmail() ?></p>
            <p id="contacto">Contacto: <a href=""> <?= $usuanuncio->getTelefono() ?></a></p> 

        </div>
    


        <img id="fotAnun" src="fotosAnuncios/<?= $mainfoto ?>">
        
        <div class="product-details">
            <h2><?= $anuncio->getNombre() ?></h2>
            <p><?= $anuncio->getDescripcion() ?></p>
            <p><?= $anuncio->getFecha_creacion() ?></p>
            <p class="precio"><?= $anuncio->getPrecio() ?>€</p>
        </div>
    </div>
</section>

<footer>
    <p>&copy; 2023 WAPALLOP <img id="logo" src="images/logo.png" alt="">. Todos los derechos reservados.</p>
</footer>

</body>
</html>

<?php else: ?>

    <section>
        <?php $mainfoto = $fotosDAO->selectMainById($anuncio->getId());?>
        <div class="product">
            <div id="datosusu">
                <h2>Vendedor: <?= $usuanuncio->getNombre() ?></h2>
                <p><?= $usuanuncio->getEmail() ?></p>
                <p id="contacto">Contacto: <a href=""> <?= $usuanuncio->getTelefono() ?></a></p>            
            </div>
        
            <img id="fotAnun" src="fotosAnuncios/<?= $mainfoto ?>">
            
            <div class="product-details">
                <h2><?= $anuncio->getNombre() ?></h2>
                <p><?= $anuncio->getDescripcion() ?></p>
                <p><?= $anuncio->getFecha_creacion() ?></p>
                <p class="precio"><?= $anuncio->getPrecio() ?>€</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2023 WAPALLOP <img id="logo" src="images/logo.png" alt="">. Todos los derechos reservados.</p>
    </footer>

    </body>
    </html>

<?php endif; ?>

<?php else: ?>
    <section>
        <?php $mainfoto = $fotosDAO->selectMainById($anuncio->getId());?>
        <div class="product">
            <div id="datosusu">
                <h2>Vendedor: <?= $usuanuncio->getNombre() ?></h2>
                <p><?= $usuanuncio->getEmail() ?></p>
                <p id="contacto">Contacto: <a href=""> <?= $usuanuncio->getTelefono() ?></a></p>            
            </div>

            <img id="fotAnun" src="fotosAnuncios/<?= $mainfoto ?>">
            
            <div class="product-details">
                <h2><?= $anuncio->getNombre() ?></h2>
                <p><?= $anuncio->getDescripcion() ?></p>
                <p><?= $anuncio->getFecha_creacion() ?></p>
                <p class="precio"><?= $anuncio->getPrecio() ?>€</p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2023 WAPALLOP <img id="logo" src="images/logo.png" alt="">. Todos los derechos reservados.</p>
    </footer>

    </body>
    </html>
<?php endif; ?>

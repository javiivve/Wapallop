<?php
class AnunciosDAO {
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getById($id) : Anuncio|null {
        if(!$stmt=$this->conn->prepare("SELECT * FROM anuncios WHERE id = ?")){
            echo"Error en la SQL:".$this->conn->error;
        }
            $stmt->bind_param('i',$id);
            $stmt->execute();
            $result=$stmt->get_result();

            if($result->num_rows==1){
                $anuncio=$result->fetch_object(Anuncio::class);
                return $anuncio;
            }else{
                return null;
            }
      }

                            public function getByIdUsuario($id_usuario):array {
                                if(!$stmt=$this->conn->prepare("SELECT * FROM anuncios WHERE id_usuario = ?")){
                                    echo"Error en la SQL:".$this->conn->error;
                                }
                                    $stmt->bind_param('i',$id_usuario);
                                    $stmt->execute();
                                    $result=$stmt->get_result();

                                    $array_anunid = array();
    
                                    while($anun = $result->fetch_object(Anuncio::class)){
                                        $array_anunid[] = $anun;
                                    }
                                    return $array_anunid;
                                }
                            
  
    function insert(Anuncio $anuncio):int|bool{
        if(!$stmt = $this->conn->prepare("INSERT INTO anuncios(nombre, descripcion, precio, id_usuario) VALUES(?,?,?,?)")){
            die("Error al preparar la consulta insert: " . $this->conn->error );
        }
        $nombre = $anuncio->getNombre();
        $descripcion = $anuncio->getDescripcion();
        $precio = $anuncio->getPrecio();
        $id_usuario=$anuncio->getId_usuario();
        $stmt->bind_param('ssii', $nombre, $descripcion, $precio, $id_usuario);
        if($stmt->execute()){
            return $stmt->insert_id;
        }
        else{
            return false;
        }
    }


public function getAll():array {
    if(!$stmt = $this->conn->prepare("SELECT * FROM anuncios order by fecha_creacion desc"))
    {
        echo "Error en la SQL: " . $this->conn->error;
    }
    //Ejecutamos la SQL
    $stmt->execute();
    //Obtener el objeto mysql_result
    $result = $stmt->get_result();

    $array_anuncios = array();
    
    while($anuncio = $result->fetch_object(Anuncio::class)){
        $array_anuncios[] = $anuncio;
    }
    return $array_anuncios;
}

function delete($id):bool{

    if(!$stmt = $this->conn->prepare("DELETE FROM anuncios WHERE id = ?"))
    {
        echo "Error en la SQL: " . $this->conn->error;
    }
    //Asociar las variables a las interrogaciones(parámetros)
    $stmt->bind_param('i',$id);
    //Ejecutamos la SQL
    $stmt->execute();
    //Comprobamos si ha borrado algún registro o no
    if($stmt->affected_rows==1){
        return true;
    }
    else{
        return false;
    }
    
}

function update($anuncio){
    if(!$stmt = $this->conn->prepare("UPDATE anuncios SET nombre=?, descripcion=?, precio=? WHERE id=?")){
        die("Error al preparar la consulta update: " . $this->conn->error );
    }
    $precio = $anuncio->getPrecio();
    $nombre = $anuncio->getNombre();
    $descripcion = $anuncio->getDescripcion();
    $id = $anuncio->getId();
    $stmt->bind_param('ssii',$nombre, $descripcion, $precio, $id);
    return $stmt->execute();
}


function filtrarAnuncio($titulo):array|bool{
    if(!$stmt = $this->conn->prepare("SELECT * FROM anuncios WHERE nombre LIKE ?")){
        die ("Error al preparar la consulta insert: " . $this->conn->error);
    }
    $searchPattern = '%' . $titulo . '%';
    $stmt->bind_param('s',$searchPattern);
    $stmt->execute();
    $array_anuncio = array();
    $result = $stmt->get_result();
    while($anuncio = $result->fetch_object(Anuncio::class)){
        $array_anuncio[] = $anuncio;
    }
    if (empty($array_anuncio)) {
        return false;
    }else{
        return $array_anuncio;
    }
}


}

?>

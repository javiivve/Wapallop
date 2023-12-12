<?php

class UsuariosDAO {
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    /**
     * Obtiene un usuario de la BD en función del email
     * @return Usuario Devuelve un Objeto de la clase Usuario o null si no existe
     */
    public function getByEmail($email):Usuario|null {
        if(!$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE email = ?"))
        {
            echo "Error en la SQL: " . $this->conn->error;
        }
        //Asociar las variables a las interrogaciones(parámetros)
        $stmt->bind_param('s',$email);
        //Ejecutamos la SQL
        $stmt->execute();
        //Obtener el objeto mysql_result
        $result = $stmt->get_result();

        //Si ha encontrado algún resultado devolvemos un objeto de la clase Mensaje, sino null
        if($result->num_rows >= 1){
            $usuario = $result->fetch_object(Usuario::class);
            return $usuario;
        }
        else{
            return null;
        }
    } 


    public function getBySid($sid):Usuario|null {
        if(!$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE sid = ?"))
        {
            echo "Error en la SQL: " . $this->conn->error;
        }
        //Asociar las variables a las interrogaciones(parámetros)
        $stmt->bind_param('s',$sid);
        //Ejecutamos la SQL
        $stmt->execute();
        //Obtener el objeto mysql_result
        $result = $stmt->get_result();

        //Si ha encontrado algún resultado devolvemos un objeto de la clase Mensaje, sino null
        if($result->num_rows >= 1){
            $usuario = $result->fetch_object(Usuario::class);
            return $usuario;
        }
        else{
            return null;
        }
    } 

    public function getById($id):Usuario|null {
        if(!$stmt = $this->conn->prepare("SELECT * FROM usuarios WHERE id = ?"))
        {
            echo "Error en la SQL: " . $this->conn->error;
        }
        //Asociar las variables a las interrogaciones(parámetros)
        $stmt->bind_param('i',$id);
        //Ejecutamos la SQL
        $stmt->execute();
        //Obtener el objeto mysql_result
        $result = $stmt->get_result();

        //Si ha encontrado algún resultado devolvemos un objeto de la clase Mensaje, sino null
        if($result->num_rows >= 1){
            $usuario = $result->fetch_object(Usuario::class);
            return $usuario;
        }
        else{
            return null;
        }
    } 

    /**
     * Obtiene todos los usuarios de la tabla mensajes
     */
    public function getAll():array {
        if(!$stmt = $this->conn->prepare("SELECT * FROM usuarios"))
        {
            echo "Error en la SQL: " . $this->conn->error;
        }
        //Ejecutamos la SQL
        $stmt->execute();
        //Obtener el objeto mysql_result
        $result = $stmt->get_result();

        $array_usuarios = array();
        
        while($usuario = $result->fetch_object(Usuario::class)){
            $array_usuarios[] = $usuario;
        }
        return $array_usuarios;
    }

    function insert(Usuario $usuario):bool{
        if(!$stmt = $this->conn->prepare("INSERT INTO usuarios(foto, nombre, email, password, telefono, poblacion, sid) VALUES(?,?,?,?,?,?,?)")){
            die("Error al preparar la consulta insert: " . $this->conn->error );
        }
        $foto = $usuario->getFoto();
        $nombre = $usuario->getNombre();
        $email = $usuario->getEmail();
        $password = $usuario->getPassword();
        $telefono = $usuario->getTelefono();
        $poblacion = $usuario->getPoblacion();
        $sid = $usuario->getSid();
        $stmt->bind_param('ssssiss',$foto, $nombre, $email, $password, $telefono, $poblacion, $sid);
        if($stmt->execute()){
            return true;
        }
        else{
            return false;
        }
    }
}
?>

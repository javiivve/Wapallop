<?php

class fotosDAO {
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }
  
    function insert(int $id, array $fotos):bool{
        if(!$stmt = $this->conn->prepare("INSERT INTO fotos(id, foto) VALUES(?, ?)")){
            die("Error al preparar la consulta insert: " . $this->conn->error );
        }
       
        foreach($fotos as $foto){
            $stmt->bind_param('is',$id, $foto);
            $stmt->execute();
        }
        
        if($this->conn->affected_rows == count($fotos)){
            return true;
        }
        else{
            return false;
        }
    }

    
    public function selectMainById(int $id):string|null {
        if (!$stmt = $this->conn->prepare("SELECT foto FROM fotos WHERE id = ? ORDER BY foto LIMIT 1")) {
            die("Error al preparar la consulta insert: " . $this->conn->error );}
    
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result->num_rows == 1){
                return $result->fetch_column();
            } else {
                return null;
            }
        }

        public function selectAllById(int $id):string|null {
            if (!$stmt = $this->conn->prepare("SELECT * FROM fotos WHERE id = ?")) {
                die("Error al preparar la consulta insert: " . $this->conn->error );}
        
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
        
                if ($result->num_rows == 1){
                    return $result->fetch_column();
                } else {
                    return null;
                }
            }

        


}
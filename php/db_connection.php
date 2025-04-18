<?php
class Connection{
    //varibili per la connessione connessione
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "tecWeb";
    private $conn;

    //connessione al database
    public function __construct(){

        $this->conn = new mysqli($this->servername,$this->username, $this->password,$this->dbname);
            if ($this->conn->connect_errno) 
            {
                throw new Exception("ServerError");
            }
            else
            {
                //echo "Connessione riuscita";
            }
    }
    
    public function getConnection(){
        return $this->conn;
    }

    public function closeConnection(){
        if($this->conn){
            $this->conn->close();
        }
    }

    public function isConnected(){
        if($this->conn->connect_errno){
            return false;
        }
        else{
            return true;
        }
    }
}
?>
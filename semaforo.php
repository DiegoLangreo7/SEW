<?php
class Record {
    public $server;
    public $user;
    public $pass;
    public $dbname;

    public function __construct() {
        $this->server = "localhost"; 
        $this->user = "DBUSER2024";  
        $this->pass = "DBPSWD2024"; 
        $this->dbname = "records";   

        $this->checkAndImportSQL(); 
    }

    public function createTableRegistro($filePath) {
        $db = new mysqli($this->server, $this->user, $this->pass);
    
        if ($db->connect_error) {
            die("Error: Conexión fallida.");
        }
    
        if (!$db->select_db($this->dbname)) {
            $db->query("CREATE DATABASE IF NOT EXISTS {$this->dbname}");
            $db->select_db($this->dbname);
        }
    
        if (file_exists($filePath)) {
            $sql = file_get_contents($filePath);
            if ($db->multi_query($sql)) {
                do {
                    if ($result = $db->store_result()) {
                        $result->free();
                    }
                } while ($db->next_result());
            } else {
                echo "Error al ejecutar el archivo SQL: " . $db->error;
            }
        } else {
            echo "El archivo SQL no existe.";
        }
    
        $db->close();
    }
    

    private function checkAndImportSQL() {
        $db = new mysqli($this->server, $this->user, $this->pass, $this->dbname);

        $tableExists = $db->query("SHOW TABLES LIKE 'registro'");
        if ($tableExists->num_rows === 0) {
            $this->createTableRegistro('multimedia\sources\registro.sql');
        }
        $db->close();
    }

    public function insertRecord($nombre, $apellidos, $nivel, $tiempo) {
        $db = new mysqli($this->server, $this->user, $this->pass, $this->dbname);

        if ($db->connect_error) {
            die("Conexión fallida: " . $db->connect_error);
        }

        $stmt = $db->prepare("INSERT INTO registro (nombre, apellidos, nivel, tiempo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nombre, $apellidos, $nivel, $tiempo);

        if ($stmt->execute()) {
            echo "Registro exitoso"; 
        } else {
            echo "Error al guardar el record: " . $stmt->error;
        }

        $stmt->close();
        $db->close();
    }

    public function getTopRecords($nivel) {
        $db = new mysqli($this->server, $this->user, $this->pass, $this->dbname);

        if ($db->connect_error) {
            die("Conexión fallida: " . $db->connect_error);
        }

        $stmt = $db->prepare("SELECT nombre, apellidos, nivel, tiempo FROM registro WHERE nivel = ? ORDER BY tiempo ASC LIMIT 10");
        $stmt->bind_param("s", $nivel);
        $stmt->execute();
        $result = $stmt->get_result();

        $records = [];
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }

        $stmt->close();
        $db->close();

        return $records;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $nivel = $_POST['nivel'];
    $tiempo = $_POST['tiempo'];

    $record = new Record();
    $record->insertRecord($nombre, $apellidos, $nivel, $tiempo);
    
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['nivel'])) {
    $nivel = (string)$_GET['nivel'];
    $record = new Record();
    $topRecords = $record->getTopRecords($nivel);

    header('Content-Type: application/json');
    echo json_encode([
        "status" => "success",
        "top_results" => $topRecords
    ]);
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Diego García González">
    <meta name="description" content="Juego de tiempo de reacción de F1">
    <meta name="keywords" content="html, css, javascript">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="multimedia/imagenes/favicon.ico" type="image/x-icon">
    <title>Semáforo F1 - Juego de Reacción</title>    
    <link rel="stylesheet" type="text/css" href="css/estilo.css" />
    <link rel="stylesheet" type="text/css" href="css/layout.css" />
    <link rel="stylesheet" href="css/semaforo_grid.css">
    <script src="js/semaforo.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>

</head>
<body>
    <header>
        <h1><a href="index.html">F1 Desktop</a></h1>
        <nav> 
            <a href="index.html">Inicio</a>
            <a href="piloto.html">Pilotos</a>
            <a href="noticias.html">Noticias</a>
            <a href="calendario.html">Calendario</a>
            <a href="metereologia.html">Meteorología</a>
            <a href="circuito.html">Circuitos</a>
            <a href="viajes.php">Viajes</a>
            <a href="juegos.html" class="active">Juegos</a>
        </nav>
    </header>
    <p>
        Estás en: <a href="index.html">Inicio</a> >>> Juegos >>> Juego de tiempo de reaccion
    </p>
    <h2>Juegos</h2>
    <ul>
        <li>
            <a href="memoria.html">Juego de Memoria</a>
        </li>
        <li>
            <a href="semaforo.php" class="active">Juego de tiempo de reacción</a>
        </li>
        <li>
            <a href="api.html">Resultados ultima carrera</a>
        </li>
        <li>
			<a href="php/libre.php">Ejercicio libre</a>
		</li>
    </ul>
    <main>
        <!-- Aquí es donde se generarán los elementos dinámicamente -->
    </main>
</body>
</html>

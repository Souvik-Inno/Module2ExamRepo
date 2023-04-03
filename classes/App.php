<?php

  /**
   *  Class defined to perform methods on apps in database.
   */
  class App {

    /**
   *  @var mysqli $conn
   *    Connects to mysql database.
   */
    public $conn;

    /**
     *  Function to connect to Database.
     * 
     *  @return void
     */
    public function connectDB() {
      require_once("classes/Pass.php");
      $pass = new Pass();
      $this->conn = new mysqli($pass->getServerName(), $pass->getusername(), 
      $pass->getPassword(), $pass->getDBName());
      if ($this->conn->connect_error) {
        die("Connection failed: " . $this->conn->connect_error);
      }
    }

    /**
     *  Adds app in database
     *  
     *  @return void
     */
    public function addApp($name, $image, $developer, $downloadCount) {
      $this->connectDB();
      $appName = $name . '.apk';
      $query = "INSERT INTO app(name, description, image, developer, downloadCount, file)
                VALUES ($name, 'app desc', $image, $developer, $downloadCount, $appName)";
      $this->conn->query($query);
    }

    /**
     *  Gets all the apps in the database
     * 
     *  @return bool|mysqli_result
     *    returns mysqli_result when successful.
     */
    public function getApps() {
      $this->connectDB();
      $query = "SELECT * from app";
      $result = $this->conn->query($query);
      return $result;
    }

    /**
     *  Downloads the app.
     * 
     *  @param int $rowId
     *    gets the rowId of the field to be changed.
     * 
     *  @return int $count
     *    returns updated download count.
     */
    public function downloadApp($rowId) {
      $this->connectDB();
      $query = "SELECT downloadCount from app where id = $rowId";
      $result = $this->conn->query($query);
      $count = 0;
      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $count = $row["downloadCount"] + 1;
      }
      $query = "UPDATE app
      SET downloadCount = $count
      WHERE id = $rowId;";
      $this->conn->query($query);
      return $count;
    }

  }

?>


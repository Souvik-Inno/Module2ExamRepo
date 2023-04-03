<?php

  /**
   *  Class LoginData contains data for login.
   */
  class LoginData {
    /**
     *  @var string $email
     *    Contains user email.
     */
    public $email;
    /**
     *  @var string $password
     *    Contains password.
     */
    private $password;
    /**
     *  @var string $privilege
     *    Contains access as User/Admin.
     */
    public $privilege;
    /**
     *  @var mysqli $conn
     *    Connects to mysql database.
     */
    public $conn;
    /**
     *  @var string $message
     *    Contains success or fail message.
     */
    public $message;

    /**
     *  Constructor initializes variables on form submit and logs in user.
     */
    public function __construct() {
      if(isset($_POST['loginSubmit'])) {
        $this->email = $_POST['loginEmail'];
        $this->password = $_POST['loginPass'];
        $this->privilege = $_POST['privilege'];
        $this->loginUser();
      }
    }

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
     *  Function logs in user on correct form submission.
     *  Then heads to home page of basics tasks.
     * 
     *  @return void
     */
    public function loginUser() {
      $this->connectDB();
      // $this->password = md5($this->password);
      $query = "SELECT email FROM user
      WHERE email = '$this->email' AND password = '$this->password' AND privilege = '$this->privilege'";
      $result = $this->conn->query($query);
      if($result->num_rows == 0) {
        $this->message = "*Enter valid Email/Password/Privilege.";
      }
      else {
        $_SESSION['email'] = $this->email;
        $_SESSION['logged'] = TRUE;
        $_SESSION['privilege'] = $this->privilege;
        $this->message = "Login Successfull";
        header("location: feed.php");
      }
    }

    /**
     *  Destructor closes connection if open.
     */
    public function __destruct() {
      if ($this->conn) {
        $this->conn->close();
      }
    }

  }
?>
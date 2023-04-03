<?php

  require("classes/App.php");
  $newApp = new App();
  if(isset($_FILES['file'])){
    $file_name = $_FILES['file']['name'];
    $file_size = $_FILES['file']['size'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    $file_ext = strtolower(end(explode('.',$_FILES['file']['name'])));
  
    // set the upload directory and file path
    $upload_dir = "uploads/";
    $file_path = $upload_dir . $file_name;
  
    // move the uploaded file to the upload directory
    move_uploaded_file($file_tmp, $file_path);
  
    // do something with the file
    $response = "File uploaded successfully: $file_name ($file_size bytes)";
    echo $response;
  }
?>


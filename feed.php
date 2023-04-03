<?php

  // If not logged in go to home page. 
  session_start();
  if (!isset($_SESSION['logged'])) {
    header('location: index.php');
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>⚫️</text></svg>">
  <!-- Including style.css. -->
  <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="navbar-container container">
      <a class="navbar-brand" href="#">App Store</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home
              <span class="sr-only">(current)</span>
            </a>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0" action="logout.php">
          <button class="btn btn-outline-danger my-2 my-sm-0" type="submit">Log Out</button>
        </form>
      </div>
    </div>
  </nav>
  <div class="main m-3">
    <div class="main-container container">
      <div class="sidebar flex-align-mid">
        <div class="sidebar-container flex-column">
          <div class="profile-section">
            <h6>My Apps</h6>
          </div>
        </div>
      </div>
      <div class="items-container">
        <?php
          if ($_SESSION['privilege'] == 'Admin') {
          ?>
            <div class="status box">
              <div class="status-main">
                <div class="app-name">
                  <input type="text" class="status-textarea" name="app_name" id="app_name" placeholder="Enter app name">
                </div>
              </div>
              <div class="status-actions">
                <div class="status-menu">
                  <input type="file" id="image" name="image">
                </div>
                <button class="status-share" id="status-share-btn" onclick="postApp()">Post</button>
              </div>
            </div>
          <?php
          } 
          ?>
        <ul class="products" id="products">
          <?php

            require("classes/App.php");
            $apps = new App();
            $allApps = $apps->getApps();
            $iterator = 0;
            while($row = mysqli_fetch_assoc($allApps)) {
          ?>
          <li class="flex-just-mid">
            <div class="product-details bg-dark">
              <div class="product-details--content flex-column">
                <h5>
                  <?php echo $row["name"] ?>
                </h5>
                <span class="app-desc">
                  <?php echo $row["description"] ?>
                </span>
                <img src=<?php echo $row["image"] ?> class="app-img">
                <span>
                  <?php echo $row["developer"] ?>
                </span>
                <span id="<?php echo $iterator; ?>">
                  <?php echo $row["downloadCount"] ?>
                </span>
                <button class="download-btn" onclick="downloadApp(<?php echo $row['id'] ?>, <?php echo $iterator ?>)">Download</button>
              </div>
            </div>
          </li>
          <?php
              $iterator++;
            }
          ?>
        </ul>
      </div>
    </div>
  </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="js/feedquery.js"></script>

</html>
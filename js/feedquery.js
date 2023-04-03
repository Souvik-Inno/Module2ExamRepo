function postApp() {
  var name = $("#app_name").val();
  var file_data = $("#image").prop("files")[0];
  var form_data = new FormData();
  form_data.append("name", name);
  form_data.append("file", file_data);
  $.ajax({
    type: "POST",
    url: "addApp.php",
    data: form_data,
    success: function(data){
      $("#products").prepend(`
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
      `);
    }
  });
}

function downloadApp(rowId, iterator) {
  $.ajax({
    type: "POST",
    url: "downloadApp.php",
    data: {
      rowId: rowId,
    },
    success: function(data){
      console.log(data);
      $("#"+iterator).html(data);
    }
  });
}
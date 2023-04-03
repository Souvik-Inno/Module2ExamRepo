itr = 0;
function downloadApp(rowId, iterator) {
  itr = iterator;
  $.ajax({
    type: "POST",
    url: "/downloadApp",
    dataType: 'json',
    data: {
      rowId: rowId,
    },
    success: function (data) {
      console.log(data);
      $("#" + iterator).html(data.downloads);
    }
  });
}

function reviewShare(rowId, iterator) {
  itr = iterator;
  var arr = $("input[name='comment-input']").map(function () {
    return this.value;
  }).get();
  var val = arr[itr];
  $.ajax({
    type: "POST",
    url: "/review",
    dataType: 'json',
    data: {
      rowId: rowId,
      review: val,
    },
    success: function (data) {
      console.log(data);
      $(".comments-section").eq(itr).prepend(`
      <li>
        <div class="comment box">
          <div class="status-main" id="comment-main">
            <textarea id="comment-box-form" class="comment-textarea" name="post-text" disabled>${data.review}</textarea>
          </div>
        </div>
      </li>
      `);
      $("#" + iterator).html(data.downloads);
    }
  });
}

function editApp() {
  var appName = window.prompt("Enter app name:");
  var appDesc = window.prompt("Enter app description:");

  $.ajax({
    url: "/upload",
    type: "POST",
    dataType: 'json',
    data: formData,
    success: function(data) {
      console.log("success");
    }
  });
}



$(document).ready(function () {
  var fileData = $('input[type="file"]')[0].files[0];
  $('#image').change(function () {
    fileData = $(this).val();
  });
  // Upload post using ajax.
  $("#status-share-btn").click(function () {
    fileData = $('input[type="file"]')[0].files[0];
    var formData = new FormData();
    formData.append('app_name', $('#app_name').val());
    if (fileData != undefined) {
      formData.append('image', fileData);
    }
    if ($('#status-box-form').val() == "" && fileData == undefined) {
      alert("Enter valid post");
    }
    else {
      $.ajax({
        url: "/upload",
        type: "POST",
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
          // Handle the success response from the server.
          itr++;
          $('#app_name').val("");
          $('input[name="image"]').val("");
          $("#products").append(`
          <li class="flex-just-mid">
            <div class="product-details bg-dark">
              <div class="product-details--content flex-column">
                <h5>
                  ${data.name}
                </h5>
                <span class="app-desc">
                  ${data.description}
                </span>
                <img src=${data.image} class="app-img">
                <span>
                  ${data.developer}
                </span>
                <span id="${itr}">
                  ${data.downloadCount}
                </span>
                <button class="download-btn" onclick="downloadApp(${data.id}, ${itr})">Download</button>
              </div>
            </div>
          </li>
          `);
        },
        error: function (xhr, status, error) {
          // Handle the error response from the server.
          console.log('status: ' + status);
          console.log('Error: ' + error);
        }
      });
    }
  });
});


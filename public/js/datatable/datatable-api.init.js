//==================================================//
// Individual column searching (select inputs)       //
//==================================================//

$(".datatable-select-inputs").DataTable();

$(document).ready(function () {
    // Check if the .datatable-select-inputs element exists
    if ($('.datatable-select-inputs').length) {
        $('body').append(`

          <button class="btn btn-primary p-3 rounded-circle d-flex align-items-center justify-content-center customizer-btn" id='report-expor-btn-for-list'" onclick='reportExportBtnForList()'>
              <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Export">
              <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="1.5"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-export"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M11.5 21h-4.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v5m-5 6h7m-3 -3l3 3l-3 3" /></svg>
              </span>
          </button>

          <style>
.report-loading-screen {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.loader {
    width: 50px;
    aspect-ratio: 1;
    border-radius: 50%;
    border: 8px solid #0000;
    border-right-color: #ffa50097;
    position: relative;
    animation: l24 1s infinite linear;
}
.loader:before,
.loader:after {
    content: "";
    position: absolute;
    inset: -8px;
    border-radius: 50%;
    border: inherit;
    animation: inherit;
    animation-duration: 2s;
}
.loader:after {
  animation-duration: 4s;
}
@keyframes l24 {
  100% {transform: rotate(1turn)}
}


</style>

<!-- Loading -->
<div class="report-loading-screen" id="report-loading-screen">
  <div class="loader"></div>
</div> 
      `);

        $('[data-bs-toggle="tooltip"]').tooltip();
    }
});



function reportExportBtnForList() {
  var loadingScreen = document.getElementById('report-loading-screen');
  loadingScreen.style.display = 'flex';
    var id = [];
    $('tbody .id').each(function () {
        id.push($(this).val());
    });

    var data = {
        "table_name": $('h5.card-title').text(),
        "list_ids": id,
    }

    // Get CSRF token from meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
        url: '/ExportList',
        type: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json; charset=utf-8',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function (blob, status, xhr) {
            loadingScreen.style.display = 'none';
            var filename = "";
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
            }
            if (typeof window.navigator.msSaveBlob !== 'undefined') {
                window.navigator.msSaveBlob(blob, filename);
            } else {
                var URL = window.URL || window.webkitURL;
                var downloadUrl = URL.createObjectURL(blob);

                if (filename) {
                    var a = document.createElement("a");
                    if (typeof a.download === 'undefined') {
                        window.location = downloadUrl;
                    } else {
                        a.href = downloadUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                    }
                } else {
                    window.location = downloadUrl;
                }

                setTimeout(function () {
                    URL.revokeObjectURL(downloadUrl);
                }, 100);
            }
        },
        error: function (xhr, status, error) {
          loadingScreen.style.display = 'none';
            console.error('Export failed:', error);
            alert('The export functionality is not completed yet.');
        }
    });

}

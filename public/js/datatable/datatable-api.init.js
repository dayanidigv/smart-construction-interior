

//==================================================//
// Individual column searching (select inputs)       //
//==================================================//

$(".datatable-select-inputs").DataTable({
  initComplete: function () {
    this.api()
      .columns()
      .every(function () {
        var column = this;
        var select = $(
          '<select class="form-select"><option value="">Select option</option></select>'
        )
          .appendTo($(column.footer()).empty())
          .on("change", function () {
            var val = $.fn.dataTable.util.escapeRegex($(this).val());

            column.search(val ? "^" + val + "$" : "", true, false).draw();
          });
        column
          .data()
          .unique()
          .sort()
          .each(function (d, j) {
            select.append('<option value="' + d + '">' + d + "</option>");
          });
      });
  },
  responsive: true
});

$(document).ready(function() {
  // Check if the .datatable-select-inputs element exists
  if ($('.datatable-select-inputs').length) {
      // Append the button to a specific container or the body
      $('body').append(`
          <button class="btn btn-primary p-3 rounded-circle d-flex align-items-center justify-content-center customizer-btn" id='report-expor-btn-for-list'" onclick='reportExportBtnForList()'>
              <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Export">
              <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="1.5"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-export"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M11.5 21h-4.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v5m-5 6h7m-3 -3l3 3l-3 3" /></svg>
              </span>
          </button>
      `);

      // Initialize Bootstrap tooltips if necessary
      $('[data-bs-toggle="tooltip"]').tooltip();
  }
});


function reportExportBtnForList() {
  // Get the cleaned text and split it into an array
  var theadText = $('thead').text().replace(/[\t\n]+/g, ' ').trim();
  var theadArray = theadText.split(/\s+/);

  var data = {
    "table_name": $('h5.card-title').text(),
    "search_value": $('#DataTables_Table_0_filter').find('input').val(),
    "table_heads": theadArray,
  }

  console.log(data);

  // Alert message indicating the function is not completed
  alert('The export functionality is not completed yet.');
}

const DataTableManager = (function () {
  const init = function () {
    $(document).ready(function () {
      if (jQuery('#datatable').length) {
        initDatatable();
      }
    });
  };

  /**
   * get Base URL
   * @param url
   * @returns {`${string}//${string}`}
   */
  const getBaseUrl = function (url) {
    const parsedUrl = new URL(url);
    return `${parsedUrl.protocol}//${parsedUrl.hostname}`;
  };

  /**
   * @param column
   * @param rows
   * @returns {[{column, rows: *[]}]}
   */
  const getPreselectOptions = function (column, rows = []) {
    return [
      {
        column,
        rows,
      },
    ];
  };

  const getPropertyOptions = function (columns) {
    return {
      ordering: true,
      preselect: getPreselectOptions(columns.preSelectColumns),
      columndefs: [
        ...Array.from({ length: columns.totalColumns }, (_, i) => ({
          searchPanes: { show: true },
          targets: [i],
        })),
        { targets: '_all', className: 'text-left dt-left' },
      ],
      buttons: [],
      rowCallback: function (row, data) {},
    };
  };

  const getActiveJobsOptions = function (columns) {
    const currentDate = new Date();
    const timestamp = currentDate.getTime();
    let full_url = window.location.href;

    //Added condition for col 3 preselect value when url has parameter 'job_status' and equal to 'Booked'
    if(full_url.includes('?') && window.location.search == '?job_status=Booked'){
      var presel = [
        {
          column: 3,
          rows: ['Booked']
        }
      ]
    }else{
      var presel = getPreselectOptions(columns.preSelectColumns)
    }

    return {
      ordering: true,
      preselect: presel,
      columndefs: [
        { searchPanes: { show: false }, targets: [5] },
        { searchPanes: { show: false }, targets: [6] },
        ...Array.from({ length: columns.totalColumns }, (_, i) => ({
          searchPanes: { show: true },
          targets: [i],
        })),
        { targets: '_all', className: 'text-left dt-left' },
      ],
      buttons: [
        {
          extend: 'excelHtml5',
          className: 'btn btn-primary',
          text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
          filename: 'Active Jobs ' + timestamp,
          exportOptions: {
            columns: [0, 1, 3, 4, 5, 6, 7],
            modifier: {
              search: 'applied',
              order: 'applied',
            },
            orthogonal: 'export',
          },
          customizeData: function (excelData) {
            for (var i = 0; i < excelData.body.length; i++) {
              var noticeEntry = excelData.body[i][4];
              var shortTermRental = excelData.body[i][5];

              if (noticeEntry === 'N/A') {
                excelData.body[i][4] = 'NO';
              } else {
                excelData.body[i][4] = 'YES';
              }

              if (shortTermRental === 'N/A') {
                excelData.body[i][5] = 'NO';
              } else {
                excelData.body[i][5] = 'YES';
              }
            }
          },
        },
      ],
      rowCallback: function (row, data) {},
    };
  };

  const getReportOptions = function (columns) {
    return {
      ordering: true,
      preselect: getPreselectOptions(columns.preSelectColumns),
      columndefs: [
        { searchPanes: { show: false }, targets: [2], orderable: false },
        ...Array.from({ length: columns.totalColumns }, (_, i) => ({
          searchPanes: { show: true },
          targets: [i],
        })),
        { targets: '_all', className: 'text-left dt-left' },
      ],
      buttons: [],
      rowCallback: function (row, data) {},
    };
  };

  const getReportActiveServiceOptions = function (columns) {
    return {
      ordering: true,
      preselect: getPreselectOptions(columns.preSelectColumns),
      columndefs: [
        ...Array.from({ length: columns.totalColumns }, (_, i) => ({
          searchPanes: { show: true },
          targets: [i],
        })),
        { targets: '_all', className: 'text-left dt-left' },
      ],
      buttons: [
        {
          extend: 'excelHtml5',
          className: 'btn btn-primary',
          text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
          filename: 'Active_services_excel ',
        },
        {
          extend: 'pdfHtml5',
          className: 'btn btn-primary',
          text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
          // filename: 'Active_services_pdf ',
          action: function (e, dt, button, config) {
            alert('export here');
          },
        },
      ],
      rowCallback: function (row, data) {},
    };
  };

  
  const getActiveJobsOptionsBooked = function (columns) {
    const currentDate = new Date();
    const timestamp = currentDate.getTime();
    return {
      ordering: true,
      preselect: [{
          column: 3,
          rows: ['Booked']
      }],
      columndefs: [
        { searchPanes: { show: false }, targets: [5] },
        { searchPanes: { show: false }, targets: [6] },
        ...Array.from({ length: columns.totalColumns }, (_, i) => ({
          searchPanes: { show: true },
          targets: [i],
        })),
        { targets: '_all', className: 'text-left dt-left' },
      ],
      buttons: [
        {
          extend: 'excelHtml5',
          className: 'btn btn-primary',
          text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
          filename: 'Active Jobs ' + timestamp,
          exportOptions: {
            columns: [0, 1, 3, 4, 5, 6, 7],
            modifier: {
              search: 'applied',
              order: 'applied',
            },
            orthogonal: 'export',
          },
          customizeData: function (excelData) {
            for (var i = 0; i < excelData.body.length; i++) {
              var noticeEntry = excelData.body[i][4];
              var shortTermRental = excelData.body[i][5];

              if (noticeEntry === 'N/A') {
                excelData.body[i][4] = 'NO';
              } else {
                excelData.body[i][4] = 'YES';
              }

              if (shortTermRental === 'N/A') {
                excelData.body[i][5] = 'NO';
              } else {
                excelData.body[i][5] = 'YES';
              }
            }
          },
        },
      ],
      rowCallback: function (row, data) {},
    };
  };

  const initDatatable = function () {
    let dtOptions = {
      ordering: false,
      ajax: '',
      processing: false,
      serverSide: false,
      preselect: [],
      columndefs: [],
      buttons: [],
    };

    if (window.location) {
      // Access or modify the Location object
      const pathname = window.location.pathname;
      console.log('🚀 ~ initDatatable ~ pathname:', pathname);
      console.log('🚀 ~ initDatatable ~ pathname:', window.location.search);
      let columns = 0;

      switch (pathname) {
        case '/properties':
        case '/properties/':
          // Parse the URL to get the search parameters
          var urlParams = new URLSearchParams(window.location.search);
          // Get the value of the "type" query parameter
          var typeValue = urlParams.get('type');
          // Log the result
          console.log('Type Query Parameter:', typeValue);

          columns = {
            preSelectColumns: 2,
            totalColumns: 4,
          };

          if (typeValue == 'not_compliant') {
            columns = {
              preSelectColumns: 5,
              totalColumns: 5,
            };
          }

          dtOptions = getPropertyOptions(columns);

          break;
        case '/jobs':
          columns = {
            preSelectColumns: 4,
            totalColumns: 8,
          };
          dtOptions = getActiveJobsOptions(columns);
          break;
        
        case '/reports':
          columns = {
            preSelectColumns: 3,
            totalColumns: 3,
          };
          dtOptions = getReportOptions(columns);
          break;

      }
    }

    /**
     * Reposition EVERYTHING lol
     */
    jQuery('#datatable')
      .on('init.dt', function () {
        console.log('init');

        //Get the search field, set placeholder instructions
        let input = jQuery('#datatable_filter input')
          .attr('placeholder', 'Search Table')
          .clone(true, true);
        // console.log(input);

        // remove label and focus on field on load
        jQuery('#datatable_filter label').remove();
        input.appendTo('#datatable_filter').focus();

        let addBtn = jQuery('.addBtn');

        if (addBtn.length) {
          addBtn.appendTo('.dtHeader');
        }

        $('.dtsp-panesContainer').hide();

        $(
          '<div id="btn-toggle-searchpanes" title="Toggle SearchPanes"><i class="fa fa-sliders" aria-hidden="true"></i></div>'
        )
          .on('click', function () {
            $('.dtsp-panesContainer').toggle();
          })
          .appendTo('#datatable_filter');
      })
      .on('draw.dt', function () {
        console.log('draw');
      })
      .DataTable({
        dom: '<"dtHeader"fB>P<"dtFlexRow"il><t><"dtFlexRowCenter"p>',
        pageLength: 100,
        lengthMenu: [100, 250, 500],
        ordering: dtOptions.ordering,
        searchPanes: {
          initCollapsed: true,
          preSelect: dtOptions.preselect,
        },
        columnDefs: dtOptions.columndefs,
        buttons: dtOptions.buttons,
        rowCallback: dtOptions.rowCallback,
        language: {
          paginate: {
            previous: '&laquo;',
            next: '&raquo;',
          },
          lengthMenu: '_MENU_',
        },
        stripeClasses: ['even', 'odd'],
      });
  };

  return {
    init: init,
  };
})();
// Initialize ->> It's showtime :)
DataTableManager.init();

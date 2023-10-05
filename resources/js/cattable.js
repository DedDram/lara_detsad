jQuery(document).ready(function (jQuery) {
    jQuery('#cattable').dataTable({
        "iDisplayLength": 100,
        "sPaginationType": "full_numbers",
        "bInfo": false,
        "columnDefs": [
            { "visible": false, "targets": 4 }
        ],
		"oLanguage": {
            "sUrl": "/lang/ru/ru_RU.txt"
        },
        "order": [[ 4, 'asc' ], [ 2, 'desc' ]],
        "drawCallback": function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;
            var nums = [];
            var n = 1;

            api.column(4, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    jQuery(rows).eq( i ).before(
                        '<tr class="group"><td></td><td colspan="3">'+group+'</td></tr>'
                    );
                    last = group;
                    n = 1;
                }
                nums[i] = n;
				n++;
            });
			api.column(0, {page:'current'}).nodes().each( function (cell, i) {
				cell.innerHTML = nums[i];
			});
			jQuery('#cattable').show();
        }
    });
});

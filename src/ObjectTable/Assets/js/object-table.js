jQuery(document).ready(function($) {
    var currentSort = { column: 'default_column', order: 'asc' };

    // Function to perform the AJAX call.
    function updateTable(configId) {
        $('table').css('cursor', 'wait');
        var data = {
            action: 'object_handle_sort',
            search_term: null,
            sort_column: null,
            sort_order: currentSort.order,
            config_id: configId
        };
        if (currentSort.column !== 'default_column') {
            data.sort_column = currentSort.column;
        }
        if ($('#searchInput' + configId).val() !== '') {
            data.search_term = $('#searchInput' + configId).val()
        }
        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: data,
            success: function(data) {
                $('#objectTable' + configId  + ' tbody').empty().html(data.data.html);
                $('th').removeClass('asc desc'); 
                $('#table' + configId + 'Header_' + currentSort.column).addClass(currentSort.order);
            },
            complete: function() {
                $('table').css('cursor', 'default');
                $('#searchInput' + configId).prop('disabled', false);
            }
        });
    }

    var searchTimeout;

    $('.search-input').on('input', function() {
        var inputId = $(this).attr('id'); 
        var matches = inputId.match(/searchInput(\d+)/); 

        // Clear any existing timeout to reset the timer.
        clearTimeout(searchTimeout);

        if (matches) {
            var configId = matches[1];

            // Set a new timeout
            searchTimeout = setTimeout(function() {
                $('#searchInput' + configId).prop('disabled', true);
                updateTable(configId); 
            }, 1000); 
        }
    });

    // Sort listener.
    $('th').on('click', function() {
        var headerId = $(this).attr('id');
        var matches = headerId.match(/table(\d+)Header_/);
        if (matches) {
            var configId = matches[1];
            var column = headerId.replace('table' + configId + 'Header_', '');
            var order = $(this).hasClass('asc') ? 'desc' : 'asc';

            // Update currentSort with the new order.
            currentSort = { column: column, order: order };

            updateTable(configId);
        }
    });
});
jQuery(document).ready(function($) {
    var currentSort = {};
    var currentPage = {}; 

    // Function to perform the AJAX call.
    function updateTable(configId, page) {
        $('#searchInput' + configId).prop('disabled', true);
        $('#searchButton' + configId).prop('disabled', true);
        $('table').css('cursor', 'wait');
        $('#tablePagination' + configId).css('cursor', 'loading');

        if (configId in currentSort === false) {
            currentSort[configId] = { column: 'default_column', order: 'asc' };
        }
        if (configId in currentPage === false) {
            currentPage[configId] = 1;
        }

        page = page || currentPage[configId];

        var data = {
            action: 'object_handle_sort',
            search_term: null,
            sort_column: null,
            sort_order: currentSort[configId].order,
            page: page,
            config_id: configId
        };

        if (currentSort[configId].column !== 'default_column') {
            data.sort_column = currentSort[configId].column;
        }
        if ($('#searchInput' + configId).val() !== '') {
            data.search_term = $('#searchInput' + configId).val()
            if (currentPage[configId] !== 1) {
                page = 1;
                data.page = page;
            }
        }

        $.ajax({
            url: '/wp-admin/admin-ajax.php',
            type: 'POST',
            data: data,
            failure: function() {
                updateTable(configId, 1);
            },
            success: function(data) {
                $('#objectTable' + configId  + ' tbody').empty().html(data.data.html);
                $('th').removeClass('asc desc'); 

                $('#table' + configId + 'Header_' + currentSort[configId].column.replace(/ /g, '_')).addClass(currentSort[configId].order);
                if (page !== currentPage[configId]) {
                    $('#tablePaginationCurrent' + configId).html(page);
                    currentPage[configId] = page;
                }
            },
            complete: function() {
                $('table').css('cursor', 'default');
                $('#tablePagination' + configId).css('cursor', 'default');
                $('#searchInput' + configId).prop('disabled', false);
                $('#searchButton' + configId).prop('disabled', false);

            }
        });
    }

    // Event listeners for pagination links
    $('.table-pagination a').on('click', function(e) {
        e.preventDefault(); // Prevent the default anchor behavior

        var clickedId = $(this).attr('id');
        var configIdMatch = clickedId.match(/tablePagination(Previous|Next|Current)(\d+)/);
        var configId, newPage;

        // Handle previous and next buttons
        if (configIdMatch) {
            configId = configIdMatch[2]; 

            if (configId in currentPage === false) {
                currentPage[configId] = 1;
            }

            // Determine the new page number based on the clicked button
            if (configIdMatch[1] === 'Previous') {
                newPage = currentPage[configId] - 1;
            }
            if (configIdMatch[1] === 'Current') {
                newPage = currentPage[configId];
            }
            if (configIdMatch[1] === 'Next') {
                newPage = currentPage[configId] + 1;
            }
        }

        // Refresh the table
        updateTable(configId, newPage);
    });

    var searchTimeout;

    $('.search-button').on('click', function() {
        var inputId = $(this).attr('id'); 
        var matches = inputId.match(/searchButton(\d+)/); 

        // Clear any existing timeout to reset the timer.
        // clearTimeout(searchTimeout);

        if (matches) {
            var configId = matches[1];

            // Set a new timeout
            // searchTimeout = setTimeout(function() {
            updateTable(configId); 
            // }, 500); 
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
            
            var thColumn = column.replace(/_/g, ' ');
            currentSort[configId] = { column: thColumn, order: order };

            updateTable(configId);
        }
    });
});
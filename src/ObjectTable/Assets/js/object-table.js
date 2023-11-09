jQuery(document).ready(function($) {
    // Event listener for the header click
    $('th').on('click', function() {
        var headerId = $(this).attr('id'); 
        // Use a regex to extract the configId.
        var matches = headerId.match(/table(\d+)Header_/); 
        if (matches) {
            $('html, body').css({'cursor' : 'wait'});
            var configId = matches[1]; 
             // Get the column to sort by.
            var column = headerId.replace('table' + configId + 'Header_', '');
            var order = $(this).hasClass('asc') ? 'desc' : 'asc'; 
            
            // Fetch data with custom PHP and WP AJAX.
            $.ajax({
                url: '/wp-admin/admin-ajax.php', 
                type: 'POST',
                data: {
                    action: 'object_handle_sort', 
                    sort_column: column,
                    sort_order: order,
                    config_id: configId
                },
                success: function(data) {
                    $('#objectTable' + configId  + ' tbody').empty(); 
                    $('#objectTable' + configId  + ' tbody').html(data.data.html);
                    $('th').removeClass('asc desc');
                    $('#table' + configId + 'Header_' + column).addClass(order);
                },
                complete: function() {
                    $('html, body').css({'cursor' : 'default'});
                }
            });
        }
    });
});



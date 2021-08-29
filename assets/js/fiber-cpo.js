jQuery(document).ready(function($){
    $('table.wp-list-table tbody').sortable({
        items: '> tr',
        axis: 'y',
        opacity: 0.5,
        cursor: 'grab',
        cancel: 'input, textarea, button, select, option, .inline-edit-row',
        update: function(){
            $("#wpbody").addClass("fa-loading");
            $.ajax({
                url: fiber_cpo.ajax_url,
                type: 'POST',
                data: {
                    action: 'fiber_cpo_update',
                    cpo_data: $(this).sortable('serialize'),
                    post_type: $('input[name="post_type"]').val(),
                    post_status: $('input[name="post_status"]').val(),
                },
                success: function(){
                    $("#wpbody").removeClass("fa-loading");
                }
            });
        }
    });
});
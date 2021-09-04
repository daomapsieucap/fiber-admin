jQuery(document).ready(function($){
    // CPT
    $('table.wp-list-table tbody').sortable({
        items: '> tr',
        axis: 'y',
        opacity: 0.5,
        cursor: 'grab',
        cancel: 'input, textarea, button, select, option, .inline-edit-row',
        update: function(){
            $("#wpbody").addClass("fa-loading");
            if($('input[name="taxonomy"]').length > 0){
                $.ajax({
                    url: fiad_cpo.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'fiad_cpo_tax_update',
                        cpo_data: $(this).sortable('serialize'),
                        taxonomy: $('input[name="taxonomy"]').val()
                    },
                    success: function(){
                        $("#wpbody").removeClass("fa-loading");
                    }
                });
            }else{
                $.ajax({
                    url: fiad_cpo.ajax_url,
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
        }
    });
});
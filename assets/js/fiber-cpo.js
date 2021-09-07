jQuery(document).ready(function($){
    $('table.wp-list-table tbody').sortable({
        items: '> tr',
        axis: 'y',
        opacity: 0.5,
        cursor: 'grab',
        cancel: 'input, textarea, button, select, option, .inline-edit-row',
        start: function(event, ui){
            $(this).find('.placeholder-style td:nth-child(2)').addClass('hidden-td')

            //copy item html to placeholder html
            ui.placeholder.html(ui.item.html());

            //hide the items but keep the height/width.
            ui.placeholder.css('visibility', 'hidden');
        },
        stop: function(event, ui){
            ui.item.css('display', '')
        },
        //add helper function to keep draggable object the same width
        helper: function(e, tr){
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index){
                // Set helper cell sizes to match the original sizes
                $(this).width($originals.eq(index).width());
            });
            return $helper;
        },
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
                        action: 'fiad_cpo_update',
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
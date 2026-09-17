jQuery(document).ready(function($){
    /**
     * Upload field
     */
    $('.fiber-admin-input__img').each(function(){
        const $fieldset = $(this),
            $input = $fieldset.find('.fiber-admin-image-value'),
            $thumb = $fieldset.find('.fiber-admin-image-thumbnail'),
            $removeWrap = $fieldset.find('.fiber-admin-remove-wrap');

        let frame;

        function setImage(url){
            $input.val(url);
            if(url){
                $thumb.removeClass('fiber-admin-image-thumbnail--empty').html($('<img/>', {src: url}));
                $removeWrap.show();
            }else{
                $thumb.addClass('fiber-admin-image-thumbnail--empty').text($thumb.data('empty-label'));
                $removeWrap.hide();
            }
        }

        $thumb.on('click', function(e){
            e.preventDefault();

            if(frame){
                frame.open();
                return;
            }

            frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: false
            });

            frame.on('select', function(){
                const attachment = frame.state().get('selection').first().toJSON();
                setImage(attachment.url);
            });

            frame.open();
        });

        $fieldset.find('.fiber-admin-remove-image').on('click', function(e){
            e.preventDefault();
            setImage('');
        });
    });

    /**
     * Color picker field
     */
    $('.fiber-color-field').wpColorPicker();
});

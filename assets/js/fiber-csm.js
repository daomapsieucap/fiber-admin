jQuery(document).ready(function($){
    /**
     * Upload field
     */

    // $preview
    $('.fiad_csm_logo input[type="text"]').on('change', function(){
        let $preview = $(this).closest('fieldset').find('img'),
            $input = $(this).closest('fieldset').find('input');
        if(!$(this).val()){
            $preview.hide();
        }else{
            if(!$preview.attr('src')){
                $preview.attr('src', $input.val());
            }
            $preview.show();
        }
    });

    $('.fiad_csm_background_image input[type="text"]').on('change', function(){
        let $preview = $(this).closest('fieldset').find('img'),
            $input = $(this).closest('fieldset').find('input');
        if(!$(this).val()){
            $preview.hide();
        }else{
            if(!$preview.attr('src')){
                $preview.attr('src', $input.val());
            }
            $preview.show();
        }
    });

    // image upload
    $('.fiber-admin-upload').each(function(){
        const $uploadElement = $(this),
            $target = $uploadElement.closest('fieldset').find('input'),
            $preview = $uploadElement.closest('fieldset').find('img');

        let customUploader;

        if(!$preview.attr('src')){
            $preview.hide();
        }

        $uploadElement.click(function(e){
            e.preventDefault();

            //If the uploader object has already been created, reopen the dialog
            if(customUploader){
                customUploader.open();
                return;
            }

            //Extend the wp.media object
            customUploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: false
            });

            //When a file is selected, grab the URL and set it as the text field's value
            customUploader.on('select', function(){
                const attachment = customUploader.state().get('selection').first().toJSON();
                $target.val(attachment.url);
                $preview.attr('src', attachment.url).show();
            });

            //Open the uploader dialog
            customUploader.open();
        });
    });
});
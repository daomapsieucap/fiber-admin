jQuery(document).ready(function($){
    /**
     * Upload field
     */
    // preview
    $('.fiber-admin-input__img input[type="text"]').on('change', function(){
        const preview = $(this).closest('fieldset').find('img');
        if(!$(this).val()){
            preview.hide();
        }else{
            preview.show();
        }
    });

    // image upload
    $('.fiber-admin-upload').each(function(){
        const uploadElement = $(this),
            target = uploadElement.closest('fieldset').find('input'),
            preview = uploadElement.closest('fieldset').find('img');

        let customUploader;

        if(!preview.attr('src')){
            preview.hide();
        }

        uploadElement.click(function(e){
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
                target.val(attachment.url);
                preview.attr('src', attachment.url).show();
            });

            //Open the uploader dialog
            customUploader.open();
        });
    });

    /**
     * Color picker field
     */
    $('.fiber-color-field').wpColorPicker();
});
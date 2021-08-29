jQuery(document).ready(function($){
    // Upload field
    const upload_element = $('.fiberadmin-upload'),
        target = upload_element.closest('fieldset').find('input'),
        preview = upload_element.closest('fieldset').find('img');

    let custom_uploader;

    upload_element.click(function(e){
        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if(custom_uploader){
            custom_uploader.open();
            return;
        }
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function(){
            const attachment = custom_uploader.state().get('selection').first().toJSON();
            target.val(attachment.url);
            preview.attr('src', attachment.url);
        });
        //Open the uploader dialog
        custom_uploader.open();
    });

    // Color picker field
    $('.fiber-color-field').wpColorPicker();
});
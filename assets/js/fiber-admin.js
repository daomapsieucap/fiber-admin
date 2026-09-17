jQuery(document).ready(function($){
    /**
     * Upload field
     */
    let activeMinSize = null;

    if(window.wp && wp.media && wp.media.view && wp.media.view.Attachment && wp.media.view.Attachment.Library && !wp.media.view.Attachment.Library.fiberAdminPatched){
        const OriginalLibraryAttachment = wp.media.view.Attachment.Library;

        wp.media.view.Attachment.Library = OriginalLibraryAttachment.extend({
            render: function(){
                OriginalLibraryAttachment.prototype.render.apply(this, arguments);

                if(activeMinSize){
                    const width = this.model.get('width'),
                        height = this.model.get('height'),
                        invalid = (activeMinSize.width && (!width || width < activeMinSize.width))
                            || (activeMinSize.height && (!height || height < activeMinSize.height));

                    this.$el.toggleClass('fiber-admin-attachment-disabled', !!invalid);
                }

                return this;
            },
            toggleSelection: function(options){
                if(this.$el.hasClass('fiber-admin-attachment-disabled')){
                    return;
                }

                return OriginalLibraryAttachment.prototype.toggleSelection.apply(this, arguments);
            }
        });

        wp.media.view.Attachment.Library.fiberAdminPatched = true;
    }

    $('.fiber-admin-input__img').each(function(){
        const $fieldset = $(this),
            $input = $fieldset.find('.fiber-admin-image-value'),
            $thumb = $fieldset.find('.fiber-admin-image-thumbnail'),
            $removeButton = $fieldset.find('.fiber-admin-remove-image'),
            minWidth = parseInt($fieldset.data('min-width'), 10) || 0,
            minHeight = parseInt($fieldset.data('min-height'), 10) || 0;

        let frame;

        function setImage(url){
            $input.val(url);
            if(url){
                $thumb.removeClass('fiber-admin-image-thumbnail--empty').html($('<img/>', {src: url}));
                $removeButton.show();
            }else{
                $thumb.addClass('fiber-admin-image-thumbnail--empty').text($thumb.data('empty-label'));
                $removeButton.hide();
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
                library: {
                    type: 'image'
                },
                multiple: false
            });

            frame.on('open', function(){
                activeMinSize = (minWidth || minHeight) ? {width: minWidth, height: minHeight} : null;

                frame.state().get('library').each(function(attachment){
                    attachment.trigger('change');
                });
            });

            frame.on('close', function(){
                activeMinSize = null;
            });

            frame.on('select', function(){
                const attachment = frame.state().get('selection').first().toJSON();
                setImage(attachment.url);
            });

            frame.open();
        });

        $removeButton.on('click', function(e){
            e.preventDefault();
            setImage('');
        });
    });

    /**
     * Color picker field
     */
    $('.fiber-color-field').wpColorPicker();
});

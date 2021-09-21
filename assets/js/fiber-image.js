jQuery(document).ready(function($){
    /**
     * Disable right click on image
     */
    $('body').on('contextmenu', 'img', function(e){
        return false;
    });
});
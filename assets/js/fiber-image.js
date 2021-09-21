jQuery(document).ready(function($){
    /**
     * Disable right click on image v1.1.2
     */
    $('body').on('contextmenu', 'img', function(e){
        return false;
    });
});
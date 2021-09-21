jQuery(document).ready(function($){
    /**
     * Disable right click on image v1.1.2
     */
    $('body').on('contextmenu', 'img', function(e){
        return false;
    });

    /**
     * Disable drag image into html page v1.1.2
     */

    $('img').on('dragstart', function(event){
        event.preventDefault();
    });
});
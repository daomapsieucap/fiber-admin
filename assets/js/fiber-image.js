jQuery(document).ready(function($){
    /**
     * Disable right click on image
     */
    $("img").on("contextmenu", function(){
        return false;
    });
});
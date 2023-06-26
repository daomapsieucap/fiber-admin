jQuery(document).ready(function($){
    let $disable_protection = $("#disable_protection");
    if($disable_protection.is(':checked')){
        $(".related-disable-protection").removeClass('hidden');
    }

    $disable_protection.on("change", function(){
        if($(this).is(':checked')){
            $(".related-disable-protection").removeClass('hidden');
        }else{
            $(".related-disable-protection").addClass('hidden');
        }
    });
});
jQuery(document).ready(function(n){
    if(n("#disable_protection").is(':checked')){
        n(".related-disable-protection").removeClass('hidden');
    }

    n("#disable_protection").on("change", function(){
        if(n(this).is(':checked')){
            n(".related-disable-protection").removeClass('hidden');
        }else{
            n(".related-disable-protection").addClass('hidden');
        }
    });
});
jQuery(document).ready(function() {
    jQuery('select').each(function() {
        jQuery(this).select2({
            width: '100%',
            // placeholder: 'Select an option'
        });
    });
    
    // jQuery('#neighborhood').each(function() {
    //     jQuery(this).select2({
    //         width: '100%'
    //     });
    // });
});
/* Axios HTTP Client */
Vue.prototype.$http = axios;

/* Vue Mixin */
Vue.mixin({
    methods: {
        set_errors: function(errors) {
            // Clean the old errors
            this.reset_errors();

            if ( typeof errors === 'string') 
            {
                toastr.error(errors);
            }

            // Iterate the errors object and append the errors under each field
            if (!!Object.keys(errors).length) {
                Object.keys(errors).forEach(function(key) {
                    jQuery('#' + key)
                    .next('.help-block.with-errors')
                    .append(
                        jQuery('<ul/>')
                        .addClass('list-unstyled')
                        .append(
                            jQuery.map(
                                jQuery.type(errors[key]) == 'array' ? 
                                errors[key] : [errors[key]], function(error) { 
                                    return jQuery('<li/>').text(error);
                                }
                            )
                        )
                    );
                });
            }
        },
        reset_errors: function(){
            jQuery('.help-block.with-errors').html('');
        }
    }
});

Object.filter = function( obj, predicate) {
    var result = {}, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key) && !predicate(obj[key], key)) {
            result[key] = obj[key];
        }
    }

    return result;
};

$(function () {
    /* Toastr options */
    toastr.options = {
        progressBar: true,
        timeOut: 2000,
        positionClass: "toast-top-center"
    }
});
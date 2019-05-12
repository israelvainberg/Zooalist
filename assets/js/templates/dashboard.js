/* Axios HTTP Client */
Vue.prototype.$http = axios;

/* Vue Mixin */
Vue.mixin({
    methods: {
        number_format: function(number, decimals, decPoint, thousandsSep) {
            decimals = Math.abs(decimals) || 0;
            number = parseFloat(number);
            
            if (!decPoint || !thousandsSep) {
                decPoint = '.';
                thousandsSep = ',';
            }
            
            var roundedNumber = Math.round(Math.abs(number) * ('1e' + decimals)) + '';
            var numbersString = decimals ? (roundedNumber.slice(0, decimals * -1) || 0) : roundedNumber;
            var decimalsString = decimals ? roundedNumber.slice(decimals * -1) : '';
            var formattedNumber = "";
            
            while (numbersString.length > 3) {
                formattedNumber += thousandsSep + numbersString.slice(-3)
                numbersString = numbersString.slice(0, -3);
            }
            
            if (decimals && decimalsString.length === 1) {
                while (decimalsString.length < decimals) {
                    decimalsString = decimalsString + decimalsString;
                }
            }
            
            return (number < 0 ? '-' : '') + numbersString + formattedNumber + (decimalsString ? (decPoint + decimalsString) : '');
        },
        set_errors: function(errors, next) {
            // Clean the old errors
            this.reset_errors();

            // Iterate the errors object and append the errors under each field
            if (!!Object.keys(errors).length) {
                Object.keys(errors).forEach(function(key) {
                    jQuery('#' + key)[ ! next ? 'nextAll' : 'next']('.help-block.with-errors').append(
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
    $(".colorpicker").asColorPicker();
    $(".complex-colorpicker").asColorPicker({
        mode: 'complex'
    });
    $(".gradient-colorpicker").asColorPicker({
        mode: 'gradient'
    });

    $('body').on('close.bs.alert', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(e.target).slideUp();
    });

    // Smart Wizard
    $('#smartwizard').smartWizard({
        selected: 0,
        theme: 'default',
        transitionEffect: 'fade',
        showStepURLhash: false,
        toolbarSettings: {
            toolbarPosition: 'bottom',
            toolbarButtonPosition: 'end'
        }
    });

    var $finalStep = false;

    $('#smartwizard').on('showStep', function(e, anchorObject, stepNumber, stepDirection, stepPosition) {
        if ( stepNumber == 2 ) {
            $(".sw-btn-next").text('Let’s Go');
            $(".sw-btn-next").removeClass('disabled');
        } else {
            $(".sw-btn-next").text('Next');
        }

        setTimeout(function(){
            $finalStep = (stepPosition == 'final');
        }, 10);
     });

    $(".sw-btn-next").click(function () {
        if (!!$finalStep ) {
            $('#IntroModal').modal('hide');
        }
    });

    $(".popover").popover({trigger: "hover"});

    if (isMobile()) {
        $('body').css('cursor', 'pointer');
    }

    $('html, body').on('click', function (e) {
        //did not click a popover toggle, or icon in popover toggle, or popover
        if ($(e.target).data('toggle') !== 'popover'
            && $(e.target).parents('[data-toggle="popover"]').length === 0
            && $(e.target).parents('.popover.in').length === 0) {
            $('[data-toggle="popover"]').popover('hide');
        }
    });

    /* Data binding */
    Taptac.user = new Vue({
        el: '#user',
        data: Taptac.model.user
    });

    /* Toastr options */
    toastr.options = {
        progressBar: true,
        timeOut: 2000,
        positionClass: "toast-top-center"
    }
});
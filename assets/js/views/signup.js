Zooalist.model.signup.control = {
    sending: false
}

/* Reactive Instances */
Zooalist.signup = new Vue({
    el: '#signup',
    data: Zooalist.model.signup,
    methods: {
        save: function(event){
            // Check if the client validation failed
            if(event.isDefaultPrevented() || !!this.control.sending) return;

            // Prevent the native submit event
            event.preventDefault();

            // Activate the spinner & disable the submit button
            this.control.sending = true;

            // Send the data to the server
            this.$http
            .post('signup', Zooalist.model.signup.data)
            .then(function(response) {
                if(!response.data.status) {
                    toastr.error(response.data.message);
                } else {
                    // Clean the old errors
                    this.reset_errors();

                    // Show the response message & redirect the user
                    toastr.success(response.data.message, '', {
                        onHidden: function() {
                            window.location.href = Zooalist.base_url;
                        }.bind(this)
                    });
                }
            }.bind(this))
            .catch(function (error) {
                // Display the errors coming from the server
                this.set_errors(error.response.data.message);
            }.bind(this))
            .then(function(){
                this.control.sending = false;
            }.bind(this));
        },
    }
});

$(function() { 
    $.fn.validator.Constructor.INPUT_SELECTOR = ':input:not([type="hidden"], [type="submit"], [type="reset"], [type="file"], button)';
    $('#signup').validator({
        custom: {
            equals: function($el) {
                if (!!$el.val() && $el.val() !== $('#' + $el.data("equals")).val()) {
                    return $el.attr('data-equals-error') || "Passwords are not equal";
                }
            }
        }
    }).on('submit', Zooalist.signup.save);
});
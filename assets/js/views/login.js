Zooalist.model.login.control = {
    sending: false,
    shown: true,
    redirect: false
}

/* Reactive Instances */
Zooalist.login = new Vue({
    el: '#login',
    data: Zooalist.model.login,
    methods: {
        save: function(event){
            // Check if the client validation failed
            if(event.isDefaultPrevented() || !!this.control.sending) return;

            // Prevent the native submit event
            event.preventDefault();

            // Activate the spinner & disable the submit button
            this.control.sending = true;

            // Get the target url (after the login request)
            this.control.redirect = this.$cookies.get('redirect');

            // Send the data to the server
            this.$http
            .post('login', Zooalist.model.login.data)
            .then(function(response) {
                if(!response.data.status) {
                    toastr.error(response.data.message);
                } else {
                    // Clean the old errors
                    this.reset_errors();

                    // Show the response message & redirect the user
                    toastr.success(response.data.message, '', {
                        onHidden: function() {
                            if (!!this.control.redirect) {
                                this.$cookies.remove('redirect');
                                window.location.href = this.control.redirect;
                            } else {
                                window.location.href = Zooalist.base_url;
                            }
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

Zooalist.model.recover.control = {
    sending: false,
    shown: false
}

/* Reactive Instances */
Zooalist.recover = new Vue({
    el: '#recover',
    data: Zooalist.model.recover,
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
            .post('recover', Zooalist.model.recover.data)
            .then(function(response) {
                if(!response.data.status) {
                    toastr.error(response.data.message);
                } else {
                    // Clean the old errors
                    this.reset_errors();

                    toastr.success(response.data.message);
                }
            })
            .catch(function (error) {
                // Display the errors coming from the server
                this.set_errors(error.response.data.message);
            }.bind(this))
            .then(function(){
                this.control.sending = false;
            }.bind(this));
        }
    }
});

$(function() { 
    $.fn.validator.Constructor.INPUT_SELECTOR = ':input:not([type="hidden"], [type="submit"], [type="reset"], [type="file"], button)';
    $('#login').validator().on('submit', Zooalist.login.save);
    $('#recover').validator().on('submit', Zooalist.recover.save);

    // ============================================================== 
    // Login and Recover Password 
    // ============================================================== 
    $('#to-recover').on('click', function() {
        $("#login").slideUp();
        $("#recover").fadeIn();
    });

    $('#from-recover').on('click', function(){
        $("#recover").fadeOut();
        $("#login").slideDown();
    });
});
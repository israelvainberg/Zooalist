/* Posts Control */
Zooalist.model.posts.control = {
    sending: false
}

/* Posts */
Zooalist.posts = new Vue({
    el: '#posts',
    data: Zooalist.model.posts
});

/* Users Control */
Zooalist.model.users.control = {
    sending: false
}

/* Users */
Zooalist.users = new Vue({
    el: '#users',
    data: Zooalist.model.users,
    methods: {
        invite: function(user){
            this.$http
            .post('requests', {id: user.user_id})
            .then(function(response) {
                if(!response.data.status) {
                    toastr.error(response.data.message);
                } else {
                    // Clean the old errors
                    this.reset_errors();

                    // Show the response message
                    toastr.success(response.data.message);

                    // Update the status of the user (should be done using state management)
                    user.is_pending = true;
                    user.sent = "1";
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
        accept: function(user){
            this.$http
            .put('requests', {id: user.user_id})
            .then(function(response) {
                if(!response.data.status) {
                    toastr.error(response.data.message);
                } else {
                    // Clean the old errors
                    this.reset_errors();

                    // Show the response message
                    toastr.success(response.data.message);

                    // Update the status of the user (should be done using state management)
                    user.is_friend = true;
                    user.is_pending = false;

                    // TODO: We can fetch the users posts and merge them to the currently loaded collection
                }
            }.bind(this))
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

/* New post */
Zooalist.newPost = new Vue({
    el: '#new_post',
    data: {
        content: '',
        control: {
            sending: false
        }
    },
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
            .post('posts', {content: this.content})
            .then(function(response) {
                if(!response.data.status) {
                    toastr.error(response.data.message);
                } else {
                    // Clean the old errors
                    this.reset_errors();

                    // Show the response message
                    toastr.success(response.data.message);

                    // Reset the model
                    this.content = '';
                }
            }.bind(this))
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
    $('#new_post').validator().on('submit', Zooalist.newPost.save);
});
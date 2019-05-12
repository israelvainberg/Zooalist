<div class="row">
    <div class="col-sm-2">
        <a href="<?= base_url() ?>logout">Logout</a>
    </div>
    <div class="col-sm-10">
        <form class="form-inline pull-right" id="new_post">
            <label class="sr-only" for="content">Post content</label>
            <input 
                type="text" 
                class="form-control mb-2 mr-sm-2" 
                id="content" 
                placeholder="Write something..." 
                v-model="content"
            >
            <button type="submit" class="btn btn-primary mb-2">Send</button>
        </form>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-4" id="users">
        <h3> Users </h3>
        <div class="cards-container" v-if="data.length">
            <div class="card" v-for="user in data">
                <div class="card-body">
                    <h5 class="card-title">{{ user.firstname }} {{ user.lastname }}</h5>
                    <a 
                        href="#" 
                        class="btn btn-primary btn-sm" 
                        v-if="!user.is_friend && !user.is_pending" 
                        v-on:click.prevent="invite(user)"
                    >
                        Send friend request
                    </a>
                    <div v-if="user.is_pending">
                        <span v-if="user.sent == 1">Invitation sent</span>
                        <span v-if="user.received == 1">
                            <a 
                                href="#" 
                                class="btn btn-info btn-sm" 
                                v-on:click.prevent="accept(user)"
                            >
                                Accept invitation
                            </a>
                        </span>
                    </div>
                    <div v-if="user.is_friend">
                        You are friends!
                    </div>
                </div>
            </div>
        </div>
        <div class="empty-message" v-else>
            No Users
        </div>     
    </div>
    <div class="col-sm-12 col-md-8" id="posts">
        <h3>Posts</h3>
        <div class="cards-container" v-if="data.length">
            <div class="card" v-for="post in data">
                <div class="card-body">
                    <h5 class="card-title">{{ post.firstname }} {{ post.lastname }}</h5>
                    <p class="card-text">{{ post.content }}</p>
                    <small>{{ post.created }}</small>
                </div>
            </div>
        </div>
        <div class="empty-message" v-else>
            No New Posts
        </div>
    </div>
</div>
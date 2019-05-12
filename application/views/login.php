<div class="login-register">
    <div class="login-box card">
        <div class="card-body">
            <form 
                id="login" 
                class="form-horizontal form-material" 
                autocomplete="false" 
                novalidate="true" 
                data-toggle="validator" 

            >
                <h3 class="box-title m-b-20">Sign In</h3>
                <div class="form-group">
                    <div class="col-xs-12">
                        <label for="email" class="sr-only control-label">Email</label>
                        <input 
                            class="form-control" 
                            type="email" 
                            id="email" 
                            placeholder="your@email.com" 
                            data-required-error="Please provide an email address" 
                            data-type-error="Please provide a valid email address" 
                            v-model="data.email" 
                            required
                        >
                        <div class="help-block with-errors text-danger"></div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <label for="password" class="sr-only control-label">Password</label>
                        <input 
                            class="form-control" 
                            type="password" 
                            id="password" 
                            placeholder="Password" 
                            v-model="data.password" 
                            data-required-error="Please provide a password" 
                            required
                        >
                        <div class="help-block with-errors text-danger"></div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-12">
                        <div class="checkbox checkbox-info pull-left p-t-0">
                            <input 
                                id="checkbox-signup" 
                                type="checkbox" 
                                class="filled-in chk-col-light-blue" 
                                v-model="data.remember"
                            >
                            <label for="checkbox-signup"> Remember me </label>
                        </div>
                        <a href="javascript:void(0)" id="to-recover" class="text-dark pull-right">
                            <i class="fa fa-lock m-r-5"></i> Forgot pwd?
                        </a>
                    </div>
                </div>
                <div class="form-group text-center">
                    <div class="col-xs-12 p-b-20">
                        <button 
                            class="btn btn-block btn-lg btn-info btn-rounded" 
                            type="submit" 
                            v-bind:disabled="control.sending"
                        >
                            <i class="fa fa-spinner spin" v-show="control.sending"></i>
                            Log In
                        </button>
                    </div>
                </div>
                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center">
                        Don't have an account? <a href="<?= base_url() . 'signup' ?>" class="text-info m-l-5"><b>Sign Up</b></a>
                    </div>
                </div>
            </form>
            <form 
                class="form-horizontal" 
                id="recover"
            >
                <div class="form-group">
                    <div class="col-xs-12">
                        <h3>Recover Password</h3>
                        <p class="text-muted">Enter your Email and instructions will be sent to you!</p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <label for="recover-email" class="sr-only control-label">Email address</label>
                        <input 
                            class="form-control" 
                            placeholder="your@email.com" 
                            id="recover-email" 
                            type="email" 
                            data-required-error="Please provide an email address" 
                            data-type-error="Please provide a valid email address" 
                            v-model="data.email" 
                            required
                        >
                        <div class="help-block with-errors text-danger"></div>
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button 
                            class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" 
                            type="submit" 
                            v-bind:disabled="control.sending"
                        >
                            <i class="fa fa-spinner spin" v-show="control.sending"></i>
                            Reset
                        </button>
                    </div>
                </div>
                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center">
                        <a href="javascript:void(0)" id="from-recover" class="text-dark text-center">
                            No wait. I got it!
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
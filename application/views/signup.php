<div class="login-register login-signup">
  <div class="login-box card">
      <div class="card-body">
          <form id="signup" class="form-horizontal form-material">
              <h3 class="box-title m-b-20">Sign Up</h3>
              <div class="form-group">
                  <div class="col-xs-12">
                    <label for="firstname" class="sr-only">First Name</label>
                    <input 
                        type="text" 
                        id="firstname" 
                        placeholder="First name" 
                        class="form-control form-control-line" 
                        data-required-error="Please provide your first name" 
                        v-model="data.firstname" 
                        required
                    >
                    <div class="help-block with-errors text-danger"></div>
                  </div>
              </div>
              <div class="form-group">
                  <div class="col-xs-12">
                    <label for="lastname" class="sr-only">Last Name</label>
                    <input 
                        type="text" 
                        id="lastname" 
                        placeholder="Last name" 
                        class="form-control form-control-line" 
                        data-required-error="Please provide your last name" 
                        v-model="data.lastname" 
                        required
                    >
                    <div class="help-block with-errors text-danger"></div>
                  </div>
              </div>
              <div class="form-group">
                  <div class="col-xs-12">
                  <label for="email" class="sr-only">Email</label>
                    <input 
                        type="email" 
                        placeholder="your@email.com" 
                        class="form-control form-control-line" 
                        name="email" 
                        id="email" 
                        data-type-error="Please provide a valid email address" 
                        data-required-error="Please provide an email address" 
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
              <div class="form-group">
                  <div class="col-xs-12">
                    <label for="confirm" class="sr-only control-label">Confirm Password</label>
                    <input 
                        class="form-control" 
                        type="password" 
                        id="confirm" 
                        placeholder="Confirm Password" 
                        data-equals="password" 
                        data-equals-error="Passwords do not match" 
                        data-required-error="Re-enter your password" 
                        v-model="data.confirm" 
                        required
                    >
                    <div class="help-block with-errors text-danger"></div>
                  </div>
              </div>
              <div class="form-group row">
                  <div class="col-md-12">
                      <div class="checkbox checkbox-success p-t-0">
                          <input id="checkbox-signup" type="checkbox"  class="filled-in chk-col-light-blue">
                          <label for="checkbox-signup"> I agree to all <a href="javascript:void(0)">Terms</a></label>
                      </div>
                  </div>
              </div>
              <div class="form-group text-center p-b-20">
                  <div class="col-xs-12">
                        <button 
                            class="btn btn-info btn-lg btn-block btn-rounded text-uppercase waves-effect waves-light" 
                            type="submit"
                        >
                        Sign Up
                        </button>
                  </div>
              </div>
              <div class="form-group m-b-0">
                  <div class="col-sm-12 text-center">
                      Already have an account? <a href="<?= base_url() ?>login" class="text-info m-l-5"><b>Sign In</b></a>
                  </div>
              </div>
          </form>
      </div>
  </div>
</div>
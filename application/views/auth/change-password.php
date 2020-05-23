<div class="container">

    <div class="card o-hidden border-0 shadow-lg my-5  col-lg-7 mx-auto">
        <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">
                <!-- <div class="col-lg-5 d-none d-lg-block bg-register-image"></div> -->
                <div class="col-lg">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900">Change Your Password for</h1>
                            <h5 class="mb-4"><?= $this->session->userdata('reset_email'); ?></h5>
                        </div>

                        <?= $this->session->flashdata('message'); ?>
                        <form class="user" method="post" action="<?= base_url('auth/changePassword'); ?>">
                            <div class="form-group">
                                <input type="password" class="form-control form-control-user" id="password1" name="password1" placeholder="New Password">
                                <?= form_error('password1', '<small class="text-danger">', '</small>'); ?>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control form-control-user" id="password2" name="password2" placeholder="Repeat New Password">
                            </div>
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Change Password
                            </button>
                        </form>
                        <hr>
                        <div class="text-center">
                            <a class="small" href="<?= base_url('auth'); ?>">Already have an account? Login!</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <h1 class="h6 text-gray-900 mb-4">SmartPOS - Copyright &copy; Wirianta - <?= date('Y'); ?></h1>
            </div>
        </div>
    </div>

</div>
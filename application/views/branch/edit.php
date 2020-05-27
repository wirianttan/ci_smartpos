<!-- Begin Page Content -->
<div class="container">

    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="row">
        <div class="col-lg">


            <!-- <?= form_open_multipart('branch/add'); ?> -->

            <form class="branch" action="<?= base_url('branch/editbranch/') .  $branch['id']; ?>" method="post">
                <?= $this->session->flashdata('message'); ?>
                <div class="form-row mt-3 justify-content-md-center">
                    <div class="form-group col-md-5">
                        <label for="branchcode">Outlet Code</label>
                        <input type="text" class="form-control" id="branchcode" name="branchcode" value="<?= $branch['branchcode']; ?>" readonly>
                        <div>
                            <?= form_error('branchcode', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="form-group col-md-5">
                        <label for="branchname">Outlet Name</label>
                        <input type="text" class="form-control form-control-user" id="branchname" name="branchname" value="<?= $branch['branchname']; ?>">
                        <div>
                            <?= form_error('branchname', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="form-group col-md-10">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" value="<?= $branch['address']; ?>">
                        <div>
                            <?= form_error('address', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="form-group col-md-10">
                        <label for="city">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?= $branch['city']; ?>">
                        <div>
                            <?= form_error('city', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="form-group col-md-10">
                        <label for="phone">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= $branch['phone']; ?>">
                        <div>
                            <?= form_error('phone', '<small class="text-danger">', '</small>'); ?>
                        </div>
                    </div>
                    <div class="form-group col-md-10">
                        <div class="form-check">
                            <?php if ($branch['is_active'] == 1) : ?>
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <?php else : ?>
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active">
                            <?php endif; ?>
                            <label class="form-check-label" for="gridCheck">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer mt-3">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    <a href="<?= base_url('branch'); ?>" class="btn btn-secondary">Close</a>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>



</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->
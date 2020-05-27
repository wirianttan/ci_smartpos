<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->

    <div class="row">
        <div class="col-md-6">
            <h1 class="h5 mb-4 text-gray-800"><?= $title; ?></h1>
        </div>
        <div class="col-md-6 justify-content-end mb-3">
            <form action="" method="post">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search ..." name="keyword">
                    <div class="input-group-append">
                        <button class="btn btn-secondary" type="submit">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg">
            <div class="row">
                <div class="col-md-4">
                    <a href="<?= base_url('branch/addbranch'); ?>" class="btn btn-primary mb-3">Add New</a>
                </div>

                <div class="col-md-8">
                    <?= $this->pagination->create_links(); ?>
                </div>
            </div>


            <?= $this->session->flashdata('message'); ?>

            <?php if (empty($branch)) : ?>
                <div class="alert alert-danger mt-3" role="alert">
                    No record
                </div>
            <?php else : ?>
                <table class="table table-hover mt-3">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Code</th>
                            <th scope="col">Name</th>
                            <th scope="col">Address</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($branch as $b) : ?>
                            <tr>
                                <th scope="row"><?= ++$start; ?></th>
                                <td><?= $b['branchcode']; ?></td>
                                <td><?= $b['branchname']; ?></td>
                                <td><?= $b['address']; ?></td>
                                <td>
                                    <a class="badge badge-success" href="<?= base_url('branch/editbranch/') . $b['id']; ?>">edit</a>
                                    <a class="badge badge-danger tombol-hapus" id="<?= $b['branchcode'] . ' - ' . $b['branchname']; ?>" href="<?= base_url('branch/deletebranch/') . $b['id']; ?>">delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>



</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Modal -->
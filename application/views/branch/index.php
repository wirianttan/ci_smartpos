<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h5 mb-4 text-gray-800"><?= $title; ?></h1>

    <div class="row">
        <div class="col-lg-8">

            <a href="<?= base_url('branch/addbranch'); ?>" class="btn btn-primary mb-3">Add New</a>

            <?= $this->session->flashdata('message'); ?>

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Code</th>
                        <th scope="col">Name</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($branch as $b) : ?>
                        <tr>
                            <th scope="row"><?= $i; ?></th>
                            <td><?= $b['branchcode']; ?></td>
                            <td><?= $b['branchname']; ?></td>
                            <td>
                                <a class="badge badge-success" href="<?= base_url('branch/editbranch/') . $b['id']; ?>">edit</a>
                                <a class="badge badge-danger" href="<?= base_url('branch/deletebranch/') . $b['id']; ?>">delete</a>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>



</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->

<!-- Modal -->
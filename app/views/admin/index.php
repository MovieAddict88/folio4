<div class="container mt-5">
    <div class="row">
        <div class="col-lg-6">
            <?php Flasher::flash(); ?>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-6">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formModal">
                Add Student
            </button>
        </div>
    </div>

    <h3>Student List</h3>
    <ul class="list-group">
        <?php foreach ($data['students'] as $student) : ?>
            <li class="list-group-item">
                <?= $student['name']; ?>
                <a href="<?= BASEURL . '/admin/delete/' . $student['id']; ?>" class="badge text-bg-danger float-end ms-1" onclick="return confirm('are you sure?');">delete</a>
                <a href="#" class="badge text-bg-success float-end ms-1 tampilModalUbah" data-bs-toggle="modal" data-bs-target="#formModal" data-id="<?= $student['id']; ?>">update</a>
                <a href="<?= BASEURL . '/qrcode/generate/' . $student['id']; ?>" class="badge text-bg-primary float-end ms-1">generate qr</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Modal -->
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="judulModal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="judulModal">Add Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="<?= BASEURL; ?>/admin/add" method="post">
            <input type="hidden" name="id" id="id">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="mb-3">
                <label for="course" class="form-label">Course</label>
                <input type="text" class="form-control" id="course" name="course">
            </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Add Data</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    $(function() {
        $('.tampilModalUbah').on('click', function() {
            $('#judulModal').html('Update Student');
            $('.modal-footer button[type=submit]').html('Update Data');
            $('.modal-body form').attr('action', '<?= BASEURL; ?>/admin/update');

            const id = $(this).data('id');

            $.ajax({
                url: '<?= BASEURL; ?>/admin/getupdate',
                data: {id : id},
                method: 'post',
                dataType: 'json',
                success: function(data) {
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#course').val(data.course);
                    $('#id').val(data.id);
                }
            });
        });
    });
</script>
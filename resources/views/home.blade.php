<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>AJAX CRUD</h1>
        <div align="right">
            <button type="button" name="tambah_data" id="tambah_data"
            class="btn btn-primary btn-sm">Tambah Data</button>
        </div>
        <br>
            <table id="user_table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th width="35%">Deskripsi</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>

    <div class="modal fade" id="formModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Tambah Data</h4>
                </div>
                <div class="modal-body">
                    <span id="form_result"></span>
                    <form method="POST" class="form-horizontal" id="tambah_form">
                        @csrf
                        <div class="form-group">
                            <label class="control-label col-md-4">Judul</label>
                            <div class="col-md-8">
                                <input type="text" name="title" id="title" class="form-control">
                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <label class="control-label col-md-4">Deskripsi</label>
                            <div class="col-md-8">
                                <textarea name="description" id="description" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                        <br>
                        <div class="form-group" align="center">
                            <input type="hidden" name="action" id="action" value="Add">
                            <input type="hidden" name="hidden_id" id="hidden_id">
                            <input type="submit" name="action_button" id="action_button" class="btn btn-primary" value="Add">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmModal" role="dialog" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                    >&times;</button>
                    <h2 class="modal-title">Konfirmasi</h2>
                </div>
                <div class="modal-body">
                    <h4 align="center" style="margin: 0;">Apakah anda yakin akan menghapus data ini?</h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" name="ok_button" id="ok_button"
                    >Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function (){

            // Inisialisasi datatable
            $('#user_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('books.index') }}",
                },
                columns: [
                    {
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ]
            });

            // Memunculkan modal
            $('#tambah_data').click(function (){
                $('.modal-title').text('Tambah Data');
                $('#action_button').val('Tambah');
                $('#action').val('Add');
                $('#form_result').val('');
                $('#tambah_form')[0].reset();

                $('#formModal').modal('show');
            });

            // Saat tombol add di klik, ini eventnya
            $('#tambah_form').on('submit', function(event){
                event.preventDefault();
                var actionUrl = '';

                // Cek Kondisi jika mengklik tombol add
                if($('#action').val() == 'Add')
                {
                    actionUrl = "{{ route('books.store') }}";
                }

                // Cek Kondisi jika mengklik tombol edit
                if($('#action').val() == 'Edit')
                {
                    actionUrl = "{{ route('books.update') }}";
                }

                $.ajax({
                    url: actionUrl,
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(data)
                    {
                        var html = '';

                        // Pesan error
                        if(data.errors)
                        {
                            html = '<div class="alert alert-danger">';
                            for(var count = 0; count < data.errors.length; count++)
                            {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                        }

                        // Pesan sukses
                        if(data.success)
                        {
                            html = '<div class="alert alert-success">'+ data.success +'</div>';
                            // Mereset form jika data sukses ditambahkan
                            $('#tambah_form')[0].reset();
                            $('#user_table').DataTable().ajax.reload();
                            location.reload(true);
                        }

                        // Memunculkan pesan sukses atau error dengan memanggil variabel "html"
                        $('#form_result').html(html);
                    }
                });
            });

            // Fungsi edit
            $(document).on('click', '.edit', function(){
                // Mengambil / fetch ID dari button hidden
                var id = $(this).attr('id');
                $('#form_result').html('');
                $.ajax({
                    url: "/books/"+id+"/edit",
                    dataType: "JSON",
                    success: function(data)
                    {
                        $('#title').val(data.result.title);
                        $('#description').val(data.result.description);
                        $('#hidden_id').val(id);
                        $('.modal-title').text('Edit Data');
                        $('#action_button').val('Edit');
                        $('#action').val('Edit');
                        $('#formModal').modal('show');
                    }
                })
            });

            var user_id;

            $(document).on('click', '.delete', function(){
                user_id = $(this).attr('id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function(){
                $.ajax({
                    url: "/books/destroy/"+user_id,
                    beforeSend:function(){
                        $('#ok_button').text('Deleting...');
                    },
                    success:function(data){
                        setTimeout(function(){
                            $('#confirmModal').modal('hide');
                            $('#user_table').DataTable().ajax.reload();
                            alert('Data Deleted');
                        }, 500);
                    }
                });
            });
        });
    </script>
</body>
</html>

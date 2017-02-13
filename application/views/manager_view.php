<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url('assets/favicon-32x32.png') ?>">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Manager</title>

        <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/datatables/css/dataTables.bootstrap.css') ?>" rel="stylesheet">
        <link href="<?php echo base_url('assets/navbar.css') ?>" rel="stylesheet">

    </head>
    <body>

        <div class="container">

            <!-- Static navbar -->
            <?php $this->load->view('navbar'); ?>

            <h3>Manager</h3>
            <br />
            <button class="btn btn-success" onclick="add_user()"><i class="glyphicon glyphicon-plus"></i> Add User</button>
            <br />
            <br />
            <table id="table" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Status</th>
                        <th>Roles</th>
                        <th style="width:150px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>

                <tfoot>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Status</th>
                        <th>Roles</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <script src="<?php echo base_url('assets/jquery/jquery-2.2.2.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/bootstrap/js/bootstrap.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js') ?>"></script>
        <script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.js') ?>"></script>

        <?php $class = strtolower($this->router->fetch_class()); ?>
        <script type="text/javascript">

                var save_method; //for save method string
                var table;
                $(document).ready(function () {
                    table = $('#table').DataTable({
                        "processing": true, //Feature control the processing indicator.
                        "serverSide": true, //Feature control DataTables' server-side processing mode.

                        // Load data for the table's content from an Ajax source
                        "ajax": {
                            "url": "<?php echo site_url($class . '/ajax_list') ?>",
                            "type": "POST"
                        },
                        //Set column definition initialisation properties.
                        "columnDefs": [
                            {
                                "targets": [-1], //last column
                                "orderable": false, //set not orderable
                            },
                        ],
                    });
                });

                function add_user()
                {
                    save_method = 'add';
                    $('#form')[0].reset(); // reset form on modals
                    $('#modal_form').modal('show'); // show bootstrap modal
                    $('.modal-title').text('Add User'); // Set Title to Bootstrap modal title
                }

                function edit_user(id)
                {
                    save_method = 'update';
                    $('#form')[0].reset(); // reset form on modals

                    //Ajax Load data from ajax
                    $.ajax({
                        url: "<?php echo site_url($class . '/ajax_edit/') ?>/" + id,
                        type: "GET",
                        dataType: "JSON",
                        success: function (data)
                        {

                            $('[name="_id"]').val(data._id);
                            $('[name="username"]').val(data.username);
                            $('[name="password"]').val(data.password);
                            $('[name="password_old"]').val(data.password);
                            $('[name="status"]').val(data.status);
                            //$('[name="roles"]').val(data.roles);

                            var i = 0;
                            var roles = JSON.parse(data.roles);
                            while ($('#form')[0]['roles_' + i]) {
                                if ($.inArray($('#form')[0]['roles_' + i].value, roles) > -1) {
                                    $('#form')[0]['roles_' + i].checked = true;
                                } else {
                                    $('#form')[0]['roles_' + i].checked = false;
                                }
//                                if ($('#form')[0]['roles_' + i].checked) {
//                                    roles.push($('#form')[0]['roles_' + i].value);
//                                }
                                i++;
                            }

                            $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                            $('.modal-title').text('Edit Apps'); // Set title to Bootstrap modal title

                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            alert('Error get data from ajax');
                        }
                    });
                }

                function reload_table()
                {
                    table.ajax.reload(null, false); //reload datatable ajax
                }

                function save()
                {
                    var url;
                    if (save_method == 'add')
                    {
                        url = "<?php echo site_url($class . '/ajax_add') ?>";
                    } else
                    {
                        url = "<?php echo site_url($class . '/ajax_update') ?>";
                    }

                    var formData = new FormData();
                    formData.append('_id', $('#form')[0]._id.value);
                    formData.append('username', $('#form')[0].username.value);
                    formData.append('password', $('#form')[0].password.value);
                    formData.append('password_old', $('#form')[0].password_old.value);
                    formData.append('status', $('#form')[0].status.value);

                    var i = 0;
                    var roles = [];
                    while ($('#form')[0]['roles_' + i]) {
                        if ($('#form')[0]['roles_' + i].checked) {
                            roles.push($('#form')[0]['roles_' + i].value);
                        }
                        i++;
                    }
                    formData.append('roles', JSON.stringify(roles).replace(new RegExp(',', 'g'), ', '));

                    // ajax adding data to database
                    $.ajax({
                        url: url,
                        type: "POST",
                        //data: $('#form').serialize(),
                        //dataType: "JSON",
                        data: formData,
                        processData: false,
                        contentType: false,
                        enctype: 'multipart/form-data',
                        success: function (data)
                        {
                            //if success close modal and reload ajax table
                            data = JSON.parse(data);
                            if (data.status) {
                                $('#modal_form').modal('hide');
                                reload_table();
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {
                            alert('Error adding / update data');
                        }
                    });
                }

                function delete_user(id)
                {
                    if (confirm('Are you sure delete this data?'))
                    {
                        // ajax delete data to database
                        $.ajax({
                            url: "<?php echo site_url($class . '/ajax_delete') ?>/" + id,
                            type: "POST",
                            dataType: "JSON",
                            success: function (data)
                            {
                                //if success reload ajax table
                                $('#modal_form').modal('hide');
                                reload_table();
                            },
                            error: function (jqXHR, textStatus, errorThrown)
                            {
                                alert('Error delete data');
                            }
                        });

                    }
                }

        </script>

        <!-- Bootstrap modal -->
        <div class="modal fade" id="modal_form" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title">User Form</h3>
                    </div>
                    <div class="modal-body form">
                        <form action="#" id="form" class="form-horizontal">
                            <input type="hidden" value="" name="_id"/>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3">Username</label>
                                    <div class="col-md-9">
                                        <input name="username" placeholder="Username" class="form-control" type="text">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Password</label>
                                    <div class="col-md-9">
                                        <input name="password" placeholder="Password" class="form-control" type="password">
                                        <input name="password_old" placeholder="Password" class="hidden" type="password">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Status</label>
                                    <div class="col-md-9">
                                        <select name="status" class="form-control">
                                            <option value="active">active</option>
                                            <option value="non active">non active</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Roles</label>
                                    <?php
                                    $arr_roles = ['Billionaire', 'cloudcode', 'UADashboard', 'PushAdmin', 'Manager', 'Konglomerat', 'Almighty'];
                                    sort($arr_roles);
                                    ?>
                                    <div class="col-md-9">
                                        <?php foreach ($arr_roles as $key => $value) { ?>
                                            <input name="roles_<?php echo $key; ?>" type="checkbox" value="<?php echo $value; ?>">
                                            <?php echo $value; ?><br>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Save</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- End Bootstrap modal -->
    </body>
</html>
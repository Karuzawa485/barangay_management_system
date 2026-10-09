<section class="app-page">
    <div class="page-header-panel">
        <div>
            <h1>Users</h1>
            <p>Manage administrator and staff accounts with a cleaner dark control panel.</p>
        </div>
        <div class="page-actions">
            <button class="btn-modern btn-primary-glow" type="button" id="create_new"><i class="fa-solid fa-user-plus"></i><span>Add New User</span></button>
        </div>
    </div>

    <div class="content-panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">User Directory</h2>
                <div class="panel-subtitle">Maintain access and account roles for the barangay management team.</div>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-shell">
                <table class="table" id="users-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT * FROM `admin_list` where admin_id != 1 order by `fullname` asc";
                        $qry = $conn->query($sql);
                        $i = 1;
                        while($row = $qry->fetchArray()):
                            $parts = preg_split('/\s+/', trim($row['fullname']));
                            $avatar = strtoupper(substr($parts[0] ?? 'U',0,1) . substr($parts[1] ?? $parts[0] ?? 'S',0,1));
                        ?>
                        <tr>
                            <td class="mono"><?php echo $i++; ?></td>
                            <td>
                                <div class="resident-chip">
                                    <div class="avatar-chip"><?php echo $avatar ?></div>
                                    <div><?php echo $row['fullname'] ?></div>
                                </div>
                            </td>
                            <td class="mono"><?php echo $row['username'] ?></td>
                            <td><span class="badge-soft <?php echo ($row['type'] == 1) ? 'purple' : 'cyan' ?>"><?php echo ($row['type'] == 1)? "Administrator" : 'Staff' ?></span></td>
                            <td>
                                <div class="panel-toolbar">
                                    <button type="button" class="btn-modern-sm btn-info-soft edit_data" data-id="<?php echo $row['admin_id'] ?>"><i class="fa-regular fa-pen-to-square"></i><span>Edit</span></button>
                                    <button type="button" class="btn-modern-sm btn-danger-soft delete_data" data-id="<?php echo $row['admin_id'] ?>" data-name="<?php echo $row['fullname'] ?>"><i class="fa-regular fa-trash-can"></i><span>Delete</span></button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<script>
    $(function(){
        $('#create_new').click(function(){
            uni_modal('Add New User',"manage_admin.php")
        })
        $('.edit_data').click(function(){
            uni_modal('Edit User Details',"manage_admin.php?id="+$(this).attr('data-id'))
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from list?",'delete_data',[$(this).attr('data-id')])
        })
        $('#users-table').dataTable({
            columnDefs: [{ orderable: false, targets: [4] }]
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_admin',
            method:'POST',
            data:{id:$id},
            dataType:'JSON',
            error:err=>{
                console.log(err)
                alert("An error occurred.")
                $('#confirm_modal button').attr('disabled',false)
            },
            success:function(resp){
                if(resp.status == 'success'){
                    location.reload()
                }else{
                    alert("An error occurred.")
                    $('#confirm_modal button').attr('disabled',false)
                }
            }
        })
    }
</script>

<section class="app-page">
    <div class="page-header-panel">
        <div>
            <h1>Residents Address List</h1>
            <p>Manage resident address entries, status records, and printable listings for barangay documentation.</p>
        </div>
        <div class="page-actions">
            <button class="btn-modern btn-primary-glow" type="button" id="create_new"><i class="fa-solid fa-plus"></i><span>Add New Entry</span></button>
            <button class="btn-modern btn-ghost-dark" type="button" id="print"><i class="fa-solid fa-print"></i><span>Print List</span></button>
        </div>
    </div>

    <div class="content-panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Resident Address Register</h2>
                <div class="panel-subtitle">Track full names, age, address location, and current status.</div>
            </div>
        </div>
        <div class="panel-body">
            <div id="outprint" class="table-shell">
                <table class="table" id="tbl-list">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Age</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT * FROM complaint_list order by strftime('%s',date_created) asc";
                        $qry = $conn->query($sql);
                        $i = 1;
                        while($row = $qry->fetchArray()):
                            $parts = preg_split('/\s+/', trim($row['full_name']));
                            $avatar = strtoupper(substr($parts[0] ?? 'R',0,1) . substr($parts[1] ?? $parts[0] ?? 'A',0,1));
                        ?>
                        <tr>
                            <td class="mono"><?php echo $i++; ?></td>
                            <td>
                                <div class="resident-chip">
                                    <div class="avatar-chip"><?php echo $avatar ?></div>
                                    <div><?php echo $row['full_name'] ?></div>
                                </div>
                            </td>
                            <td class="mono"><?php echo $row['age'] ?></td>
                            <td><?php echo $row['address'] ?></td>
                            <td><span class="badge-soft cyan"><?php echo $row['status'] ?></span></td>
                            <td>
                                <div class="panel-toolbar">
                                    <button type="button" class="btn-modern-sm btn-primary-soft view_data" data-id="<?php echo $row['complaint_id'] ?>"><i class="fa-regular fa-eye"></i><span>View</span></button>
                                    <button type="button" class="btn-modern-sm btn-info-soft edit_data" data-id="<?php echo $row['complaint_id'] ?>"><i class="fa-regular fa-pen-to-square"></i><span>Edit</span></button>
                                    <button type="button" class="btn-modern-sm btn-danger-soft delete_data" data-id="<?php echo $row['complaint_id'] ?>"><i class="fa-regular fa-trash-can"></i><span>Delete</span></button>
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

<noscript>
    <div class="d-flex w-100 align-items-center">
        <div class="col-2 px-3">
            <center><img src="<?php echo is_file('./../uploads/logo.png') ? './../uploads/logo.png' : './../images/no-image-available.png' ?>" alt="Barangay Logo" class="img-fluid rounded-0" width="100px" height="100px"></center>
        </div>
        <div class="col-8 flex-grow-1 lh-1">
            <p class="m-0 text-center">Republic of the Philippines</p>
            <p class="m-0 text-center"><?php echo $_SESSION['system_info']['city'] ?></p>
            <div class="clearfix"></div>
            <p class="fw-bold text-center"><large><?php echo $_SESSION['system_info']['barangay_name'] ?></large></p>
            <p class="fw-bold text-center">Residents Adress List</p>
        </div>
        <div class="col-2"></div>
    </div>
    <hr>
</noscript>
<script>
    var dtable;
    $(function(){
        $('#create_new').click(function(){
            uni_modal('Add Address Resident',"manage_complaint.php",'mid-large')
        })
        $('.edit_data').click(function(){
            uni_modal('Edit Complaint\'s Details',"manage_complaint.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.view_data').click(function(){
            uni_modal('Complaint Details',"view_complaint.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this Complaint from List?",'delete_data',[$(this).attr('data-id')])
        })
        dtable = $('#tbl-list').dataTable({
            columnDefs: [
                { orderable: false, targets: [5] }
            ]
        })
        $('#print').click(function(){
            dtable.fnDestroy()
            var _p = $('#outprint').clone()
            var _h = $('head').clone()
            var _header = $('noscript').html()
            var el = $('<div>')
            _p.find('#tbl-list tr').each(function(){
                $(this).find('td,th').last().remove()
            })
            el.append(_h)
            el.append(_header)
            el.append(_p)
            
            var nw = window.open("","_blank","width=1000,height=900,top=50,left=250")
            nw.document.write(el.html())
            nw.document.close()
            setTimeout(() => {
                nw.print()
                setTimeout(() => {
                    nw.close()
                    dtable = $('#tbl-list').dataTable({
                        columnDefs: [
                            { orderable: false, targets: [5] }
                        ]
                    })
                }, 200)
            }, 500)
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_complaint',
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

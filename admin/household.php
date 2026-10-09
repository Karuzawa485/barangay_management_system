<section class="app-page">
    <div class="page-header-panel">
        <div>
            <h1>Households</h1>
            <p>Review registered household heads, contact details, and barangay profile information in one place.</p>
        </div>
        <div class="page-actions">
            <button class="btn-modern btn-primary-glow" type="button" id="create_new"><i class="fa-solid fa-plus"></i><span>Add New Household</span></button>
        </div>
    </div>

    <div class="content-panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Household Directory</h2>
                <div class="panel-subtitle">Search, review, and manage resident household records.</div>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-shell">
                <table class="table" id="household-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date Added</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Details</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT h.*,(h.lastname || ', ' || h.firstname || ', ' || h.middlename) as fullname, p.purok FROM household_list h inner join `purok_list` p on h.purok_id = p.purok_id order by `fullname` asc";
                        $qry = $conn->query($sql);
                        $i = 1;
                        while($row = $qry->fetchArray()):
                            $initials = strtoupper(substr($row['firstname'] ?? '', 0, 1) . substr($row['lastname'] ?? '', 0, 1));
                        ?>
                        <tr>
                            <td class="mono"><?php echo $i++; ?></td>
                            <td class="mono text-muted"><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
                            <td>
                                <div class="resident-chip">
                                    <div class="avatar-chip"><?php echo $initials ?: 'HH' ?></div>
                                    <div><?php echo $row['fullname'] ?></div>
                                </div>
                            </td>
                            <td class="mono"><?php echo $row['contact'] ?></td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span><span class="text-muted">House #:</span> <?php echo $row['house_no'] ?></span>
                                    <span><span class="text-muted">Purok:</span> <?php echo $row['purok'] ?></span>
                                    <span><span class="text-muted">Sex:</span> <?php echo !empty($row['sex']) ? $row['sex'] : 'N/A' ?></span>
                                    <span><span class="text-muted">Civil Status:</span> <?php echo !empty($row['civil_status']) ? $row['civil_status'] : 'N/A' ?></span>
                                    <span><span class="text-muted">DOB:</span> <?php echo !empty($row['date_of_birth']) ? date("M d, Y", strtotime($row['date_of_birth'])) : 'N/A' ?></span>
                                    <span><span class="text-muted">Nationality:</span> <?php echo !empty($row['nationality']) ? $row['nationality'] : 'N/A' ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="panel-toolbar">
                                    <button type="button" class="btn-modern-sm btn-primary-soft view_data" data-id="<?php echo $row['household_id'] ?>"><i class="fa-regular fa-eye"></i><span>View</span></button>
                                    <button type="button" class="btn-modern-sm btn-info-soft edit_data" data-id="<?php echo $row['household_id'] ?>"><i class="fa-regular fa-pen-to-square"></i><span>Edit</span></button>
                                    <button type="button" class="btn-modern-sm btn-danger-soft delete_data" data-id="<?php echo $row['household_id'] ?>" data-name="<?php echo $row['fullname'] ?>"><i class="fa-regular fa-trash-can"></i><span>Delete</span></button>
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
            uni_modal('Add Household',"manage_household.php",'mid-large')
        })
        $('.edit_data').click(function(){
            uni_modal('Edit Household Details',"manage_household.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.view_data').click(function(){
            uni_modal('Household Details',"view_household.php?id="+$(this).attr('data-id'),'mid-large')
        })
        $('.delete_data').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from Household List?",'delete_data',[$(this).attr('data-id')])
        })
        $('#household-table').dataTable({
            columnDefs: [
                { orderable: false, targets: [4,5] }
            ]
        })
    })
    function delete_data($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_household',
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

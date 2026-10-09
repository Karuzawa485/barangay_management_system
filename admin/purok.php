<section class="app-page">
    <div class="page-header-panel">
        <div>
            <h1>Purok List</h1>
            <p>Organize barangay purok records and keep location groupings clean and up to date.</p>
        </div>
        <div class="page-actions">
            <button class="btn-modern btn-primary-glow" type="button" id="new_purok"><i class="fa-solid fa-plus"></i><span>Add Purok</span></button>
        </div>
    </div>

    <div class="content-panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Purok Directory</h2>
                <div class="panel-subtitle">Quick access list for purok setup, editing, and cleanup.</div>
            </div>
        </div>
        <div class="panel-body">
            <div class="row g-3">
                <?php 
                $dept_qry = $conn->query("SELECT * FROM `purok_list` order by `purok` asc");
                while($row = $dept_qry->fetchArray()):
                ?>
                <div class="col-md-6 col-xl-4">
                    <div class="detail-item h-100 d-flex align-items-center justify-content-between">
                        <div>
                            <div class="detail-label">Purok</div>
                            <div class="detail-value"><?php echo $row['purok'] ?></div>
                        </div>
                        <div class="panel-toolbar">
                            <button type="button" class="btn-modern-sm btn-info-soft edit_purok" data-id="<?php echo $row['purok_id'] ?>" data-name="<?php echo $row['purok'] ?>"><i class="fa-regular fa-pen-to-square"></i></button>
                            <button type="button" class="btn-modern-sm btn-danger-soft delete_purok" data-id="<?php echo $row['purok_id'] ?>" data-name="<?php echo $row['purok'] ?>"><i class="fa-regular fa-trash-can"></i></button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>
<script>
    $(function(){
        $('#new_purok').click(function(){
            uni_modal('Add New Purok',"manage_purok.php")
        })
        $('.edit_purok').click(function(){
            uni_modal('Edit Purok Details',"manage_purok.php?id="+$(this).attr('data-id'))
        })
        $('.delete_purok').click(function(){
            _conf("Are you sure to delete <b>"+$(this).attr('data-name')+"</b> from purok List?",'delete_purok',[$(this).attr('data-id')])
        })
    })
    function delete_purok($id){
        $('#confirm_modal button').attr('disabled',true)
        $.ajax({
            url:'./../Actions.php?a=delete_purok',
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

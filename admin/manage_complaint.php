<?php
require_once("./../DBConnection.php");
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `complaint_list` where complaint_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="form-shell">
    <form action="" id="complaint-form">
        <input type="hidden" name="id" value="<?php echo isset($complaint_id) ? $complaint_id : '' ?>">
        <div class="form-group">
            <label for="full_name" class="control-label">Full Name</label>
            <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo isset($full_name) ? $full_name : '' ?>" required>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="age" class="control-label">Age</label>
                    <input type="number" name="age" id="age" min="0" class="form-control mono" value="<?php echo isset($age) ? $age : '' ?>" required>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label for="status" class="control-label">Status</label>
                    <input type="text" name="status" id="status" class="form-control" value="<?php echo isset($status) ? $status : '' ?>" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="address" class="control-label">Address</label>
            <textarea rows="3" name="address" id="address" class="form-control" required><?php echo isset($address) ? $address : '' ?></textarea>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#complaint-form').submit(function(e){
            e.preventDefault()
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>').addClass('pop_msg alert')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('Saving...')
            $.ajax({
                url:'./../Actions.php?a=save_complaint',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert-danger').text("An error occurred.")
                    _this.prepend(_el)
                    $('#uni_modal button').attr('disabled',false)
                    $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload()
                    }else{
                        _el.addClass('alert-danger').text(resp.msg)
                        _this.prepend(_el)
                    }
                    $('#uni_modal button').attr('disabled',false)
                    $('#uni_modal button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>

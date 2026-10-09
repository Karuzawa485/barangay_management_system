<?php
require_once("../DBConnection.php");
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `admin_list` where admin_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="form-shell">
    <form action="" id="user-form">
        <input type="hidden" name="id" value="<?php echo isset($admin_id) ? $admin_id : '' ?>">
        <div class="form-group">
            <label for="fullname" class="control-label">Full Name</label>
            <input type="text" name="fullname" id="fullname" required class="form-control" value="<?php echo isset($fullname) ? $fullname : '' ?>">
        </div>
        <div class="form-group">
            <label for="username" class="control-label">Username</label>
            <input type="text" name="username" id="username" required class="form-control" value="<?php echo isset($username) ? $username : '' ?>">
        </div>
        <div class="form-group">
            <label for="type" class="control-label">Type</label>
            <select name="type" id="type" class="form-select" required>
                <option value="1" <?php echo isset($type) && $type == 1 ? 'selected' : '' ?>>Administrator</option>
                <option value="2" <?php echo isset($type) && $type == 2 ? 'selected' : '' ?>>Staff</option>
            </select>
        </div>
    </form>
</div>

<script>
    $(function(){
        $('#user-form').submit(function(e){
            e.preventDefault()
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>').addClass('pop_msg alert')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('Saving...')
            $.ajax({
                url:'./../Actions.php?a=save_admin',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert-danger').text("An error occurred.")
                    _this.prepend(_el)
                    $('#uni_modal button').attr('disabled',false)
                    $('#uni_modal button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert-success').text(resp.msg)
                        _this.prepend(_el)
                        $('#uni_modal').on('hide.bs.modal',function(){
                            location.reload()
                        })
                        if("<?php echo isset($admin_id) ?>" != 1){
                            _this.get(0).reset()
                        }
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

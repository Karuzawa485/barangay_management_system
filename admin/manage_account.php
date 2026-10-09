<?php
require_once("../DBConnection.php");
$qry = $conn->query("SELECT * FROM `admin_list` where admin_id = '{$_SESSION['admin_id']}'");
foreach($qry->fetchArray() as $k => $v){
    $$k = $v;
}
?>
<section class="app-page">
    <div class="page-header-panel">
        <div>
            <h1>Manage Account</h1>
            <p>Update your personal access details and keep your administrator credentials secure.</p>
        </div>
    </div>

    <div class="content-panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Profile Settings</h2>
                <div class="panel-subtitle">Change your display name, username, and password.</div>
            </div>
        </div>
        <div class="panel-body">
            <form action="" id="user-form" class="form-shell" style="max-width: 720px;">
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
                    <label for="password" class="control-label">New Password</label>
                    <input type="password" name="password" id="password" class="form-control" value="">
                </div>
                <div class="form-group">
                    <label for="old_password" class="control-label">Old Password</label>
                    <input type="password" name="old_password" id="old_password" class="form-control" value="">
                </div>
                <div class="form-text mb-3">Leave the new password blank if you only want to update your profile details.</div>
                <div class="panel-toolbar justify-content-end">
                    <button class="btn-modern-sm btn-primary-glow">Update Account</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    $(function(){
        $('#user-form').submit(function(e){
            e.preventDefault()
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
            _el.addClass('pop_msg alert')
            $.ajax({
                url:'./../Actions.php?a=update_credentials',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert-danger').text("An error occurred.")
                    _this.prepend(_el)
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload()
                    }else{
                        _el.addClass('alert-danger').text(resp.msg)
                        _this.prepend(_el)
                    }
                }
            })
        })
    })
</script>

<style>
    #logo-img{
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 24px;
        border: 1px solid var(--border);
        background: rgba(7,11,20,0.5);
        padding: 0.5rem;
    }
</style>
<section class="app-page">
    <div class="page-header-panel">
        <div>
            <h1>Barangay & System Info</h1>
            <p>Update the identity, jurisdiction, and visual branding used across the barangay management system.</p>
        </div>
    </div>

    <div class="content-panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">System Configuration</h2>
                <div class="panel-subtitle">Edit the official barangay details and upload the logo displayed in reports.</div>
            </div>
        </div>
        <div class="panel-body">
            <form action="" id="sys-info" class="form-shell">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="barangay_name" class="control-label">Barangay Name</label>
                            <input type="text" name="barangay_name" class="form-control" value="<?php echo isset($_SESSION['system_info']['barangay_name']) ? $_SESSION['system_info']['barangay_name'] : "" ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="city" class="control-label">City/Municipality</label>
                            <input type="text" name="city" class="form-control" value="<?php echo isset($_SESSION['system_info']['city']) ? $_SESSION['system_info']['city'] : "" ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="province" class="control-label">Province</label>
                            <input type="text" name="province" class="form-control" value="<?php echo isset($_SESSION['system_info']['province']) ? $_SESSION['system_info']['province'] : "" ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="zip_code" class="control-label">Zip Code</label>
                            <input type="text" name="zip_code" class="form-control mono" value="<?php echo isset($_SESSION['system_info']['zip_code']) ? $_SESSION['system_info']['zip_code'] : "" ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="logo" class="control-label">Logo</label>
                            <input type="file" name="logo" id="logo" class="form-control" accept="image/png,image/jpeg" onchange="display_img(this)">
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                        <img src="<?php echo is_file('./../uploads/logo.png') ? './../uploads/logo.png' : './../images/no-image-available.png' ?>" id="logo-img" alt="Barangay Logo">
                    </div>
                </div>
                <div class="panel-toolbar mt-3 justify-content-end">
                    <button class="btn-modern-sm btn-primary-glow" type="submit">Save</button>
                    <button class="btn-modern-sm btn-ghost-dark" type="reset">Reset</button>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    function display_img(input){
        if (input.files && input.files[0]) {
            var reader = new FileReader()
            reader.onload = function (e) {
                $('#logo-img').attr('src', e.target.result)
            }
            reader.readAsDataURL(input.files[0])
        }
    }
    $(function(){
        $('#sys-info').submit(function(e){
            e.preventDefault()
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
            _el.addClass('pop_msg alert')
            $('.panel-body button').attr('disabled',true)
            $('.panel-body button[type="submit"]').text('saving data...')
            $.ajax({
                url:'./../Actions.php?a=save_settings',
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
                    $('.panel-body button').attr('disabled',false)
                    $('.panel-body button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload()
                    }else{
                        _el.addClass('alert-danger').text(resp.msg)
                        _this.prepend(_el)
                    }
                    $('.panel-body button').attr('disabled',false)
                    $('.panel-body button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>

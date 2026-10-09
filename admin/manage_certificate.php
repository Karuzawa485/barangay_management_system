<?php
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM certificate_list where certificate_id = '{$_GET['id']}'");
    $row = $qry->fetchArray();
    if($row){
        foreach($row as $k => $v){
            $$k = $v;
        }
    }
}
$type = isset($_GET['type']) ? $_GET['type'] : (isset($type) ? $type : '');
?>
<div class="form-shell">
    <form action="" id="certificate-form">
        <input type="hidden" name="id" value="<?php echo isset($certificate_id) ? $certificate_id : '' ?>">
        <input type="hidden" name="type" value="<?php echo $type ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="control_number" class="control-label">Control Number</label>
                    <input type="text" name="control_number" id="control_number" class="form-control mono" value="<?php echo isset($control_number) ? $control_number : '' ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="date_issued" class="control-label">Date Issued</label>
                    <input type="date" name="date_issued" id="date_issued" class="form-control mono" value="<?php echo isset($date_issued) ? $date_issued : date('Y-m-d') ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="full_name" class="control-label">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo isset($full_name) ? $full_name : '' ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="address" class="control-label">Address</label>
                    <input type="text" name="address" id="address" class="form-control" value="<?php echo isset($address) ? $address : '' ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="age" class="control-label">Age</label>
                    <input type="number" name="age" id="age" class="form-control mono" value="<?php echo isset($age) ? $age : '' ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="civil_status" class="control-label">Civil Status</label>
                    <select name="civil_status" id="civil_status" class="form-select" required>
                        <option value="" disabled>Select Status</option>
                        <option value="Single" <?php echo isset($civil_status) && $civil_status == 'Single' ? 'selected' : '' ?>>Single</option>
                        <option value="Married" <?php echo isset($civil_status) && $civil_status == 'Married' ? 'selected' : '' ?>>Married</option>
                        <option value="Widowed" <?php echo isset($civil_status) && $civil_status == 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                        <option value="Divorced" <?php echo isset($civil_status) && $civil_status == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                        <option value="Separated" <?php echo isset($civil_status) && $civil_status == 'Separated' ? 'selected' : '' ?>>Separated</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="purpose" class="control-label">Purpose</label>
                    <input type="text" name="purpose" id="purpose" class="form-control" value="<?php echo isset($purpose) ? $purpose : '' ?>" required>
                </div>
            </div>
            <?php if($type == 'clearance'): ?>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="cedula_number" class="control-label">Cedula Number</label>
                    <input type="text" name="cedula_number" id="cedula_number" class="form-control mono" value="<?php echo isset($cedula_number) ? $cedula_number : '' ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="or_number" class="control-label">OR Number</label>
                    <input type="text" name="or_number" id="or_number" class="form-control mono" value="<?php echo isset($or_number) ? $or_number : '' ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expiration_date" class="control-label">Expiration Date</label>
                    <input type="date" name="expiration_date" id="expiration_date" class="form-control mono" value="<?php echo isset($expiration_date) ? $expiration_date : '' ?>" required>
                </div>
            </div>
            <?php elseif($type == 'indigency'): ?>
            <div class="col-12">
                <div class="form-group">
                    <label for="specific_purpose" class="control-label">Specific Purpose</label>
                    <input type="text" name="specific_purpose" id="specific_purpose" class="form-control" value="<?php echo isset($specific_purpose) ? $specific_purpose : '' ?>" placeholder="e.g., medical assistance, scholarship, legal aid" required>
                </div>
            </div>
            <?php elseif($type == 'certification'): ?>
            <div class="col-12">
                <div class="form-group">
                    <label for="certification_body" class="control-label">Certification Body</label>
                    <textarea name="certification_body" id="certification_body" class="form-control" rows="3" required><?php echo isset($certification_body) ? $certification_body : '' ?></textarea>
                    <div class="form-text">Describe the specific fact being certified, such as residency duration or land occupancy.</div>
                </div>
            </div>
            <?php endif; ?>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="punong_barangay" class="control-label">Punong Barangay</label>
                    <input type="text" name="punong_barangay" id="punong_barangay" class="form-control" value="<?php echo isset($punong_barangay) ? $punong_barangay : '' ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="receipt_number" class="control-label">Official Receipt Number</label>
                    <input type="text" name="receipt_number" id="receipt_number" class="form-control mono" value="<?php echo isset($receipt_number) ? $receipt_number : '' ?>" required>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('#certificate-form').submit(function(e){
            e.preventDefault()
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>').addClass('pop_msg alert')
            _this.closest('.modal').find('button').attr('disabled',true)
            _this.closest('.modal').find('button[type="submit"]').text('Saving...')
            $.ajax({
                url:'./../Actions.php?a=save_certificate',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert-danger').text("An error occurred.")
                    _this.prepend(_el)
                    _this.closest('.modal').find('button').attr('disabled',false)
                    _this.closest('.modal').find('button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert-success').text(resp.msg)
                        _this.prepend(_el)
                        setTimeout(() => {
                            _this.closest('.modal').modal('hide')
                            location.reload()
                        }, 1200)
                    }else{
                        _el.addClass('alert-danger').text(resp.msg)
                        _this.prepend(_el)
                    }
                    _this.closest('.modal').find('button').attr('disabled',false)
                    _this.closest('.modal').find('button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>

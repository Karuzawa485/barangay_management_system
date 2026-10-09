<?php
require_once("./../DBConnection.php");
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `household_list` where household_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<div class="form-shell">
    <form action="" id="household-form">
        <input type="hidden" name="id" value="<?php echo isset($household_id) ? $household_id : '' ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="house_no" class="control-label">Household #</label>
                    <input type="text" pattern="[0-9]+" name="house_no" id="house_no" class="form-control mono" value="<?php echo isset($house_no) ? $house_no : '' ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="contact" class="control-label">Contact #</label>
                    <input type="text" pattern="[0-9]+" name="contact" id="contact" class="form-control mono" value="<?php echo isset($contact) ? $contact : '' ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="lastname" class="control-label">Last Name</label>
                    <input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($lastname) ? $lastname : '' ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="firstname" class="control-label">First Name</label>
                    <input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($firstname) ? $firstname : '' ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="middlename" class="control-label">Middle Name</label>
                    <input type="text" name="middlename" id="middlename" class="form-control" value="<?php echo isset($middlename) ? $middlename : '' ?>" placeholder="Optional">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email" class="control-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($email) ? $email : '' ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="occupation" class="control-label">Occupation</label>
                    <input type="text" name="occupation" id="occupation" class="form-control" value="<?php echo isset($occupation) ? $occupation : '' ?>" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="sex" class="control-label">Sex</label>
                    <select name="sex" id="sex" class="form-select">
                        <option value="" <?php echo !isset($sex) || empty($sex) ? 'selected' : '' ?>>Select Sex</option>
                        <option value="Male" <?php echo isset($sex) && $sex == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?php echo isset($sex) && $sex == 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="civil_status" class="control-label">Civil Status</label>
                    <select name="civil_status" id="civil_status" class="form-select">
                        <option value="" <?php echo !isset($civil_status) || empty($civil_status) ? 'selected' : '' ?>>Select Civil Status</option>
                        <option value="Single" <?php echo isset($civil_status) && $civil_status == 'Single' ? 'selected' : '' ?>>Single</option>
                        <option value="Married" <?php echo isset($civil_status) && $civil_status == 'Married' ? 'selected' : '' ?>>Married</option>
                        <option value="Widowed" <?php echo isset($civil_status) && $civil_status == 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                        <option value="Divorced" <?php echo isset($civil_status) && $civil_status == 'Divorced' ? 'selected' : '' ?>>Divorced</option>
                        <option value="Separated" <?php echo isset($civil_status) && $civil_status == 'Separated' ? 'selected' : '' ?>>Separated</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="date_of_birth" class="control-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control mono" value="<?php echo isset($date_of_birth) ? $date_of_birth : '' ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="nationality" class="control-label">Nationality</label>
                    <input type="text" name="nationality" id="nationality" class="form-control" value="<?php echo isset($nationality) ? $nationality : '' ?>">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="purok_id" class="control-label">Purok</label>
                    <select name="purok_id" id="purok_id" class="form-select select2">
                        <option value="" disabled <?php echo !isset($purok_id) ? 'selected' : '' ?>></option>
                        <?php 
                        $purok = $conn->query("SELECT * FROM purok_list");
                        while($row = $purok->fetchArray()):
                        ?>
                        <option value="<?php echo $row['purok_id'] ?>" <?php echo isset($purok_id) && $purok_id == $row['purok_id'] ? 'selected' : "" ?>><?php echo $row['purok'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    $(function(){
        $('.select2').select2({
            dropdownParent: $('#uni_modal')
        })
        $('#household-form').submit(function(e){
            e.preventDefault()
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>').addClass('pop_msg alert')
            $('#uni_modal button').attr('disabled',true)
            $('#uni_modal button[type="submit"]').text('Saving...')
            $.ajax({
                url:'./../Actions.php?a=save_household',
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

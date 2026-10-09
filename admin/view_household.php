<?php
require_once("./../DBConnection.php");
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT h.*,(h.lastname || ', ' || h.firstname || ', ' || h.middlename) as fullname, p.purok FROM household_list h inner join `purok_list` p on h.purok_id = p.purok_id where h.household_id = '{$_GET['id']}'");
    foreach($qry->fetchArray() as $k => $v){
        $$k = $v;
    }
}
?>
<style>
    #uni_modal .modal-footer{
        display:none !important;
    }
</style>
<div class="form-shell">
    <div id="outprint" class="detail-list">
        <div class="detail-item"><div class="detail-label">Household #</div><div class="detail-value mono"><?php echo $house_no ?></div></div>
        <div class="detail-item"><div class="detail-label">Resident Name</div><div class="detail-value"><?php echo $fullname ?></div></div>
        <div class="detail-item"><div class="detail-label">Contact #</div><div class="detail-value mono"><?php echo $contact ?></div></div>
        <div class="detail-item"><div class="detail-label">Email</div><div class="detail-value"><?php echo $email ?></div></div>
        <div class="detail-item"><div class="detail-label">Occupation</div><div class="detail-value"><?php echo $occupation ?></div></div>
        <div class="detail-item"><div class="detail-label">Sex</div><div class="detail-value"><?php echo (isset($sex) && !empty($sex)) ? $sex : 'N/A' ?></div></div>
        <div class="detail-item"><div class="detail-label">Civil Status</div><div class="detail-value"><?php echo (isset($civil_status) && !empty($civil_status)) ? $civil_status : 'N/A' ?></div></div>
        <div class="detail-item"><div class="detail-label">Date of Birth</div><div class="detail-value mono"><?php echo (isset($date_of_birth) && !empty($date_of_birth)) ? date("M d, Y", strtotime($date_of_birth)) : 'N/A' ?></div></div>
        <div class="detail-item"><div class="detail-label">Nationality</div><div class="detail-value"><?php echo (isset($nationality) && !empty($nationality)) ? $nationality : 'N/A' ?></div></div>
        <div class="detail-item" style="grid-column: 1 / -1;"><div class="detail-label">Address</div><div class="detail-value"><?php echo $purok ?>, <?php echo isset($_SESSION['system_info']['barangay_name']) ? $_SESSION['system_info']['barangay_name'] : '' ?>, <?php echo isset($_SESSION['system_info']['city']) ? $_SESSION['system_info']['city'] : '' ?>, <?php echo isset($_SESSION['system_info']['province']) ? $_SESSION['system_info']['province'] : '' ?>, <?php echo isset($_SESSION['system_info']['zip_code']) ? $_SESSION['system_info']['zip_code'] : '' ?></div></div>
    </div>
    <div class="panel-toolbar justify-content-end mt-3">
        <button class="btn-modern-sm btn-info-soft" id='print' type="button"><i class="fa fa-print"></i><span>Print</span></button>
        <button class="btn-modern-sm btn-ghost-dark" data-bs-dismiss='modal' type="button"><i class="fa fa-times"></i><span>Close</span></button>
    </div>
</div>
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
            <p class="fw-bold text-center">Household Resident Information</p>
        </div>
        <div class="col-2"></div>
    </div>
    <hr>
</noscript>
<script>
$(function(){
    $('#print').click(function(){
        var _p = $('#outprint').clone()
        var _h = $('head').clone()
        var _header = $('noscript').html()
        var el = $('<div>')
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
            }, 200)
        }, 500)
    })
})
</script>

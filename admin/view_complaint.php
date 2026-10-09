<?php
require_once("./../DBConnection.php");
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM complaint_list where complaint_id = '{$_GET['id']}'");
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
    <div class="detail-list">
        <div class="detail-item"><div class="detail-label">Full Name</div><div class="detail-value"><?php echo $full_name ?></div></div>
        <div class="detail-item"><div class="detail-label">Age</div><div class="detail-value mono"><?php echo $age ?></div></div>
        <div class="detail-item" style="grid-column: 1 / -1;"><div class="detail-label">Address</div><div class="detail-value"><?php echo $address ?></div></div>
        <div class="detail-item"><div class="detail-label">Status</div><div class="detail-value"><?php echo $status ?></div></div>
        <div class="detail-item"><div class="detail-label">Entry Date/Time</div><div class="detail-value mono"><?php echo date("M d, Y H:i",strtotime($date_created)) ?></div></div>
    </div>
    <div class="panel-toolbar justify-content-end mt-3">
        <button class="btn-modern-sm btn-ghost-dark" data-bs-dismiss='modal' type="button"><i class="fa fa-times"></i><span>Close</span></button>
    </div>
</div>

<?php
require_once('../DBConnection.php');
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM certificate_list where certificate_id = '{$_GET['id']}'");
    $row = $qry->fetchArray();
    if($row){
        $data = $row;
    }else{
        echo "<script>alert('Certificate not found'); window.history.back();</script>";
        exit;
    }
}else{
    echo "<script>alert('Certificate ID is required'); window.history.back();</script>";
    exit;
}
$brgy = [
    'province' => $_SESSION['system_info']['province'] ?? 'Province',
    'municipality' => $_SESSION['system_info']['city'] ?? 'Municipality',
    'barangay' => $_SESSION['system_info']['barangay_name'] ?? 'Barangay Name'
];
?>
<style>
    #uni_modal .modal-footer{
        display:none !important;
    }
</style>
<div class="form-shell">
    <div class="content-panel" style="background: rgba(7,11,20,0.18); box-shadow:none;">
        <div class="panel-head">
            <div>
                <h2 class="panel-title"><?php echo ucfirst($data['type']); ?> Preview</h2>
                <div class="panel-subtitle">Control No. <span class="mono"><?php echo $data['control_number']; ?></span></div>
            </div>
            <span class="badge-soft green">Issued</span>
        </div>
        <div class="panel-body">
            <div class="detail-list">
                <div class="detail-item"><div class="detail-label">Resident</div><div class="detail-value"><?php echo strtoupper($data['full_name']); ?></div></div>
                <div class="detail-item"><div class="detail-label">Official Receipt</div><div class="detail-value mono"><?php echo $data['receipt_number']; ?></div></div>
                <div class="detail-item"><div class="detail-label">Civil Status</div><div class="detail-value"><?php echo $data['civil_status']; ?></div></div>
                <div class="detail-item"><div class="detail-label">Date Issued</div><div class="detail-value mono"><?php echo !empty($data['date_issued']) ? date('F d, Y', strtotime($data['date_issued'])) : 'N/A'; ?></div></div>
                <div class="detail-item" style="grid-column:1 / -1;"><div class="detail-label">Address</div><div class="detail-value"><?php echo $data['address']; ?>, Barangay <?php echo $brgy['barangay']; ?>, <?php echo $brgy['municipality']; ?>, <?php echo $brgy['province']; ?></div></div>
                <div class="detail-item" style="grid-column:1 / -1;"><div class="detail-label">Purpose</div><div class="detail-value"><?php echo $data['purpose']; ?></div></div>
                <?php if($data['type'] == 'clearance'): ?>
                <div class="detail-item"><div class="detail-label">Cedula Number</div><div class="detail-value mono"><?php echo $data['cedula_number']; ?></div></div>
                <div class="detail-item"><div class="detail-label">OR Number</div><div class="detail-value mono"><?php echo $data['or_number']; ?></div></div>
                <div class="detail-item"><div class="detail-label">Expiration Date</div><div class="detail-value mono"><?php echo !empty($data['expiration_date']) ? date('F d, Y', strtotime($data['expiration_date'])) : 'N/A'; ?></div></div>
                <?php elseif($data['type'] == 'indigency'): ?>
                <div class="detail-item" style="grid-column:1 / -1;"><div class="detail-label">Specific Purpose</div><div class="detail-value"><?php echo $data['specific_purpose']; ?></div></div>
                <?php elseif($data['type'] == 'certification'): ?>
                <div class="detail-item" style="grid-column:1 / -1;"><div class="detail-label">Certification Body</div><div class="detail-value"><?php echo $data['certification_body']; ?></div></div>
                <?php endif; ?>
                <div class="detail-item"><div class="detail-label">Punong Barangay</div><div class="detail-value"><?php echo $data['punong_barangay']; ?></div></div>
            </div>
        </div>
    </div>
    <div class="panel-toolbar justify-content-end mt-3">
        <button class="btn-modern-sm btn-info-soft" type="button" onclick="window.open('print_certificate.php?id=<?php echo $data['certificate_id']; ?>', '_blank')"><i class="fa-solid fa-print"></i><span>Print</span></button>
        <button class="btn-modern-sm btn-ghost-dark" data-bs-dismiss='modal' type="button"><i class="fa fa-times"></i><span>Close</span></button>
    </div>
</div>

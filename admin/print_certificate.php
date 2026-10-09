<?php
require_once('../DBConnection.php');
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM certificate_list where certificate_id = '{$_GET['id']}'");
    $row = $qry->fetchArray();
    if($row){
        $data = $row;
    }else{
        echo "<script>alert('Certificate not found'); window.close();</script>";
        exit;
    }
}else{
    echo "<script>alert('Certificate ID is required'); window.close();</script>";
    exit;
}

// Get barangay info
$brgy_qry = $conn->query("SELECT * FROM system_info LIMIT 1");
$brgy_row = $brgy_qry->fetchArray();
$brgy = [
    'province' => $brgy_row['province'] ?? 'Province',
    'municipality' => $brgy_row['municipality'] ?? 'Municipality', 
    'barangay' => $brgy_row['barangay'] ?? 'Barangay Name'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($data['type']); ?> - <?php echo $data['control_number']; ?></title>
    <style>
        @media print {
            body { margin: 0; background: #fff; color: #000; }
            .no-print { display: none; }
            .certificate { page-break-inside: avoid; background: #fff; color: #000; border-color: #000; box-shadow: none; }
            .signature-line,
            .seal-box { border-color: #000 !important; }
        }
        body {
            margin: 0;
            min-height: 100vh;
            padding: 24px;
            background:
                radial-gradient(circle at top left, rgba(59,130,246,0.16), transparent 28%),
                radial-gradient(circle at bottom right, rgba(6,182,212,0.12), transparent 30%),
                #070B14;
            color: #F0F4FF;
            font-family: 'Sora', sans-serif;
            font-size: 14px;
            line-height: 1.65;
        }
        .certificate {
            position: relative;
            max-width: 860px;
            margin: 0 auto;
            padding: 40px;
            border: 1px solid rgba(59,130,246,0.15);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(13,21,37,0.98), rgba(18,29,53,0.94));
            box-shadow: 0 28px 70px rgba(0,0,0,0.35);
        }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 24px; margin: 0; text-transform: uppercase; }
        .header h2,
        .header h3 { color: #BFDBFE; }
        .header p { margin: 5px 0; color: #CBD5E1; }
        .content { margin: 20px 0; }
        .signature-section { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature { text-align: center; width: 200px; }
        .signature-line { border-bottom: 1px solid rgba(240,244,255,0.6); margin-bottom: 5px; }
        .seal { text-align: center; margin-top: 20px; }
        .control-number {
            position: absolute;
            top: 22px;
            right: 24px;
            font-size: 11px;
            color: #67E8F9;
            font-family: 'JetBrains Mono', monospace;
        }
        .resident-name {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
            color: #F0F4FF;
        }
        .seal-box {
            width: 100px;
            height: 100px;
            border: 1px solid rgba(240,244,255,0.45);
            border-radius: 50%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94A3B8;
        }
        .no-print {
            text-align: center;
            margin: 20px auto 0;
        }
        .no-print button {
            min-height: 42px;
            padding: 0.7rem 1rem;
            margin: 0 0.35rem;
            border-radius: 14px;
            border: 1px solid rgba(59,130,246,0.15);
            background: rgba(18,29,53,0.92);
            color: #F0F4FF;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .no-print button:hover {
            transform: translateY(-2px);
            border-color: rgba(59,130,246,0.38);
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="control-number">
            Control No: <?php echo $data['control_number']; ?><br>
            OR No: <?php echo $data['receipt_number']; ?>
        </div>
        
        <div class="header">
            <h1>Republic of the Philippines</h1>
            <p>Province of <?php echo $brgy['province']; ?></p>
            <p>Municipality of <?php echo $brgy['municipality']; ?></p>
            <h2>Barangay <?php echo $brgy['barangay']; ?></h2>
            <h3><?php echo ucfirst($data['type']); ?></h3>
        </div>

        <div class="content">
            <p>This is to certify that:</p>
            
            <p class="resident-name">
                <?php echo strtoupper($data['full_name']); ?>
            </p>
            
            <p>of legal age, <?php echo $data['civil_status']; ?>, and a resident of <?php echo $data['address']; ?>, Barangay <?php echo $brgy['barangay']; ?>, Municipality of <?php echo $brgy['municipality']; ?>, Province of <?php echo $brgy['province']; ?>.</p>

            <?php if($data['type'] == 'clearance'): ?>
            <p>This is to certify that the above-named person is of good moral character and has no derogatory record on file in this Barangay.</p>
            <p>Cedula No: <?php echo $data['cedula_number']; ?> | OR No: <?php echo $data['or_number']; ?></p>
            <p>This clearance is valid until: <?php echo !empty($data['expiration_date']) ? date('F d, Y', strtotime($data['expiration_date'])) : 'N/A'; ?></p>
            <?php elseif($data['type'] == 'indigency'): ?>
            <p>This is to certify that the above-named person belongs to a low-income household and is financially incapable of paying fees required for <?php echo $data['specific_purpose']; ?>.</p>
            <?php elseif($data['type'] == 'certification'): ?>
            <p><?php echo $data['certification_body']; ?></p>
            <?php endif; ?>

            <p>This certification is issued upon the request of the above-named person for <?php echo $data['purpose']; ?>.</p>
            
            <p>Issued this <?php echo !empty($data['date_issued']) ? date('jS', strtotime($data['date_issued'])) : 'N/A'; ?> day of <?php echo !empty($data['date_issued']) ? date('F, Y', strtotime($data['date_issued'])) : 'N/A'; ?> at Barangay <?php echo $brgy['barangay']; ?>, Municipality of <?php echo $brgy['municipality']; ?>, Province of <?php echo $brgy['province']; ?>.</p>
        </div>

        <div class="signature-section">
            <div class="signature">
                <div class="signature-line"></div>
                <p><?php echo $data['punong_barangay']; ?></p>
                <p>Punong Barangay</p>
            </div>
            <div class="seal">
                <div class="seal-box">
                    <span style="font-size: 10px;">BARANGAY SEAL</span>
                </div>
            </div>
        </div>
    </div>

    <div class="no-print">
        <button onclick="window.print()">Print Certificate</button>
        <button onclick="window.close()">Close</button>
    </div>

    <script>
        window.onload = function() {
            // Auto-print if desired
            // window.print();
        }
    </script>
</body>
</html>

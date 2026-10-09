<?php
session_start();
if(!isset($_SESSION['admin_id'])){
    header("Location:./login.php");
    exit;
}
require_once('../DBConnection.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
if($_SESSION['type'] != 1 && in_array($page,array('maintenance','admin','manage_admin'))){
    header("Location:./");
    exit;
}
$page_titles = [
    'home' => 'Dashboard',
    'household' => 'Households',
    'complaints' => 'Residents Address List',
    'certificates' => 'Barangay Clearance/Certificates',
    'admin' => 'Users',
    'system_info' => 'Barangay/System Info',
    'purok' => 'Purok List',
    'manage_account' => 'Manage Account'
];
$current_title = $page_titles[$page] ?? ucwords(str_replace('_',' ',$page));
$user_name = $_SESSION['fullname'] ?? 'Administrator';
$name_parts = preg_split('/\s+/', trim($user_name));
$initials = '';
foreach($name_parts as $name_part){
    if($name_part !== ''){
        $initials .= strtoupper(substr($name_part, 0, 1));
    }
}
$initials = substr($initials ?: 'AD', 0, 2);
$user_role = ($_SESSION['type'] ?? 0) == 1 ? 'System Administrator' : 'Staff Account';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucwords(str_replace('_',' ',$page)) ?> |  Barangay Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../select2/css/select2.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../summernote/summernote-lite.min.css">
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../select2/js/select2.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../summernote/summernote-lite.min.js"></script>
    <link rel="stylesheet" href="../DataTables/datatables.min.css">
    <script src="../DataTables/datatables.min.js"></script>
    <script src="../fontawesome/js/all.min.js"></script>
    <script src="../js/script.js"></script>
    <style>
        :root{
            --bg: #070B14;
            --surface: #0D1525;
            --surface-2: #121D35;
            --surface-3: rgba(18, 29, 53, 0.82);
            --border: rgba(59,130,246,0.15);
            --primary: #3B82F6;
            --secondary: #06B6D4;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --purple: #8B5CF6;
            --text: #F0F4FF;
            --muted: #64748B;
            --mono: 'JetBrains Mono', monospace;
            --font: 'Sora', sans-serif;
            --sidebar-width: 260px;
            --transition: all 0.2s ease;
            --bs-primary: #3B82F6 !important;
            --bs-success-rgb:16,185,129 !important;
        }
        html,body{
            height:100%;
            width:100%;
            background: var(--bg);
            color: var(--text);
            font-family: var(--font);
        }
        body{
            margin: 0;
            overflow-x: hidden;
            background:
                radial-gradient(circle at top left, rgba(59,130,246,0.18), transparent 30%),
                radial-gradient(circle at bottom right, rgba(6,182,212,0.12), transparent 28%),
                var(--bg);
        }
        body.sidebar-open{
            overflow: hidden;
        }
        a{
            color: inherit;
            text-decoration: none;
        }
        main{
            min-height: 100vh;
        }
        .app-shell{
            position: relative;
            min-height: 100vh;
        }
        .ambient-blob{
            position: fixed;
            inset: auto;
            width: 24rem;
            height: 24rem;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            opacity: 0.55;
            z-index: 0;
        }
        .ambient-blob.blob-1{
            top: -7rem;
            left: -7rem;
            background: rgba(59,130,246,0.16);
        }
        .ambient-blob.blob-2{
            right: -8rem;
            bottom: -8rem;
            background: rgba(6,182,212,0.12);
        }
        .sidebar-backdrop{
            position: fixed;
            inset: 0;
            background: rgba(7,11,20,0.6);
            backdrop-filter: blur(4px);
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            z-index: 20;
        }
        .sidebar-backdrop.show{
            opacity: 1;
            visibility: visible;
        }
        .sidebar{
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            padding: 1.25rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            background: rgba(13,21,37,0.92);
            border-right: 1px solid var(--border);
            box-shadow: 0 20px 60px rgba(0,0,0,0.35);
            backdrop-filter: blur(16px);
            z-index: 30;
            animation: sidebarSlideIn 0.45s ease;
        }
        .brand{
            display: flex;
            align-items: center;
            gap: 0.9rem;
            padding: 0.85rem 0.95rem;
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(59,130,246,0.18), rgba(6,182,212,0.10));
            border: 1px solid rgba(59,130,246,0.2);
        }
        .brand-mark{
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 12px 30px rgba(59,130,246,0.35);
            font-size: 1.1rem;
        }
        .brand-copy small,
        .sidebar-section-label,
        .profile-role{
            color: var(--muted);
        }
        .brand-copy strong{
            display: block;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        .sidebar-nav{
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            min-height: 0;
            overflow-y: auto;
            padding-right: 0.25rem;
        }
        .sidebar-section-label{
            display: block;
            margin: 0 0 0.7rem 0.35rem;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .nav-list{
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }
        .nav-link-item{
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.92rem 1rem;
            border-radius: 18px;
            color: rgba(240,244,255,0.88);
            transition: var(--transition);
        }
        .nav-link-item::before{
            content: "";
            position: absolute;
            left: -1rem;
            top: 12px;
            bottom: 12px;
            width: 3px;
            border-radius: 999px;
            background: transparent;
            transition: var(--transition);
        }
        .nav-link-item:hover,
        .nav-link-item.active{
            background: rgba(59,130,246,0.12);
            color: var(--text);
            transform: translateX(4px);
        }
        .nav-link-item.active::before{
            background: var(--primary);
            box-shadow: 0 0 14px rgba(59,130,246,0.9);
        }
        .nav-icon{
            width: 1.5rem;
            text-align: center;
            font-size: 1rem;
        }
        .nav-badge{
            margin-left: auto;
            min-width: 1.8rem;
            padding: 0.18rem 0.5rem;
            border-radius: 999px;
            background: rgba(59,130,246,0.18);
            border: 1px solid rgba(59,130,246,0.35);
            color: #bfdbfe;
            text-align: center;
            font-size: 0.75rem;
            font-family: var(--mono);
        }
        .profile-card{
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 1rem;
            border-radius: 20px;
            background: linear-gradient(180deg, rgba(18,29,53,0.92), rgba(13,21,37,0.9));
            border: 1px solid var(--border);
        }
        .profile-avatar{
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(59,130,246,0.9), rgba(6,182,212,0.8));
            color: white;
            font-weight: 700;
        }
        .profile-name{
            font-weight: 600;
        }
        .page-shell{
            position: relative;
            z-index: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 1.25rem;
        }
        .topbar{
            position: sticky;
            top: 1rem;
            z-index: 15;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.2rem;
            margin-bottom: 1.2rem;
            border-radius: 24px;
            background: rgba(13,21,37,0.7);
            border: 1px solid var(--border);
            backdrop-filter: blur(16px);
            box-shadow: 0 18px 40px rgba(0,0,0,0.24);
            animation: topbarFadeDown 0.45s ease;
        }
        .topbar-left{
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
        }
        .menu-toggle{
            display: none;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: rgba(18,29,53,0.95);
            color: var(--text);
        }
        .breadcrumb-copy small{
            display: block;
            color: var(--muted);
            margin-bottom: 0.2rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 0.72rem;
        }
        .breadcrumb-copy strong{
            display: block;
            font-size: 1.05rem;
        }
        .topbar-right{
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .topbar-search{
            display: flex;
            align-items: center;
            gap: 0.65rem;
            min-width: 260px;
            padding: 0.82rem 1rem;
            border-radius: 16px;
            background: rgba(18,29,53,0.88);
            border: 1px solid transparent;
            transition: var(--transition);
        }
        .topbar-search:focus-within{
            border-color: rgba(59,130,246,0.35);
            box-shadow: 0 0 0 4px rgba(59,130,246,0.12);
        }
        .topbar-search input{
            width: 100%;
            background: transparent;
            border: none;
            outline: none;
            color: var(--text);
            font-size: 0.95rem;
        }
        .topbar-search input::placeholder{
            color: var(--muted);
        }
        .icon-button{
            position: relative;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: rgba(18,29,53,0.92);
            color: var(--text);
            transition: var(--transition);
        }
        .icon-button:hover,
        .menu-toggle:hover{
            transform: translateY(-2px);
            border-color: rgba(59,130,246,0.45);
            box-shadow: 0 10px 25px rgba(59,130,246,0.18);
        }
        .notification-dot{
            position: absolute;
            top: 10px;
            right: 10px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #FB7185;
            box-shadow: 0 0 0 3px rgba(251,113,133,0.18);
        }
        .page-content{
            animation: contentFadeUp 0.45s ease;
        }
        #page-container{
            min-width: 0;
        }
        .dynamic_alert{
            border-radius: 18px;
            border: 1px solid var(--border);
            box-shadow: 0 16px 40px rgba(0,0,0,0.18);
        }
        .app-page{
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .page-header-panel,
        .content-panel,
        .stats-grid-modern{
            position: relative;
            z-index: 1;
        }
        .page-header-panel{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.35rem 1.5rem;
            border-radius: 28px;
            background:
                radial-gradient(circle at top right, rgba(59,130,246,0.20), transparent 26%),
                linear-gradient(135deg, rgba(13,21,37,0.96), rgba(18,29,53,0.9));
            border: 1px solid var(--border);
            box-shadow: 0 24px 60px rgba(0,0,0,0.25);
        }
        .page-header-panel h1,
        .page-header-panel h2,
        .panel-title{
            margin: 0;
            font-size: clamp(1.25rem, 1.1rem + 0.7vw, 1.9rem);
            font-weight: 700;
        }
        .page-header-panel p,
        .panel-subtitle{
            margin: 0.35rem 0 0;
            color: var(--muted);
        }
        .page-actions{
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .content-panel{
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(13,21,37,0.96), rgba(18,29,53,0.9));
            border: 1px solid var(--border);
            box-shadow: 0 24px 60px rgba(0,0,0,0.22);
            overflow: hidden;
        }
        .panel-head{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.5rem 0.85rem;
        }
        .panel-toolbar{
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .panel-body{
            padding: 0 1.5rem 1.4rem;
        }
        .stats-grid-modern{
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }
        .stat-tile{
            position: relative;
            overflow: hidden;
            padding: 1.25rem;
            border-radius: 24px;
            background: linear-gradient(180deg, rgba(13,21,37,0.96), rgba(18,29,53,0.9));
            border: 1px solid var(--border);
            box-shadow: 0 18px 45px rgba(0,0,0,0.2);
            transition: var(--transition);
        }
        .stat-tile:hover{
            transform: translateY(-4px);
            box-shadow: 0 24px 50px rgba(0,0,0,0.26);
        }
        .stat-tile::before{
            content: "";
            position: absolute;
            left: 1rem;
            right: 1rem;
            top: 0;
            height: 3px;
            border-radius: 999px;
            opacity: 0;
            transition: var(--transition);
        }
        .stat-tile:hover::before{
            opacity: 1;
        }
        .stat-tile.blue::before{ background: linear-gradient(90deg, #3B82F6, transparent); }
        .stat-tile.cyan::before{ background: linear-gradient(90deg, #06B6D4, transparent); }
        .stat-tile.purple::before{ background: linear-gradient(90deg, #8B5CF6, transparent); }
        .stat-tile.green::before{ background: linear-gradient(90deg, #10B981, transparent); }
        .stat-tile .tile-icon{
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        .stat-tile.blue .tile-icon{ background: rgba(59,130,246,0.16); color: #93C5FD; }
        .stat-tile.cyan .tile-icon{ background: rgba(6,182,212,0.16); color: #67E8F9; }
        .stat-tile.purple .tile-icon{ background: rgba(139,92,246,0.16); color: #C4B5FD; }
        .stat-tile.green .tile-icon{ background: rgba(16,185,129,0.16); color: #86EFAC; }
        .tile-value{
            font-family: var(--mono);
            font-size: clamp(1.55rem, 1.35rem + 0.8vw, 2.1rem);
            font-weight: 600;
        }
        .tile-label{
            margin-top: 0.25rem;
            color: var(--muted);
            font-size: 0.92rem;
        }
        .btn-modern,
        .btn-modern-sm{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: var(--text);
            border: 1px solid transparent;
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none;
        }
        .btn-modern{
            padding: 0.9rem 1.15rem;
            border-radius: 16px;
            font-weight: 600;
        }
        .btn-modern-sm{
            min-height: 40px;
            padding: 0.55rem 0.9rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .btn-modern:hover,
        .btn-modern-sm:hover{
            transform: translateY(-2px);
            color: var(--text);
        }
        .btn-primary-glow{
            background: linear-gradient(135deg, #3B82F6, #06B6D4);
            box-shadow: 0 12px 30px rgba(59,130,246,0.28);
        }
        .btn-ghost-dark{
            background: rgba(18,29,53,0.84);
            border-color: var(--border);
        }
        .btn-ghost-dark:hover{
            border-color: rgba(59,130,246,0.45);
            box-shadow: 0 12px 24px rgba(59,130,246,0.14);
        }
        .btn-success-soft{
            background: rgba(16,185,129,0.14);
            border-color: rgba(16,185,129,0.28);
            color: #A7F3D0;
        }
        .btn-danger-soft{
            background: rgba(239,68,68,0.14);
            border-color: rgba(239,68,68,0.28);
            color: #FCA5A5;
        }
        .btn-info-soft{
            background: rgba(6,182,212,0.14);
            border-color: rgba(6,182,212,0.28);
            color: #67E8F9;
        }
        .btn-primary-soft{
            background: rgba(59,130,246,0.14);
            border-color: rgba(59,130,246,0.28);
            color: #BFDBFE;
        }
        .toolbar-control,
        .form-control,
        .form-select{
            min-height: 44px;
            padding: 0.72rem 0.95rem;
            border-radius: 14px !important;
            border: 1px solid var(--border);
            background: rgba(7,11,20,0.5) !important;
            color: var(--text) !important;
            box-shadow: none !important;
            transition: var(--transition);
        }
        textarea.form-control{
            min-height: 110px;
            resize: vertical;
        }
        .form-control::placeholder{
            color: var(--muted);
        }
        .toolbar-control:focus,
        .form-control:focus,
        .form-select:focus{
            border-color: rgba(59,130,246,0.4);
            box-shadow: 0 0 0 4px rgba(59,130,246,0.12) !important;
        }
        .form-label,
        .control-label{
            margin-bottom: 0.45rem;
            color: #dbe7ff;
            font-weight: 600;
        }
        .form-group{
            margin-bottom: 1rem;
        }
        .form-text,
        small.text-muted,
        .text-muted{
            color: var(--muted) !important;
        }
        .form-shell{
            padding: 0.25rem;
        }
        .form-grid{
            display: grid;
            gap: 1rem;
        }
        .detail-list{
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }
        .detail-item{
            padding: 1rem;
            border-radius: 18px;
            background: rgba(7,11,20,0.35);
            border: 1px solid rgba(59,130,246,0.1);
        }
        .detail-label{
            color: var(--muted);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.3rem;
        }
        .detail-value{
            color: var(--text);
            font-weight: 600;
            word-break: break-word;
        }
        .dataTables_wrapper{
            color: var(--text);
        }
        .table{
            --bs-table-color: var(--text);
            --bs-table-bg: transparent;
            --bs-table-border-color: rgba(59,130,246,0.1);
            --bs-table-striped-bg: rgba(59,130,246,0.04);
            --bs-table-striped-color: var(--text);
            --bs-table-active-bg: rgba(59,130,246,0.08);
            --bs-table-active-color: var(--text);
            --bs-table-hover-bg: rgba(59,130,246,0.07);
            --bs-table-hover-color: var(--text);
            color: var(--text);
            background: transparent !important;
            border-color: rgba(59,130,246,0.1) !important;
            margin-bottom: 0;
        }
        .table > :not(caption) > * > *{
            background: transparent !important;
            color: inherit !important;
            box-shadow: none !important;
            border-bottom-color: rgba(59,130,246,0.1) !important;
        }
        .table-striped > tbody > tr:nth-of-type(odd) > *{
            background: rgba(59,130,246,0.04) !important;
        }
        .table-hover > tbody > tr:hover > *{
            background: rgba(59,130,246,0.07) !important;
        }
        .table-shell,
        .dataTables_wrapper,
        .dataTables_scroll,
        .dataTables_scrollHead,
        .dataTables_scrollBody{
            background: transparent !important;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate{
            color: var(--muted) !important;
            padding: 0.85rem 0;
        }
        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select{
            margin-left: 0.35rem;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(7,11,20,0.5);
            color: var(--text);
            padding: 0.4rem 0.75rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button{
            border-radius: 12px !important;
            border: 1px solid var(--border) !important;
            background: rgba(18,29,53,0.85) !important;
            color: var(--text) !important;
            margin-left: 0.35rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover{
            border-color: rgba(59,130,246,0.38) !important;
            background: rgba(59,130,246,0.14) !important;
            color: var(--text) !important;
        }
        table.dataTable{
            width: 100% !important;
            border-collapse: separate !important;
            border-spacing: 0 !important;
            color: var(--text);
            background: transparent !important;
        }
        table.dataTable.no-footer{
            border-bottom: 1px solid rgba(59,130,246,0.1) !important;
        }
        table.dataTable tbody tr,
        table.dataTable thead tr,
        table.dataTable tfoot tr{
            background: transparent !important;
        }
        table.dataTable thead th{
            padding: 1rem 0.9rem !important;
            color: #A5B4FC !important;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-bottom: 1px solid rgba(59,130,246,0.12) !important;
            background: transparent !important;
        }
        table.dataTable tbody td{
            padding: 1rem 0.9rem !important;
            border-bottom: 1px solid rgba(59,130,246,0.1) !important;
            background: transparent !important;
            color: var(--text) !important;
            vertical-align: middle;
        }
        table.dataTable tbody tr:hover td{
            background: rgba(59,130,246,0.07) !important;
        }
        table.dataTable.display tbody tr.odd > .sorting_1,
        table.dataTable.order-column.stripe tbody tr.odd > .sorting_1{
            background: rgba(59,130,246,0.04) !important;
        }
        table.dataTable.display tbody tr.even > .sorting_1,
        table.dataTable.order-column.stripe tbody tr.even > .sorting_1{
            background: transparent !important;
        }
        table.dataTable.hover tbody tr:hover > .sorting_1,
        table.dataTable.display tbody tr:hover > .sorting_1{
            background: rgba(59,130,246,0.07) !important;
        }
        .sorting,
        .sorting_asc,
        .sorting_desc,
        .sorting_1{
            background-image: none !important;
        }
        .table-shell{
            overflow-x: auto;
        }
        .badge-soft{
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.78rem;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-soft.blue{ background: rgba(59,130,246,0.14); border-color: rgba(59,130,246,0.28); color: #BFDBFE; }
        .badge-soft.cyan{ background: rgba(6,182,212,0.14); border-color: rgba(6,182,212,0.28); color: #67E8F9; }
        .badge-soft.green{ background: rgba(16,185,129,0.14); border-color: rgba(16,185,129,0.28); color: #A7F3D0; }
        .badge-soft.purple{ background: rgba(139,92,246,0.14); border-color: rgba(139,92,246,0.28); color: #DDD6FE; }
        .badge-soft.amber{ background: rgba(245,158,11,0.14); border-color: rgba(245,158,11,0.28); color: #FCD34D; }
        .avatar-chip{
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(59,130,246,0.8), rgba(6,182,212,0.8));
            color: white;
            font-weight: 700;
            font-size: 0.86rem;
        }
        .resident-chip{
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        .mono{
            font-family: var(--mono);
        }
        .modal-content{
            background: linear-gradient(180deg, rgba(13,21,37,0.98), rgba(18,29,53,0.95));
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.4);
        }
        .modal-header,
        .modal-footer{
            border-color: rgba(59,130,246,0.12);
            padding: 1rem 1.15rem;
        }
        .modal-body{
            padding: 1rem 1.15rem 1.15rem;
        }
        .modal-title{
            font-weight: 700;
        }
        .dropdown-menu{
            background: rgba(13,21,37,0.98);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.35);
        }
        .dropdown-item{
            color: var(--text);
        }
        .dropdown-item:hover,
        .dropdown-item:focus{
            color: var(--text);
            background: rgba(59,130,246,0.12);
        }
        .select2-container--default .select2-selection--single{
            min-height: 44px;
            border-radius: 14px !important;
            border: 1px solid var(--border) !important;
            background: rgba(7,11,20,0.5) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered{
            color: var(--text) !important;
            line-height: 42px !important;
            padding-left: 0.95rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow{
            height: 42px !important;
            right: 10px !important;
        }
        .select2-dropdown{
            background: rgba(13,21,37,0.98) !important;
            border: 1px solid var(--border) !important;
        }
        .select2-search__field{
            background: rgba(7,11,20,0.5) !important;
            color: var(--text) !important;
            border: 1px solid var(--border) !important;
        }
        .select2-results__option{
            color: var(--text);
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable{
            background: rgba(59,130,246,0.18) !important;
        }
        .truncate-1 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }
        .truncate-3 {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .thumbnail-img{
            width:50px;
            height:50px;
            margin:2px
        }
        .display-select-image{
            width:60px;
            height:60px;
            margin:2px
        }
        img.display-image {
            width: 100%;
            height: 45vh;
            object-fit: cover;
            background: black;
        }
        .modal-dialog.large {
            width: 80% !important;
            max-width: unset;
        }
        .modal-dialog.mid-large {
            width: 50% !important;
            max-width: unset;
        }
        .img-del-btn{
            right: 2px;
            top: -3px;
        }
        .img-del-btn>.btn{
            font-size: 10px;
            padding: 0px 2px !important;
        }
        span.select2-container.select2-container--default.select2-container--open {
            z-index: 9999;
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(18,29,53,0.75);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(100,116,139,0.5);
            border-radius: 999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(100,116,139,0.85);
        }
        @keyframes sidebarSlideIn{
            from{transform: translateX(-28px); opacity: 0;}
            to{transform: translateX(0); opacity: 1;}
        }
        @keyframes topbarFadeDown{
            from{transform: translateY(-20px); opacity: 0;}
            to{transform: translateY(0); opacity: 1;}
        }
        @keyframes contentFadeUp{
            from{transform: translateY(22px); opacity: 0;}
            to{transform: translateY(0); opacity: 1;}
        }
        @media (max-width: 991.98px){
            .sidebar{
                transform: translateX(-100%);
                transition: var(--transition);
                animation: none;
            }
            body.sidebar-open .sidebar{
                transform: translateX(0);
            }
            .page-shell{
                margin-left: 0;
                padding: 1rem;
            }
            .menu-toggle{
                display: inline-grid;
                place-items: center;
            }
            .topbar{
                top: 0.75rem;
            }
            .stats-grid-modern{
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width:720px){
            .modal-dialog.large,
            .modal-dialog.mid-large {
                width: 100% !important;
                max-width: unset;
            }
            .topbar{
                flex-direction: column;
                align-items: stretch;
            }
            .topbar-right{
                width: 100%;
            }
            .topbar-search{
                min-width: 0;
                flex: 1 1 auto;
            }
            .page-header-panel,
            .panel-head,
            .panel-body{
                padding-left: 1rem;
                padding-right: 1rem;
            }
            .page-header-panel{
                flex-direction: column;
                align-items: flex-start;
            }
            .detail-list,
            .stats-grid-modern{
                grid-template-columns: 1fr;
            }
        }
        @media (max-width:575.98px){
            .page-shell{
                padding: 0.85rem;
            }
            .topbar{
                padding: 0.9rem;
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>
    <main>
    <div class="app-shell">
        <div class="ambient-blob blob-1"></div>
        <div class="ambient-blob blob-2"></div>
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
        <aside class="sidebar" id="appSidebar">
            <a class="brand" href="./">
                <div class="brand-mark">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="brand-copy">
                    <small>Admin Portal</small>
                    <strong>Barangay Management System</strong>
                </div>
            </a>
            <div class="sidebar-nav">
                <section>
                    <span class="sidebar-section-label">Main Menu</span>
                    <div class="nav-list">
                        <a class="nav-link-item <?php echo ($page == 'home') ? 'active' : '' ?>" href="./">
                            <span class="nav-icon">🏠</span>
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-link-item <?php echo ($page == 'household') ? 'active' : '' ?>" href="./?page=household">
                            <span class="nav-icon">👪</span>
                            <span>Households</span>
                        </a>
                        <a class="nav-link-item <?php echo ($page == 'complaints') ? 'active' : '' ?>" href="./?page=complaints">
                            <span class="nav-icon">📍</span>
                            <span>Residents Address List</span>
                        </a>
                        <a class="nav-link-item <?php echo ($page == 'certificates') ? 'active' : '' ?>" href="./?page=certificates">
                            <span class="nav-icon">📄</span>
                            <span>Barangay Clearance/Certificates</span>
                            <span class="nav-badge">3</span>
                        </a>
                        <?php if($_SESSION['type'] == 1): ?>
                        <a class="nav-link-item <?php echo ($page == 'admin') ? 'active' : '' ?>" href="./?page=admin">
                            <span class="nav-icon">👤</span>
                            <span>Users</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </section>
                <?php if($_SESSION['type'] == 1): ?>
                <section>
                    <span class="sidebar-section-label">Settings</span>
                    <div class="nav-list">
                        <a class="nav-link-item <?php echo ($page == 'system_info') ? 'active' : '' ?>" href="./?page=system_info">
                            <span class="nav-icon">⚙️</span>
                            <span>Barangay/System Info</span>
                        </a>
                        <a class="nav-link-item <?php echo ($page == 'purok') ? 'active' : '' ?>" href="./?page=purok">
                            <span class="nav-icon">🗺️</span>
                            <span>Purok List</span>
                        </a>
                    </div>
                </section>
                <?php endif; ?>
            </div>
            <div class="profile-card">
                <div class="profile-avatar"><?php echo htmlspecialchars($initials) ?></div>
                <div>
                    <div class="profile-name truncate-1"><?php echo htmlspecialchars($user_name) ?></div>
                    <div class="profile-role"><?php echo $user_role ?></div>
                </div>
            </div>
        </aside>
        <div class="page-shell">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="menu-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="breadcrumb-copy">
                        <small>Dashboard / <?php echo htmlspecialchars($current_title) ?></small>
                        <strong><?php echo htmlspecialchars($current_title) ?></strong>
                    </div>
                </div>
                <div class="topbar-right">
                    <label class="topbar-search mb-0">
                        <i class="fa-solid fa-magnifying-glass" style="color: var(--muted);"></i>
                        <input type="text" placeholder="Quick search..." id="globalSearch">
                    </label>
                    <button type="button" class="icon-button" aria-label="Notifications">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notification-dot"></span>
                    </button>
                    <div class="dropdown">
                        <button class="icon-button dropdown-toggle" type="button" id="topbarMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-gear"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="topbarMenu">
                            <li><a class="dropdown-item" href="./?page=manage_account">Manage Account</a></li>
                            <li><a class="dropdown-item" href="./../Actions.php?a=logout">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>
            <div class="page-content" id="page-container">
        <?php 
            if(isset($_SESSION['flashdata'])):
        ?>
        <div class="dynamic_alert alert alert-<?php echo $_SESSION['flashdata']['type'] ?>">
        <div class="float-end"><a href="javascript:void(0)" class="text-decoration-none" style="color: var(--text);" onclick="$(this).closest('.dynamic_alert').hide('slow').remove()">x</a></div>
            <?php echo $_SESSION['flashdata']['msg'] ?>
        </div>
        <?php unset($_SESSION['flashdata']) ?>
        <?php endif; ?>
        <?php
            include $page.'.php';
        ?>
            </div>
        </div>
    </div>
    </div>
    </main>
    <div class="modal fade" id="uni_modal" role='dialog' data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn-modern-sm btn-primary-glow" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
            <button type="button" class="btn-modern-sm btn-ghost-dark" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="uni_modal_secondary" role='dialog' data-bs-backdrop="static" data-bs-keyboard="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
            <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn-modern-sm btn-primary-glow" id='submit' onclick="$('#uni_modal_secondary form').submit()">Save</button>
            <button type="button" class="btn-modern-sm btn-ghost-dark" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
    <div class="modal fade" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content rounded-0">
            <div class="modal-header py-2">
            <h5 class="modal-title">Confirmation</h5>
        </div>
        <div class="modal-body">
            <div id="delete_content"></div>
        </div>
        <div class="modal-footer py-1">
            <button type="button" class="btn-modern-sm btn-danger-soft" id='confirm' onclick="">Continue</button>
            <button type="button" class="btn-modern-sm btn-ghost-dark" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
        </div>
    </div>
</body>
<script>
    $(function(){
        const body = $('body')
        const toggleSidebar = () => body.toggleClass('sidebar-open')
        const closeSidebar = () => body.removeClass('sidebar-open')

        $('#sidebarToggle').on('click', toggleSidebar)
        $('#sidebarBackdrop').on('click', closeSidebar)

        $(window).on('resize', function(){
            if(window.innerWidth > 991){
                closeSidebar()
            }
        })
    })
</script>
</html>

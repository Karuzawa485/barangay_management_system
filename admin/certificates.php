<?php
$sample_certificates = [
    [
        'id' => 1,
        'control_no' => 'BMS-CLR-2026-001',
        'type' => 'Clearance',
        'resident_name' => 'John Andrew Peralta',
        'date_issued' => '2026-04-24',
        'status' => 'Issued'
    ],
    [
        'id' => 2,
        'control_no' => 'BMS-CRT-2026-002',
        'type' => 'Certification',
        'resident_name' => 'John Andrew Peralta',
        'date_issued' => '2026-04-24',
        'status' => 'Issued'
    ],
    [
        'id' => 3,
        'control_no' => 'BMS-IND-2026-003',
        'type' => 'Indigency',
        'resident_name' => 'John Andrew Peralta',
        'date_issued' => '2026-04-24',
        'status' => 'Issued'
    ]
];

$stats = [
    ['label' => 'Total Certificates', 'value' => count($sample_certificates), 'accent' => 'blue', 'icon' => 'fa-layer-group'],
    ['label' => 'Issued', 'value' => 3, 'accent' => 'cyan', 'icon' => 'fa-circle-check'],
    ['label' => 'Clearances', 'value' => 1, 'accent' => 'purple', 'icon' => 'fa-file-shield'],
    ['label' => 'Other Certs', 'value' => 2, 'accent' => 'green', 'icon' => 'fa-file-lines'],
];
?>
<style>
    .certificates-page{
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    .hero-panel,
    .stats-grid,
    .table-card{
        position: relative;
        z-index: 1;
    }
    .hero-panel{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.4rem 1.5rem;
        border-radius: 28px;
        background:
            radial-gradient(circle at top right, rgba(59,130,246,0.20), transparent 26%),
            linear-gradient(135deg, rgba(13,21,37,0.95), rgba(18,29,53,0.9));
        border: 1px solid var(--border);
        box-shadow: 0 24px 60px rgba(0,0,0,0.28);
    }
    .hero-copy h1{
        margin: 0;
        font-size: clamp(1.4rem, 1.2rem + 1vw, 2.15rem);
        font-weight: 700;
    }
    .hero-copy p{
        margin: 0.45rem 0 0;
        color: var(--muted);
        max-width: 42rem;
    }
    .hero-actions{
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .btn-modern{
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        padding: 0.92rem 1.15rem;
        border-radius: 16px;
        border: 1px solid transparent;
        color: var(--text);
        font-weight: 600;
        transition: var(--transition);
        cursor: pointer;
    }
    .btn-modern:hover{
        transform: translateY(-2px);
    }
    .btn-primary-glow{
        background: linear-gradient(135deg, #3B82F6, #06B6D4);
        box-shadow: 0 12px 30px rgba(59,130,246,0.28);
    }
    .btn-ghost{
        background: rgba(18,29,53,0.84);
        border-color: var(--border);
    }
    .btn-ghost:hover{
        border-color: rgba(59,130,246,0.45);
        box-shadow: 0 12px 24px rgba(59,130,246,0.14);
    }
    .stats-grid{
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }
    .stat-card{
        position: relative;
        overflow: hidden;
        padding: 1.25rem;
        border-radius: 24px;
        background: linear-gradient(180deg, rgba(13,21,37,0.96), rgba(18,29,53,0.9));
        border: 1px solid var(--border);
        box-shadow: 0 16px 40px rgba(0,0,0,0.22);
        transition: var(--transition);
    }
    .stat-card::before{
        content: "";
        position: absolute;
        top: 0;
        left: 1rem;
        right: 1rem;
        height: 3px;
        border-radius: 999px;
        opacity: 0;
        transition: var(--transition);
    }
    .stat-card:hover{
        transform: translateY(-5px);
        box-shadow: 0 22px 50px rgba(0,0,0,0.28);
    }
    .stat-card:hover::before{
        opacity: 1;
    }
    .stat-head{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .stat-icon{
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 16px;
        font-size: 1.1rem;
    }
    .stat-value{
        font-family: var(--mono);
        font-size: clamp(1.6rem, 1.45rem + 0.8vw, 2.15rem);
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    .stat-label{
        color: var(--muted);
        font-size: 0.92rem;
    }
    .accent-blue .stat-icon{
        background: rgba(59,130,246,0.16);
        color: #93C5FD;
    }
    .accent-blue::before{
        background: linear-gradient(90deg, #3B82F6, rgba(59,130,246,0));
    }
    .accent-cyan .stat-icon{
        background: rgba(6,182,212,0.16);
        color: #67E8F9;
    }
    .accent-cyan::before{
        background: linear-gradient(90deg, #06B6D4, rgba(6,182,212,0));
    }
    .accent-purple .stat-icon{
        background: rgba(139,92,246,0.16);
        color: #C4B5FD;
    }
    .accent-purple::before{
        background: linear-gradient(90deg, #8B5CF6, rgba(139,92,246,0));
    }
    .accent-green .stat-icon{
        background: rgba(16,185,129,0.16);
        color: #6EE7B7;
    }
    .accent-green::before{
        background: linear-gradient(90deg, #10B981, rgba(16,185,129,0));
    }
    .table-card{
        border-radius: 28px;
        background: linear-gradient(180deg, rgba(13,21,37,0.96), rgba(18,29,53,0.9));
        border: 1px solid var(--border);
        box-shadow: 0 26px 60px rgba(0,0,0,0.24);
        overflow: hidden;
    }
    .table-card-header{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.3rem 1.5rem 0.9rem;
    }
    .table-card-title{
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }
    .table-card-subtitle{
        margin-top: 0.25rem;
        color: var(--muted);
        font-size: 0.92rem;
    }
    .table-toolbar{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0 1.5rem 1rem;
        flex-wrap: wrap;
    }
    .toolbar-group{
        display: flex;
        align-items: center;
        gap: 0.65rem;
        color: var(--muted);
    }
    .toolbar-control{
        min-height: 44px;
        padding: 0.72rem 0.95rem;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: rgba(7,11,20,0.45);
        color: var(--text);
        outline: none;
        transition: var(--transition);
    }
    .toolbar-control:focus{
        border-color: rgba(59,130,246,0.4);
        box-shadow: 0 0 0 4px rgba(59,130,246,0.12);
    }
    .toolbar-control.mono{
        font-family: var(--mono);
    }
    .table-wrap{
        overflow-x: auto;
        padding: 0 1rem;
    }
    .modern-table{
        width: 100%;
        min-width: 980px;
        border-collapse: separate;
        border-spacing: 0;
        color: var(--text);
    }
    .modern-table thead th{
        padding: 1rem 0.9rem;
        color: #A5B4FC;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        border-bottom: 1px solid rgba(59,130,246,0.12);
        white-space: nowrap;
    }
    .modern-table tbody tr{
        transition: var(--transition);
    }
    .modern-table tbody tr:hover{
        background: rgba(59,130,246,0.07);
    }
    .modern-table td{
        padding: 1rem 0.9rem;
        border-bottom: 1px solid rgba(59,130,246,0.1);
        vertical-align: middle;
    }
    .table-index,
    .date-mono,
    .control-no{
        font-family: var(--mono);
    }
    .control-no{
        color: #67E8F9;
        font-size: 0.92rem;
    }
    .date-mono{
        color: var(--muted);
        font-size: 0.88rem;
    }
    .pill{
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.42rem 0.78rem;
        border-radius: 999px;
        border: 1px solid transparent;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .pill-clearance{
        background: rgba(59,130,246,0.14);
        border-color: rgba(59,130,246,0.28);
        color: #BFDBFE;
    }
    .pill-certification{
        background: rgba(139,92,246,0.14);
        border-color: rgba(139,92,246,0.28);
        color: #DDD6FE;
    }
    .pill-indigency{
        background: rgba(245,158,11,0.14);
        border-color: rgba(245,158,11,0.28);
        color: #FCD34D;
    }
    .resident{
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }
    .resident-avatar{
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
    .resident-name{
        font-weight: 600;
    }
    .status-pill{
        background: rgba(16,185,129,0.14);
        border-color: rgba(16,185,129,0.28);
        color: #A7F3D0;
    }
    .status-dot{
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--success);
        box-shadow: 0 0 0 0 rgba(16,185,129,0.6);
        animation: pulse 1.8s infinite;
    }
    .action-group{
        display: flex;
        align-items: center;
        gap: 0.55rem;
    }
    .action-btn{
        min-width: 42px;
        min-height: 38px;
        padding: 0.45rem 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.42rem;
        border-radius: 12px;
        border: 1px solid transparent;
        background: rgba(18,29,53,0.85);
        color: var(--text);
        transition: var(--transition);
        cursor: pointer;
    }
    .action-btn:hover{
        transform: translateY(-2px);
    }
    .action-btn.view{
        color: #BFDBFE;
        border-color: rgba(59,130,246,0.25);
        background: rgba(59,130,246,0.10);
    }
    .action-btn.print{
        color: #67E8F9;
        border-color: rgba(6,182,212,0.25);
        background: rgba(6,182,212,0.10);
    }
    .action-btn.delete{
        color: #FCA5A5;
        border-color: rgba(239,68,68,0.25);
        background: rgba(239,68,68,0.10);
    }
    .table-footer{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem 1.5rem 1.3rem;
        color: var(--muted);
        flex-wrap: wrap;
    }
    .pagination-group{
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .pagination-btn{
        min-width: 42px;
        height: 42px;
        padding: 0 0.85rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: rgba(18,29,53,0.85);
        color: var(--text);
        transition: var(--transition);
    }
    .pagination-btn.active,
    .pagination-btn:hover{
        border-color: rgba(59,130,246,0.38);
        background: rgba(59,130,246,0.14);
    }
    .pagination-btn:disabled{
        opacity: 0.45;
        cursor: not-allowed;
    }
    .empty-state{
        text-align: center;
        color: var(--muted);
        padding: 2rem 1rem;
    }
    @keyframes pulse{
        0%{box-shadow: 0 0 0 0 rgba(16,185,129,0.55);}
        70%{box-shadow: 0 0 0 10px rgba(16,185,129,0);}
        100%{box-shadow: 0 0 0 0 rgba(16,185,129,0);}
    }
    @media (max-width: 1199.98px){
        .stats-grid{
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 767.98px){
        .hero-panel,
        .table-card-header,
        .table-toolbar,
        .table-footer{
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .hero-panel{
            flex-direction: column;
            align-items: flex-start;
        }
        .hero-actions{
            width: 100%;
        }
        .hero-actions .btn-modern{
            flex: 1 1 auto;
        }
        .stats-grid{
            grid-template-columns: 1fr;
        }
        .table-toolbar{
            flex-direction: column;
            align-items: stretch;
        }
        .toolbar-group{
            width: 100%;
        }
        .toolbar-control{
            width: 100%;
        }
    }
</style>

<section class="certificates-page">
    <div class="hero-panel">
        <div class="hero-copy">
            <h1>Barangay Clearance & Certificates</h1>
            <p>Track issued documents, monitor activity, and access certificate records through a sleek streamlined workspace built for daily barangay operations.</p>
        </div>
        <div class="hero-actions">
            <button type="button" class="btn-modern btn-primary-glow" id="addCertificateBtn">
                <i class="fa-solid fa-plus"></i>
                <span>Add Certificate</span>
            </button>
            <button type="button" class="btn-modern btn-ghost" id="printAllBtn">
                <i class="fa-solid fa-print"></i>
                <span>Print All</span>
            </button>
        </div>
    </div>

    <div class="stats-grid">
        <?php foreach($stats as $stat): ?>
        <article class="stat-card accent-<?php echo $stat['accent'] ?>">
            <div class="stat-head">
                <div class="stat-icon">
                    <i class="fa-solid <?php echo $stat['icon'] ?>"></i>
                </div>
            </div>
            <div class="stat-value"><?php echo str_pad((string) $stat['value'], 2, '0', STR_PAD_LEFT) ?></div>
            <div class="stat-label"><?php echo $stat['label'] ?></div>
        </article>
        <?php endforeach; ?>
    </div>

    <article class="table-card">
        <div class="table-card-header">
            <div>
                <h2 class="table-card-title">Issued Records</h2>
                <div class="table-card-subtitle">Live-ready certificate register with modern filters, status cues, and quick actions.</div>
            </div>
        </div>

        <div class="table-toolbar">
            <div class="toolbar-group">
                <span>Show</span>
                <select id="entriesSelect" class="toolbar-control mono">
                    <option value="3">3</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>entries</span>
            </div>
            <div class="toolbar-group">
                <input type="text" id="tableSearch" class="toolbar-control" placeholder="Search certificates, types, control numbers...">
            </div>
        </div>

        <div class="table-wrap">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Control No.</th>
                        <th>Type</th>
                        <th>Resident Name</th>
                        <th>Date Issued</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="certificateTableBody">
                <?php foreach($sample_certificates as $index => $certificate): ?>
                    <?php
                    $initials = implode('', array_map(function($part){
                        return strtoupper(substr($part, 0, 1));
                    }, array_slice(explode(' ', $certificate['resident_name']), 0, 2)));
                    $type_class = strtolower($certificate['type']) === 'clearance' ? 'pill-clearance' : (strtolower($certificate['type']) === 'certification' ? 'pill-certification' : 'pill-indigency');
                    ?>
                    <tr
                        data-control="<?php echo htmlspecialchars($certificate['control_no']) ?>"
                        data-type="<?php echo htmlspecialchars($certificate['type']) ?>"
                        data-name="<?php echo htmlspecialchars($certificate['resident_name']) ?>"
                        data-date="<?php echo htmlspecialchars(date('M d, Y', strtotime($certificate['date_issued']))) ?>"
                        data-status="<?php echo htmlspecialchars($certificate['status']) ?>"
                    >
                        <td class="table-index"><?php echo $index + 1 ?></td>
                        <td class="control-no"><?php echo htmlspecialchars($certificate['control_no']) ?></td>
                        <td><span class="pill <?php echo $type_class ?>"><?php echo htmlspecialchars($certificate['type']) ?></span></td>
                        <td>
                            <div class="resident">
                                <div class="resident-avatar"><?php echo htmlspecialchars($initials) ?></div>
                                <div class="resident-name"><?php echo htmlspecialchars($certificate['resident_name']) ?></div>
                            </div>
                        </td>
                        <td class="date-mono"><?php echo htmlspecialchars(date('M d, Y', strtotime($certificate['date_issued']))) ?></td>
                        <td>
                            <span class="pill status-pill"><span class="status-dot"></span><?php echo htmlspecialchars($certificate['status']) ?></span>
                        </td>
                        <td>
                            <div class="action-group">
                                <button type="button" class="action-btn view" onclick="handleCertificateAction('View', '<?php echo htmlspecialchars($certificate['control_no']) ?>')">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                                <button type="button" class="action-btn print" onclick="handleCertificateAction('Print', '<?php echo htmlspecialchars($certificate['control_no']) ?>')">
                                    <i class="fa-solid fa-print"></i>
                                </button>
                                <button type="button" class="action-btn delete" onclick="handleCertificateAction('Delete', '<?php echo htmlspecialchars($certificate['control_no']) ?>')">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div id="tableMeta">Showing 1 to 3 of 3 entries</div>
            <div class="pagination-group">
                <button type="button" class="pagination-btn" id="prevPageBtn">Prev</button>
                <button type="button" class="pagination-btn active" id="pageIndicator">1</button>
                <button type="button" class="pagination-btn" id="nextPageBtn">Next</button>
            </div>
        </div>
    </article>
</section>

<script>
    const handleCertificateAction = (action, controlNo) => {
        const actionLabels = {
            View: 'Opening certificate preview',
            Print: 'Preparing print layout',
            Delete: 'Delete action queued for'
        }
        window.alert(`${actionLabels[action]} ${controlNo}.`)
    }

    $(function(){
        const $rows = $('#certificateTableBody tr')
        const $search = $('#tableSearch')
        const $entries = $('#entriesSelect')
        const $meta = $('#tableMeta')
        const $prev = $('#prevPageBtn')
        const $next = $('#nextPageBtn')
        const $indicator = $('#pageIndicator')
        let currentPage = 1

        const getFilteredRows = () => {
            const term = $search.val().toLowerCase().trim()
            return $rows.filter(function(){
                const haystack = [
                    $(this).data('control'),
                    $(this).data('type'),
                    $(this).data('name'),
                    $(this).data('date'),
                    $(this).data('status')
                ].join(' ').toLowerCase()
                return haystack.includes(term)
            })
        }

        const renderTable = () => {
            const perPage = parseInt($entries.val(), 10)
            const filteredRows = getFilteredRows()
            const total = filteredRows.length
            const totalPages = Math.max(1, Math.ceil(total / perPage))
            currentPage = Math.min(currentPage, totalPages)
            const start = (currentPage - 1) * perPage
            const end = start + perPage

            $rows.hide()
            filteredRows.slice(start, end).show()

            if(total === 0){
                if(!$('#emptyStateRow').length){
                    $('#certificateTableBody').append('<tr id="emptyStateRow"><td colspan="7" class="empty-state">No matching certificates found.</td></tr>')
                }
                $meta.text('Showing 0 to 0 of 0 entries')
            }else{
                $('#emptyStateRow').remove()
                $meta.text(`Showing ${start + 1} to ${Math.min(end, total)} of ${total} entries`)
            }

            $indicator.text(currentPage)
            $prev.prop('disabled', currentPage === 1 || total === 0)
            $next.prop('disabled', currentPage >= totalPages || total === 0)
        }

        $search.on('input', function(){
            currentPage = 1
            renderTable()
        })

        $entries.on('change', function(){
            currentPage = 1
            renderTable()
        })

        $prev.on('click', function(){
            if(currentPage > 1){
                currentPage -= 1
                renderTable()
            }
        })

        $next.on('click', function(){
            const totalPages = Math.max(1, Math.ceil(getFilteredRows().length / parseInt($entries.val(), 10)))
            if(currentPage < totalPages){
                currentPage += 1
                renderTable()
            }
        })

        $('#addCertificateBtn').on('click', function(){
            window.alert('Add Certificate clicked. Connect this button to your create modal when you are ready.')
        })

        $('#printAllBtn').on('click', function(){
            window.print()
        })

        renderTable()
    })
</script>

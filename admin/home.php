<?php
$household = $conn->query("SELECT count(household_id) as count FROM household_list")->fetchArray()['count'] ?? 0;
$resident_addresses = $conn->query("SELECT count(complaint_id) as count FROM complaint_list")->fetchArray()['count'] ?? 0;
$certificates = $conn->query("SELECT count(certificate_id) as count FROM certificate_list")->fetchArray()['count'] ?? 0;
$users_count = $_SESSION['type'] == 1 ? ($conn->query("SELECT count(admin_id) as count FROM admin_list")->fetchArray()['count'] ?? 0) : 0;
?>
<section class="app-page">
    <div class="page-header-panel">
        <div>
            <h1>Dashboard Overview</h1>
            <p>Monitor households, resident address records, certificates, and system activity from a single dark streamlined command center.</p>
        </div>
        <div class="page-actions">
            <a href="./?page=household" class="btn-modern btn-primary-glow"><i class="fa-solid fa-user-plus"></i><span>Add Resident</span></a>
            <a href="./?page=certificates" class="btn-modern btn-ghost-dark"><i class="fa-solid fa-file-circle-plus"></i><span>Open Certificates</span></a>
        </div>
    </div>

    <div class="stats-grid-modern">
        <a href="./?page=household" class="stat-tile blue">
            <div class="tile-icon"><i class="fa-solid fa-house-user"></i></div>
            <div class="tile-value"><?php echo str_pad((string) $household, 2, '0', STR_PAD_LEFT) ?></div>
            <div class="tile-label">Households</div>
        </a>
        <a href="./?page=complaints" class="stat-tile cyan">
            <div class="tile-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="tile-value"><?php echo str_pad((string) $resident_addresses, 2, '0', STR_PAD_LEFT) ?></div>
            <div class="tile-label">Residents Address List</div>
        </a>
        <a href="./?page=certificates" class="stat-tile purple">
            <div class="tile-icon"><i class="fa-solid fa-file-lines"></i></div>
            <div class="tile-value"><?php echo str_pad((string) $certificates, 2, '0', STR_PAD_LEFT) ?></div>
            <div class="tile-label">Certificates Issued</div>
        </a>
        <a href="<?php echo $_SESSION['type'] == 1 ? './?page=admin' : './?page=manage_account' ?>" class="stat-tile green">
            <div class="tile-icon"><i class="fa-solid fa-users-gear"></i></div>
            <div class="tile-value"><?php echo str_pad((string) ($_SESSION['type'] == 1 ? $users_count : 1), 2, '0', STR_PAD_LEFT) ?></div>
            <div class="tile-label"><?php echo $_SESSION['type'] == 1 ? 'System Users' : 'Active Account' ?></div>
        </a>
    </div>

    <div class="content-panel">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Resident Analytics</h2>
                <div class="panel-subtitle">Live charts for population distribution and civil status breakdown.</div>
            </div>
        </div>
        <div class="panel-body">
            <div class="row g-4">
                <div class="col-xl-4">
                    <div class="stat-tile blue h-100">
                        <div class="tile-icon"><i class="fa-solid fa-people-group"></i></div>
                        <div class="tile-value" id="totalPopulation">00</div>
                        <div class="tile-label">Total Residents</div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="content-panel h-100" style="background: rgba(7,11,20,0.24); box-shadow: none;">
                        <div class="panel-head">
                            <div>
                                <h3 class="panel-title" style="font-size:1rem;">By Sex</h3>
                                <div class="panel-subtitle">Current resident ratio</div>
                            </div>
                        </div>
                        <div class="panel-body pt-0">
                            <div style="height: 280px;">
                                <canvas id="sexChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="content-panel h-100" style="background: rgba(7,11,20,0.24); box-shadow: none;">
                        <div class="panel-head">
                            <div>
                                <h3 class="panel-title" style="font-size:1rem;">By Civil Status</h3>
                                <div class="panel-subtitle">Household demographics snapshot</div>
                            </div>
                        </div>
                        <div class="panel-body pt-0">
                            <div style="height: 280px;">
                                <canvas id="civilStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function(){
        loadResidentStats()
    })

    function loadResidentStats() {
        $.ajax({
            url: './../Actions.php?a=get_resident_stats',
            method: 'GET',
            dataType: 'json',
            success: function(resp) {
                if(resp.status == 'success') {
                    $('#totalPopulation').text(String(resp.data.total_population || 0).padStart(2, '0'))

                    const commonOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, color: '#94A3B8' },
                                grid: { color: 'rgba(59,130,246,0.12)' }
                            },
                            y: {
                                ticks: { color: '#E2E8F0' },
                                grid: { display: false }
                            }
                        }
                    }

                    new Chart(document.getElementById('sexChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['Male', 'Female'],
                            datasets: [{
                                data: [resp.data.sex.male || 0, resp.data.sex.female || 0],
                                backgroundColor: ['rgba(59,130,246,0.82)', 'rgba(6,182,212,0.82)'],
                                borderRadius: 10,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            ...commonOptions,
                            indexAxis: 'y'
                        }
                    })

                    new Chart(document.getElementById('civilStatusChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['Single', 'Married', 'Widowed', 'Divorced', 'Separated'],
                            datasets: [{
                                data: [
                                    resp.data.civil_status.Single || 0,
                                    resp.data.civil_status.Married || 0,
                                    resp.data.civil_status.Widowed || 0,
                                    resp.data.civil_status.Divorced || 0,
                                    resp.data.civil_status.Separated || 0
                                ],
                                backgroundColor: [
                                    'rgba(59,130,246,0.82)',
                                    'rgba(6,182,212,0.82)',
                                    'rgba(139,92,246,0.82)',
                                    'rgba(16,185,129,0.82)',
                                    'rgba(245,158,11,0.82)'
                                ],
                                borderRadius: 10,
                                borderSkipped: false
                            }]
                        },
                        options: {
                            ...commonOptions,
                            indexAxis: 'y'
                        }
                    })
                }
            }
        })
    }
</script>

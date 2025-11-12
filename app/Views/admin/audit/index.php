<?php
$entries = $data['entries'] ?? [];
$dates = $data['dates'] ?? [];
$selectedDate = $data['selectedDate'] ?? date('Y-m-d');
$level = $data['level'] ?? '';
$q = $data['q'] ?? '';
$page = (int)($data['page'] ?? 1);
$perPage = (int)($data['perPage'] ?? 50);
$total = (int)($data['total'] ?? 0);
$pages = max(1, (int)ceil($total / max(1,$perPage)));

$content = ' 
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="h4 mb-1">Audit Logs</h2>
      <p class="text-muted mb-0">Security and activity trail with filters and export</p>
    </div>
    <div>
      <a class="btn btn-outline-primary btn-sm" href="/admin/audit-logs/download?date=' . htmlspecialchars($selectedDate) . '">
        <i class="bi bi-download me-1"></i>Download ' . htmlspecialchars($selectedDate) . '
      </a>
    </div>
  </div>

  <form class="card shadow-sm mb-4" method="GET" action="/admin/audit-logs">
    <div class="card-body row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Date</label>
        <select class="form-select" name="date">';
        foreach ($dates as $d) {
            $sel = ($d === $selectedDate) ? ' selected' : '';
            $content .= '<option value="' . htmlspecialchars($d) . '"' . $sel . '>' . htmlspecialchars($d) . '</option>';
        }
        if (empty($dates)) {
            $content .= '<option value="' . htmlspecialchars($selectedDate) . '" selected>' . htmlspecialchars($selectedDate) . '</option>';
        }
        $content .= '</select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Level</label>
        <select class="form-select" name="level">
          <option value="">All</option>
          <option value="INFO"' . ($level==='INFO'?' selected':'') . '>INFO</option>
          <option value="WARNING"' . ($level==='WARNING'?' selected':'') . '>WARNING</option>
          <option value="ERROR"' . ($level==='ERROR'?' selected':'') . '>ERROR</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Search</label>
        <input type="text" class="form-control" name="q" value="' . htmlspecialchars($q) . '" placeholder="Action, user, IP, details...">
      </div>
      <div class="col-md-2">
        <label class="form-label">Per page</label>
        <input type="number" class="form-control" name="per_page" min="1" max="200" value="' . htmlspecialchars((string)$perPage) . '">
      </div>
      <div class="col-md-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
        <a class="btn btn-outline-secondary" href="/admin/audit-logs"><i class="bi bi-x-circle me-1"></i>Clear</a>
      </div>
    </div>
  </form>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover table-striped mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th width="160">Timestamp</th>
              <th width="100">Level</th>
              <th width="220">Action</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>';
            if (empty($entries)) {
                $content .= '<tr><td colspan="4" class="text-center text-muted py-4">No entries</td></tr>';
            } else {
                foreach ($entries as $e) {
                    $content .= '<tr>
                      <td><code>' . htmlspecialchars($e['ts'] ?? '') . '</code></td>
                      <td><span class="badge ' . (strtoupper($e['level']??'')==='ERROR'?'bg-danger':(strtoupper($e['level']??'')==='WARNING'?'bg-warning text-dark':'bg-info')) . '">' . htmlspecialchars(strtoupper($e['level'] ?? '')) . '</span></td>
                      <td>' . htmlspecialchars($e['action'] ?? '') . '</td>
                      <td><pre class="mb-0 small" style="white-space: pre-wrap;">' . htmlspecialchars(json_encode($e['details'] ?? [], JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT)) . '</pre></td>
                    </tr>';
                }
            }
          $content .= '</tbody>
        </table>
      </div>
      <div class="d-flex justify-content-between align-items-center p-3">
        <div class="text-muted small">Showing page ' . htmlspecialchars((string)$page) . ' of ' . htmlspecialchars((string)$pages) . ' (' . htmlspecialchars((string)$total) . ' items)</div>
        <div class="btn-group">
          <a class="btn btn-outline-secondary btn-sm' . ($page<=1?' disabled':'') . '" href="' . htmlspecialchars('/admin/audit-logs?date='.$selectedDate.'&level='.$level.'&q='.$q.'&per_page='.$perPage.'&page='.max(1,$page-1)) . '">Prev</a>
          <a class="btn btn-outline-secondary btn-sm' . ($page>=$pages?' disabled':'') . '" href="' . htmlspecialchars('/admin/audit-logs?date='.$selectedDate.'&level='.$level.'&q='.$q.'&per_page='.$perPage.'&page='.min($pages,$page+1)) . '">Next</a>
        </div>
      </div>
    </div>
  </div>
</div>
';

include __DIR__ . '/../../layouts/admin.php';

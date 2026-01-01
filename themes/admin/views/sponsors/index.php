<?php
/**
 * Sponsors Management Interface
 */
$page_title = $page_title ?? 'Manage Sponsors - Admin Panel';
$sponsors = $sponsors ?? [];
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-handshake me-2 text-primary"></i>Sponsorships</h1>
        <p class="text-gray-600">Manage B2B partners and ad campaigns.</p>
    </div>
    <div class="col-md-6 text-end">
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addSponsorModal">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Add Partner
        </button>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary">Active Partners</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="sponsorsTable">
                <thead class="table-light">
                    <tr>
                        <th>Partner</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Active Campaigns</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($sponsors as $sponsor): ?>
                    <tr>
                        <td class="align-middle">
                            <div class="d-flex align-items-center">
                                <?php if($sponsor['logo_path']): ?>
                                    <img src="/public/uploads/sponsors/<?= $sponsor['logo_path'] ?>" class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="rounded me-2 bg-light d-flex align-items-center justify-content-center text-muted fw-bold" style="width: 40px; height: 40px; border: 1px solid #ddd;">
                                        <?= substr($sponsor['name'], 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($sponsor['name']) ?></div>
                                    <a href="<?= htmlspecialchars($sponsor['website_url']) ?>" target="_blank" class="small text-muted"><i class="fas fa-link me-1"></i>Website</a>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">
                            <div><?= htmlspecialchars($sponsor['contact_person']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($sponsor['contact_email']) ?></small>
                        </td>
                        <td class="align-middle">
                            <?php if($sponsor['status'] === 'active'): ?>
                                <span class="badge bg-success rounded-pill">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary rounded-pill">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="align-middle">
                            <!-- Helper to count campaigns could go here -->
                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#createCampaignModal" 
                                    onclick="setSponsorId(<?= $sponsor['id'] ?>, '<?= htmlspecialchars($sponsor['name'], ENT_QUOTES) ?>')">
                                <i class="fas fa-bullhorn me-1"></i> New Campaign
                            </button>
                        </td>
                        <td class="align-middle">
                            <button class="btn btn-sm btn-light text-primary"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-light text-danger"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($sponsors)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No partners found. Add your first sponsor!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Sponsor Modal -->
<div class="modal fade" id="addSponsorModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="/admin/sponsors/store" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title">Add New Partner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Website URL</label>
                    <input type="url" name="website_url" class="form-control" placeholder="https://...">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="contact_email" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Partner</button>
            </div>
        </form>
    </div>
</div>

<!-- Create Campaign Modal -->
<div class="modal fade" id="createCampaignModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="/admin/sponsors/campaigns/create">
            <input type="hidden" name="sponsor_id" id="campaignSponsorId">
            <div class="modal-header">
                <h5 class="modal-title">Launch Campaign for <span id="campaignSponsorName" class="text-primary"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Campaign Title (Internal)</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Summer Cement Promo">
                </div>
                <div class="mb-3">
                    <label class="form-label">Target Calculator (Slug)</label>
                    <input type="text" name="calculator_slug" class="form-control" required placeholder="e.g. concrete">
                    <small class="text-muted">The URL slug of the calculator where this ad will appear.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ad Text</label>
                    <input type="text" name="ad_text" class="form-control" placeholder="Global Steel - Strongest in Nepal">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" required value="<?= date('Y-m-d', strtotime('+30 days')) ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Priority</label>
                        <input type="number" name="priority" class="form-control" value="0">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Max Impressions</label>
                        <input type="number" name="max_impressions" class="form-control" value="0" placeholder="0 = Unlimited">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Launch Campaign</button>
            </div>
        </form>
    </div>
</div>

<script>
function setSponsorId(id, name) {
    document.getElementById('campaignSponsorId').value = id;
    document.getElementById('campaignSponsorName').innerText = name;
}
</script>

<?php
// app/Views/user/billing.php
$this->extend('layouts/main');
$this->section('content');
?>

<div class="row g-4">
    <!-- ── Billing Form ─────────────────────────────────── -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
            <div class="card-header bg-white border-bottom py-3" style="border-radius:14px 14px 0 0;">
                <h6 class="mb-0 fw-700"><i class="bi bi-calculator-fill text-primary me-2"></i>Compute Electric Bill</h6>
            </div>
            <div class="card-body">
                <div id="formErrors" class="alert alert-danger d-none small"></div>

                <form id="billingForm">
                    <div class="mb-3">
                        <label class="form-label fw-600 small">Select Client <span class="text-danger">*</span></label>
                        <select class="form-select" name="client_id" id="client_id" required>
                            <option value="">— Choose client —</option>
                        </select>
                    </div>

                    <div id="clientInfo" class="alert alert-info small d-none mb-3">
                        <i class="bi bi-person-vcard me-1"></i>
                        <strong>Meter No:</strong> <span id="meterNo"></span> &nbsp;|&nbsp;
                        <strong>Address:</strong> <span id="clientAddr"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600 small">Billing Month <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" name="billing_month" id="billing_month" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-600 small">Previous Reading (KW) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="prev_reading" id="prev_reading" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600 small">Current Reading (KW) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="curr_reading" id="curr_reading" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600 small">Due Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="due_date" id="due_date" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600 small">Notes (Optional)</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="btnCompute">
                        <i class="bi bi-calculator me-2"></i>Compute & Save Bill
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Rate Reference & Result ──────────────────────── -->
    <div class="col-lg-6">
        <!-- Rate Table -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
            <div class="card-header bg-white border-bottom py-3" style="border-radius:14px 14px 0 0;">
                <h6 class="mb-0 fw-700"><i class="bi bi-table text-warning me-2"></i>Tiered Rate Reference</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr><th>Consumption Range</th><th>Rate per KW</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1 – 200 KW</td>
                            <td><span class="badge bg-success">₱10.00</span></td>
                        </tr>
                        <tr>
                            <td>201 – 500 KW</td>
                            <td><span class="badge bg-warning text-dark">₱13.00</span></td>
                        </tr>
                        <tr>
                            <td>501 KW and above</td>
                            <td><span class="badge bg-danger">₱15.00</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Live Preview -->
        <div class="card border-0 shadow-sm" style="border-radius:14px;" id="previewCard">
            <div class="card-header bg-white border-bottom py-3" style="border-radius:14px 14px 0 0;">
                <h6 class="mb-0 fw-700"><i class="bi bi-eye-fill text-info me-2"></i>Live Preview</h6>
            </div>
            <div class="card-body">
                <div class="row text-center g-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="text-muted small">Consumption</div>
                            <div class="fs-4 fw-700 text-primary" id="previewKW">—</div>
                            <div class="small text-muted">KW</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3" style="background:#f0fdf4;">
                            <div class="text-muted small">Amount Due</div>
                            <div class="fs-4 fw-700 text-success" id="previewAmt">—</div>
                            <div class="small text-muted">PHP</div>
                        </div>
                    </div>
                </div>
                <div id="tierBreakdown" class="mt-3 small text-muted d-none">
                    <hr>
                    <div class="fw-600 mb-1">Breakdown:</div>
                    <div id="breakdownLines"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>
<?php $this->section('scripts'); ?>
<script>
$(function () {
    const BASE = '<?= base_url() ?>';
    let clients = [];

    // Load clients
    $.get(BASE + 'user/billing/clients', function (data) {
        clients = data;
        $.each(data, function (i, c) {
            $('#client_id').append(`<option value="${c.id}" data-meter="${c.meter_no}" data-addr="${c.address}">${c.full_name} (${c.client_no})</option>`);
        });
    });

    // Show client info on select
    $('#client_id').on('change', function () {
        const opt = $(this).find(':selected');
        if ($(this).val()) {
            $('#meterNo').text(opt.data('meter'));
            $('#clientAddr').text(opt.data('addr'));
            $('#clientInfo').removeClass('d-none');
        } else {
            $('#clientInfo').addClass('d-none');
        }
    });

    // Live preview calculation
    function computePreview(kw) {
        let amount = 0, lines = '';
        if (kw <= 0) return { amount: 0, lines: '' };
        if (kw <= 200) {
            amount = kw * 10;
            lines = `${kw} KW × ₱10.00 = ₱${(kw*10).toFixed(2)}`;
        } else if (kw <= 500) {
            amount = 200*10 + (kw-200)*13;
            lines = `200 KW × ₱10.00 = ₱2,000.00<br>${(kw-200).toFixed(2)} KW × ₱13.00 = ₱${((kw-200)*13).toFixed(2)}`;
        } else {
            amount = 200*10 + 300*13 + (kw-500)*15;
            lines = `200 KW × ₱10.00 = ₱2,000.00<br>300 KW × ₱13.00 = ₱3,900.00<br>${(kw-500).toFixed(2)} KW × ₱15.00 = ₱${((kw-500)*15).toFixed(2)}`;
        }
        return { amount, lines };
    }

    function updatePreview() {
        const prev = parseFloat($('#prev_reading').val()) || 0;
        const curr = parseFloat($('#curr_reading').val()) || 0;
        const kw   = curr - prev;

        if (curr > prev && kw > 0) {
            const { amount, lines } = computePreview(kw);
            $('#previewKW').text(kw.toFixed(2));
            $('#previewAmt').text('₱' + amount.toLocaleString('en-PH', {minimumFractionDigits:2}));
            $('#breakdownLines').html(lines + `<br><strong class="text-dark">Total: ₱${amount.toFixed(2)}</strong>`);
            $('#tierBreakdown').removeClass('d-none');
        } else {
            $('#previewKW').text('—');
            $('#previewAmt').text('—');
            $('#tierBreakdown').addClass('d-none');
        }
    }

    $('#prev_reading, #curr_reading').on('input', updatePreview);

    // Submit
    $('#billingForm').on('submit', function (e) {
        e.preventDefault();
        $('#formErrors').addClass('d-none').html('');
        const btn = $('#btnCompute');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Computing...');

        $.post(BASE + 'user/billing/compute', $(this).serialize(), function (res) {
            btn.prop('disabled', false).html('<i class="bi bi-calculator me-2"></i>Compute & Save Bill');
            if (res.success) {
                showToast(res.message + ` | ₱${parseFloat(res.amount).toFixed(2)}`, 'success');
                $('#billingForm')[0].reset();
                $('#clientInfo').addClass('d-none');
                $('#previewKW').text('—');
                $('#previewAmt').text('—');
                $('#tierBreakdown').addClass('d-none');
            } else {
                let html = '<ul class="mb-0">';
                $.each(res.errors, (k, v) => { html += `<li>${v}</li>`; });
                html += '</ul>';
                $('#formErrors').removeClass('d-none').html(html);
            }
        }).fail(function () {
            btn.prop('disabled', false).html('<i class="bi bi-calculator me-2"></i>Compute & Save Bill');
            showToast('Server error. Please try again.', 'danger');
        });
    });
});
</script>
<?php $this->endSection(); ?>
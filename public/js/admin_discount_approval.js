/**
 * Real-Time Admin Discount Approval Handler for Vawa Scents POS
 */
$(document).ready(function() {
    // Only run if user can approve discounts (checked via header or window flag)
    var isApprover = $('meta[name="user-can-approve-discounts"]').attr('content') === '1' || $('#admin_discount_approval_flag').length > 0;
    if (!isApprover) {
        return;
    }

    var activeApprovalId = null;
    var seenRequestIds = {};
    var pollingInterval = null;

    // Play pleasant notification chime using Web Audio API
    function playNotificationChime() {
        try {
            var AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            var ctx = new AudioContext();
            
            var now = ctx.currentTime;
            var osc1 = ctx.createOscillator();
            var osc2 = ctx.createOscillator();
            var gain = ctx.createGain();

            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(587.33, now); // D5
            osc1.frequency.setValueAtTime(880, now + 0.1); // A5

            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880, now + 0.15); // A5
            osc2.frequency.setValueAtTime(1174.66, now + 0.25); // D6

            gain.gain.setValueAtTime(0.3, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.6);

            osc1.connect(gain);
            osc2.connect(gain);
            gain.connect(ctx.destination);

            osc1.start(now);
            osc1.stop(now + 0.3);
            osc2.start(now + 0.15);
            osc2.stop(now + 0.6);
        } catch (e) {
            console.log('Audio chime error:', e);
        }
    }

    // Poll for pending discount approvals
    function checkPendingApprovals() {
        $.ajax({
            url: '/pos/discount-approvals/pending',
            dataType: 'json',
            success: function(res) {
                var count = res.count || 0;
                var requests = res.requests || [];

                // Update badge in header if element exists
                var badge = $('#pending_discount_approvals_badge');
                if (badge.length) {
                    if (count > 0) {
                        badge.text(count).removeClass('tw-hidden').show();
                    } else {
                        badge.addClass('tw-hidden').hide();
                    }
                }

                if (count > 0) {
                    // Check for newly arrived requests
                    var hasNew = false;
                    $.each(requests, function(i, req) {
                        if (!seenRequestIds[req.id]) {
                            seenRequestIds[req.id] = true;
                            hasNew = true;
                        }
                    });

                    // If modal is not currently open, show the latest request
                    if (!$('#adminDiscountApprovalModal').hasClass('in') && !$('#adminDiscountApprovalModal').is(':visible')) {
                        if (hasNew || !activeApprovalId) {
                            openApprovalModal(requests[0].id);
                        }
                    }
                }
            }
        });
    }

    // Open Approval Modal with full request details
    window.openApprovalModal = function(id) {
        activeApprovalId = id;
        $('#current_approval_id').val(id);

        $.ajax({
            url: '/pos/discount-approvals/details/' + id,
            dataType: 'json',
            success: function(res) {
                if (!res.success) return;

                playNotificationChime();

                $('#approval_request_meta').html(
                    '<strong>' + res.cashier_name + '</strong> &bull; ' + res.location_name + ' &bull; <span class="tw-text-amber-300">' + res.created_at + '</span>'
                );

                if (res.cashier_notes && res.cashier_notes.trim() !== '') {
                    $('#approval_cashier_note').text(res.cashier_notes);
                    $('#approval_cashier_note_wrapper').removeClass('tw-hidden');
                } else {
                    $('#approval_cashier_note_wrapper').addClass('tw-hidden');
                }

                // Render Cart items
                var tbody = $('#approval_cart_items_table tbody');
                tbody.empty();
                var items = res.cart_snapshot || [];
                if (items.length === 0) {
                    tbody.append('<tr><td colspan="5" class="text-center text-muted">No line item breakdown available</td></tr>');
                } else {
                    $.each(items, function(i, item) {
                        var qty = item.quantity || 1;
                        var price = item.unit_price || 0;
                        tbody.append(
                            '<tr>' +
                                '<td>' + (i + 1) + '</td>' +
                                '<td><strong>' + (item.product_name || item.name || 'Product') + '</strong>' + (item.sub_sku ? ' <span class="tw-text-xs tw-text-gray-400">(' + item.sub_sku + ')</span>' : '') + '</td>' +
                                '<td class="text-center">' + qty + '</td>' +
                                '<td class="text-right">' + __currency_trans_from_en(price, true) + '</td>' +
                                '<td class="text-right tw-font-semibold">' + __currency_trans_from_en(price * qty, true) + '</td>' +
                            '</tr>'
                        );
                    });
                }

                // Financial Cards
                var origTotal = parseFloat(res.original_total) || 0;
                var reqDiscount = parseFloat(res.requested_discount_amount) || 0;
                var resTotal = parseFloat(res.resulting_total) || 0;

                $('#approval_original_total').html(__currency_trans_from_en(origTotal, true));
                
                var discountLabel = res.discount_type === 'percentage' ? reqDiscount + '%' : __currency_trans_from_en(reqDiscount, true);
                $('#approval_requested_discount').html(discountLabel);
                $('#approval_resulting_total').html(__currency_trans_from_en(resTotal, true));

                // Reset modify fields
                $('input[name="admin_approval_action_type"][value="approve"]').prop('checked', true);
                $('#approval_modify_fields_wrapper').addClass('tw-hidden');
                $('#admin_mod_discount_type').val(res.discount_type).trigger('change');
                $('#admin_mod_discount_amount').val(reqDiscount);
                $('#admin_approval_notes').val('');

                recalculateAdminResultingTotal();

                $('#adminDiscountApprovalModal').modal('show');
            }
        });
    };

    // Toggle between Approve As Requested vs Modify
    window.toggleApprovalMode = function(mode) {
        if (mode === 'modify') {
            $('#approval_modify_fields_wrapper').removeClass('tw-hidden');
            $('#btnAdminApproveDiscount').html('<i class="fas fa-edit tw-mr-1"></i> Approve Modified Amount').removeClass('btn-success').addClass('btn-primary');
        } else {
            $('#approval_modify_fields_wrapper').addClass('tw-hidden');
            $('#btnAdminApproveDiscount').html('<i class="fas fa-check-circle tw-mr-1"></i> Approve Discount').removeClass('btn-primary').addClass('btn-success');
        }
        recalculateAdminResultingTotal();
    };

    // Recalculate preview in modify mode
    window.recalculateAdminResultingTotal = function() {
        var origTotal = __read_number($('#approval_original_total'));
        // If NaN, extract float
        if (isNaN(origTotal) || origTotal === 0) {
            origTotal = parseFloat($('#approval_original_total').text().replace(/[^0-9.-]+/g, '')) || 0;
        }

        var mode = $('input[name="admin_approval_action_type"]:checked').val();
        if (mode === 'modify') {
            var discType = $('#admin_mod_discount_type').val();
            var discAmount = parseFloat($('#admin_mod_discount_amount').val()) || 0;
            var finalDiscountVal = 0;
            if (discType === 'percentage') {
                finalDiscountVal = (origTotal * discAmount) / 100;
            } else {
                finalDiscountVal = discAmount;
            }
            var newTotal = Math.max(0, origTotal - finalDiscountVal);
            $('#admin_mod_resulting_total_preview').html(__currency_trans_from_en(newTotal, true));
        }
    };

    // Submit Response (Approve, Modify, or Reject)
    window.submitAdminDiscountResponse = function(action) {
        var id = $('#current_approval_id').val();
        if (!id) return;

        var mode = $('input[name="admin_approval_action_type"]:checked').val();
        var actualAction = action === 'reject' ? 'reject' : (mode === 'modify' ? 'modify' : 'approve');

        var data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            action: actualAction,
            approved_discount_type: $('#admin_mod_discount_type').val(),
            approved_discount_amount: $('#admin_mod_discount_amount').val(),
            admin_notes: $('#admin_approval_notes').val(),
        };

        var btn = actualAction === 'reject' ? $('#btnAdminRejectDiscount') : $('#btnAdminApproveDiscount');
        var origText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin tw-mr-1"></i> Processing...');

        $.ajax({
            url: '/pos/discount-approvals/respond/' + id,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(res) {
                btn.prop('disabled', false).html(origText);
                if (res.success) {
                    toastr.success(res.msg);
                    $('#adminDiscountApprovalModal').modal('hide');
                    activeApprovalId = null;

                    // Immediately recheck for remaining requests
                    setTimeout(checkPendingApprovals, 500);
                } else {
                    toastr.error(res.msg || 'Error processing response');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html(origText);
                var errorMsg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Error processing discount approval';
                toastr.error(errorMsg);
            }
        });
    };

    // Start polling every 5 seconds
    pollingInterval = setInterval(checkPendingApprovals, 5000);
    // Initial check
    setTimeout(checkPendingApprovals, 1500);
});

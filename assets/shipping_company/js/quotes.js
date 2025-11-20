// ==============================
// Initialize on document ready
// ==============================
$(document).ready(function () {
    // Set default selections for add mode
    $('#cod_yes').prop('checked', true);
    $('#status_active').prop('checked', true);
});

// ==============================
// Additional Charges Management
// ==============================
let chargeCounter = 0;

function addChargeRow(key = '', value = '') {
    chargeCounter++;
    const row = `
            <div class="charge-row" data-charge-id="${chargeCounter}">
                <input type="text" class="form-control charge-key" placeholder="Charge name (e.g., GST)" value="${key}">
                <input type="number" step="0.01" min="0" class="form-control charge-value" placeholder="Amount" value="${value}">
                <button type="button" class="btn-remove-charge" onclick="removeChargeRow(${chargeCounter})">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        `;
    $('#additionalChargesContainer').append(row);
}

function removeChargeRow(id) {
    $(`[data-charge-id="${id}"]`).remove();
}

function collectCharges() {
    const charges = {};
    $('.charge-row').each(function () {
        const key = $(this).find('.charge-key').val().trim();
        const value = $(this).find('.charge-value').val().trim();
        if (key && value) {
            charges[key] = parseFloat(value);
        }
    });
    return charges;
}

function loadCharges(chargesJson) {
    $('#additionalChargesContainer').empty();
    chargeCounter = 0;

    if (chargesJson) {
        try {
            const charges = typeof chargesJson === 'string' ? JSON.parse(chargesJson) : chargesJson;
            for (const [key, value] of Object.entries(charges)) {
                addChargeRow(key, value);
            }
        } catch (e) {
            console.error('Error parsing charges:', e);
        }
    }
}

$(document).on('click', '#addChargeBtn', function () {
    addChargeRow();
});

// ==============================
// Reset form helper
// ==============================
function resetQuoteForm() {
    $('#quoteForm')[0].reset();
    $('#quote_id').val('');

    // Reset radio buttons to defaults
    $('#cod_yes').prop('checked', true);
    $('#status_active').prop('checked', true);

    // Clear additional charges
    $('#additionalChargesContainer').empty();
    chargeCounter = 0;

    // Clear validation states
    $('#quoteForm').find('.is-invalid').removeClass('is-invalid');
    $('#quoteForm').find('.invalid-feedback').remove();
}

// ==============================
// Add/Edit Quote Form Submit
// ==============================
$(document).on('submit', '#quoteForm', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    var $form = $(this);
    var $submitBtn = $('#saveQuoteBtn');
    var originalBtnHtml = $submitBtn.html();
    var quoteId = $('#quote_id').val();

    // Collect additional charges and set to hidden field
    const charges = collectCharges();
    $('#additional_charges_json').val(JSON.stringify(charges));

    // Disable button
    $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');

    var formData = new FormData(this);

    $.ajax({
        type: "POST",
        url: base_url + 'shipping_company/quotes/' + (quoteId ? 'update' : 'create'),
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        dataType: 'json',
        success: function (result) {
            // Restore button
            $submitBtn.prop('disabled', false).html(originalBtnHtml);

            if (result.error == false) {
                // Hide modal
                $('#quoteModal').modal('hide');

                // Refresh table
                $('#quotesTable').bootstrapTable('refresh');

                // Show success toast
                if (typeof iziToast !== 'undefined') {
                    iziToast.success({
                        title: 'Success',
                        message: result.message || 'Quote saved successfully',
                        position: 'topRight'
                    });
                }
            } else {
                // Show error toast
                if (typeof iziToast !== 'undefined') {
                    iziToast.error({
                        title: 'Error',
                        message: result.message || 'Something went wrong',
                        position: 'topRight'
                    });
                }
            }

            // Update CSRF token
            if (result.csrfName && result.csrfHash) {
                $('input[name="' + result.csrfName + '"]').val(result.csrfHash);
            }
        },
        error: function (xhr, status, error) {
            $submitBtn.prop('disabled', false).html(originalBtnHtml);

            if (typeof iziToast !== 'undefined') {
                iziToast.error({
                    title: 'Error',
                    message: 'Server error. Please try again.',
                    position: 'topRight'
                });
            }
            console.error('AJAX Error:', error);
        }
    });

    return false;
});

// ==============================
// Open Add Quote Modal
// ==============================
$(document).on('click', '#addQuoteBtn', function () {
    resetQuoteForm();
    $('#quoteModalTitle').text('Add Quote');
    $('#quoteModal').modal('show');
});

// ==============================
// Open Edit Quote Modal
// ==============================
$(document).on('click', '.edit-quote', function () {
    var id = $(this).data('id');

    $.ajax({
        url: base_url + 'shipping_company/quotes/get/' + id,
        type: 'GET',
        dataType: 'json',
        success: function (resp) {
            if (resp.error) {
                if (typeof iziToast !== 'undefined') {
                    iziToast.error({
                        title: 'Error',
                        message: resp.message || 'Quote not found',
                        position: 'topRight'
                    });
                }
                return;
            }

            var d = resp.data;

            // Reset form first
            resetQuoteForm();

            // Populate fields
            $('#quote_id').val(d.id);
            $('#zipcode').val(d.zipcode);
            $('#price').val(d.price);
            $('#eta_text').val(d.eta_text);

            // Set radio buttons
            if (d.cod_available == 1) {
                $('#cod_yes').prop('checked', true);
            } else {
                $('#cod_no').prop('checked', true);
            }

            if (d.is_active == 1) {
                $('#status_active').prop('checked', true);
            } else {
                $('#status_inactive').prop('checked', true);
            }

            // Load additional charges
            loadCharges(d.additional_charges);

            // Update modal title and show
            $('#quoteModalTitle').text('Edit Quote #' + d.id);
            $('#quoteModal').modal('show');
        },
        error: function (xhr, status, error) {
            if (typeof iziToast !== 'undefined') {
                iziToast.error({
                    title: 'Error',
                    message: 'Failed to load quote data',
                    position: 'topRight'
                });
            }
            console.error('AJAX Error:', error);
        }
    });
});

// ==============================
// Delete Quote
// ==============================
$(document).on('click', '.delete-quote', function () {
    var id = $(this).data('id');
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = $('input[name="' + csrfName + '"]').val();

    Swal.fire({
        title: 'Delete Quote?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            var deleteData = {
                id: id
            };
            deleteData[csrfName] = csrfHash;

            return $.ajax({
                url: base_url + 'shipping_company/quotes/delete',
                type: 'POST',
                data: deleteData,
                dataType: 'json'
            }).done(function (response) {
                // Update CSRF token immediately
                if (response.csrfName && response.csrfHash) {
                    $('input[name="' + response.csrfName + '"]').val(response.csrfHash);
                    csrfHash = response.csrfHash;
                }

                if (response.error == false) {
                    return response;
                } else {
                    throw new Error(response.message || 'Delete failed');
                }
            }).fail(function (xhr, status, error) {
                console.error('Delete AJAX failed:', xhr.responseText);
                throw new Error('Server error: ' + error);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then(function (result) {
        if (result.value) {
            var response = result.value;

            // Show success message
            Swal.fire({
                title: 'Deleted!',
                text: response.message || 'Quote has been deleted.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            }).then(function () {
                // Refresh table after success message
                $('#quotesTable').bootstrapTable('refresh');
            });
        }
    }).catch(function (error) {
        console.error('SweetAlert error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to delete quote. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});

// ==============================
// Modal events
// ==============================
$('#quoteModal').on('hidden.bs.modal', function () {
    resetQuoteForm();
});

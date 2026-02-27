<style>
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(100%); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    .sliding-warning {
        color: #ff0000;
        font-weight: bold;
        font-size: 11px;
        opacity: 0;
        transform: translateX(100%);
        transition: opacity 0.8s ease-in-out, transform 0.8s ease-in-out;
    }

    .select2-container--default .select2-selection--single {
        border: 1px solid #ced4da;
    }

    .select2-container--default.is-invalid .select2-selection--single {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: none;
        color: #dc3545;
        font-size: 80%;
        margin-top: 0.25rem;
    }
</style>

<div class="modal custom-modal fade" id="registerProfessionalModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="registerProfessionalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-award me-2"></i>Register New Professional
                </h5>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="bx bx-x"></i>
                </button>
            </div>

            <div class="modal-body">
                <form class="needs-validation" action="{{ route('professionals.store') }}" method="POST" onsubmit="return validateForm()" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="registration_no" class="form-label">
                                <i class="bx bx-id-card me-2"></i>Registration No <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="registration_no" id="registration_no" placeholder="e.g. REG-2024-001" required>
                            <div class="invalid-feedback">Please enter a registration number</div>
                        </div>

                        <div class="col-md-6">
                            <label for="full_name" class="form-label">
                                <i class="bx bx-user me-2"></i>Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="full_name" id="full_name" placeholder="Enter professional's full name" required>
                            <div class="invalid-feedback">Full name is required</div>
                        </div>

                        <div class="col-md-6">
                            <label for="departmentSelect" class="form-label">
                                <i class="bx bx-building me-2"></i>Organization/Department <span class="text-danger">*</span>
                            </label>
                            <select class="form-control select2" id="departmentSelect" name="department_id" required>
                                <option value="">Select Organization</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select an organization</div>
                        </div>

                        <div class="col-md-6">
                            <label for="license_type" class="form-label">
                                <i class="bx bx-certification me-2"></i>License/Membership Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-control select2" name="license_type" required>
                                <option value="">Select Type</option>
                                <option value="Practitioner">Practitioner</option>
                                <option value="Consultant">Consultant</option>
                                <option value="Associate">Associate</option>
                                <option value="Fellow">Fellow</option>
                            </select>
                            <div class="invalid-feedback">Please select a license type</div>
                        </div>

                        <div class="col-md-6">
                            <label for="specialty" class="form-label">
                                <i class="bx bx-briefcase me-2"></i>Specialty/Field <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="specialty" placeholder="e.g. Structural Engineering" required>
                            <div class="invalid-feedback">Please enter the specialty field</div>
                        </div>

                        <div class="col-md-6">
                            <label for="grade" class="form-label">
                                <i class="bx bx-bar-chart-alt-2 me-2"></i>Professional Grade <span class="text-danger">*</span>
                            </label>
                            <select class="form-control select2" name="grade" required>
                                <option value="" disabled selected>Select Grade</option>
                                @foreach(['Junior', 'Senior', 'Principal', 'Lead', 'Master'] as $grade)
                                    <option value="{{ $grade }}">{{ $grade }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please select a professional grade</div>
                        </div>

                        <div class="col-md-6">
                            <label for="registration_date" class="form-label">
                                <i class="bx bx-calendar me-2"></i>Effective Registration Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" id="registration_date" name="registration_date" required>
                            <div class="invalid-feedback">Registration date is required</div>
                        </div>

                        <div class="col-md-6">
                            <label for="expiry_date" class="form-label">
                                <i class="bx bx-calendar-x me-2"></i>License Expiry Date
                            </label>
                            <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info sliding-warning" id="warningText" style="opacity: 1; transform: translateX(0);">
                                <i class="bx bx-info-circle me-2"></i>
                                Professional credentials will be verified by the council before the status is marked as "Verified".
                            </div>
                        </div>

                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mt-3">
                                <i class="bx bx-file-find me-2"></i>Accompanying Certifications
                            </h5>
                            <div id="certContainer">
                                <div class="row mb-2 cert-row">
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="certs[0][name]" placeholder="Certification Name (e.g. PMP, ISO)">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="text" class="form-control" name="certs[0][body]" placeholder="Issuing Body">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-success" onclick="addCertRow()">
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">
                            <i class="bx bx-x"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bx bx-save"></i> Complete Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Simplified dynamic row function for Certifications
    function addCertRow() {
        const container = document.getElementById('certContainer');
        const rowCount = container.getElementsByClassName('cert-row').length;
        const newRow = document.createElement('div');
        newRow.className = 'row mb-2 cert-row';
        newRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" class="form-control" name="certs[${rowCount}][name]" placeholder="Certification Name">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" name="certs[${rowCount}][body]" placeholder="Issuing Body">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.cert-row').remove()">
                    <i class="bx bx-minus"></i>
                </button>
            </div>
        `;
        container.appendChild(newRow);
    }

    // Standard Validation Logic
    function validateForm() {
        let isValid = true;
        const form = document.querySelector('.needs-validation');
        const submitBtn = document.getElementById('submitBtn');

        form.classList.add('was-validated');

        form.querySelectorAll('[required]').forEach(field => {
            if (!field.value) {
                isValid = false;
                if ($(field).hasClass('select2')) {
                    $(field).next('.select2-container').find('.select2-selection').addClass('is-invalid');
                }
            } else {
                if ($(field).hasClass('select2')) {
                    $(field).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                }
            }
        });

        if (isValid) {
            submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Processing...';
            submitBtn.disabled = true;
        }

        return isValid;
    }

    $(document).ready(function() {
        $('.select2').select2({
            dropdownParent: $('#registerProfessionalModal'),
            width: '100%'
        });
    });
</script>
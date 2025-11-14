<div class="login-box w-auto">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <form class="form-horizontal" method="POST" id="shipping_company_registration_form" enctype="multipart/form-data">
                            <div class="card-body">
                                <div class="login-logo">
                                    <a href="<?= base_url('shipping_company/login'); ?>">
                                        <img src="<?= base_url() . $logo; ?>" alt="Logo">
                                    </a>
                                </div>

                                <h4 class="mb-4">Shipping Company Registration</h4>
                                <h5>Company Details</h5>
                                <hr>

                                <!-- Company Name -->
                                <div class="form-group row">
                                    <label for="company_name" class="col-sm-2 col-form-label">
                                        Company Name <span class='text-danger text-sm'>*</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                            class="form-control"
                                            id="company_name"
                                            name="company_name"
                                            placeholder="Company Name">
                                    </div>
                                </div>

                                <!-- Mobile -->
                                <div class="form-group row">
                                    <label for="mobile" class="col-sm-2 col-form-label">
                                        Mobile <span class='text-danger text-sm'>*</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                            class="form-control"
                                            id="mobile"
                                            name="mobile"
                                            maxlength="16"
                                            oninput="validateNumberInput(this)"
                                            placeholder="Enter Mobile">
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="form-group row">
                                    <label for="email" class="col-sm-2 col-form-label">
                                        Email <span class='text-danger text-sm'>*</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="email"
                                            class="form-control"
                                            id="email"
                                            name="email"
                                            placeholder="Enter Email">
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="form-group row">
                                    <label for="password" class="col-sm-2 col-form-label">
                                        Password <span class='text-danger text-sm'>*</span>
                                    </label>
                                    <div class="input-group col-sm-10">
                                        <input type="password"
                                            class="form-control form-input passwordToggle"
                                            id="password"
                                            name="password"
                                            placeholder="Type Password here"
                                            required>
                                        <span class="input-group-text togglePassword" style="cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="form-group row">
                                    <label for="confirm_password" class="col-sm-2 col-form-label">
                                        Confirm Password <span class='text-danger text-sm'>*</span>
                                    </label>
                                    <div class="input-group col-sm-10">
                                        <input type="password"
                                            class="form-control form-input passwordToggle"
                                            id="confirm_password"
                                            name="confirm_password"
                                            placeholder="Type Confirm Password here"
                                            required>
                                        <span class="input-group-text togglePassword" style="cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="form-group row">
                                    <label for="address" class="col-sm-2 col-form-label">
                                        Address <span class='text-danger text-sm'>*</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text"
                                            class="form-control"
                                            id="address"
                                            name="address"
                                            placeholder="Enter Address">
                                    </div>
                                </div>

                                <!-- Assign Zipcodes -->
                                <div class="form-group row">
                                    <label for="assign_zipcode" class="col-form-label col-sm-2">
                                        Serviceable Zipcodes <span class='text-danger text-sm'>*</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <select name="assign_zipcode[]"
                                            class="assign_zipcode search_zipcode form-control w-100"
                                            id="assign_zipcode"
                                            multiple>
                                            <?php if (isset($zipcodes) && !empty($zipcodes)) { ?>
                                                <?php foreach ($zipcodes as $row) { ?>
                                                    <option value="<?= $row['id']; ?>">
                                                        <?= $row['zipcode']; ?>
                                                    </option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <small class="form-text text-muted">
                                            Select zipcodes where your company provides delivery service.
                                        </small>
                                    </div>
                                </div>

                                <!-- KYC Documents -->
                                <div class="form-group row">
                                    <label for="kyc_documents" class="col-sm-2 col-form-label">
                                        KYC Documents <span class='text-danger text-sm'>*</span>
                                    </label>
                                    <div class="col-sm-10">
                                        <span class="text-danger d-block mb-1">
                                            *Upload KYC documents (Registration certificate, Tax ID, etc.)
                                        </span>
                                        <input type="file"
                                            class="form-control file_upload_height"
                                            name="kyc_documents[]"
                                            id="kyc_documents"
                                            accept="image/*,application/pdf"
                                            multiple />
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="form-group">
                                    <button type="reset" class="btn btn-warning">Reset</button>
                                    <button type="submit" class="btn btn-success" id="shipping_company_submit_btn">
                                        Submit
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                    <!--/.card-->
                </div>
                <!--/.col-md-12-->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

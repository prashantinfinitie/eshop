<!-- breadcrumb -->
<div class="content-wrapper deeplink_wrapper">
    <section class="wrapper bg-soft-grape">
        <div class="container py-3 py-md-5">
            <nav class="d-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-decoration-none"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('my-account/profile') ?>" class="text-decoration-none"><?= !empty($this->lang->line('dashboard')) ? str_replace('\\', '', $this->lang->line('dashboard')) : 'Dashboard' ?></a></li>
                    <?php if (isset($right_breadcrumb) && !empty($right_breadcrumb)) {
                        foreach ($right_breadcrumb as $row) {
                    ?>
                            <li class="breadcrumb-item"><?= $row ?></li>
                    <?php }
                    } ?>
                    <li class="breadcrumb-item active text-muted" aria-current="page"><?= !empty($this->lang->line('address')) ? str_replace('\\', '', $this->lang->line('address')) : 'Address' ?></li>
                </ol>
            </nav>
            <!-- /nav -->
        </div>
        <!-- /.container -->
    </section>
</div>
<!-- end breadcrumb -->

<section class="my-account-section">
    <div class="container mb-15">
        <div class="my-8">
            <?php $this->load->view('front-end/' . THEME . '/pages/dashboard') ?>
        </div>
        <div class="col-12">
            <div class=' border-0'>
                <div class="card-header bg-white">
                    <h1 class="h4"><?= !empty($this->lang->line('addresses')) ? str_replace('\\', '', $this->lang->line('addresses')) : 'Addresses' ?></h1>
                </div>
                <hr class="mt-5 mb-5">
                <div class="card">
                    <form action="<?= base_url('my-account/add-address') ?>" method="POST" id="add-address-form" class="mt-4 p-4">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                <label for="name" class="control-label"><?= !empty($this->lang->line('name')) ? str_replace('\\', '', $this->lang->line('name')) : 'Name' ?></label>
                                <input type="text" class="form-control" id="address_name" name="name" placeholder="<?= !empty($this->lang->line('name')) ? str_replace('\\', '', $this->lang->line('name')) : 'Name' ?>" />
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                <label for="mobile_number" class="control-label"><?= !empty($this->lang->line('mobile_number')) ? str_replace('\\', '', $this->lang->line('mobile_number')) : 'Mobile Number' ?></label>
                                <input type="text" class="form-control" id="mobile_number" name="mobile" placeholder="<?= !empty($this->lang->line('mobile_number')) ? str_replace('\\', '', $this->lang->line('mobile_number')) : 'Mobile Number' ?>" />
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                <label for="alternate_mobile" class="control-label"><?= !empty($this->lang->line('alternate_mobile')) ? str_replace('\\', '', $this->lang->line('alternate_mobile')) : 'Alternate Mobile Number' ?></label>
                                <input type="text" class="form-control" id="alternate_mobile" name="alternate_mobile" placeholder="<?= !empty($this->lang->line('alternate_mobile')) ? str_replace('\\', '', $this->lang->line('alternate_mobile')) : 'Alternate Mobile Number' ?>" />
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                <label for="address" class="control-label"><?= !empty($this->lang->line('address')) ? str_replace('\\', '', $this->lang->line('address')) : 'Address' ?></label>
                                <textarea name="address" class="form-control" id="address" cols="30" rows="4" placeholder="#Door no, Street Address, Locality, Area, Pincode"></textarea>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12 form-group city">
                                <label for="city" class="control-label"><?= !empty($this->lang->line('city')) ? str_replace('\\', '', $this->lang->line('city')) : 'City' ?></label>
                                
                                <select class="form-control" name="city_id" id="city">
                                    <option value=""><?= !empty($this->lang->line('select_city')) ? str_replace('\\', '', $this->lang->line('select_city')) : '--Select City--' ?></option>
                                    
                                </select>
                            </div>
                            
                            <div class="col-md-6 col-sm-12 col-xs-12 form-group area">
                                <label for="area" class="control-label"><?= !empty($this->lang->line('area')) ? str_replace('\\', '', $this->lang->line('area')) : 'Area' ?></label>
                                <input type="text" class="form-control" id="area" name="general_area_name" placeholder="<?= !empty($this->lang->line('area')) ? str_replace('\\', '', $this->lang->line('area')) : 'Area' ?>" />
                            </div>
                            
                            <div class="col-md-6 col-sm-12 col-xs-12 form-group area">
                                <label for="pincode" class="control-label"><?= !empty($this->lang->line('pincode')) ? str_replace('\\', '', $this->lang->line('pincode')) : 'Zipcode' ?></label>
                                <select name="pincode" id="pincode" class="form-control">
                                    <option value=""><?= !empty($this->lang->line('select_zipcode')) ? str_replace('\\', '', $this->lang->line('select_zipcode')) : '--Select Zipcode--' ?></option>
                                </select>
                            </div>

                            <div class="col-md-6 col-sm-12 col-xs-12 form-group city_name d-none">
                                <label for="city" class="control-label"><?= !empty($this->lang->line('city')) ? str_replace('\\', '', $this->lang->line('city')) : 'City Name' ?></label>
                                <input type="text" class="form-control " id="city_name" name="city_name" placeholder="<?= !empty($this->lang->line('city')) ? str_replace('\\', '', $this->lang->line('city')) : 'City Name' ?>" />
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12 form-group area_name d-none">
                                <label for="area" class="control-label"><?= !empty($this->lang->line('area')) ? str_replace('\\', '', $this->lang->line('area')) : 'Area' ?></label>
                                <input type="text" class="form-control " id="area_name" name="area_name" placeholder="<?= !empty($this->lang->line('area')) ? str_replace('\\', '', $this->lang->line('area')) : 'Area' ?>" />
                            </div>

                            <div class="col-md-6 col-sm-12 col-xs-12 form-group pincode_name d-none">
                                <label for="area" class="control-label"><?= !empty($this->lang->line('pincode')) ? str_replace('\\', '', $this->lang->line('pincode')) : 'Zipcode' ?></label>
                                <input type="text" class="form-control " id="pincode_name" name="pincode_name" placeholder="<?= !empty($this->lang->line('pincode')) ? str_replace('\\', '', $this->lang->line('pincode')) : 'Zipcode' ?>" />
                            </div>

                            <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                <label for="state" class="control-label"><?= !empty($this->lang->line('state')) ? str_replace('\\', '', $this->lang->line('state')) : 'State' ?></label>
                                <input type="text" class="form-control" id="state" name="state" placeholder="<?= !empty($this->lang->line('state')) ? str_replace('\\', '', $this->lang->line('state')) : 'State' ?>" />
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                                <label for="country" class="control-label"><?= !empty($this->lang->line('country')) ? str_replace('\\', '', $this->lang->line('country')) : 'Country' ?></label>
                                <input type="text" class="form-control" name="country" id="country" placeholder="<?= !empty($this->lang->line('country')) ? str_replace('\\', '', $this->lang->line('country')) : 'Country' ?>" />
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                <label for="country" class="control-label"><?= !empty($this->lang->line('type')) ? str_replace('\\', '', $this->lang->line('type')) : 'Type : ' ?></label>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="type" id="home" value="home" />
                                    <label for="home" class="form-check-label text-dark"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="type" id="office" value="office" placeholder="Office" />
                                    <label for="office" class="form-check-label text-dark"><?= !empty($this->lang->line('office')) ? str_replace('\\', '', $this->lang->line('office')) : 'Office' ?></label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" name="type" id="other" value="other" placeholder="Other" />
                                    <label for="other" class="form-check-label text-dark"><?= !empty($this->lang->line('other')) ? str_replace('\\', '', $this->lang->line('other')) : 'Other' ?></label>
                                </div>
                            </div>

                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <input type="submit" class="btn btn-primary btn-sm" id="save-address-submit-btn" value="Save" />
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                                <div id="save-address-result"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <hr class="mt-5 mb-5">
                <div class="card-body">
                    <table id="address_list_table" class='' data-toggle="table" data-url="<?= base_url('my-account/get-address-list') ?>" data-click-to-select="true" data-side-pagination="server" data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-show-columns="true" data-show-refresh="true" data-trim-on-search="false" data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true" data-toolbar="" data-show-export="true" data-maintain-selected="true" data-export-types='["txt","excel"]' data-query-params="queryParams">
                        <thead class="thead-light">
                            <tr>
                                <th data-field="id" data-sortable="true" data-visible="false"><?= !empty($this->lang->line('id')) ? str_replace('\\', '', $this->lang->line('id')) : 'ID' ?></th>
                                <th data-field="name" data-sortable="false"><?= !empty($this->lang->line('name')) ? str_replace('\\', '', $this->lang->line('name')) : 'Name' ?></th>
                                <th data-field="type" data-sortable="false" class="col-md-5"><?= !empty($this->lang->line('type')) ? str_replace('\\', '', $this->lang->line('type')) : 'Type' ?></th>
                                <th data-field="mobile" data-sortable="false"><?= !empty($this->lang->line('mobile_number')) ? str_replace('\\', '', $this->lang->line('mobile_number')) : 'Mobile Number' ?></th>
                                <th data-field="alternate_mobile" data-sortable="false" data-visible="false"><?= !empty($this->lang->line('alternate_mobile')) ? str_replace('\\', '', $this->lang->line('alternate_mobile')) : 'Alternate Mobile' ?></th>
                                <th data-field="address" data-sortable="false"><?= !empty($this->lang->line('address')) ? str_replace('\\', '', $this->lang->line('address')) : 'Address' ?></th>
                                <th data-field="landmark" data-sortable="false" data-visible="false"><?= !empty($this->lang->line('landmark')) ? str_replace('\\', '', $this->lang->line('landmark')) : 'Landmark' ?></th>
                                <th data-field="area" data-sortable="false"><?= !empty($this->lang->line('are')) ? str_replace('\\', '', $this->lang->line('area')) : 'Area' ?></th>
                                <th data-field="city" data-sortable="false"><?= !empty($this->lang->line('city')) ? str_replace('\\', '', $this->lang->line('city')) : 'City' ?></th>
                                <th data-field="state" data-sortable="false"><?= !empty($this->lang->line('state')) ? str_replace('\\', '', $this->lang->line('state')) : 'State' ?></th>
                                <th data-field="pincode" data-sortable="false"><?= !empty($this->lang->line('pincode')) ? str_replace('\\', '', $this->lang->line('pincode')) : 'Pincode' ?></th>
                                <th data-field="country" data-sortable="false"><?= !empty($this->lang->line('country')) ? str_replace('\\', '', $this->lang->line('country')) : 'Country' ?></th>
                                <th data-field="action" data-events="editAddress" data-sortable="true"><?= !empty($this->lang->line('action')) ? str_replace('\\', '', $this->lang->line('action')) : 'Action' ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="modal fade edit-modal-lg" id="address-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"><?= !empty($this->lang->line('edit_address')) ? str_replace('\\', '', $this->lang->line('edit_address')) : 'Edit Address' ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body ps-10 pt-0">
                <form action="<?= base_url('my-account/edit-address') ?>" method="POST" id="edit-address-form" class="mt-4">
                    <input type="hidden" name="id" id="address_id" value="" />
                    <div class="row">
                        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                            <label for="name" class="form-check-label"><?= !empty($this->lang->line('name')) ? str_replace('\\', '', $this->lang->line('name')) : 'Name' ?></label>
                            <input type="text" class="form-control" id="edit_name" name="name" placeholder="<?= !empty($this->lang->line('name')) ? str_replace('\\', '', $this->lang->line('name')) : 'Name' ?>" />
                        </div>
                        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                            <label for="mobile_number" class="form-check-label"><?= !empty($this->lang->line('mobile_number')) ? str_replace('\\', '', $this->lang->line('mobile_number')) : 'Mobile Number' ?></label>
                            <input type="text" class="form-control" id="edit_mobile" name="mobile" placeholder="<?= !empty($this->lang->line('mobile_number')) ? str_replace('\\', '', $this->lang->line('mobile_number')) : 'Mobile Number' ?>" />
                        </div>
                        <div class="col-md-4 col-sm-12 col-xs-12 form-group">
                            <label for="address" class="form-check-label"><?= !empty($this->lang->line('address')) ? str_replace('\\', '', $this->lang->line('address')) : 'Address' ?></label>
                            <input type="text" class="form-control" name="address" id="edit_address" placeholder="<?= !empty($this->lang->line('address')) ? str_replace('\\', '', $this->lang->line('address')) : 'Address' ?>" />
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 form-group edit_city">

                            <label for="edit_city" class="form-check-label"><?= !empty($this->lang->line('city')) ? str_replace('\\', '', $this->lang->line('city')) : 'City' ?></label>
                            <select name="city_id" id="edit_city" class="form-control">
                                <option value><?= !empty($this->lang->line('select_city')) ? str_replace('\\', '', $this->lang->line('select_city')) : '--Select City--' ?></option>
                                <option value="0"><?= !empty($this->lang->line('other')) ? str_replace('\\', '', $this->lang->line('other')) : 'other' ?></option>
                                
                            </select>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 form-group area">
                            <label for="area" class="control-label"><?= !empty($this->lang->line('area')) ? str_replace('\\', '', $this->lang->line('area')) : 'Area' ?></label>
                            <input type="text" class="form-control" id="edit_area" name="edit_general_area_name" placeholder="<?= !empty($this->lang->line('area')) ? str_replace('\\', '', $this->lang->line('area')) : 'Area' ?>" />
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 form-group other_areas d-none">
                            <label for="area" class="form-check-label"><?= !empty($this->lang->line('area')) ? str_replace('\\', '', $this->lang->line('area')) : 'Area' ?></label>
                            <input type="text" class="form-control" id="other_areas_value" name="other_areas" placeholder="<?= !empty($this->lang->line('area')) ? str_replace('\\', '', $this->lang->line('area')) : 'Area' ?>" />
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 form-group other_pincode d-none">
                            <label for="area" class="form-check-label"><?= !empty($this->lang->line('pincode')) ? str_replace('\\', '', $this->lang->line('pincode')) : 'Zipcode' ?></label>
                            <input type="text" class="form-control " id="other_pincode_value" name="pincode_name" placeholder="<?= !empty($this->lang->line('pincode')) ? str_replace('\\', '', $this->lang->line('pincode')) : 'Zipcode' ?>" />
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 form-group area">
                            <label for="pincode" class="control-label"><?= !empty($this->lang->line('pincode')) ? str_replace('\\', '', $this->lang->line('pincode')) : 'Zipcode' ?></label>
                            <select name="pincode" id="edit_pincode" class="form-control">
                            </select>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                            <label for="state" class="form-check-label"><?= !empty($this->lang->line('state')) ? str_replace('\\', '', $this->lang->line('state')) : 'State' ?></label>
                            <input type="text" class="form-control" id="edit_state" name="state" placeholder="<?= !empty($this->lang->line('state')) ? str_replace('\\', '', $this->lang->line('state')) : 'State' ?>" />
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 form-group">
                            <label for="country" class="form-check-label"><?= !empty($this->lang->line('country')) ? str_replace('\\', '', $this->lang->line('country')) : 'Country' ?></label>
                            <input type="text" class="form-control" name="country" id="edit_country" placeholder="<?= !empty($this->lang->line('country')) ? str_replace('\\', '', $this->lang->line('country')) : 'Country' ?>" />
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                            <label for="country" class="form-check-label"><?= !empty($this->lang->line('type')) ? str_replace('\\', '', $this->lang->line('type')) : 'Type : ' ?></label>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="type" id="edit_home" value="home" />
                                <label for="home" class="form-check-label text-dark"><?= !empty($this->lang->line('home')) ? str_replace('\\', '', $this->lang->line('home')) : 'Home' ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="type" id="edit_office" value="office" placeholder="Office" />
                                <label for="office" class="form-check-label text-dark"><?= !empty($this->lang->line('office')) ? str_replace('\\', '', $this->lang->line('office')) : 'Office' ?></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="type" id="edit_other" value="other" placeholder="Other" />
                                <label for="other" class="form-check-label text-dark"><?= !empty($this->lang->line('other')) ? str_replace('\\', '', $this->lang->line('other')) : 'Other' ?></label>
                            </div>
                        </div>

                        <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                            <input type="submit" class="btn btn-primary btn-sm" id="edit-address-submit-btn" value="Save" />
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 text-center mt-2">
                            <div id="edit-address-result"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    window.editAddress = {
        'click .edit-address': function(e, value, row, index) {
            
            $("#address_id").val(row.id);
            $("#edit_name").val(row.name);
            $("#edit_area").val(row.area);
            $("#edit_mobile").val(row.mobile);
            $("#edit_address").val(row.address);
            $("#edit_state").val(row.state);
            $("#edit_country").val(row.country);
            $("#edit_pincode").val(row.pincode);

            if (row.city_id == 0 || row.city_id == "") {
                
                $('.edit_area').addClass('d-none');
                $('.edit_pincode').addClass('d-none');
                $("#other_areas_value").val(row.area);
                $("#other_city_value").val(row.area);
                $('.other_pincode').removeClass('d-none');
                $("#other_pincode_value").val(row.pincode);
                $("#edit_city").val(row.city_id);
            } else if (row.system_pincode == 0) {

                $("#edit_city").val(row.city_id).trigger('change', [row.pincode]);
                $('.other_pincode').removeClass('d-none');
                $("#other_pincode_value").val(row.pincode);
            } else {
                $("#edit_city").val(row.city_id).trigger('change', [row.pincode]);
            }
            if (row.type != "") {
                $('input[type=radio][value=' + row.type.toLowerCase() + ']').attr('checked', true);
            }
        }
    };
</script>
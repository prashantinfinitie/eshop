"use strict";

/* ---------------------------------------------------------------------------------------------------------------------------------------------------

Common-Functions or events
 1.login-Module
 2.Product-Module
 3.Category-Module
 4.Order-Module
 5.Featured_Section-Module
 6.Notifation-Module
 7.Faq-Module
 8.Slider-Module
 9.Offer-Module
 10.Promo_code-Module
 11.Delivery_boys-Module
 12.Settings-Module
 13.City-Module
 14.Transaction_Module
 15.Customer-Wallet-Module
 16.Fund-Transfer-Module
 17.Return-Request-Module
 18.Tax-Module
 19.Image Upload
 20.Client Api Key Module
 21.System Users
 22.custom-notification-Module
 23. whatsapp status
--------------------------------------------------------------------------------------------------------------------------------------------------- */

var auth_settings = $('#auth_settings').val();
$(function () {
    $('[data-toggle="popover"]').popover()
})
$(document).ready(function () {
    $('#loading').hide();
});

var from = 'admin';
if (window.location.href.indexOf("seller/") > -1) {
    from = 'seller';
}

$.event.special.touchstart = {
    setup: function (_, ns, handle) {
        this.addEventListener("touchstart", handle, {
            passive: !ns.includes("noPreventDefault")
        });
    }
};
// $(document).ready(function () {
//     $('.kv-fa').rating({
//         theme: 'krajee-fa',
//         filledStar: '<i class="fas fa-star"></i>',
//         emptyStar: '<i class="far fa-star"></i>',
//         showClear: false,
//         size: 'md'
//     });
// });


$(document).on('load-success.bs.table', '#product-rating-table', function (event) {

    $('.kv-fa').rating({
        theme: 'krajee-fa',
        filledStar: '<i class="fas fa-star"></i>',
        emptyStar: '<i class="far fa-star"></i>',
        showClear: false,
        size: 'md'
    });

});

$(document).on('load-success.bs.table', '#products_table', function (event) {

    $('.kv-fa').rating({
        theme: 'krajee-fa',
        filledStar: '<i class="fas fa-star"></i>',
        emptyStar: '<i class="far fa-star"></i>',
        showClear: false,
        size: 'md'
    });

});

$(document).on('column-switch.bs.table', '#products_table', function (event) {

    $('.kv-fa').rating({
        theme: 'krajee-fa',
        filledStar: '<i class="fas fa-star"></i>',
        emptyStar: '<i class="far fa-star"></i>',
        showClear: false,
        size: 'md'
    });

});


$(document).on('click', '.delete-product-rating', function () {
    var cat_id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/product/delete_rating',
                    data: {
                        id: cat_id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                        csrfName = response['csrfName'];
                        csrfHash = response['csrfHash'];
                    });
            });
        },
        allowOutsideClick: false
    });
});




iziToast.settings({
    position: 'topRight',
});

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

var attributes_values_selected = [];
var variant_values_selected = [];
var value_check_array = [];
var attributes_selected_variations = [];
var attributes_values = [];
var pre_selected_attr_values = [];
var current_attributes_selected = [];
var current_variants_selected = [];
var attribute_flag = 0;
var pre_selected_attributes_name = [];
var current_selected_image;
var attributes_values = [];
var all_attributes_values = [];
var counter = 0;
var variant_counter = 0;
var currentDate = new Date();
var currentYear = currentDate.getFullYear();

//-------------
//- CATEGORY EISE PRODUCT SALE CHART -
//-------------
// Get context with jQuery - using jQuery's .get() method.

if (document.getElementById('sales_piechart_3d')) {
    $.ajax({
        url: base_url + from + '/home/category_wise_product_count',
        type: 'GET',
        dataType: 'json',
        success: function (result) {
            console.log('AJAX Success:', result);
            google.charts.load("current", {
                packages: ["corechart"]
            });
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {

                var data = google.visualization.arrayToDataTable(result);

                var options = {
                    title: '',
                    is3D: true,
                    legend: { position: 'right', textStyle: { fontSize: 15 } },
                    chartArea: { width: '80%' }
                };

                var chart = new google.visualization.PieChart(document.getElementById('sales_piechart_3d'));
                chart.draw(data, options);
            }
        }
    });
}

function update_sales_chart() {
    var startDate = $('#start_date').val();
    var endDate = $('#end_date').val();
    var sellerId = $('#seller_ids').val(); // Get selected seller

    $.ajax({
        url: base_url + from + '/sales_inventory/top_selling_products',
        type: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate,
            seller_id: sellerId // <-- Add this
        },
        dataType: 'json',
        success: function (result) {
            console.log('AJAX Success (Sales Chart):', result);

            google.charts.load("current", {
                packages: ["corechart"]
            });
            google.charts.setOnLoadCallback(function () {
                var data = google.visualization.arrayToDataTable(result);
                var options = {
                    title: '',
                    is3D: true,
                    legend: { position: 'right', textStyle: { fontSize: 15 } },
                    chartArea: { width: '80%' }
                };
                var chart = new google.visualization.PieChart(document.getElementById('sales_piechart_3d'));
                chart.draw(data, options);
            });
        },
        error: function (xhr, status, error) {
            console.log('AJAX Error (Sales Chart):', status, error);
            $('#sales_piechart_3d').html('<p>Error loading sales chart</p>');
        }
    });
}


if (document.getElementById('piechart_3d')) {
    $.ajax({
        url: base_url + from + '/sales_inventory/top_selling_products',
        type: 'GET',
        dataType: 'json',
        success: function (result) {
            console.log('AJAX Success:', result);
            if (result.length <= 1) {
                $('#stock_piechart_3d').html('<p>No stock data available</p>');
                return;
            }
            google.charts.load("current", {
                packages: ["corechart"]
            });
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable(result);
                var options = {
                    title: '',
                    is3D: true,
                    legend: { position: 'right', textStyle: { fontSize: 15 } },
                    chartArea: { width: '80%' }
                };
                var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
                chart.draw(data, options);
            }
        },
        error: function (xhr, status, error) {
            console.log('AJAX Error:', status, error);
            $('#stock_piechart_3d').html('<p>Error loading chart</p>');
        }
    });
}

$(document).ready(function () {
    $('#sales-report-table').on('load-success.bs.table', function (e, data) {
        if (data && data.total_order_sum) {
            $('#total-order-sum').text(data.total_order_sum);
        } else {
            $('#total-order-sum').text('0.00');
        }
    });
});

if ((window.location.href.indexOf('admin/home') > -1) || (window.location.href.indexOf('seller/home') > -1)) {
    // Function to fetch sales data and render the charts
    $(document).ready(function () {

        // Function to fetch sales data and render the charts
        function fetchAndRenderCharts() {
            $.ajax({
                url: base_url + from + "/home/fetch_sales",
                type: "GET",
                dataType: "json",
                success: function (response) {
                    // Assuming response data structure as you provided
                    let monthlyData = response[0];
                    let weeklyData = response[1];
                    let dailyData = response[2];

                    const data = {
                        Monthly: {
                            series: [{
                                name: 'Monthly Revenue',
                                data: monthlyData.total_sale || []
                            }],
                            categories: monthlyData.month_name || [],
                            colors: ['#1E90FF']

                        },
                        Weekly: {
                            series: [{
                                name: 'Weekly Revenue',
                                data: weeklyData.total_sale || []
                            }],
                            categories: weeklyData.week || [],
                            colors: ['#32CD32']
                        },
                        Daily: {
                            series: [{
                                name: 'Daily Revenue',
                                data: dailyData.total_sale || []
                            }],
                            categories: dailyData.day || [],
                            colors: ['#990099']

                        }
                    };

                    let chartData = data['Monthly'];


                    const options = {
                        chart: {
                            type: 'bar',
                            height: 350
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
                                endingShape: 'rounded'
                            },
                        },
                        series: chartData.series,
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: chartData.categories
                        },
                        yaxis: {
                            labels: {
                                formatter: function (value) {
                                    return (value / 1000) +
                                        '00k'; // Divide by 1000 to convert to thousands and then add '00k'
                                }
                            }
                        },
                        fill: {
                            opacity: 1,
                        },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    var currencySymbol = "<?php echo $currency_symbol; ?>";
                                    return currencySymbol + val;
                                }
                            }
                        }
                    };


                    const chart = new ApexCharts(document.querySelector(".chart-container"), options);
                    chart.render();

                    $(".chart-height li a").on("click", function () {
                        $(".chart-height li a").removeClass('active');
                        $(this).addClass('active');

                        chartData = data[$(this).attr("href").replace('#', '')];

                        chart.updateOptions({
                            series: chartData.series,
                            xaxis: {
                                categories: chartData.categories
                            }
                        });
                    });
                },
                error: function (error) {
                    console.error("Error fetching data: ", error);
                }
            });
        }

        // Initial chart rendering
        fetchAndRenderCharts();

    });
}

$(document).on("click", '[data-toggle="lightbox"]', function (event) {
    event.preventDefault();
    $(this).ekkoLightbox();
});

var url = window.location.origin + window.location.pathname;
var $selector = $('.sidebar a[href="' + url + '"]');
$($selector).addClass('active');
$($selector).closest('ul').closest('li').addClass('menu-open');
$($selector).closest('ul').removeAttr('style');
$($selector).closest('ul').closest('li').find('a[href*="#"').addClass('active');

var tmp = [];
var permute_counter = 0;

//User defined functions


function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}

function containsAll(needles, haystack) {
    for (var i = 0; i < needles.length; i++) {
        if ($.inArray(needles[i], haystack) == -1) return false;
    }
    return true;
}

function getPermutation(args) {
    var r = [],
        max = args.length - 1;

    function helper(arr, i) {
        for (var j = 0, l = args[i].length; j < l; j++) {
            var a = arr.slice(0); // clone arr
            a.push(args[i][j]);
            if (i == max)
                r.push(a);
            else
                helper(a, i + 1);
        }
    }
    helper([], 0);
    return r;
}


function clear_form_elements(class_name) {
    jQuery("." + class_name).find(':input').each(function () {
        switch (this.type) {
            case 'password':
            case 'text':
            case 'textarea':
            case 'file':
            case 'select-one':
            case 'select-multiple':
            case 'date':
            case 'number':
            case 'tel':
            case 'email':
                jQuery(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
                break;
        }
    });
}

function add_product_variant_html(type) {

    if (type == 'packet') {
        var html = "<div class='row offset-md-1 border-bottom ml-5 mr-5 mb-3'><div class='col-md-12 mt-2 remove_pro_btn'><div class='card-tools float-right'> <label>Remove</label> <button type='button' class='btn btn-tool' id='remove_product_btn'> <i class='text-danger far fa-times-circle fa-2x '></i> </button></div></div><div class='form-group col-md-4'> <label for='inputPassword' class='col-sm-4 col-form-label'>Measurement</label><div class='col-sm-10'> <span><input type='number' name='packet_measurement[]' ></span></div></div><div class='form-group col-md-4'> <label for='inputPassword' class='col-sm-6 col-form-label'>Unit</label><div class='col-sm-6'> <select class='form-control valid' name='packet_measurement_unit_id[]' aria-invalid='false'><option value='1'>kg</option><option value='2'>gm</option><option value='3'>ltr</option><option value='4'>ml</option><option value='5'>pack</option> </select></div></div><div class='form-group col-md-4'> <label for='inputPassword' class='col-sm-4 col-form-label'>Price</label><div class='col-sm-10'> <span><input type='number' class='price' name='packet_price[]'></span></div></div><div class='form-group col-md-4'> <label for='inputPassword' class='col-sm-4 col-form-label'>Discounted Price</label><div class='col-sm-10'> <span><input type='number' class='discount' name='packet_discnt[]'></span></div></div><div class='form-group col-md-4'> <label for='inputPassword' class='col-sm-4 col-form-label'>Stock</label><div class='col-sm-10'> <input type='number' name='packet_stock[]'></div></div><div class='form-group col-md-4'> <label for='inputPassword' class='col-sm-4 col-form-label'>Unit</label><div class='col-sm-6'> <select class='form-control valid' name='packet_stock_unit_id[]' aria-invalid='false'><option value='1'>kg</option><option value='2'>gm</option><option value='3'>ltr</option><option value='4'>ml</option><option value='5'>pack</option> </select></div></div><div class='form-group col-md-4'> <label for='inputPassword' class='col-sm-4 col-form-label'>Status</label><div class='col-sm-6'> <select name='packet_serve_for[]' class='form-control' required='' aria-invalid='false'><option value='Available'>Available</option><option value='Sold Out'>Sold Out</option> </select></div></div></div>";
        return html;
    } else {
        var html = '<div class="row offset-md-1 border-bottom ml-5 mr-5 mb-3"><div class="col-md-12 mt-2 remove_pro_btn"><div class="card-tools float-right"> <label>Remove</label> <button type="button" class="btn btn-tool" id="remove_product_btn"> <i class="text-danger far fa-times-circle fa-2x "></i> </button></div></div><div class="form-group col-md-3 col-12"> <label for="inputPassword" class="col-sm-12 col-form-label">Measurement</label><div class="col-12"> <span><input type="number" name="loose_measurement[]" class="col-12" ></span></div></div><div class="form-group col-md-3"> <label for="inputPassword" class="col-sm-6 col-form-label">Unit</label><div class="col-sm-12"> <select class="form-control valid" name="loose_measurement_unit_id[] col-12" aria-invalid="false"><option value="1">kg</option><option value="2">gm</option><option value="3">ltr</option><option value="4">ml</option><option value="5">pack</option> </select></div></div><div class="form-group col-md-3"> <label for="inputPassword" class="col-sm-4 col-form-label">Price</label><div class="col-sm-10"> <span><input type="number" name="loose_price[]" class="col-12 price"></span></div></div><div class="form-group col-md-3"> <label for="inputPassword" class="col-sm-12 col-form-label">Discounted Price</label><div class="col-sm-10"> <span><input type="number" name="loose_discnt[]" class="col-12 discount"></span></div></div></div>';
        return html;
    }

}


function save_attributes() {

    attributes_values = [];
    all_attributes_values = [];
    var tmp = $('.product-attr-selectbox');
    $.each(tmp, function (index) {
        var data = $(tmp[index]).closest('.row').find('.multiple_values').select2('data');
        var tmp_values = [];
        for (var i = 0; i < data.length; i++) {
            if (!$.isEmptyObject(data[i])) {
                tmp_values[i] = data[i].id;
            }
        }
        if (!$.isEmptyObject(data)) {
            all_attributes_values.push(tmp_values);
        }
        if ($(tmp[index]).find('.is_attribute_checked').is(':checked')) {
            if (!$.isEmptyObject(data)) {
                attributes_values.push(tmp_values);
            }
        }
    });


}

function create_variants(preproccessed_permutation_result = false, from) {

    var html = "";
    var is_appendable = false;
    var permutated_attribute_value = [];
    if (preproccessed_permutation_result != false) {
        var response = preproccessed_permutation_result;
        is_appendable = true;
    } else {
        var response = getPermutation(attributes_values);
    }
    var selected_variant_ids = JSON.stringify(response);
    var selected_attributes_values = JSON.stringify(attributes_values);

    $('.no-variants-added').hide();
    $.ajax({
        type: 'GET',
        url: base_url + from + '/product/get_variants_by_id',
        data: {
            variant_ids: selected_variant_ids,
            attributes_values: selected_attributes_values,
        },
        dataType: 'json',
        success: function (data) {
            var result = data['result'];
            html += '<div ondragstart="return false;"><a class="btn btn-outline-primary btn-sm mb-3" href="javascript:void(0)" id="expand_all">Expand All</a>' +
                '<a class="btn btn-outline-primary btn-sm mb-3 ml-4" href="javascript:void(0)" id="collapse_all">Collapse All</a></div>';
            $.each(result, function (a, b) {

                variant_counter++;
                var attr_name = 'pro_attr_' + variant_counter;
                html += '<div class="form-group move row my-auto p-2 border rounded bg-gray-light product-variant-selectbox"><div class="col-1 text-center my-auto"><i class="fas fa-sort"></i></div>';
                var tmp_variant_value_id = " ";
                $.each(b, function (key, value) {
                    tmp_variant_value_id = tmp_variant_value_id + " " + value.id;
                    html += '<div class="col-2"> <input type="text" class="col form-control" value="' + value.value + '" readonly></div>';
                });

                html += '<input type="hidden" name="variants_ids[]" value="' + tmp_variant_value_id + '"><div class="col my-auto row justify-content-center"> <a data-toggle="collapse" class="btn btn-tool text-primary" data-target="#' + attr_name + '" aria-expanded="true"><i class="fas fa-angle-down fa-2x"></i> </a> <button type="button" class="btn btn-tool remove_variants"> <i class="text-danger far fa-times-circle fa-2x "></i> </button></div><div class="col-12" id="variant_stock_management_html"><div id=' + attr_name + ' style="" class="collapse">';
                if ($('.variant_stock_status').is(':checked') && $('.variant-stock-level-type').val() == 'variable_level') {

                    html += '<div class="form-group row">' +
                        '<div class="col col-xs-12 mt-3">' +
                        '<label class="control-label">Price :</label>' +
                        '<input type="number" name="variant_price[]" class="col form-control price varaint-must-fill-field" min="0" step="0.01"></div>' +
                        '<div class="col col-xs-12 mt-3"><label class="control-label">Special Price :</label>' +
                        '<input type="number" name="variant_special_price[]" class="col form-control discounted_price" min="0" step="0.01"></div>' +
                        '<div class="col col-xs-12 mt-3"> <label class="control-label">SKU :</label> <input type="text" name="variant_sku[]" class="col form-control varaint-must-fill-field"></div>' +
                        '<div class="col col-xs-12 mt-3"> <label class="control-label">Total Stock :</label> <input type="number" min = "1" name="variant_total_stock[]" class="col form-control varaint-must-fill-field"></div>' +
                        '<div class="col col-xs-12 mt-3"> <label class="control-label">Stock Status :</label>' +
                        ' <select type="text" name="variant_level_stock_status[]" class="col form-control varaint-must-fill-field"><option value="1">In Stock</option><option value="0">Out Of Stock</option> </select></div></div>' +
                        '<div class="form-group row mt-3" id="product-dimensions">' +
                        '<div class="col-6">' +
                        '<label for="weight" class="control-label col-md-12"><small>(These are the product parcel"s dimentions.)</small ></label > ' +
                        '</div>' +
                        '</div>' +
                        '<div class="form-group row">' +
                        '<div class="col col-xs-12">' +
                        '<label for="weight" class="control-label col-md-12">Weight <small>(kg)</small> <span class="text-danger text-xs">*</span></label>' +
                        '<input type="number" class="form-control" name="weight[]" placeholder="Weight" id="weight" value="" step="0.01">' +
                        '</div>' +
                        '<div class="col col-xs-12">' +
                        '<label for="height" class="control-label col-md-12">Height <small>(cms)</small></label>' +
                        '<input type="number" class="form-control" name="height[]" placeholder="Height" id="height" value="" step="0.01">' +
                        '</div>' +
                        '<div class="col col-xs-12">' +
                        '<label for="breadth" class="control-label col-md-12">Breadth <small>(cms)</small></label>' +
                        '<input type="number" class="form-control" name="breadth[]" placeholder="Breadth" id="breadth" value="" step="0.01">' +
                        '</div>' +
                        '<div class="col col-xs-12">' +
                        '<label for="length" class="control-label col-md-12">Length <small>(cms)</small></label>' +
                        '<input type="number" class="form-control" name="length[]" placeholder="Length" id="length" value="" step="0.01">' +
                        '</div></div>';
                } else {

                    html += '<div class="form-group row">' +
                        '<div class="col col-xs-12 mt-3"><label class="control-label">Price :</label>' +
                        '<input type="number" name="variant_price[]" class="col form-control price varaint-must-fill-field" min="0" step="0.01"></div>' +
                        '<div class="col col-xs-12 mt-3"><label class="control-label">Special Price :</label>' +
                        '<input type="number" name="variant_special_price[]" class="col form-control discounted_price" min="0" step="0.01"></div></div>' +
                        '<div class="form-group row mt-3" id="product-dimensions">' +
                        '<div class="col-6">' +
                        '<label for="weight" class="control-label col-md-12"><small>(These are the product parcel"s dimentions.)</small ></label > ' +
                        '</div>' +
                        '</div>' +
                        '<div class="form-group row">' +
                        '<div class="col col-xs-12">' +
                        '<label for="weight" class="control-label col-md-12">Weight <small>(kg)</small> <span class="text-danger text-xs">*</span></label>' +
                        '<input type="number" class="form-control" name="weight[]" placeholder="Weight" id="weight" value="" step="0.01">' +
                        '</div>' +
                        '<div class="col col-xs-12">' +
                        '<label for="height" class="control-label col-md-12">Height <small>(cms)</small></label>' +
                        '<input type="number" class="form-control" name="height[]" placeholder="Height" id="height" value="" step="0.01">' +
                        '</div>' +
                        '<div class="col col-xs-12">' +
                        '<label for="breadth" class="control-label col-md-12">Breadth <small>(cms)</small></label>' +
                        '<input type="number" class="form-control" name="breadth[]" placeholder="Breadth" id="breadth" value="" step="0.01">' +
                        '</div>' +
                        '<div class="col col-xs-12">' +
                        '<label for="length" class="control-label col-md-12">Length <small>(cms)</small></label>' +
                        '<input type="number" class="form-control" name="length[]" placeholder="Length" id="length" value="" step="0.01">' +
                        '</div>' +
                        '<div class="col col-xs-12"></div>' +
                        '</div>'
                }
                html += '<div class="col-12 pt-3"><label class="control-label">Images :</label><div class="col-md-3"><a class="uploadFile img btn btn-primary text-white btn-sm"  data-input="variant_images[' + a + '][]" data-isremovable="1" data-is-multiple-uploads-allowed="1" data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class="fa fa-upload"></i> Upload</a> </div><div class="container-fluid row image-upload-section"></div></div>';
                html += '</div></div></div></div></div>';
            });

            if (is_appendable == false) {
                $('#variants_process').html(html);
            } else {
                $('#variants_process').append(html);
            }
            $('#variants_process').unblock();
        }
    });
}
$(document).on('click', '#expand_all', function () {
    $('.product-variant-selectbox').children('#variant_stock_management_html').find('div').addClass('show');
});

$(document).on('click', '#collapse_all', function () {
    $('.product-variant-selectbox').children('#variant_stock_management_html').find('div').removeClass('show');
});

function create_attributes(value, selected_attr) {
    counter++;
    var $attribute = $('#attributes_values_json_data').find('.select_single');
    var $options = $($attribute).clone().html();
    var $selected_attrs = [];
    if (selected_attr) {
        $.each(selected_attr.split(","), function () {
            $selected_attrs.push($.trim(this));
        });
    }

    var attr_name = 'pro_attr_' + counter;

    // product-attr-selectbox
    if ($('#product-type').val() == 'simple_product') {
        var html = '<div class="form-group move row my-auto p-2 border rounded bg-gray-light product-attr-selectbox" id=' + attr_name + '><div class="col-md-1 col-sm-12 text-center my-auto"><i class="fas fa-sort"></i></div><div class="col-md-4 col-sm-12"> <select name="attribute_id[]" class="attributes select_single" data-placeholder=" Type to search and select attributes"><option value=""></option>' + $options + '</select></div><div class="col-md-4 col-sm-12"> <select name="attribute_value_ids[]" class="multiple_values" multiple="" data-placeholder=" Type to search and select attributes values"><option value=""></option> </select></div><div class="col-md-2 col-sm-6 text-center py-1 align-self-center"> <button type="button" class="btn btn-tool remove_attributes"> <i class="text-danger far fa-times-circle fa-2x "></i> </button></div></div>';
    } else {
        $('#note').removeClass('d-none');
        var html = '<div class="form-group row move my-auto p-2 border rounded bg-gray-light product-attr-selectbox" id=' + attr_name + '><div class="col-md-1 col-sm-12 text-center my-auto"><i class="fas fa-sort"></i></div><div class="col-md-4 col-sm-12"> <select name="attribute_id[]" class="attributes select_single" data-placeholder=" Type to search and select attributes"><option value=""></option>' + $options + '</select></div><div class="col-md-4 col-sm-12"> <select name="attribute_value_ids[]" class="multiple_values" multiple="" data-placeholder=" Type to search and select attributes values"><option value=""></option> </select></div><div class="col-md-2 col-sm-6 text-center py-1 align-self-center"><input type="checkbox" name="variations[]" class="is_attribute_checked custom-checkbox mt-2"></div><div class="col-md-1 col-sm-6 text-center py-1 align-self-center"> <button type="button" class="btn btn-tool remove_attributes"> <i class="text-danger far fa-times-circle fa-2x "></i> </button></div></div>';
    }
    $('#attributes_process').append(html);
    if (selected_attr) {
        if ($.inArray(value.name, $selected_attrs) > -1) {
            $("#attributes_process").find('.product-attr-selectbox').last().find('.is_attribute_checked').prop('checked', true).addClass('custom-checkbox mt-2');
            $("#attributes_process").find('.product-attr-selectbox').last().find('.remove_attributes').addClass('remove_edit_attribute').removeClass('remove_attributes');

        }
    }
    $("#attributes_process").find('.product-attr-selectbox').last().find(".attributes").select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    }).val(value.name);

    $("#attributes_process").find('.product-attr-selectbox').last().find(".attributes").trigger('change');
    $("#attributes_process").find('.product-attr-selectbox').last().find(".select_single").trigger('select2:select');

    var multiple_values = [];
    $.each(value.ids.split(","), function () {
        multiple_values.push($.trim(this));
    });

    $("#attributes_process").find('.product-attr-selectbox').last().find(".multiple_values").select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    }).val(multiple_values);
    $("#attributes_process").find('.product-attr-selectbox').last().find(".multiple_values").trigger('change');
}

function create_fetched_attributes_html(from) {
    var edit_id = $('input[name="edit_product_id"]').val();
    $.ajax({
        type: 'GET',
        url: base_url + from + '/product/fetch_attributes_by_id',
        data: {
            edit_id: edit_id,
            [csrfName]: csrfHash,
        },
        dataType: 'json',
        success: function (data) {
            csrfName = data['csrfName'];
            csrfHash = data['csrfHash'];
            var result = data['result'];

            if (!$.isEmptyObject(result.attr_values)) {

                $.each(result.attr_values, function (key, value) {
                    create_attributes(value, result.pre_selected_variants_names);
                });

                $.each(result['pre_selected_variants_ids'], function (key, val) {
                    var tempArray = [];
                    if (val.variant_ids) {
                        $.each(val.variant_ids.split(','), function (k, v) {
                            tempArray.push($.trim(v));
                        });
                        pre_selected_attr_values[key] = tempArray;
                    }
                });

                if (result.pre_selected_variants_names) {
                    $.each(result.pre_selected_variants_names.split(','), function (key, value) {
                        pre_selected_attributes_name.push($.trim(value));
                    });
                }
            } else {
                $('.no-attributes-added').show();
                $('#save_attributes').addClass('d-none');
            }
        }
    });
    return $.Deferred().resolve();
}

function search_category_wise_products() {
    var category_id = $('#category_parent').val();
    if (category_id != '') {
        $.ajax({
            data: {
                'cat_id': category_id,
            },
            type: 'GET',
            url: base_url + 'admin/product/search_category_wise_products',
            dataType: 'json',
            success: function (result) {
                var html = "";
                var i = 0;
                if (!$.isEmptyObject(result)) {
                    $.each(result, function (index, value) {
                        html += '<li class="list-group-item d-flex bg-gray-light align-items-center h-25 ui-sortable-handle" id="product_id-' + value['id'] + '">';
                        html += '<div class="col-md-1"><span> ' + i + ' </span></div>';
                        html += '<div class="col-md-3"><span> ' + value['row_order'] + ' </span></div>';
                        html += '<div class="col-md-4"><span>' + value['name'] + '</span></div>';
                        html += '<div class="col-md-4"><img src="' + base_url + value['image'] + '"  class="w-25"></div>';
                        i++;
                    });
                    $('#sortable').html(html);
                } else {

                    iziToast.error({
                        message: 'No Products Are Available',
                    });

                    html += '<li class="list-group-item d-flex justify-content-center bg-gray-light align-items-center h-25 ui-sortable-handle" id="product_id-3"><div class="col-md-12 d-flex justify-content-center"><span>No Products  Are  Available</span></div></li>';
                    $('#sortable').html(html);
                }
            }
        });
    } else {
        iziToast.error({
            message: 'Category Field Should Be Selected',
        });
    }
}



function save_product(form) {

    $('input[name="product_type"]').val($('#product-type').val());
    if ($('.simple_stock_management_status').is(':checked')) {
        $('input[name="simple_product_stock_status"]').val($('#simple_product_stock_status').val());
    } else {
        $('input[name="simple_product_stock_status"]').val('');
    }
    $('#product-type').prop('disabled', true);
    $('.product-attributes').removeClass('disabled');
    $('.product-variants').removeClass('disabled');
    $('.simple_stock_management_status').prop('disabled', true);

    var catid = $('#product_category_tree_view_html').jstree("get_selected");
    var formData = new FormData(form);
    var submit_btn = $('#submit_btn');
    var btn_html = $('#submit_btn').html();
    var btn_val = $('#submit_btn').val();
    var button_text = (btn_html != '' || btn_html != 'undefined') ? btn_html : btn_val;
    save_attributes();
    formData.append('category_id', catid);
    formData.append('attribute_values', all_attributes_values);
    formData.append(csrfName, csrfHash);

    $.ajax({
        type: 'POST',
        url: $(form).attr('action'),
        data: formData,
        beforeSend: function () {
            submit_btn.html('Please Wait..');
            submit_btn.attr('disabled', true);
        },
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (result) {
            csrfName = result['csrfName'];
            csrfHash = result['csrfHash'];

            if (result['error'] == true) {
                submit_btn.html(button_text);
                submit_btn.attr('disabled', false);
                iziToast.error({
                    message: result['message'],
                });

            } else {
                submit_btn.html(button_text);
                submit_btn.attr('disabled', false);
                iziToast.success({
                    message: result['message'],
                });
                setTimeout(function () {
                    location.reload();
                }, 600);
            }
        }
    });
}


function get_variants(edit_id, from) {
    return $.ajax({
        type: 'GET',
        url: base_url + from + '/product/fetch_variants_values_by_pid',
        data: {
            edit_id: edit_id
        },
        dataType: 'json'
    })
        .done(function (data) {
            return data.responseCode != 200 ?
                $.Deferred().reject(data) : data;
        });

}

function create_fetched_variants_html(add_newly_created_variants = false, from) {

    var newArr1 = [];
    for (var i = 0; i < pre_selected_attr_values.length; i++) {
        var temp = newArr1.concat(pre_selected_attr_values[i]);
        newArr1 = [...new Set(temp)];

    }
    var newArr2 = [];
    for (var i = 0; i < attributes_values.length; i++) {
        newArr2 = newArr2.concat(attributes_values[i]);
    }


    current_attributes_selected = $.grep(newArr2, function (x) {
        return $.inArray(x, newArr1) < 0
    });

    if (containsAll(newArr1, newArr2)) {
        var temp = [];
        if (!$.isEmptyObject(current_attributes_selected)) {
            $.ajax({
                type: 'GET',
                url: base_url + from + '/product/fetch_attribute_values_by_id',
                data: {
                    id: current_attributes_selected,
                },
                dataType: 'json',
                success: function (result) {
                    temp = result;
                    $.each(result, function (key, value) {
                        if (pre_selected_attributes_name.indexOf($.trim(value.name)) > -1) {
                            delete temp[key];
                        }
                    });
                    var resetArr = temp.filter(function () {
                        return true;
                    });
                    setTimeout(function () {
                        var edit_id = $('input[name="edit_product_id"]').val();
                        get_variants(edit_id, from).done(function (data) {
                            create_editable_variants(data.result, resetArr, add_newly_created_variants);
                        });
                    }, 1000);
                }
            });
        } else {
            if (attribute_flag == 0) {
                var edit_id = $('input[name="edit_product_id"]').val();
                get_variants(edit_id, from).done(function (data) {
                    create_editable_variants(data.result, false, add_newly_created_variants);
                });
            }
        }
    } else {
        var edit_id = $('input[name="edit_product_id"]').val();
        get_variants(edit_id, from).done(function (data) {
            create_editable_variants(data.result, false, add_newly_created_variants);
        });
    }
}

function create_editable_variants(data, newly_selected_attr = false, add_newly_created_variants = false) {
    if (data[0].variant_ids) {
        $('#reset_variants').show();
        var html = '';

        if (!$.isEmptyObject(attributes_values) && add_newly_created_variants == true) {
            var permuted_value_result = getPermutation(attributes_values);
        }
        $.each(data, function (a, b) {

            if (!$.isEmptyObject(permuted_value_result) && add_newly_created_variants == true) {
                var permuted_value_result_temp = permuted_value_result;
                var varinat_ids = b.variant_ids.split(',');
                $.each(permuted_value_result_temp, function (index, value) {
                    if (containsAll(varinat_ids, value)) {
                        permuted_value_result.splice(index, 1);
                    }
                });
            }

            variant_counter++;
            var attr_name = 'pro_attr_' + variant_counter;

            html += '<div class="form-group move row my-auto p-2 border rounded bg-gray-light product-variant-selectbox"><div class="col-1 text-center my-auto"><i class="fas fa-sort"></i></div>';
            html += '<input type="hidden" name="edit_variant_id[]" value=' + b.id + '>';
            var tmp_variant_value_id = "";
            var varaint_array = [];
            var varaint_ids_temp_array = [];
            var flag = 0;
            var variant_images = "";
            var image_html = "";
            if (b.images) {
                variant_images = JSON.parse(b.images);
            }

            $.each(b.variant_ids.split(","), function (key) {
                varaint_ids_temp_array[key] = $.trim(this);
            });

            $.each(b.variant_values.split(","), function (key) {
                varaint_array[key] = $.trim(this);
            });

            if (variant_images) {
                $.each(variant_images, function (img_key, img_value) {
                    image_html += '<div class="col-md-3 col-sm-12 shadow bg-white rounded m-3 p-3 text-center grow"><div class="image-upload-div"><img src=' + base_url + img_value + ' alt="Image Not Found"></div> <a href="javascript:void(0)" class="delete-img m-3" data-id="' + b.id + '" data-field="images" data-img=' + img_value + ' data-table="product_variants" data-path="uploads/media/" data-isjson="true"> <span class="btn btn-block bg-gradient-danger btn-xs"><i class="far fa-trash-alt "></i> Delete</span></a> <input type="hidden" name="variant_images[' + a + '][]"  value=' + img_value + '></div>';
                });
            }

            for (var i = 0; i < varaint_array.length; i++) {
                html += '<div class="col-2 variant_col"> <input type="hidden"  value="' + varaint_ids_temp_array[i] + '"><input type="text" class="col form-control" value="' + varaint_array[i] + '" readonly></div>';
            }
            if (newly_selected_attr != false && newly_selected_attr.length > 0) {
                for (var i = 0; i < newly_selected_attr.length; i++) {
                    var tempVariantsIds = [];
                    var tempVariantsValues = [];
                    $.each(newly_selected_attr[i].attribute_values_id.split(','), function () {
                        tempVariantsIds.push($.trim(this));
                    });
                    html += '<div class="col-2"><select class="col new-added-variant form-control" ><option value="">Select Attribute</option>';
                    $.each(newly_selected_attr[i].attribute_values.split(','), function (key) {
                        tempVariantsValues.push($.trim(this));
                        html += '<option value="' + tempVariantsIds[key] + '">' + tempVariantsValues[key] + '</option>';
                    });
                    html += '</select></div>';
                }
            }
            html += '<input type="hidden" name="variants_ids[]" value="' + b.attribute_value_ids + '"><div class="col my-auto row justify-content-center"> <a data-toggle="collapse" class="btn btn-tool text-primary" data-target="#' + attr_name + '" aria-expanded="true"><i class="fas fa-angle-down fa-2x"></i> </a> <button type="button" class="btn btn-tool remove_variants"> <i class="text-danger far fa-times-circle fa-2x "></i> </button></div><div class="col-12" id="variant_stock_management_html"><div id=' + attr_name + ' style="" class="collapse">';

            if ($('.variant_stock_status').is(':checked') && $('.variant-stock-level-type').val() == 'variable_level') {

                var selected = (b.availability == '0') ? 'selected' : ' ';
                html += '<div class="form-group row">' +
                    '<div class="col col-xs-12"><label class="control-label">Price :</label>' +
                    '<input type="number" name="variant_price[]" class="col form-control price varaint-must-fill-field" value="' + b.price + '" min="0" step="0.01"></div>' +
                    '<div class="col col-xs-12"><label class="control-label">Special Price :</label>' +
                    '<input type="number" name="variant_special_price[]" class="col form-control discounted_price" min="0" value="' + b.special_price + '" step="0.01"></div>' +
                    '<div class="col col-xs-12"> <label class="control-label">Sku :</label> ' +
                    '<input type="text" name="variant_sku[]" class="col form-control varaint-must-fill-field"  value="' + b.sku + '" ></div>' +
                    '<div class="col col-xs-12"> <label class="control-label">Total Stock :</label> ' +
                    '<input type="number" min="1" name="variant_total_stock[]" class="col form-control varaint-must-fill-field" value="' + b.stock + '"></div>' +
                    '<div class="col col-xs-12"> <label class="control-label">Stock Status :</label>' +
                    ' <select type="text" name="variant_level_stock_status[]" class="col form-control varaint-must-fill-field">' +
                    '<option value="1">In Stock</option><option value="0"  ' + selected + '  >Out Of Stock</option> </select></div></div>' +
                    '<div class="form-group row mt-3" id="product-dimensions">' +
                    '<div class="col-6">' +
                    '<label for="weight" class="control-label col-md-12"><small>(These are the product parcel"s dimentions.)</small ></label > ' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group row">' +
                    '<div class="col col-xs-12">' +
                    '<label for="weight" class="control-label col-md-12">Weight <small>(kg)</small> <span class="text-danger text-xs">*</span></label>' +
                    '<input type="number" class="form-control" name="weight[]" placeholder="Weight" id="weight" value="' +
                    b.weight +
                    '" step="0.01">' +
                    '</div>' +
                    '<div class="col col-xs-12">' +
                    '<label for="height" class="control-label col-md-12">Height <small>(cms)</small></label>' +
                    '<input type="number" class="form-control" name="height[]" placeholder="Height" id="height" value="' +
                    b.height +
                    '" step="0.01">' +
                    '</div>' +
                    '<div class="col col-xs-12">' +
                    '<label for="breadth" class="control-label col-md-12">Breadth <small>(cms)</small></label>' +
                    '<input type="number" class="form-control" name="breadth[]" placeholder="Breadth" id="breadth" value="' +
                    b.breadth +
                    '" step="0.01">' +
                    '</div>' +
                    '<div class="col col-xs-12">' +
                    '<label for="length" class="control-label col-md-12">Length <small>(cms)</small></label>' +
                    '<input type="number" class="form-control" name="length[]" placeholder="Length" id="length" value="' +
                    b.length +
                    '" step="0.01">' +
                    '</div></div>';
            } else {

                html += '<div class="form-group row">' +
                    '<div class="col col-xs-12"><label class="control-label">Price :</label>' +
                    '<input type="number" name="variant_price[]" class="col form-control price varaint-must-fill-field" value="' + b.price + '" min="0" step="0.01"></div>' +
                    '<div class="col col-xs-12"><label class="control-label">Special Price :</label>' +
                    '<input type="number" name="variant_special_price[]" class="col form-control discounted_price"  min="0" value="' + b.special_price + '" step="0.01"></div></div>' +
                    '<div class="form-group row mt-3" id="product-dimensions">' +
                    '<div class="col-6">' +
                    '<label for="weight" class="control-label col-md-12"><small>(These are the product parcel"s dimentions.)</small ></label > ' +
                    '</div>' +
                    '</div>' +
                    '<div class="form-group row">' +
                    '<div class="col col-xs-12">' +
                    '<label for="weight" class="control-label col-md-12">Weight <small>(kg)</small> <span class="text-danger text-xs">*</span></label>' +
                    '<input type="number" class="form-control" name="weight[]" placeholder="Weight" id="weight" value="' +
                    b.weight +
                    '" step="0.01">' +
                    '</div>' +
                    '<div class="col col-xs-12">' +
                    '<label for="height" class="control-label col-md-12">Height <small>(cms)</small></label>' +
                    '<input type="number" class="form-control" name="height[]" placeholder="Height" id="height" value="' +
                    b.height +
                    '" step="0.01">' +
                    '</div>' +
                    '<div class="col col-xs-12">' +
                    '<label for="breadth" class="control-label col-md-12">Breadth <small>(cms)</small></label>' +
                    '<input type="number" class="form-control" name="breadth[]" placeholder="Breadth" id="breadth" value="' +
                    b.breadth +
                    '" step="0.01">' +
                    '</div>' +
                    '<div class="col col-xs-12">' +
                    '<label for="length" class="control-label col-md-12">Length <small>(cms)</small></label>' +
                    '<input type="number" class="form-control" name="length[]" placeholder="Length" id="length" value="' +
                    b.length +
                    '" step="0.01">' +
                    '</div></div>';
            }
            html += '<div class="col-12 pt-3"><label class="control-label">Images :</label>' +
                '<div class="col-md-3">' +
                '<a class="uploadFile img btn btn-primary text-white btn-sm"  data-input="variant_images[' + a + '][]" data-isremovable="1" data-is-multiple-uploads-allowed="1" data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo">' +
                '<i class="fa fa-upload"></i> Upload</a> ' +
                '</div>' +
                '<div class="container-fluid row image-upload-section"> ' + image_html + ' </div></div>';
            html += '</div></div></div>';

            $('#variants_process').html(html);
        });

        if (!$.isEmptyObject(attributes_values) && add_newly_created_variants == true) {
            create_variants(permuted_value_result, from);
        }

    }
}

function status_date_wise_search() {
    $('.table-striped').bootstrapTable('refresh');
    update_sales_chart();
}


function resetfilters() {
    $('#datepicker').val('');
    $('#media-type').val('');
    $('#start_date').val('');
    $('#end_date').val('');
    $('.form-control').val('');
    status_date_wise_search();
}

function formatRepo(repo) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__meta'>" +
        "<div class='select2-result-repository__title'>" + repo.product_name + "</div>";

    if (repo.description) {
        markup += "<div class='select2-result-repository__description'> In " + repo.category_name + "</div>";
    }

    return markup;
}

function formatRepo1(repo) {

    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__meta'>" +
        "<div class='select2-result-repository__title'>" + repo.zipcode + "</div>";

    return markup;
}

function formatRepoSelection(repo) {
    return repo.product_name || repo.text;
}

function formatRepoSelection1(repo) {
    return repo.zipcode || repo.text;
}

function mediaParams(p) {
    return {
        'type': $('#media_type').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        'seller_id': $('input[name="seller_id"]').val(),
    };
}

function mediaUploadParams(p) {
    return {
        'type': $('#media-type').val(),
        "start_date": $('#start_date').val(),
        "end_date": $('#end_date').val(),
        "seller_id": $('#seller_id').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function noti_query_params(p) {
    return {
        "message_type": $('#message_type').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}


function home_query_params(p) {
    return {
        "start_date": $('#start_date').val(),
        "end_date": $('#end_date').val(),
        "order_status": $('#order_status').val(),
        "payment_method": $('#payment_method').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function queryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function ticket_queryParams(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function customer_wallet_query_params(p) {
    return {
        transaction_type: 'wallet',
        user_type: 'members',
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function seller_wallet_query_params(p) {
    return {
        transaction_type: 'wallet',
        user_type: 'seller',
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function order_tracking_query_params(p) {
    return {
        "order_id": $('input[name="order_id"]').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}
function digital_order_mails_query_params(p) {
    return {
        "order_item_id": $('input[name="order_item_id"]').val(),
        "order_id": $('input[name="order_id"]').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function category_query_params(p) {
    return {
        "category_id": $('#category_id').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function blog_query_params(p) {
    return {
        "category_id": $('#category_parent').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function brand_query_params(p) {
    return {
        "brand_id": $('#brand_id').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

$(document).ready(function () {
    $('#media_remove').click(function () {
        var ids = $.map($('#media-table').bootstrapTable('getSelections'), function (row) {
            return row.id;
        });

        if (ids.length > 0) {

            Swal.fire({
                title: 'Are You Sure!',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            method: 'POST',
                            url: base_url + from + '/media/media_delete',
                            data: { 'ids': ids, [csrfName]: csrfHash },
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    $('#media-table').bootstrapTable('remove', {
                                        field: 'id',
                                        values: ids
                                    });
                                    $('#media-table').bootstrapTable('refresh');
                                    window.location.reload();
                                    Swal.fire('Success', 'Files Deleted!', 'success');
                                } else {
                                    Swal.fire('Oops...', result['message'], 'error');
                                }
                                resolve();
                            },
                            error: function (xhr, status, error) {
                                Swal.fire('Oops...', 'Something went wrong!', 'error');
                                reject(error);
                            }
                        });
                    });
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire('Cancelled!', 'Your data is safe.', 'error');
                }
            });
        } else {
            alert('Please select at least one item to delete.');
        }
    });
});
$(document).ready(function () {
    $('#zipcode_remove').click(function () {
        var ids = $.map($('#zipcode-table').bootstrapTable('getSelections'), function (row) {
            return row.id;
        });

        if (ids.length > 0) {

            Swal.fire({
                title: 'Are You Sure!',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            method: 'POST',
                            url: base_url + from + '/Area/delete_zipcode_multi',
                            data: { 'ids': ids, [csrfName]: csrfHash },
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    $('#zipcode-table').bootstrapTable('remove', {
                                        field: 'id',
                                        values: ids
                                    });
                                    $('#zipcode-table').bootstrapTable('refresh');
                                    window.location.reload();
                                    Swal.fire('Success', 'Files Deleted!', 'success');
                                } else {
                                    Swal.fire('Oops...', result['message'], 'error');
                                }
                                resolve();
                            },
                            error: function (xhr, status, error) {
                                Swal.fire('Oops...', 'Something went wrong!', 'error');
                                reject(error);
                            }
                        });
                    });
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire('Cancelled!', 'Your data is safe.', 'error');
                }
            });
        } else {
            alert('Please select at least one item to delete.');
        }
    });
});
function product_query_params(p) {
    return {
        "category_id": $('#category_parent').val(),
        "seller_id": $('#seller_filter').val(),
        "status": $('#status_filter').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function seller_status_params(s) {
    return {
        "seller_status": $('#seller_status_filter').val(),
        limit: s.limit,
        sort: s.sort,
        order: s.order,
        offset: s.offset,
        search: s.search
    };
}
function affiliate_status_params(s) {
    return {
        "affiliate_status": $('#affiliate_status_filter').val(),
        limit: s.limit,
        sort: s.sort,
        order: s.order,
        offset: s.offset,
        search: s.search
    };
}
function delivery_boy_status_params(p) {
    return {
        "delivery_boy_status": $('#delivery_boy_status_filter').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function shipping_company_status_params(p) {
    return {
        "shipping_company_status": $('#shipping_company_status_filter').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function stock_query_params(p) {
    return {
        "status": $('#status_filter').val(),
        limit: p.limit,
        offset: p.offset,
        sort: p.sort,
        order: p.order,
        search: p.search,
        seller_id: $('#seller_filter').val(),
        category_id: $('#category_parent').val(),
    };
}

function payment_request_queryParams(p) {
    return {
        "user_filter": $('#user_filter').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}
function wallet_transaction_queryParams(p) {
    return {
        "transaction_type_filter": $('#transaction_type_filter').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function ratingParams(p) {
    return {
        "category_id": $('#category_parent').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}


function address_query_params(p) {
    return {
        user_id: $('#address_user_id').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function orders_query_params(p) {
    return {
        "start_date": $('#start_date').val(),
        "end_date": $('#end_date').val(),
        "order_status": $('#order_status').val(),
        "user_id": $('#order_user_id').val(),
        "seller_id": $('#order_seller_id').val(),
        "payment_method": $('#payment_method').val(),
        "order_type": $('#order_type').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function transaction_query_params(p) {
    return {
        transaction_type: 'transaction',
        user_id: $('#transaction_user_id').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function sales_report_query_params(p) {
    return {
        "start_date": $('#start_date').val(),
        "end_date": $('#end_date').val(),
        "seller_id": $('#seller_id').val(),
        "payment_method": $('#payment_method_filter').val(),
        "order_status": $('#order_status_filter').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function sales_inventory_report_query_params(p) {
    return {
        "start_date": $('#start_date').val(),
        "end_date": $('#end_date').val(),
        "seller_id": $('#seller_ids').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}
$(document).on("change", "#send_to", function (e) {
    e.preventDefault();
    var type_val = $(this).val();
    if (type_val == 'specific_user') {
        // to specific user
        $('.notification-users').removeClass('d-none');
    } else {
        $('.notification-users').addClass('d-none');
    }
});

$(document).on('change', '.type_event_trigger', function (e, data) {
    e.preventDefault();
    var type_val = $(this).val();
    if (type_val != 'default' && type_val != '') {
        if (type_val == 'categories') {
            $('.slider-categories').removeClass('d-none');
            $('.notification-categories').removeClass('d-none');
            $('.offer-products').addClass('d-none');
            $('.offer-categories').removeClass('d-none');
            $('.slider-products').addClass('d-none');
            $('.notification-products').addClass('d-none');
            $('.slider-url').addClass('d-none');
            $('.offer-url').addClass('d-none');
            $('.notification-url').addClass('d-none');
        } else if (type_val == 'products') {
            $('.offer-products').removeClass('d-none');
            $('.offer-categories').addClass('d-none');
            $('.slider-products').removeClass('d-none');
            $('.notification-products').removeClass('d-none');
            $('.slider-categories').addClass('d-none');
            $('.notification-categories').addClass('d-none');
            $('.offer-url').addClass('d-none');
            $('.slider-url').addClass('d-none');
            $('.notification-url').addClass('d-none');
        } else if (type_val == 'slider_url') {
            $('.slider-url').removeClass('d-none');
            $('.slider-categories').addClass('d-none');
            $('.notification-categories').addClass('d-none');
            $('.slider-products').addClass('d-none');
            $('.offer-url').removeClass('d-none');
            $('.offer-categories').addClass('d-none');
            $('.notification-products').addClass('d-none');
            $('.notification-url').addClass('d-none');
        } else if (type_val == 'offer_url') {
            $('.offer-url').removeClass('d-none');
            $('.offer-categories').addClass('d-none');
            $('.offer-products').addClass('d-none');
            $('.slider-categories').addClass('d-none');
            $('.notification-categories').addClass('d-none');
            $('.slider-products').addClass('d-none');
            $('.notification-products').addClass('d-none');
            $('.notification-url').addClass('d-none');
        } else if (type_val == 'notification_url') {
            $('.offer-products').addClass('d-none');
            $('.offer-categories').addClass('d-none');
            $('.notification-url').removeClass('d-none');
            $('.offer-url').addClass('d-none');
            $('.slider-categories').addClass('d-none');
            $('.notification-categories').addClass('d-none');
            $('.slider-products').addClass('d-none');
            $('.notification-products').addClass('d-none');
        }
    } else {
        $('.slider-categories').addClass('d-none');
        $('.slider-url').addClass('d-none');
        $('.slider-products').addClass('d-none');
        $('.offer-url').addClass('d-none');
        $('.offer-products').addClass('d-none');
        $('.offer-categories').addClass('d-none');
        $('.notification-categories').addClass('d-none');
        $('.notification-products').addClass('d-none');
        $('.notification-url').addClass('d-none');
    }
});
if ($("input[data-bootstrap-switch]").length) {

    $("input[data-bootstrap-switch]").each(function () {
        $('input[data-bootstrap-switch]').bootstrapSwitch();
    });
}

$(document).on('click', '.sendMailBtn', function () {
    var email = $(this).data('email');
    $('.ManageOrderEmail').val(email);

});
$(document).ready(function () {
    $('#sms-gateway-modal').on('hidden.bs.modal', function () {

        $('.smsgateway_setting_form').removeClass('d-none');
        $('.update_notification_module').removeClass('d-none');
    });
});



$(document).on('click', '.edit_sms_modal', function () {

    $('#sms-gateway-modal').modal('show');
    var id = $(this).data('id');
    var url = $(this).data('url');
    $.ajax({
        type: "POST",
        url: base_url + "admin/custom_sms/view_sms_by_id",
        data: {
            'id': id,
            [csrfName]: csrfHash,

        },
        dataType: "json",
        success: function (response) {

            // Replace all \r\n (escaped)
            var cleanMessage = response.data.message.replace(/\\r\\n/g, ' ');

            $('#edit_id').val(response.data.id);
            $('#edit_title').val(response.data.title);
            $('#edit-text-box').val(cleanMessage);
            $('#selected_type').val(response.data.type);
            $('#selected_type_hidden').val(response.data.type);
            var type = response.data.type;
            if (type) {
                $('.' + type).removeClass('d-none');
            }

            $(".hashtag").click(function () {
                var data = $("textarea#text-box").text();
                var tab = $.trim($(this).text());
                var message = data + tab;
                $('textarea#text-box').val(message);
            });
            $(".hashtag_input").click(function () {
                var data = $("#udt_title").val();
                var tab = $.trim($(this).text());
                var message = data + tab;
                $('input#update_title').val(message);
            });

            setTimeout(function () {
                $('.sms-modal').unblock();
            }, 2000);

        }
    });

})

$(document).on('click', '.update_sms_data', function () {

});


$(document).on('click', '.edit_btn', function () {

    var id = $(this).data('id');
    var url = $(this).data('url');

    console.log(url, id);
    $('.edit-modal-lg').modal('show').find('.modal-body').load(base_url + url + '?edit_id=' + id + ' .form-submit-event', function () {



        if ($("input[data-bootstrap-switch]").length) {

            $("input[data-bootstrap-switch]").each(function () {
                $('input[data-bootstrap-switch]').bootstrapSwitch();
            });
        }
        $('#category_parent').select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
            templateResult: function (data) {
                if (!data.element) {
                    return data.text;
                }

                var $element = $(data.element);

                var $wrapper = $('<span></span>');
                $wrapper.addClass($element[0].className);

                $wrapper.text(data.text);

                return $wrapper;
            }
        });
        $('.select_multiple').each(function () {
            $(this).select2({
                theme: 'bootstrap4',
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
            });
        });

        $(".search_admin_product").select2({
            ajax: {
                url: base_url + 'admin/product/get_product_data',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (response, params) {

                    params.page = params.page || 1;

                    return {
                        results: response.rows,
                        pagination: {
                            more: (params.page * 30) < response.total
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection,
            theme: 'bootstrap4',
            placeholder: 'Search for products'
        });

        var input = $('.tags');

        if (input) {
            $.each(input, function (indexInArray, element) {
                // Check if Tagify is already initialized
                if (!element._tagify) {
                    // Initialize Tagify
                    new Tagify(element);
                }

            });
        }
        $(".search_admin_digital_product").select2({
            ajax: {
                url: base_url + 'admin/product/get_digital_product_data',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (response, params) {
                    params.page = params.page || 1;

                    return {
                        results: response.rows,
                        pagination: {
                            more: (params.page * 30) < response.total
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection,
            theme: 'bootstrap4',
            placeholder: 'Search for products'
        });

        custommessageAutoFill()

        searchable_zipcodes();
        searchable_cities();
        setTimeout(function () {
            $('.edit-modal-lg').unblock();
        }, 2000);

    });
});

function custommessageAutoFill() {
    const inputs = document.querySelectorAll(".text-box")
    const titleInput = document.querySelectorAll(".update_title")

    if (inputs.length == 2) {
        initializeInputFiller(".hashtag", inputs[1])
        initializeInputFiller(".hashtag_input", titleInput[1])
    } else {
        initializeInputFiller(".hashtag", inputs[0])
        initializeInputFiller(".hashtag_input", titleInput[0])
    }
}



function searchable_zipcodes() {
    var seller_id = $('input[name="seller_id"]').val();

    var search_zipcodes = $(".search_zipcode").select2({
        ajax: {
            url: base_url + from + '/area/get_zipcodes',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term, // search term
                    seller_id: seller_id,
                    page: params.page
                };
            },
            processResults: function (response, params) {

                params.page = params.page || 1;

                return {
                    results: response.data,
                    pagination: {
                        more: (params.page * 30) < response.total
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: formatRepo1,
        templateSelection: formatRepoSelection1,
        theme: 'bootstrap4',
        placeholder: 'Search for zipcodes',
        allowClear: Boolean($(this).data('allow-clear')),
    });
    return search_zipcodes;
}

function searchable_cities() {
    var seller_id = $('input[name="seller_id"]').val();

    return $(".city_list").select2({
        ajax: {
            url: base_url + from + '/area/get_cities',
            type: "GET",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term, // search term
                    seller_id: seller_id,
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },

        minimumInputLength: 1,
        theme: 'bootstrap4',
        placeholder: 'Search for cities',
        allowClear: Boolean($(this).data('allow-clear')),

    });
}

function searchable_zipcodes_deliveryboy() {
    var search_zipcodes = $(".deliveryboy_search_zipcode").select2({
        ajax: {
            url: base_url + 'delivery_boy/login/get_zipcodes',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (response, params) {
                params.page = params.page || 1;

                return {
                    results: response.data,
                    pagination: {
                        more: (params.page * 30) < response.total
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: formatRepo1,
        templateSelection: formatRepoSelection1,
        theme: 'bootstrap4',
        placeholder: 'Search for zipcodes',
        allowClear: Boolean($(this).data('allow-clear')),
    });
    return search_zipcodes;
}

function searchable_cities_deliveryboy() {
    return $(".deliveryboy_search_cities").select2({ // Add return statement here
        ajax: {
            url: base_url + 'delivery_boy/login/get_cities',
            type: "GET",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term, // search term
                };
            },
            processResults: function (response, params) {
                return {
                    results: response,
                };
            },
            cache: true
        },
        minimumInputLength: 1,
        theme: 'bootstrap4',
        placeholder: 'Search for cities',
        allowClear: Boolean($(this).data('allow-clear')),
    });
}





$(document).on('click', '.view_address', function () {
    var id = $(this).data('id');
    var url = $(this).data('url');;

});

$(document).on('click', '.view_btn', function () {
    var id = $(this).data('id');
    var url = $(this).data('url');
    $('.modal-body').load(base_url + url + '?edit_id=' + id + ' .form-submit-event');
    $('.modal-title').html('Manage Promo code');
    $('.modal-body').addClass('view');
    $(".edit-modal-lg").modal();
});

$(document).on('click', '.return_reason_view_btn', function () {
    var id = $(this).data('id');
    var url = $(this).data('url');
    $('.modal-body').load(base_url + url + '?edit_id=' + id + ' .form-submit-event');
    $('.modal-title').html('Manage Return Reason');
    $('.modal-body').addClass('view');
    $(".edit-modal-lg").modal();
});

$(document).on('hidden.bs.modal', '.edit-modal-lg', function () {
    $('.edit-modal-lg .modal-body').removeClass('view');
    $('#add_promocode .modal-body').removeClass('view');
    $('.edit-modal-lg .modal-body').html('');
})

//form-submit-event

function beforeSubmit(e) {

    try {
        if ($(e).attr('action').includes("admin/sellers/add_seller")) {
            if (document.getElementById("category_flag").value == "1") {
                $("#seller_model").click()
                iziToast.error({ message: "Please set commision for the given categories" });
                return false
            }
        }
    } catch (e) {

    }

    return true;
}
$(document).on('submit', '.container-fluid .form-submit-event', function (e) {
    e.preventDefault();

    if (!beforeSubmit(this)) {
        return false;
    }
    var formData = new FormData(this);
    var update_id = $('#update_id').val();

    var clickedButton = e.originalEvent?.submitter;

    if (update_id == '1') {
        var error_box = $('.edit-modal-lg #error_box');
        var submit_btn = $('.edit-modal-lg #submit_btn');
        var btn_html = $('.edit-modal-lg #submit_btn').html();
        var btn_val = $('.edit-modal-lg #submit_btn').val();
        var button_text = (btn_html != '' || btn_html != 'undefined') ? btn_html : btn_val;

    } else {
        var error_box = $('#error_box', this);
        var submit_btn = $(clickedButton);
        var btn_html = $(clickedButton).html();
        var btn_val = $(clickedButton).val();
        var button_text = (btn_html != '' || btn_html != 'undefined') ? btn_html : btn_val;
    }

    formData.append(csrfName, csrfHash);

    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: formData,
        beforeSend: function () {
            submit_btn.html('Please Wait..');
            submit_btn.attr('disabled', true);
        },
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (result) {
            csrfName = result['csrfName'];
            csrfHash = result['csrfHash'];


            console.log(result)
            if (result['error'] == true) {

                error_box.addClass("msg_error rounded p-3").removeClass('d-none msg_success');
                error_box.show().delay(1000).fadeOut();
                error_box.html(result['message']);
                submit_btn.html(button_text);
                submit_btn.attr('disabled', false);
                iziToast.error({
                    message: result['message'],
                });

            } else {

                error_box.addClass("msg_success rounded p-3").removeClass('d-none msg_error');
                error_box.show().delay(1000).fadeOut();
                error_box.html(result['message']);
                submit_btn.html(button_text);
                submit_btn.attr('disabled', false);
                if ($('.form-submit-event').hasClass('brand_add')) {
                    window.location.href = base_url + 'admin/brand/';
                }
                setTimeout(function () {
                    $('.modal').modal('hide');
                }, 1000);
                $('.table-striped').bootstrapTable('refresh');
                iziToast.success({
                    message: result['message'],
                });
                if ($('.form-submit-event').hasClass('add_affiliate_user_form')) {
                    window.location.href = base_url + 'admin/affiliate_users';
                }
                if ($('.form-submit-event').hasClass('add_affiliate_user_form_1')) {
                    window.location.href = base_url + 'affiliate/home';
                }
                if ($('.form-submit-event').hasClass('add_shipping_company')) {
                    window.location.href = base_url + 'admin/shipping-companies/manage_shipping_company';
                }

                // SHIPPING COMPANY - Don't redirect, just refresh table
                if ($('.form-submit-event').hasClass('add_shipping_company')) {
                    // Just refresh the table, don't redirect
                    $('#shipping_company_data').bootstrapTable('refresh');
                    // Reset form after modal closes
                    setTimeout(function () {
                        $('.form-submit-event')[0].reset();
                        $('#update_id').val('0');
                        $('.edit_shipping_company').remove();
                    }, 1100);
                    return; // Exit here, don't reload page
                }

                $('.form-submit-event')[0].reset();

                if (window.location.href.indexOf("login") > -1) {
                    setTimeout(function () {
                        location.reload();
                    }, 600);
                }

                setTimeout(function () {
                    location.reload();
                }, 1000);
            }
        }
    });
});

// 1.login

//forgot_page
$(document).ready(function () {
    custommessageAutoFill()
})
$(document).ready(function () {
    $('#forgot_password_page').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append(csrfName, csrfHash);
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            beforeSend: function () {
                $('#submit_btn').html('Please Wait..');
                $('#submit_btn').attr('disabled', true);
            },
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (result) {
                csrfName = result['csrfName'];
                csrfHash = result['csrfHash'];
                $('#result').html(result['message']);
                $('#result').show().delay(6000).fadeOut();
                $('#submit_btn').html('Send Email');
                $('#submit_btn').attr('disabled', false);
            }
        });
    });
});

//2.Product-Module
var edit_product_id = $('input[name=edit_product_id]').val();

if (edit_product_id) {

    create_fetched_attributes_html(from).done(function () {
        $('.no-attributes-added').hide();
        $('#save_attributes').removeClass('d-none');
        $('.no-variants-added').hide();
        save_attributes();
        create_fetched_variants_html(false, from);
    });

}

$(document).on('switchChange.bootstrapSwitch', '#is_cancelable', function (event) {
    event.preventDefault();
    var state = $(this).bootstrapSwitch('state');
    if (state) {
        $('#cancelable_till').show();
    } else {
        $('#cancelable_till').hide();
    }

});
$(document).on('switchChange.bootstrapSwitch', '#download_allowed', function (event) {
    event.preventDefault();
    var state = $(this).bootstrapSwitch('state');
    if (state) {
        $('#download_type').show();

    } else {
        $('#download_type').hide();
        $('#digital_link_container').addClass('d-none');
        $('#digital_media_container').addClass('d-none');
    }

});


$(document).on('change', '#category_parent', function () {
    $('#products_table').bootstrapTable('refresh');
});

$(document).on('change', '#category_parent', function () {
    $('#category_table').bootstrapTable('refresh');
});

$(document).on('change', '#seller_filter', function () {
    $('#products_table').bootstrapTable('refresh');
    $('#pickup_location_table').bootstrapTable('refresh');
});
$(document).on('change', '#seller_status_filter', function () {
    $('#seller_table').bootstrapTable('refresh');
});
$(document).on('change', '#affiliate_status_filter', function () {
    $('#affiliate-users-table').bootstrapTable('refresh');
});
$(document).on('change', '#delivery_boy_status_filter', function () {
    $('#delivery_boy_data').bootstrapTable('refresh');
});
$(document).on('change', '#user_filter', function () {
    $('#payment_request_table').bootstrapTable('refresh');
});
$(document).on('change', '#transaction_type_filter', function () {
    $('#affiliate_wallet_transaction_table').bootstrapTable('refresh');
});
$(document).on('change', '#message_type', function () {
    $('#system_notofication_table').bootstrapTable('refresh');
});
$(document).on('change', '#status_filter', function () {
    $('#products_table').bootstrapTable('refresh');
});
$(document).on('change', '#shipping_company_status_filter', function () {
    $('#shipping_company_data').bootstrapTable('refresh');
});


//Summer-note
$(document).ready(function () {

    var sub_id = $('#subcategory_id_js').val();
    if (typeof sub_id !== 'undefined') {
        $('#category_id').trigger('change', [{
            subcategory_id: sub_id
        }]);
    }

});

$(document).on('click', '#variation_product_btn', function (e) {
    e.preventDefault();
    var radio = $("input[name='pro_input_type']:checked").val();
    var edit_product_id = $('input[name=edit_product_id]').val();
    var html = '';
    html = add_product_variant_html(radio);
    $('#product_variance_html').append(html);
    if (typeof edit_product_id != 'undefined') {
        $("#product_variance_html").children('div').last().append("<input type='hidden' name='edit_product_variant[]'>");
    }
});
$(document).on('click', '#remove_product_btn', function (e) {
    e.preventDefault();
    $(this).closest('.row').remove();
});
$(document).on('click', '.delete-img', function () {
    var isJson = false;
    var id = $(this).data('id');
    var path = $(this).data('path');
    var field = $(this).data('field');
    var img_name = $(this).data('img');
    var table_name = $(this).data('table');
    var t = this;
    var isjson = $(this).data('isjson');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: base_url + 'admin/home/delete_image',
                    data: {
                        id: id,
                        path: path,
                        field: field,
                        img_name: img_name,
                        table_name: table_name,
                        isjson: isjson,
                        [csrfName]: csrfHash
                    },
                    dataType: 'json',
                    success: function (result) {
                        csrfName = result['csrfName'];
                        csrfHash = result['csrfHash'];
                        if (result['is_deleted'] == true) {
                            $(t).closest('div').remove();
                            Swal.fire('Success', 'Media Deleted !', 'success');
                        } else {
                            Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                        }
                    }
                });
            });
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'Your data is  safe.', 'error');
        }
    });
});

$(document).on('click', '.delete-media', function () {

    var id = $(this).data('id');
    var t = this;
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + from + '/media/delete/' + id,
                    dataType: 'json',
                    success: function (result) {
                        csrfName = result['csrfName'];
                        csrfHash = result['csrfHash'];
                        if (result['error'] == false) {
                            $('table').bootstrapTable('refresh');
                            Swal.fire('Success', 'File Deleted !', 'success');
                        } else {
                            Swal.fire('Oops...', result['message'], 'error');
                        }
                    }
                });
            });
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'Your data is  safe.', 'error');
        }
    });
});



$(document).on('focusout', '.discounted_price', function () {
    var discount_amt = parseInt($(this).val());
    var price = parseInt($(this).closest('.form-group').siblings().find('.price').val());
    if (typeof price != 'undefined' && price != "") {
        if (discount_amt > price) {
            iziToast.error({
                message: "Special price can" + "'" + "t exceed price",
            });
            $(this).val('');
        }
    }
});
$(document).on('focusout', '.price', function () {
    var price = parseInt($(this).val());
    var discount_amt = parseInt($(this).closest('.form-group').siblings().find('.discounted_price').val());
    if (typeof discount_amt != 'undefined' && discount_amt != "") {
        if (discount_amt > price) {
            iziToast.error({
                message: "Special price can" + "'" + "t exceed price",
            });
            $(this).val('');
        }
    }
});
$(document).on('click', '.clear-product-variance', function () {
    var edit_product_id = $('input[name=edit_product_id]').val();
    var radio_val = $("input[name='pro_input_type']:checked").val();
    var t = this;
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/product/delete_product',
                    type: 'GET',
                    data: {
                        'id': edit_product_id
                    },
                    dataType: 'json'
                }).done(function (response, textStatus) {
                    Swal.fire('Deleted!', response.message);
                    if (radio_val == 'packet') {
                        html = add_product_variant_html(radio_val);
                        $('#product_variance_html').html(html);
                        $('#product_loose_html').hide();
                        $('.pro_loose').hide();
                        $('.remove_pro_btn').hide();
                        $(t).hide();
                    } else {
                        $('#product_variance_html').show();
                        html = add_product_variant_html(radio_val);
                        $('#product_loose_html').show();
                        $('#product_variance_html').html(html);
                        $('.remove_pro_btn').hide();
                    }
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                });
            });
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.dismiss === Swal.DismissReason.cancel) {
            Swal.fire('Cancelled!', 'Your data is  safe.', 'error');
        }
    });
});
$('#sortable').sortable({
    axis: 'y',
    opacity: 0.6,
    cursor: 'grab'
});

$('#sortable').sortableJS({
    axis: 'y',
    opacity: 0.6,
    cursor: 'grab'
});

$(document).on('click', '#save_product_order', function () {
    var data = $('#sortable').sortable('serialize');
    $.ajax({
        data: data,
        type: 'GET',
        url: base_url + 'admin/product/update_product_order',
        dataType: 'json',
        success: function (response) {
            if (response.error == false) {
                iziToast.success({
                    message: response.message,
                });
            } else {
                iziToast.error({
                    message: response.message,
                });
            }
        }
    });
});

$(document).on('click', '#save_product_order', function () {
    var data = $('#sortable').sortableJS('serialize');
    $.ajax({
        data: data,
        type: 'GET',
        url: base_url + 'admin/product/update_product_order',
        dataType: 'json',
        success: function (response) {
            if (response.error == false) {
                iziToast.success({
                    message: response.message,
                });
            } else {
                iziToast.error({
                    message: response.message,
                });
            }
        }
    });
});

$(document).on('click', '#save_section_order', function () {
    var data = $('#sortable').sortable('serialize');
    $.ajax({
        data: data,
        type: 'GET',
        url: base_url + 'admin/featured_sections/update_section_order',
        dataType: 'json',
        success: function (response) {
            if (response.error == false) {
                iziToast.success({
                    message: response.message,
                });
            } else {
                iziToast.error({
                    message: response.message,
                });
            }
        }
    });
});


//form-submit-event
$(document).on('submit', '#save-product', function (e) {
    e.preventDefault();
    var product_type = $('#product-type').val();
    var counter = 0;
    if (product_type != 'undefined' && product_type != ' ') {

        if ($.trim(product_type) == 'simple_product') {
            if ($('.simple_stock_management_status').is(':checked')) {
                var len = 0
            } else {
                var len = 1
            }

            if ($('.stock-simple-mustfill-field').filter(function () {
                return this.value === '';
            }).length === len) {

                $('input[name="product_type"]').val($('#product-type').val());
                if ($('.simple_stock_management_status').is(':checked')) {
                    $('input[name="simple_product_stock_status"]').val($('#simple_product_stock_status').val());
                } else {
                    $('input[name="simple_product_stock_status"]').val('');
                }
                $('#product-type').prop('disabled', true);
                $('.product-attributes').removeClass('disabled');
                $('.product-variants').removeClass('disabled');
                $('.simple_stock_management_status').prop('disabled', true);

                save_product(this);
            } else {
                iziToast.error({
                    message: 'Please Fill All The Fields',
                });
            }
        }

        if ($.trim(product_type) == 'variable_product') {
            if ($('.variant_stock_status').is(":checked")) {
                var variant_stock_level_type = $('.variant-stock-level-type').val();
                if (variant_stock_level_type == 'product_level') {
                    if ($('.variant-stock-level-type').filter(function () {
                        return this.value === '';
                    }).length === 0 && $.trim($('.variant-stock-level-type').val()) != "") {

                        if ($('.variant-stock-level-type').val() == 'product_level' && $('.variant-stock-mustfill-field').filter(function () {
                            return this.value === '';
                        }).length !== 0) {
                            iziToast.error({
                                message: 'Please Fill All The Fields',
                            });
                        } else {
                            var varinat_price = $('input[name="variant_price[]"]').val();

                            if ($('input[name="variant_price[]"]').length >= 1) {

                                if ($('.varaint-must-fill-field').filter(function () {
                                    return this.value === '';
                                }).length == 0) {

                                    $('input[name="product_type"]').val($('#product-type').val());
                                    $('input[name="variant_stock_level_type"]').val($('#stock_level_type').val());
                                    $('input[name="varaint_stock_status"]').val("0");
                                    $('#product-type').prop('disabled', true);
                                    $('#stock_level_type').prop('disabled', true);
                                    $(this).removeClass('save-variant-general-settings');
                                    $('.product-attributes').removeClass('disabled');
                                    $('.product-variants').removeClass('disabled');
                                    $('.variant-stock-level-type').prop('readonly', true);
                                    $('#stock_status_variant_type').attr('readonly', true);
                                    $('.variant-product-level-stock-management').find('input,select').prop('readonly', true);
                                    $('#tab-for-variations').removeClass('d-none');
                                    $('.variant_stock_status').prop('disabled', true);
                                    $('#product-tab a[href="#product-attributes"]').tab('show');
                                    save_product(this);

                                } else {
                                    $('.varaint-must-fill-field').each(function () {
                                        $(this).css('border', '');
                                        if ($(this).val() == '') {
                                            $(this).css('border', '2px solid red');
                                            $(this).closest('#variant_stock_management_html').find('div:first').addClass('show');
                                            $('#product-tab a[href="#product-variants"]').tab('show');
                                            counter++;
                                        }
                                    });
                                }


                            } else {

                                Swal.fire('Variation Needed !', 'Atleast Add One Variation To Add The Product.', 'warning');
                            }
                        }
                    } else {
                        iziToast.error({
                            message: 'Please Fill All The Fields',
                        });
                    }
                } else {
                    if ($('input[name="variant_price[]"]').length >= 1) {

                        if ($('.varaint-must-fill-field').filter(function () {
                            return this.value === '';
                        }).length == 0) {
                            $('input[name="product_type"]').val($('#product-type').val());
                            $('.variant_stock_status').prop('disabled', true);
                            $('#product-type').prop('disabled', true);
                            $('.product-attributes').removeClass('disabled');
                            $('.product-variants').removeClass('disabled');
                            $('#tab-for-variations').removeClass('d-none');
                            save_product(this);
                        } else {
                            $('.varaint-must-fill-field').each(function () {
                                $(this).css('border', '');
                                if ($(this).val() == '') {
                                    $(this).css('border', '2px solid red');
                                    $(this).closest('#variant_stock_management_html').find('div:first').addClass('show');
                                    $('#product-tab a[href="#product-variants"]').tab('show');
                                    counter++;
                                }
                            });
                        }

                    } else {

                        Swal.fire('Variation Needed !', 'Atleast Add One Variation To Add The Product.', 'warning');

                    }
                }
            } else {

                if ($('input[name="variant_price[]"]').length == 0) {
                    Swal.fire('Variation Needed !', 'Atleast Add One Variation To Add The Product.', 'warning');
                } else {
                    if ($('.varaint-must-fill-field').filter(function () {
                        return this.value === '';
                    }).length == 0) {
                        save_product(this);
                    } else {
                        $('.varaint-must-fill-field').each(function () {
                            $(this).css('border', '');
                            if ($(this).val() == '') {
                                $(this).css('border', '2px solid red');
                                $(this).closest('#variant_stock_management_html').find('div:first').addClass('show');
                                $('#product-tab a[href="#product-variants"]').tab('show');
                                counter++;
                            }
                        });
                    }
                }
            }
        }
        if ($.trim(product_type) == 'digital_product') {

            save_product(this);
        }

    } else {
        iziToast.error({
            message: 'Please Select Product Type !',
        });
    }

    if (counter > 0) {
        iziToast.error({
            message: 'Please fill all the required fields in the variation tab !',
        });
    }

});



$(document).on('click', '#delete-product', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + from + '/product/delete_product',
                    data: {
                        id: id
                    },
                    dataType: 'json'
                }).done(function (response, textStatus) {
                    if (response.error == false) {

                        Swal.fire('Deleted!', response.message, 'success');
                    } else {
                        Swal.fire('Oops...', response.message, 'error');
                    }
                    $('table').bootstrapTable('refresh');
                    csrfName = response['csrfName'];
                    csrfHash = response['csrfHash'];
                }).fail(function (jqXHR, textStatus, errorThrown) {
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    csrfName = response['csrfName'];
                    csrfHash = response['csrfHash'];
                });
            });
        },
        allowOutsideClick: false
    });
});

// multiple_values
$('.select_single , .multiple_values , #product-type').each(function () {
    $(this).select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    });
});

$(document).on('select2:selecting', '.select_single', function (e) {

    if ($.inArray($(this).val(), attributes_values_selected) > -1) {
        //Remove value if further selected
        attributes_values_selected.splice(attributes_values_selected.indexOf($(this).select2().find(":selected").val()), 1);
    }

});

$(document).on('select2:selecting', '.select_single .variant_attributes', function (e) {

    if ($.inArray($(this).val(), variant_values_selected) > -1) {
        //Remove value if further selected
        variant_values_selected.splice(variant_values_selected.indexOf($(this).select2().find(":selected").val()), 1);

    }

});

$(document).on('select2:select', '.select_single', function (e) {
    var text = this.className;
    var type;
    $(this).closest('.row').find(".multiple_values").text(null).trigger('change');
    var data = $(this).select2().find(":selected").data("values");
    if (text.search('attributes') != -1) {
        value_check_array = attributes_values_selected.slice();
        type = 'attributes';
    }

    if (text.search('variant_attributes') != -1) {
        value_check_array = variant_values_selected.slice();
        type = 'variant_attributes';
    }

    if ($.inArray($(this).select2().find(":selected").val(), value_check_array) > -1) {
        iziToast.error({
            message: 'Attribute Already Selected',
        });
        $(this).val('').trigger('change');
    } else {
        value_check_array.push($(this).select2().find(":selected").val());
    }
    if (text.search('attributes') != -1) {
        attributes_values_selected = value_check_array.slice();
    }

    if (text.search('variant_attributes') != -1) {
        variant_values_selected = value_check_array.slice();
    }
    $(this).closest('.row').find("." + type).select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    });
    $(this).closest('.row').find(".multiple_values").select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
        data: data,
    });


});

$(document).on('click', ' #add_attributes , #tab-for-variations', function (e) {

    if (e.target.id == 'add_attributes') {

        $('.no-attributes-added').hide();
        $('#save_attributes').removeClass('d-none');
        counter++;
        var $attribute = $('#attributes_values_json_data').find('.select_single');
        var $options = $($attribute).clone().html();
        var attr_name = 'pro_attr_' + counter;
        // product-attr-selectbox
        if ($('#product-type').val() == 'simple_product') {
            var html = '<div class="form-group move row my-auto p-2 border rounded bg-gray-light product-attr-selectbox" id=' + attr_name + '><div class="col-md-1 col-sm-12 text-center my-auto"><i class="fas fa-sort"></i></div><div class="col-md-4 col-sm-12"> <select name="attribute_id[]" class="attributes select_single" data-placeholder=" Type to search and select attributes"><option value=""></option>' + $options + '</select></div><div class="col-md-4 col-sm-12 "> <select name="attribute_value_ids[]" class="multiple_values" multiple="" data-placeholder=" Type to search and select attributes values"><option value=""></option> </select></div><div class="col-md-2 col-sm-6 text-center py-1 align-self-center"> <button type="button" class="btn btn-tool remove_attributes"> <i class="text-danger far fa-times-circle fa-2x "></i> </button></div></div>';
        } else {
            $('#note').removeClass('d-none');
            var html = '<div class="form-group row move my-auto p-2 border rounded bg-gray-light product-attr-selectbox" id=' + attr_name + '><div class="col-md-1 col-sm-12 text-center my-auto"><i class="fas fa-sort"></i></div><div class="col-md-4 col-sm-12"> <select name="attribute_id[]" class="attributes select_single" data-placeholder=" Type to search and select attributes"><option value=""></option>' + $options + '</select></div><div class="col-md-4 col-sm-12 "> <select name="attribute_value_ids[]" class="multiple_values" multiple="" data-placeholder=" Type to search and select attributes values"><option value=""></option> </select></div><div class="col-md-2 col-sm-6 text-center py-1 align-self-center"><input type="checkbox" name="variations[]" class="is_attribute_checked custom-checkbox "></div><div class="col-md-1 col-sm-6 text-center py-1 align-self-center "> <button type="button" class="btn btn-tool remove_attributes"> <i class="text-danger far fa-times-circle fa-2x "></i> </button></div></div>';
        }
        $('#attributes_process').append(html);

        $("#attributes_process").last().find(".attributes").select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });

        $("#attributes_process").last().find(".multiple_values").select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });
    }

    if (e.target.id == 'tab-for-variations') {
        $('.additional-info').block({
            message: '<h6>Loading Variations</h6>',
            css: {
                border: '3px solid #E7F3FE'
            }
        });
        if (attributes_values.length > 0) {

            $('.no-variants-added').hide();
            create_variants(false, from);

        }
        setTimeout(function () {
            $('.additional-info').unblock();
        }, 3000);
    }
});

$(document).on('click', '#reset_variants', function () {

    Swal.fire({
        title: 'Are You Sure To Reset!',
        text: "You won't be able to revert this after update!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Reset it!',
        showLoaderOnConfirm: true,
        allowOutsideClick: false
    }).then((result) => {
        if (result.value) {
            $('.additional-info').block({
                message: '<h6>Reseting Variations</h6>',
                css: {
                    border: '3px solid #E7F3FE'
                }
            });
            if (attributes_values.length > 0) {
                $('.no-variants-added').hide();
                create_variants(false, 'seller');
            }
            setTimeout(function () {
                $('.additional-info').unblock();
            }, 2000);
        }
    });
});

$(document).on('click', '.remove_edit_attribute', function (e) {

    $(this).closest('.row').remove();
});

$(document).on('click', '.remove_attributes , .remove_variants', function (e) {
    Swal.fire({
        title: 'Are you sure want to delete!',
        text: "You won't be able to revert this after update!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Delete it!',
        showLoaderOnConfirm: true,
        allowOutsideClick: false
    }).then((result) => {
        if (result.value) {
            var text = this.className;
            if (text.search('remove_attributes') != -1) {
                var edit_id = $('#edit_product_id').val();

                attributes_values_selected.splice(attributes_values_selected.indexOf($(this).select2().find(":selected").val()), 1);
                $(this).closest('.row').remove();
                counter -= 1;
                var numItems = $('.product-attr-selectbox').length;
                if (numItems == 0) {
                    $('.no-attributes-added').show();
                    $('#save_attributes').addClass('d-none');
                    $('#note').addClass('d-none');

                }

            }
            if (text.search('remove_variants') != -1) {
                variant_values_selected.splice(variant_values_selected.indexOf($(this).select2().find(":selected").val()), 1);
                $(this).closest('.form-group').remove();
                variant_counter -= 1;
                var numItems = $('.product-variant-selectbox').length;
                if (numItems == 0) {
                    $('.no-variants-added').show();
                }
            }
        }
    });
});

$(document).on('select2:select', '#product-type', function () {
    var value = $(this).val();
    if ($.trim(value) != "") {
        if (value == 'simple_product') {
            $('#variant_stock_level').hide(200);
            $('#general_price_section').show(200);
            $('.simple-product-save').show(700);
            $('.product-attributes').addClass('disabled');
            $('.product-variants').addClass('disabled');
            $('#digital_product_setting').hide(200);
            $('.cod_allowed').removeClass('d-none');
            $('.is_returnable').removeClass('d-none');
            $('.is_cancelable').removeClass('d-none');
        }
        if (value == 'variable_product') {
            $('#general_price_section').hide(200);
            $('.simple-product-level-stock-management').hide(200);
            $('.simple-product-save').hide(200);
            $('.product-attributes').addClass('disabled');
            $('.product-variants').addClass('disabled');
            $('#variant_stock_level').show();
            $('#digital_product_setting').hide(200);
            $('.cod_allowed').removeClass('d-none');
            $('.is_returnable').removeClass('d-none');
            $('.is_cancelable').removeClass('d-none');
        }

    } else {
        $('.product-attributes').addClass('disabled');
        $('.product-variants').addClass('disabled');
        $('#general_price_section').hide(200);
        $('.simple-product-level-stock-management').hide(200);
        $('.simple-product-save').hide(200)
        $('#variant_stock_level').hide(200);

    }
});

$(document).on('change', '#product_type_menu', function () {
    var value = $(this).val();
    if (value == 'digital_product') {
        var html = '<option value="digital_product">Digital Product</option>';

        $('#product-type').html(html);
        $('#variant_stock_level').hide(200);
        $('#general_price_section').show(200);
        $('.simple-product-save').hide(200);
        $('.simple-product-level-stock-management').addClass('d-none');
        $('.simple_stock_management').addClass('d-none');
        $('.product-attributes').addClass('disabled');
        $('.product-variants').addClass('disabled');
        $('#digital_product_setting').show();
        $('.cod_allowed').addClass('d-none');
        $('.is_returnable').addClass('d-none');
        $('.is_cancelable').addClass('d-none');
        $('.indicator').addClass('d-none');
        $('.total_allowed_quantity').addClass('d-none');
        $('.minimum_order_quantity').addClass('d-none');
        $('.guarantee_period').addClass('d-none');
        $('.warranty_period').addClass('d-none');
        $('.quantity_step_size').addClass('d-none');
        $('.deliverable_type').addClass('d-none');
        $('.hsn_code').addClass('d-none');
        $('#product-dimensions').addClass('d-none');
        $('.is_attachment_required').addClass('d-none');
        $('.standdard_shipping').addClass('d-none');

    } else {
        var html = ' <option value=" ">Select Type</option>' +
            '<option value="simple_product">Simple Product</option>' +
            '<option value="variable_product">Variable Product</option>';
        var catid = $('#product_category_tree_view_html').jstree("get_selected");
        console.log(catid);
        $('#product-type').html(html);
        $('.cod_allowed').removeClass('d-none');
        $('.is_returnable').removeClass('d-none');
        $('.is_cancelable').removeClass('d-none');
        $('.indicator').removeClass('d-none');
        $('.total_allowed_quantity').removeClass('d-none');
        $('.minimum_order_quantity').removeClass('d-none');
        $('.guarantee_period').removeClass('d-none');
        $('.warranty_period').removeClass('d-none');
        $('.quantity_step_size').removeClass('d-none');
        $('.deliverable_type').removeClass('d-none');
        $('.hsn_code').removeClass('d-none');
        $('#product-dimensions').removeClass('d-none');
        $('.is_attachment_required').removeClass('d-none');
        $('.standdard_shipping').removeClass('d-none');
    }
});

$(document).on('change', '.variant_stock_status', function () {
    if ($(this).prop("checked") == true) {
        $(this).attr("checked", true);
        $('#stock_level').show(200);
    } else {
        $(this).attr("checked", false);
        $('#stock_level').hide(200);
    }
});

$(document).on('change', '.variant-stock-level-type', function () {
    if ($('.variant-stock-level-type').val() == 'product_level') {
        $('.variant-product-level-stock-management').show();
    }
    if ($.trim($('.variant-stock-level-type').val()) != 'product_level') {
        $('.variant-product-level-stock-management').hide();
    }
});

$(document).on('change', '.simple_stock_management_status', function () {
    if ($(this).prop("checked") == true) {
        $(this).attr("checked", true);
        $('.simple-product-level-stock-management').show(200);
    } else {
        $(this).attr("checked", false);
        $('.simple-product-level-stock-management').hide(200);
        $('.simple-product-level-stock-management').find('input').val('');
    }
});

$(document).on('click', '#save_attributes', function () {
    Swal.fire({
        title: 'Are you sure want to save changes!',
        text: "Do not save attributes if you made no changes! It will reset the variants if there are no changes in attributes or its values !",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        showLoaderOnConfirm: true,
        allowOutsideClick: false
    }).then((result) => {

        if (result.value) {
            attribute_flag = 1;
            save_attributes();
            create_fetched_variants_html(true, from);
            iziToast.success({
                message: 'Attributes Saved Succesfully',
            });
        }
    });
});

$('#attributes_process').sortable({
    axis: 'y',
    opacity: 0.6,
    cursor: 'grab'
});

$('#variants_process').sortableJS({
    axis: 'y',
    opacity: 0.6,
    cursor: 'grab'
});

$(document).on('click', '.reset-settings', function (e) {

    Swal.fire({
        title: 'Are You Sure To Reset!',
        text: "This will reset all attributes && variants too if added.",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Reset it!',
        showLoaderOnConfirm: true,
        allowOutsideClick: false
    }).then((result) => {
        if (result.value) {
            attributes_values_selected = [];
            value_check_array = [];
            pre_selected_attr_values = [];
            var html = ' <input type="hidden" name="reset_settings" value="1">' +
                '<div class="row mt-4 col-md-12 "> <nav class="w-100">' +
                '<div class="nav nav-tabs" id="product-tab" role="tablist"> ' +
                '<a class="nav-item nav-link active" id="tab-for-general-price" data-toggle="tab" href="#general-settings" role="tab" aria-controls="general-price" aria-selected="true">General</a> ' +
                '<a class="nav-item nav-link seo-section-settings" id="tab-for-general-seo-section" data-toggle="tab" href="#seo-section-settings" role="tab" aria-controls="general-seo-section" aria-selected="false">SEO Configuration</a> ' +
                '<a class="nav-item nav-link disabled product-attributes" id="tab-for-attributes" data-toggle="tab" href="#product-attributes" role="tab" aria-controls="product-attributes" aria-selected="false">Attributes</a> ' +
                '<a class="nav-item nav-link disabled product-variants d-none" id="tab-for-variations" data-toggle="tab" href="#product-variants" role="tab" aria-controls="product-variants" aria-selected="false">Variations</a></div> </nav>' +
                '<div class="tab-content p-3 col-md-12" id="nav-tabContent">' +
                '<div class="tab-pane fade active show" id="general-settings" role="tabpanel" aria-labelledby="general-settings-tab">' +
                '<div class="form-group">' +
                ' <label for="type" class="col-md-2">Type Of Product :</label>' +
                '<div class="col-md-12"> <input type="hidden" name="product_type"> ' +
                '<input type="hidden" name="simple_product_stock_status"> ' +
                '<input type="hidden" name="variant_stock_level_type"> ' +
                ' <input type="hidden" name="variant_stock_status"> ' +
                '<select name="type" id="product-type" class="form-control product-type" data-placeholder=" Type to search and select type">' +
                '<option value=" ">Select Type</option><option value="simple_product">Simple Product</option>' +
                '<option value="variable_product">Variable Product</option> </select></div></div>' +
                '<div id="product-general-settings"><div id="general_price_section" class="collapse"><div class="form-group"> ' +
                '<label for="type" class="col-md-2">Price:</label>' +
                '<div class="col-md-12"> <input type="number" name="simple_price" class="form-control stock-simple-mustfill-field price" min="0" step="0.01"></div></div>' +
                '<div class="form-group"> <label for="type" class="col-md-2">Special Price:</label><div class="col-md-12">' +
                ' <input type="number" name="simple_special_price" class="form-control discounted_price" min="0"></div></div>' +
                '<div class="form-group simple_stock_management"><div class="col"> ' +
                '<input type="checkbox" name="simple_stock_management_status" class="align-middle simple_stock_management_status">' +
                ' <span class="align-middle">Enable Stock Management</span></div></div></div>' +
                '<div class="form-group simple-product-level-stock-management collapse">' +
                '<div class="col col-xs-12"> <label class="control-label">SKU :</label> ' +
                '<input type="text" name="product_sku" class="col form-control simple-pro-sku"></div>' +
                '<div class="col col-xs-12"> <label class="control-label">Total Stock :</label> ' +
                '<input type="text" name="product_total_stock" class="col form-control stock-simple-mustfill-field"></div><div class="col col-xs-12"> ' +
                '<label class="control-label">Stock Status :</label> <select type="text" class="col form-control stock-simple-mustfill-field" id="simple_product_stock_status">' +
                '<option value="1">In Stock</option><option value="0">Out Of Stock</option> </select></div></div>' +
                '<div class="form-group collapse simple-product-save"><div class="col"> ' +
                '<a href="javascript:void(0);" class="btn btn-primary save-settings">Save Settings</a></div></div></div>' +
                '<div id="variant_stock_level" class="collapse"><div class="form-group"><div class="col">' +
                ' <input type="checkbox" name="variant_stock_management_status" class="align-middle variant_stock_status"> <span class="align-middle"> Enable Stock Management</span></div></div>' +
                '<div class="form-group collapse" id="stock_level"> <label for="type" class="col-md-2">Choose Stock Management Type:</label>' +
                '<div class="col-md-12"> <select id="stock_level_type" class="form-control variant-stock-level-type" data-placeholder=" Type to search and select type">' +
                '<option value=" ">Select Stock Type</option><option value="product_level">Product Level ( Stock Will Be Managed Generally )</option>' +
                '<option value="variable_level">Variable Level ( Stock Will Be Managed Variant Wise )</option>' +
                ' </select><div class="form-group row variant-product-level-stock-management collapse">' +
                '<div class="col col-xs-12"> <label class="control-label">SKU :</label>' +
                ' <input type="text" name="sku_variant_type" class="col form-control"></div>' +
                '<div class="col col-xs-12"> <label class="control-label">Total Stock :</label>' +
                ' <input type="text" name="total_stock_variant_type" class="col form-control variant-stock-mustfill-field"></div>' +
                '<div class="col col-xs-12"> <label class="control-label">Stock Status :</label> <select type="text" id="stock_status_variant_type" name="variant_status" class="col form-control variant-stock-mustfill-field">' +
                '<option value="1">In Stock</option><option value="0">Out Of Stock</option> </select></div></div></div></div>' +
                '<div class="form-group"><div class="col"> <a href="javascript:void(0);" class="btn btn-primary save-variant-general-settings">Save Settings</a></div></div></div></div>' +


                '<div class="tab-pane fade" id="seo-section-settings" role="tabpanel" aria-labelledby="seo-section-settings-tab">' +
                '<h4 class="bg-light m-0 px-2 py-3">SEO Configuration</h4>' +
                '<div class="d-flex bg-light">' +
                '<div class="form-group col-sm-6">' +
                '<label for="seo_page_title" class="form-label form-label-sm d-flex"> SEO Page Title </label>' +
                '<input type="text" class="form-control" id="seo_page_title" placeholder="SEO Page Title" name="seo_page_title" value="">' +
                '</div>' +
                '<div class="form-group col-sm-6">' +
                '<label for="seo_meta_keywords" class="form-label form-label-sm d-flex"> SEO Meta Keywords </label>' +
                '<input class="tags bg-white" id="seo_meta_keywords" placeholder="SEO Meta Keywords" name="seo_meta_keywords" value="" />' +
                '</div>' +
                '</div >' +
                '<div class="d-flex bg-light">' +
                '<div class="form-group col-sm-6">' +
                '<label for="seo_meta_description" class="form-label form-label-sm d-flex"> SEO Meta Description </label>' +
                '<textarea class="form-control" id="seo_meta_description" placeholder="SEO Meta Keywords" name="seo_meta_description"></textarea>' +
                '</div>' +
                '<div class="col-sm-12 col-md-6">' +
                '<div class="form-group">' +
                '<label for="image">SEO Open Graph Image <small>(Recommended Size : 131 x 131 pixels)</small></label>' +
                '<div class="col-sm-10">' +
                '<div class="col-md-12">' +
                '<a class="uploadFile img btn btn-primary text-white btn-sm" data-input="seo_og_image" data-isremovable="1" data-is-multiple-uploads-allowed="0"' +
                'data-toggle="modal" data-target="#media-upload-modal" value="Upload"><i class="fa fa-upload"></i> Upload</a>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div >' +

                '<div class="tab-pane fade" id="product-attributes" role="tabpanel" aria-labelledby="product-attributes-tab">' +
                '<div class="info col-12 p-3 d-none" id="note"><div class=" col-12 d-flex align-center"> <strong>Note : </strong> <input type="checkbox" checked="checked" class="ml-3 my-auto custom-checkbox" disabled> ' +
                '<span class="ml-3">check if the attribute is to be used for variation </span></div></div><div class="col-md-12">' +
                ' <a href="javascript:void(0);" id="add_attributes" class="btn btn-block btn-outline-primary col-md-2 float-right m-2 btn-sm">Add Attributes</a>' +
                ' <a href="javascript:void(0);" id="save_attributes" class="btn btn-block btn-outline-primary col-md-2 float-right m-2 btn-sm d-none">Save Attributes</a></div>' +
                '<div class="clearfix"></div><div id="attributes_process"><div class="form-group text-center row my-auto p-2 border rounded bg-gray-light col-md-12 no-attributes-added">' +
                '<div class="col-md-12 text-center">No Product Attribures Are Added !</div></div></div></div><div class="tab-pane fade" id="product-variants" role="tabpanel" aria-labelledby="product-variants-tab">' +
                '<div class="clearfix"></div><div class="form-group text-center row my-auto p-2 border rounded bg-gray-light col-md-12 no-variants-added">' +
                '<div class="col-md-12 text-center">No Product Variations Are Added !</div></div>' +
                '<div id="variants_process" class="ui-sortable"></div></div></div></div>';
            $('.additional-info').html(html);
            $('.no-attributes-added').show();
            $('#product-type').each(function () {
                $(this).select2({
                    theme: 'bootstrap4',
                    width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                    placeholder: $(this).data('placeholder'),
                    allowClear: Boolean($(this).data('allow-clear')),
                });
            });
        }
    });


});
$(document).on('click', '.save-settings', function (e) {
    e.preventDefault();

    if ($('.simple_stock_management_status').is(':checked')) {
        var len = 0
    } else {
        var len = 1
    }

    if ($('.stock-simple-mustfill-field').filter(function () {
        return this.value === '';
    }).length === len) {
        $('.additional-info').block({
            message: '<h6>Saving Settings</h6>',
            css: {
                border: '3px solid #E7F3FE'
            }
        });

        $('input[name="product_type"]').val($('#product-type').val());
        if ($('.simple_stock_management_status').is(':checked')) {
            $('input[name="simple_product_stock_status"]').val($('#simple_product_stock_status').val());
        } else {
            $('input[name="simple_product_stock_status"]').val('');
        }
        $('#product-type').prop('disabled', true);
        $('.product-attributes').removeClass('disabled');
        $('.product-variants').removeClass('disabled');
        $('.simple_stock_management_status').prop('disabled', true);
        setTimeout(function () {
            $('.additional-info').unblock();
        }, 2000);

    } else {
        iziToast.error({
            message: 'Please Fill All The Fields',
        });
    }
});
$(document).on('click', '.save-digital-product-settings', function (e) {
    e.preventDefault();
    $('.product-attributes').removeClass('disabled');
});
$(document).on('click', '.delete_system_noti', function () {
    var value = $(this).data('id');
    var url = base_url + 'admin/Notification_settings/delete_system_notification';
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        'id': value
                    },
                    dataType: 'json',
                })
                    .done(function (result, textStatus) {
                        if (result['error'] == false) {
                            Swal.fire('Deleted!', result['message'], 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', result['message'], 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});
$(document).on('click', '.save-variant-general-settings', function (e) {
    e.preventDefault();
    create_fetched_variants_html(true, from);
    if ($('.variant_stock_status').is(":checked")) {
        if ($('.variant-stock-level-type').filter(function () {
            return this.value === '';
        }).length === 0 && $.trim($('.variant-stock-level-type').val()) != "") {

            if ($('.variant-stock-level-type').val() == 'product_level' && $('.variant-stock-mustfill-field').filter(function () {
                return this.value === '';
            }).length !== 0) {
                iziToast.error({
                    message: 'Please Fill All The Fields',
                });
            } else {
                $('input[name="product_type"]').val($('#product-type').val());
                $('input[name="variant_stock_level_type"]').val($('#stock_level_type').val());
                $('input[name="variant_stock_status"]').val("0");
                $('#product-type').prop('disabled', true);
                $('#stock_level_type').prop('disabled', true);
                $(this).removeClass('save-variant-general-settings');
                $('.product-attributes').removeClass('disabled');
                $('.product-variants').removeClass('disabled');
                $('.variant-stock-level-type').prop('readonly', true);
                $('#stock_status_variant_type').attr('readonly', true);
                $('.variant-product-level-stock-management').find('input,select').prop('readonly', true);
                $('#tab-for-variations').removeClass('d-none');
                $('.variant_stock_status').prop('disabled', true);
                $('#product-tab a[href="#product-attributes"]').tab('show');
                Swal.fire('Settings Saved !', 'Attributes & Variations Can Be Added Now', 'success');
            }
        } else {
            iziToast.error({
                message: 'Please Fill All The Fields',
            });
        }

    } else {

        $('input[name="product_type"]').val($('#product-type').val());
        $('input[name="variant_stock_status"]').val("");
        $('input[name="variant_stock_level_type"]').val("");
        $('#product-tab a[href="#product-attributes"]').tab('show');
        $('.variant_stock_status').prop('disabled', true);
        $('#product-type').prop('disabled', true);
        $('.product-attributes').removeClass('disabled');
        $('.product-variants').removeClass('disabled');
        $('#tab-for-variations').removeClass('d-none');
        Swal.fire('Settings Saved !', 'Attributes & Variations Can Be Added Now', 'success');
    }

});


$(document).on('change', '.new-added-variant', function () {

    var myOpts = $(this).children().map(function () {
        return $(this).val();
    }).get();
    var variant_id = $(this).val();
    var curr_vals = [];
    var $variant_ids = $(this).closest('.product-variant-selectbox').find('input[name="variants_ids[]"]').val();
    $.each($variant_ids.split(','), function (key, val) {
        if (val != '') {
            curr_vals[key] = $.trim(val);
        }
    });
    var newvalues = curr_vals.filter((el) => !myOpts.includes(el));
    var len = newvalues.length;
    if (variant_id != '') {
        newvalues[len] = $.trim(variant_id);
    }
    $(this).closest('.product-variant-selectbox').find('input[name="variants_ids[]"]').val(newvalues.toString());
});



function get_seller_categories(seller_id, ignore_status, edit_id, from) {
    $.ajax({
        type: 'GET',
        url: base_url + from + '/category/get_seller_categories',
        data: {
            "ignore_status": ignore_status,
            "seller_id": seller_id
        },
        dataType: 'json',
        success: function (result) {
            $('#product_category_tree_view_html').jstree("destroy").empty();
            $('#product_category_tree_view_html').jstree({
                plugins: ["checkbox", 'themes'],
                'core': {
                    multiple: false,
                    'data': result['data'],
                },
                checkbox: {
                    three_state: false,
                    cascade: "none"
                }
            });

            $('#product_category_tree_view_html')
                .bind('ready.jstree', function (e, data) {
                    $(this).jstree(true).select_node(edit_id);
                });

            $('#product_category_tree_view_html').off('changed.jstree').on('changed.jstree', function (e, data) {
                var affiliate_categories = $('#affiliate_categories').val(); // like: "1,2,3"
                var selectedId = parseInt(data.selected[0]);

                // Convert string to array of integers
                var affiliateIds = affiliate_categories ? affiliate_categories.split(',').map(Number) : [];

                if (affiliateIds.includes(selectedId)) {
                    $('.is_in_affiliate').removeClass('d-none');
                } else {
                    $('.is_in_affiliate').addClass('d-none');
                }
            });
        }
    });
}

if (window.location.href.indexOf("admin/product") > -1) {
    var edit_id = $('input[name="category_id"]').val();
    var seller_id = $('input[name="seller_id"]').val();
    var ignore_status = $.isNumeric(edit_id) && edit_id > 0 ? 1 : 0;
    if ($.isNumeric(seller_id) && seller_id > 0) {
        get_seller_categories(seller_id, ignore_status, edit_id, 'admin');
    } else {
        $.ajax({
            type: 'GET',
            url: base_url + 'admin/category/get_categories',
            data: {
                "ignore_status": ignore_status
            },
            dataType: 'json',
            success: function (result) {
                var edit_id = $('input[name="category_id"]').val();
                $('#product_category_tree_view_html').jstree({
                    plugins: ["checkbox", 'themes'],
                    'core': {
                        'data': result['data'],
                        multiple: false
                    },
                    checkbox: {
                        three_state: false,
                        cascade: "none"
                    }
                });
                $('#product_category_tree_view_html')
                    .bind('ready.jstree', function (e, data) {
                        $(this).jstree(true).select_node(edit_id);
                    });
            }
        });
    }
}
$(document).on('click', '.update_active_status', function () {

    var update_id = $(this).data('id');
    var status = $(this).data('status');
    var table = $(this).data('table');
    if (table == "themes") {
        update_theme(update_id, status, table, from);
    } else {
        update_status(update_id, status, table, from);
    }

});
if (window.location.href.indexOf("seller/product") > -1) {
    var edit_id = $('input[name="category_id"]').val();
    var seller_id = $('input[name="seller_id"]').val();
    var ignore_status = $.isNumeric(edit_id) && edit_id > 0 ? 1 : 0;
    if ($.isNumeric(seller_id) && seller_id > 0) {
        get_seller_categories(seller_id, ignore_status, edit_id, from);
    } else {
        $.ajax({
            type: 'GET',
            url: base_url + 'seller/category/get_categories',
            data: {
                "ignore_status": ignore_status
            },
            dataType: 'json',
            success: function (result) {
                var edit_id = $('input[name="category_id"]').val();
                $('#product_category_tree_view_html').jstree({
                    plugins: ["checkbox", 'themes'],
                    'core': {
                        'data': result['data'],
                        multiple: false
                    },
                    checkbox: {
                        three_state: false,
                        cascade: "none"
                    }
                });

                $('#product_category_tree_view_html')
                    .bind('ready.jstree', function (e, data) {
                        $(this).jstree(true).select_node(edit_id);
                    });
            }
        });
    }

}

$(document).on('change', '#seller_id', function (e) {
    e.preventDefault();
    var edit_id = $('input[name="category_id"]').val();
    var seller_id = $(this).val();
    var ignore_status = $.isNumeric(edit_id) && edit_id > 0 ? 1 : 0;
    get_seller_pickup_location(seller_id)
    get_seller_categories(seller_id, ignore_status, edit_id, 'admin');
});

function get_seller_pickup_location(seller_id) {
    $.ajax({
        type: 'GET',
        url: base_url + from + '/Pickup_location/get_seller_pickup_location',
        data: {
            [csrfName]: csrfHash,
            "seller_id": seller_id
        },
        dataType: 'json',
        success: function (result) {
            var html = '';
            html = ' <option value=" ">Select Pickup Location</option>';
            if (result.rows.length > 0) {
                result.rows.forEach((value, key) => {
                    html += '<option value="' + value.pickup_location + '">' + value.pickup_location + '</option>';
                    $('#pickup_location').html(html);
                });
            }
            $('#pickup_location').html(html);

        }
    });
}

// 3.Category-Module
$(document).on('click', '#list_view', function () {
    $('#list_view_html').show();
    $('#tree_view_html').hide();
});

$(document).on('click', '#tree_view', function () {
    $('#tree_view_html').show();
    $('#list_view_html').hide();

    var category_url = base_url + 'admin/category/get_categories';
    if (from == 'seller') {
        category_url = base_url + 'seller/category/get_seller_categories';
    }
    $.ajax({
        type: 'GET',
        url: category_url,
        dataType: 'json',
        success: function (result) {
            $('#tree_view_html').jstree({
                'core': {
                    'data': result['data']
                }
            });
        }
    });
});

function update_theme(update_id, status, table, user) {

    $.ajax({
        type: 'POST',
        url: base_url + user + '/themes/switch',
        data: {
            id: update_id,
            status: status,
            table: table
        },
        dataType: 'json',
        success: function (result) {
            if (result['error'] == false) {
                iziToast.success({
                    message: '<span style="text-transform:capitalize">' + result.message + '</span>',
                });
                $('.table').bootstrapTable('refresh');
            } else {
                iziToast.error({
                    message: '<span style="text-transform:capitalize">' + result.message + '</span>',
                });
            }
        }
    });
}

function update_status(update_id, status, table, user) {
    $.ajax({
        type: 'GET',
        url: base_url + user + '/home/update_status',
        data: {
            id: update_id,
            status: status,
            table: table
        },
        dataType: 'json',
        success: function (result) {
            if (result['error'] == true) {
                iziToast.success({
                    message: '<span style="text-transform:capitalize">' + result.message + '</span> Status Updated',
                });
                $('.table').bootstrapTable('refresh');
            } else {
                iziToast.error({
                    message: '<span style="text-transform:capitalize">' + result.message + '</span> Status Not Updated',
                });
            }
        }
    });
}

$(document).on('click', '.update_default_theme', function () {
    var theme_id = $(this).data('id');
    $.ajax({
        type: 'POST',
        url: base_url + 'admin/setting/set-default-theme',
        data: {
            [csrfName]: csrfHash,
            theme_id: theme_id
        },
        dataType: 'json',
        success: function (result) {
            csrfName = result.csrfName;
            csrfHash = result.csrfHash;
            if (result['error'] == false) {
                iziToast.success({
                    message: result.message,
                });
                $('.table').bootstrapTable('refresh');
            } else {
                iziToast.error({
                    message: result.message,
                });
            }
        }
    });
});

$(document).on('submit', '#bulk_area_update_form', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrfName, csrfHash);
    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            csrfName = result['csrfName'];
            csrfHash = result['csrfHash'];
            if (result.error == false) {
                iziToast.success({
                    message: result.message,
                });
                $('#bulk_area_update_form')[0].reset();
            } else {
                iziToast.error({
                    message: result.message,
                });
            }
        }
    });
});





$(document).on('click', '.delete-category', function () {
    var cat_id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/category/delete_category',
                    data: {
                        id: cat_id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        }

                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                        csrfName = response['csrfName'];
                        csrfHash = response['csrfHash'];
                    });
            });
        },
        allowOutsideClick: false
    });
});



$(document).on('click', '.delete-blog-category', function () {
    var cat_id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/blogs/delete_category',
                    data: {
                        id: cat_id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        }

                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                        csrfName = response['csrfName'];
                        csrfHash = response['csrfHash'];
                    });
            });
        },
        allowOutsideClick: false
    });
});

$(document).on('click', '.delete-blog', function () {
    var cat_id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/blogs/delete_blog',
                    data: {
                        id: cat_id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        }

                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                        csrfName = response['csrfName'];
                        csrfHash = response['csrfHash'];
                    });
            });
        },
        allowOutsideClick: false
    });
});

$('#category_parent').each(function () {
    $(this).select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
        dropdownCssClass: "test",
        templateResult: function (data) {
            // We only really care if there is an element to pull classes from
            if (!data.element) {
                return data.text;
            }

            var $element = $(data.element);

            var $wrapper = $('<span></span>');
            $wrapper.addClass($element[0].className);

            $wrapper.text(data.text);

            return $wrapper;
        }
    });
});

$(document).on('click', '#save_category_order', function () {
    var data = $('#sortable').sortable('serialize');
    $.ajax({
        data: data,
        type: 'GET',
        url: base_url + 'admin/category/update_category_order',
        dataType: 'json',
        success: function (response) {
            if (response.error == false) {
                iziToast.success({
                    message: response.message,
                });
            } else {
                iziToast.error({
                    message: response.message,
                });
            }
        }
    });
});

$(document).on('click', '#save_category_order', function () {
    var data = $('#sortable').sortableJS('serialize');
    $.ajax({
        data: data,
        type: 'GET',
        url: base_url + 'admin/category/update_category_order',
        dataType: 'json',
        success: function (response) {
            if (response.error == false) {
                iziToast.success({
                    message: response.message,
                });
            } else {
                iziToast.error({
                    message: response.message,
                });
            }
        }
    });
});



//4.Order-Module
$('#datepicker').attr({
    'placeholder': ' Select Date Range To Filter ',
    'autocomplete': 'off'
});
$('#datepicker').on('cancel.daterangepicker', function (ev, picker) {
    $(this).val('');
    $('#start_date').val('');
    $('#end_date').val('');
});
$('#datepicker').on('apply.daterangepicker', function (ev, picker) {
    var drp = $('#datepicker').data('daterangepicker');
    $('#start_date').val(drp.startDate.format('YYYY-MM-DD'));
    $('#end_date').val(drp.endDate.format('YYYY-MM-DD'));
    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
});

$('#datepicker').daterangepicker({
    showDropdowns: true,
    alwaysShowCalendars: true,
    autoUpdateInput: false,
    ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment().subtract(29, 'days'),
    endDate: moment(),
    locale: {
        "format": "DD/MM/YYYY",
        "separator": " - ",
        "cancelLabel": 'Clear',
        'label': 'Select range of dates to filter'
    }
});

$(document).on('click', '.update_mail_status_admin', function (e) {
    var order_id = $(this).data('id');
    var status = $(this).closest('.row').find('select').val();

    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: base_url + from + '/orders/update_order_mail_status',
                    data: {
                        'order_item_id': order_id,
                        status: status,
                        [csrfName]: csrfHash
                    },
                    dataType: 'json',
                    success: function (result) {

                        csrfName = result['csrfName'];
                        csrfHash = result['csrfHash'];
                        if (result['error'] == false) {
                            iziToast.success({
                                message: result['message'],
                            });

                        } else {
                            iziToast.error({
                                message: result['message'],
                            });
                        }
                        swal.close();
                        setTimeout(function () { location.reload(); }, 1000);

                    }
                });
            });
        },
        allowOutsideClick: false
    });
});
$(document).on('click', '.update_status_admin_bulk', function (e) {
    var order_item_id = [];
    if ($('input[name="seller_id"]:checked').val() != undefined) {
        var seller_id = $('input[name="seller_id"]:checked').val();
    } else {
        var seller_id = $(this).data("seller_id");
    }
    var order_id = $('input[name="order_id"]').val();
    var status = $('.status').val();
    var deliver_by = $('#deliver_by').val();
    var order_item_ids = $('input[name="order_item_id"]:checked').serializeArray();
    $.each(order_item_ids, function (i, field) {
        order_item_id.push(field.value);
    });

    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: base_url + from + '/orders/update_order_status',
                    data: {
                        seller_id: seller_id,
                        order_id: order_id,
                        status: status,
                        deliver_by: deliver_by,
                        order_item_id: order_item_id,
                        [csrfName]: csrfHash
                    },

                    dataType: 'json',
                    success: function (result) {
                        csrfName = result['csrfName'];
                        csrfHash = result['csrfHash'];
                        if (result['error'] == false) {
                            iziToast.success({
                                message: result['message'],
                            });
                        } else {
                            iziToast.error({
                                message: result['message'],
                            });
                        }
                        swal.close();
                        setTimeout(function () { location.reload(); }, 1000);
                    }
                });
            });
        },
        allowOutsideClick: false
    });
});

$('input[type=radio][name=seller_id]').change(function () {
    $("input[type=checkbox]").attr('disabled', true);
    var seller_id = $('input[type=radio][name="seller_id"]:checked').val();
    $("input[type=checkbox][id='" + seller_id + "']").removeAttr('disabled');
});

$(document).on('change', '.consignment_status', function (e) {
    let status = $(this).val();

    let delivery_boy_otp_system = $('#delivery_boy_otp_system').val();

    if (status == "delivered" && (delivery_boy_otp_system == 1 || delivery_boy_otp_system == '1')) {
        return $('.otp-field').removeClass('d-none');
    }
    $('.otp-field').addClass('d-none');
});
$(document).on('click', '.update_status_delivery_boy', function (e) {
    let consignment_id = $(this).data('id');
    let otp_system = $(this).data('otp-system');

    let status = $('.consignment_status').val();
    let post_otp = $('#otp').val();

    if (status == "" || status == undefined) {
        return iziToast.error({
            message: "Please Fill Status",
        });
    }
    if (otp_system == 1 && status == 'delivered' && post_otp == "" && post_otp == undefined) {
        return iziToast.error({
            message: "Please Enter Otp",
        });
    }
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'delivery_boy/orders/update_order_status',
                    data: {
                        id: consignment_id,
                        status: status,
                        otp: post_otp
                    },
                    dataType: 'json',
                    success: function (result) {
                        csrfName = result['csrfName'];
                        csrfHash = result['csrfHash'];
                        if (result['error'] == false) {
                            iziToast.success({
                                message: result['message'],
                            });
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            iziToast.error({
                                message: result['message'],
                            });
                        }
                        swal.close();
                    }
                });
            });
        },
        allowOutsideClick: false
    });

});
$(document).on('click', '.update_return_status_delivery_boy', function (e) {
    let order_item_id = $(this).data('id');

    let status = $('.order_item_status').val();

    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: base_url + 'delivery_boy/orders/update_return_order_item_status',
                    data: {
                        order_item_id: order_item_id,
                        status: status,
                        csrfName: csrfHash
                    },
                    dataType: 'json',
                    success: function (result) {

                        csrfName = result['csrfName'];
                        csrfHash = result['csrfHash'];
                        if (result['error'] == false) {
                            iziToast.success({
                                message: result['message'],
                            });
                            setTimeout(function () {
                                location.reload();
                            }, 1000);
                        } else {
                            iziToast.error({
                                message: result['message'],
                            });
                        }
                        swal.close();
                    }
                });
            });
        },
        allowOutsideClick: false
    });

});

$(document).on('click', '.delete-orders', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/orders/delete_orders',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result['error'] == false) {
                            Swal.fire('Deleted!', 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', result['message'], 'error');
                        }
                    }
                });
            });
        },
        allowOutsideClick: false
    })
        .then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled!',
                    'Your data is  safe.',
                    'error'
                );
            }
        });

});
$(document).on('click', '.delete-order-items', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/orders/delete_order_items',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result['error'] == false) {
                            Swal.fire('Deleted!', 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', result['message'], 'error');
                        }
                    }
                });
            });
        },
        allowOutsideClick: false
    })
        .then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled!',
                    'Your data is  safe.',
                    'error'
                );
            }
        });

});

$(document).on('click', '.remove-sellers', function () {
    var id = $(this).data('id');
    var status = $(this).data('seller_status');
    Swal.fire({
        title: 'Are You Sure! You want to remove this Seller',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Remove it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/sellers/remove_sellers',
                    data: {
                        id: id,
                        status: status
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result['error'] == false) {
                            Swal.fire('Removed!', 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', result['message'], 'error');
                        }
                    }
                });
            });
        },
        allowOutsideClick: false
    })
        .then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled!',
                    'Your data is  safe.',
                    'error'
                );
            }
        });
});
$(document).on('click', '.delete-sellers', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure! All data & media will be remove related to this seller',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Remove it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/sellers/delete_sellers',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result['error'] == false) {
                            Swal.fire('Deleted!', result['message']);
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', result['message'], 'error');
                        }
                    }
                });
            });
        },
        allowOutsideClick: false
    })
        .then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled!',
                    'Your data is  safe.',
                    'error'
                );
            }
        });
});

//5.Featured_Section-Module
$('.select_multiple').each(function () {
    $(this).select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    });
});


$('.search_admin_product').each(function () {
    $(this).select2({
        ajax: {
            url: base_url + 'admin/product/get_product_data',
            dataType: 'json',
            delay: 250,
            data: function (data) {
                return {
                    search: data.term, // search term
                    limit: 10
                };
            },
            processResults: function (response) {

                return {
                    results: response.rows
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection,
        placeholder: 'Search for products',
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    });
});

$('.search_admin_faq_product').each(function () {
    $(this).select2({
        ajax: {
            url: base_url + 'admin/product/get_product_faq_data',
            dataType: 'json',
            delay: 250,
            data: function (data) {
                return {
                    search: data.term, // search term
                    limit: 10
                };
            },
            processResults: function (response) {

                return {
                    results: response.rows
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection,
        placeholder: 'Search for products',
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    });
});
$('.search_admin_digital_product').each(function () {
    $(this).select2({
        ajax: {
            url: base_url + 'admin/product/get_digital_product_data',
            dataType: 'json',
            delay: 250,
            data: function (data) {
                return {
                    search: data.term, // search term
                    limit: 10
                };
            },
            processResults: function (response) {
                return {
                    results: response.rows
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        minimumInputLength: 1,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection,
        placeholder: 'Search for products',
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    });
});

$(document).on('click', '#delete-featured-section', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/featured_sections/delete_featured_section',
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result['error'] == false) {
                            Swal.fire('Deleted!', result['message'], 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', result['message'], 'warning');
                        }
                    }
                });
            });
        },
        allowOutsideClick: false
    })
        .then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled!',
                    'Your data is  safe.',
                    'error'
                );
            }
        });
});

//6.Notifation-Module
$("#image_checkbox").on('click', function () {
    if (this.checked) {
        $(this).prop("checked", true);
        $('.include_image').removeClass('d-none');
    } else {
        $(this).prop("checked", false);
        $('.include_image').addClass('d-none');
    }
});

$(document).on('click', '.delete_notifications', function () {
    var value = $(this).data('id');
    var url = base_url + 'admin/Notification_settings/delete_notification';
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        'id': value
                    },
                    dataType: 'json',
                })
                    .done(function (result, textStatus) {
                        if (result['error'] == false) {
                            Swal.fire('Deleted!', result['message'], 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', result['message'], 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

//7.Faq-Module
$(document).on('click', '.edit_faq', function () {
    var faq = $(this).parent().closest('.card');
    if ($(this).hasClass('cancel')) {
        $(this).removeClass('cancel');
        $(this).html('<i class="fa fa-pen"></i>');
        $(this).closest('button').addClass('btn-primary').removeClass('btn-danger');
        $(faq).find('input').addClass('d-none');
        $(faq).find('textarea').addClass('d-none');
        $(faq).find('.faq_question').show();
        $(faq).find('.faq_answer').show();
        $(faq).find('.save').addClass('d-none');
        $(faq).find('.collapse').collapse("hide");
    } else {
        $(this).addClass('cancel');
        $(this).html('<i class="fa fa-times"></i>');
        $(this).closest('button').addClass('btn-danger').removeClass('btn-primary');
        var question = $(faq).find('.faq_question').html();
        var answer = $(faq).find('.faq_answer').html();
        $(faq).find('.faq_question').hide();
        $(faq).find('.faq_answer').hide();
        $(faq).find('.collapse').collapse("show");
        $(faq).find('input').removeClass('d-none').val($.trim(question));
        $(faq).find('.save').removeClass('d-none');
        $(faq).find('textarea').removeClass('d-none').val($.trim(answer));
    }
});

$(document).on('click', '.delete_faq', function () {
    var id = $(this).data('id');
    var t = this;
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/faq/delete_faq',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (result, textStatus) {
                        if (result['error'] == false) {
                            $(t).closest('.card').remove();
                            Swal.fire('Deleted!', result['message'], 'success');
                        } else {
                            Swal.fire('Oops...', result['message'], 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

//8.Slider-Module
$(document).on('click', '#delete-slider', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/Slider/delete_slider',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

$(document).on('change', '.product_type', function (e, data) {
    e.preventDefault();
    var sort_type_val = $(this).val();
    if (sort_type_val == 'custom_products' && sort_type_val != ' ') {
        $('.custom_products').removeClass('d-none');
    } else {
        $('.custom_products').addClass('d-none');
    }
    if (sort_type_val == 'digital_product' && sort_type_val != ' ') {
        $('.digital_products').removeClass('d-none');
    } else {
        $('.digital_products').addClass('d-none');
    }
});

//9.Offer-Module
$(document).on('click', '#delete-offer', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/Offer/delete_offer',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

//10.Promo_code-Module
$(document).on('change', '#repeat_usage', function () {
    var repeat_usage = $(this).val();

    if (typeof repeat_usage != 'undefined' && repeat_usage == '1') {
        $('#repeat_usage_html').removeClass('d-none');
    } else {
        $('#repeat_usage_html').addClass('d-none');
    }
});

$(document).on('change', '#discount_type_select', function () {
    var discount_type = $(this).val();

    if (typeof discount_type != 'percentage' && discount_type == 'percentage') {
        $('#max_discount_amount_html').removeClass('d-none');
    } else {
        $('#max_discount_amount_html').addClass('d-none');
    }

});

$(document).on('click', '#delete-promo-code', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/Promo_code/delete_promo_code',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        Swal.fire('Deleted!', response.message, 'success');
                        $('.table-striped').bootstrapTable('refresh');
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});
$(document).on('click', '#delete-return-reason', function () {
    var id = $(this).data('id');

    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/return_reasons/delete_return_reason',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        Swal.fire('Deleted!', response.message, 'success');
                        $('.table-striped').bootstrapTable('refresh');
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

//11.Delivery_boys-Module
$(document).on('click', '#delete-delivery-boys', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/Delivery_boys/delete_delivery_boys',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

//12.Settings-Module
$(document).on('click', '#delete-time-slot', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/Time_slots/delete_time_slots',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        Swal.fire('Deleted!', response.message);
                        $('.table-striped').bootstrapTable('refresh');
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

//13.City-Module
$(document).on('click', '#delete-location', function () {

    var id = $(this).data('id');
    var table = $(this).data('table');
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/Area/delete_city',
                    type: 'GET',
                    data: {
                        'id': id,
                        'table': table
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

//14.Transaction_Module
$(document).on('change', '#transaction_type', function () {
    $('.table-striped').bootstrapTable('refresh');
});

//15.Customer-Wallet-Module
$('#customers').on('check.bs.table', function (e, row) {
    $('#customer_dtls').val(row.name + " | " + row.email);
    $('#user_id').val(row.id);
});

//16.Fund-Transder-Module
$("#delivery_boy_data").on("click-cell.bs.table", function (field, value, row, $el) {
    $('#name').val($el.name);
    $('#mobile').val($el.mobile);
    $('#balance').val($el.balance);
    $('#delivery_boy_id').val($el.id);
});

//17.Return-Request-Module
$("#return_request_table").on("click-cell.bs.table", function (field, value, row, $el) {

    $('input[name="return_request_id"]').val($el.id);
    $('input[name="user_id"]').val($el.user_id);
    $('input[name="order_item_id"]').val($el.order_item_id);
    $('#user_id').val($el.user_id);
    $('#order_item_id').val($el.order_item_id);
    $('#seller_id').val($el.seller_id);
    $('#update_remarks').html($el.remarks);


    // Set the selected delivery boy based on $el.delivery_boy_id
    $('#delivery_boy_id').val($el.delivery_boy_id);

    if ($el.status_digit == 0) {
        $('.pending').prop('checked', true);
        $('#return_request_delivery_by').addClass('d-none');
    } else if ($el.status_digit == 1) {
        $('.approved').prop('checked', true);
        $('#return_request_delivery_by').removeClass('d-none');
    } else if ($el.status_digit == 2) {
        $('.rejected').prop('checked', true);
        $('#return_request_delivery_by').addClass('d-none');
    } else if ($el.status_digit == 3) {
        $('.returned').prop('checked', true);
        $('#return_request_delivery_by').addClass('d-none');
    } else if ($el.status_digit == 8) {
        $('.return_pickedup').prop('checked', true);
        $('#return_request_delivery_by').addClass('d-none');
    }
});

//18.Tax-Module
$(document).on('click', '#delete-tax', function () {
    var tax_id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/taxes/delete_tax',
                    data: {
                        id: tax_id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                        } else {
                            Swal.fire('Opps', response.message, 'warning');
                        }
                        $('table').bootstrapTable('refresh');
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

//19.Payment-Request-Module
$("#payment_request_table").on("click-cell.bs.table", function (field, value, row, $el) {
    console.log($el);

    $('input[name="payment_request_id"]').val($el.id);
    $('input[name="payment_type"]').val($el.payment_type);
    $('#update_remarks').html($el.remarks);


    if ($el.status_digit == 0) {
        $('.pending').prop('checked', true);
    } else if ($el.status_digit == 1) {
        $('.approved').prop('checked', true);
    } else if ($el.status_digit == 2) {
        $('.rejected').prop('checked', true);
    }
});

$('#upload-media').on('click', function () {
    $('.image-upload-section').removeClass('d-none');
    var $result = $('#media-upload-table').bootstrapTable('getSelections');

    var path = base_url + $result[0].sub_directory + $result[0].name;
    var sub_directory = $result[0].sub_directory + $result[0].name;
    var media_type = $('#media-upload-modal').find('input[name="media_type"]').val();
    var input = $('#media-upload-modal').find('input[name="current_input"]').val();
    var is_removable = $('#media-upload-modal').find('input[name="remove_state"]').val();
    var ismultipleAllowed = $('#media-upload-modal').find('input[name="multiple_images_allowed_state"]').val();
    var removable_btn = (is_removable == '1') ? '<button class="remove-image btn btn-danger btn-xs mt-3">Remove</button>' : '';

    $(current_selected_image).closest('.form-group').find('.image').removeClass('d-none');
    if (ismultipleAllowed == '1') {
        for (let index = 0; index < $result.length; index++) {
            $(current_selected_image).closest('.form-group').find('.image-upload-section').append('<div class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image"><div class="image-upload-div"><img class="img-fluid" alt="' + $result[index].name + '" title="' + $result[index].name + '" src=' + base_url + $result[index].sub_directory + $result[index].name + ' ><input type="hidden" name=' + input + ' value=' + $result[index].sub_directory + $result[index].name + '></div>' + removable_btn + '</div>');
        }
    } else {
        path = (media_type != 'image') ? base_url + 'assets/admin/images/' + media_type + '-file.png' : path;
        $(current_selected_image).closest('.form-group').find('.image-upload-section').html('<div class="col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image"><div class="image-upload-div"><img class="img-fluid" alt="' + $result[0].name + '" title="' + $result[0].name + '" src=' + path + ' ><input type="hidden" name=' + input + ' value=' + sub_directory + '></div>' + removable_btn + '</div>');
    }

    current_selected_image = '';
    $('#media-upload-modal').modal('hide');
});

$(document).on('show.bs.modal', '#media-upload-modal', function (event) {
    var triggerElement = $(event.relatedTarget);
    current_selected_image = triggerElement;

    var input = $(current_selected_image).data('input');
    var isremovable = $(current_selected_image).data('isremovable');
    var ismultipleAllowed = $(current_selected_image).data('is-multiple-uploads-allowed');
    var media_type = ($(current_selected_image).is('[data-media_type]')) ? $(current_selected_image).data('media_type') : 'image';
    $('#media_type').val(media_type);
    if (ismultipleAllowed == 1) {
        $('#media-upload-table').bootstrapTable('refreshOptions', {
            singleSelect: false,
        });
    } else {
        $('#media-upload-table').bootstrapTable('refreshOptions', {
            singleSelect: true,
        });
    }

    $(this).find('input[name="current_input"]').val(input);
    $(this).find('input[name="remove_state"]').val(isremovable);
    $(this).find('input[name="multiple_images_allowed_state"]').val(ismultipleAllowed);
});

$(document).on('change', '#video_type', function () {
    var video_type = $(this).val();
    if (video_type == 'youtube' || video_type == 'vimeo') {
        $("#video_link_container").removeClass('d-none');
        $("#video_media_container").addClass('d-none');
    } else if (video_type == 'self_hosted') {
        $("#video_link_container").addClass('d-none');
        $("#video_media_container").removeClass('d-none');
    } else {
        $("#video_link_container").addClass('d-none');
        $("#video_media_container").addClass('d-none');
    }
});
$(document).on('change', '#download_link_type', function () {
    var download_link_type = $(this).val();
    if (download_link_type == 'add_link') {
        $("#digital_link_container").removeClass('d-none');
        $("#digital_media_container").addClass('d-none');
    } else if (download_link_type == 'self_hosted') {
        $("#digital_link_container").addClass('d-none');
        $("#digital_media_container").removeClass('d-none');
    } else {
        $("#digital_media_container").addClass('d-none');
        $("#digital_link_container").addClass('d-none');
    }
});
if ($('#tags').length) {
    var tags_element = document.querySelector('input[name=tags]');
    new Tagify(tags_element);
}
if ($('#seo_meta_keywords').length) {
    var tags_element = document.querySelector('input[name=seo_meta_keywords]');
    new Tagify(tags_element);
}


$(document).on('show.bs.modal', '#customer-address-modal', function (event) {
    var triggerElement = $(event.relatedTarget);
    current_selected_image = triggerElement;
    var id = $(current_selected_image).data('id');
    var existing_url = $(this).find('#customer-address-table').data('url');

    if (existing_url.indexOf('?') > -1) {
        var temp = $(existing_url).text().split('?');
        var new_url = temp[0] + '?user_id=' + id;
    } else {
        var new_url = existing_url + '?user_id=' + id;
    }
    $('#customer-address-table').bootstrapTable('refreshOptions', {
        url: new_url,
    });
});
$(document).on('show.bs.modal', '#product-rating-modal', function (event) {
    var triggerElement = $(event.relatedTarget);
    current_selected_image = triggerElement;
    var id = $(current_selected_image).data('id');

    var existing_url = $(this).find('#product-rating-table').data('url');
    if (existing_url.indexOf('?') > -1) {
        var temp = $(existing_url).text().split('?');
        var new_url = temp[0] + '?product_id=' + id;
    } else {
        var new_url = existing_url + '?product_id=' + id;
    }
    $('#product-rating-table').bootstrapTable('refreshOptions', {
        url: new_url,
    });
});

$(document).on('click', '.remove-image', function (e) {
    e.preventDefault();
    $(this).closest('.image').remove();
});

$(document).on('change', '#media-type', function () {
    $('table').bootstrapTable('refresh');
});

Dropzone.autoDiscover = false;

if (document.getElementById('dropzone')) {

    var myDropzone = new Dropzone("#dropzone", {
        url: base_url + from + '/media/upload',
        paramName: "documents",
        autoProcessQueue: false,
        parallelUploads: 12,
        maxFiles: 12,
        autoDiscover: false,
        addRemoveLinks: true,
        timeout: 180000,
        dictRemoveFile: 'x',
        dictMaxFilesExceeded: 'Only 12 files can be uploaded at a time ',
        dictResponseError: 'Error',
        uploadMultiple: true,
        dictDefaultMessage: '<p><input type="submit" value="Select Files" class="btn btn-success" /><br> or <br> Drag & Drop Media Files Here</p>',
    });

    myDropzone.on("addedfile", function (file) {
        var i = 0;
        if (this.files.length) {
            var _i, _len;
            for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) {
                if (this.files[_i].name === file.name && this.files[_i].size === file.size && this.files[_i].lastModifiedDate.toString() === file.lastModifiedDate.toString()) {
                    this.removeFile(file);
                    i++;
                }
            }
        }
    });

    myDropzone.on("error", function (file, response) { });


    myDropzone.on('sending', function (file, xhr, formData) {
        formData.append(csrfName, csrfHash);
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.response);
                csrfName = response.csrfName;
                csrfHash = response.csrfHash;
                if (response['error'] == false) {
                    Dropzone.forElement('#dropzone').removeAllFiles(true);
                    $("#media-upload-table").bootstrapTable('refresh');
                    iziToast.success({
                        message: response['message'],
                    });
                    $('#media-table').bootstrapTable('refresh');
                } else {
                    iziToast.error({
                        title: 'Error',
                        message: response['message'],
                    });
                }
                $(file.previewElement).find('.dz-error-message').text(response.message);
            }
        };
    });
}
if (document.getElementById('system-update-dropzone')) {

    var systemDropzone = new Dropzone("#system-update-dropzone", {
        url: base_url + 'admin/updater/upload_update_file',
        paramName: "update_file",
        autoProcessQueue: false,
        parallelUploads: 1,
        maxFiles: 1,
        timeout: 360000,
        autoDiscover: false,
        addRemoveLinks: true,
        dictRemoveFile: 'x',
        dictMaxFilesExceeded: 'Only 1 file can be uploaded at a time ',
        dictResponseError: 'Error',
        uploadMultiple: true,
        dictDefaultMessage: '<p><input type="submit" value="Select Files" class="btn btn-success" /><br> or <br> Drag & Drop System Update / Installable / Plugin\'s .zip file Here</p>',
    });

    systemDropzone.on("addedfile", function (file) {
        var i = 0;
        if (this.files.length) {
            var _i, _len;
            for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) {
                if (this.files[_i].name === file.name && this.files[_i].size === file.size && this.files[_i].lastModifiedDate.toString() === file.lastModifiedDate.toString()) {
                    this.removeFile(file);
                    i++;
                }
            }
        }
    });

    systemDropzone.on("error", function (file, response) { });


    systemDropzone.on('sending', function (file, xhr, formData) {
        formData.append(csrfName, csrfHash);
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.response);
                csrfName = response.csrfName;
                csrfHash = response.csrfHash;
                if (response['error'] == false) {
                    iziToast.success({
                        message: response['message'],
                    });
                } else {
                    iziToast.error({
                        title: 'Error',
                        message: response['message'],
                    });
                }
                $(file.previewElement).find('.dz-error-message').text(response.message);
            }
        };
    });
    $('#system_update_btn').on('click', function (e) {
        e.preventDefault();
        systemDropzone.processQueue();
    });
}

$('#upload-files-btn').on('click', function (e) {
    e.preventDefault();
    myDropzone.processQueue();
});




$(document).on('click', '.copy-to-clipboard', function () {

    var $element = $(this).closest('tr').find('.path');
    copyToClipboard($element);
    iziToast.success({
        message: 'Image path copied to clipboard',
    });
});
$(document).on('click', '.copy-relative-path', function () {
    var $element = $(this).closest('tr').find('.relative-path');
    copyToClipboard($element);
    iziToast.success({
        message: 'Image path copied to clipboard',
    });
});


$(document).on('click', 'button[type="reset"]', function () {
    $('.image-upload-div').remove();
    $('.image-upload-section').find('.image').addClass('d-none');
});

//20.Client Api Key Module
$(document).on('click', '#delete-client', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/client_api_keys/delete_client',
                    data: {
                        id: id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                        } else {
                            Swal.fire('Opps', response.message, 'warning');
                        }
                        $('table').bootstrapTable('refresh');
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

//21.System Users
$(document).on('click', '#delete-system-users', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/system_users/delete_system_user',
                    data: {
                        id: id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                        } else {
                            Swal.fire('Opps', response.message, 'warning');
                        }
                        $('table').bootstrapTable('refresh');
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

$(document).on('change', '.system-user-role', function () {
    var role = $(this).val();
    if (role > 0) {
        $('.permission-table').removeClass('d-none');
    } else {
        $('.permission-table').addClass('d-none');
    }
});

$(document).on('click', '.remove_individual_variants', function () {
    var variant_id = $(this).closest('.variant_col').find('input[type="hidden"]').val();
    var all_variant_ids = $(this).closest('.row').find('input[name="variants_ids[]"]').val().split(',');
    all_variant_ids.splice(all_variant_ids.indexOf(variant_id), 1);
    if ($.isEmptyObject(all_variant_ids)) {
        $(this).closest('.row').remove();
    } else {
        $(this).closest('.row').find('input[name="variants_ids[]"]').val(all_variant_ids.toString());
        $(this).closest('.variant_col').remove();
    }
});

$(document).on('change', '#system_timezone', function () {
    var gmt = $(this).find(':selected').data('gmt');
    $('#system_timezone_gmt').val(gmt);
});

$('#city').on('change', function (e) {
    e.preventDefault();
    $.ajax({
        type: 'POST',
        data: {
            'city_id': $(this).val(),
            [csrfName]: csrfHash,
        },
        url: base_url + 'seller/Pickup_location/get_areas',
        dataType: 'json',
        success: function (result) {
            csrfName = result.csrfName;
            csrfHash = result.csrfHash;
            if (result.error == false) {
                var html = '';
                $.each(result.data, function (i, e) {
                    html += '<option value=' + e.id + '>' + e.name + '</option>';
                });
                $('#area').html(html);
            } else {
                Toast.fire({
                    icon: 'error',
                    title: result.message
                });
                $('#area').html('');
            }
        }
    })
});

$('#add-address-form').on('submit', function (e) {
    e.preventDefault();
    var formdata = new FormData(this);
    formdata.append(csrfName, csrfHash);
    $.ajax({
        type: 'POST',
        data: formdata,
        url: $(this).attr('action'),
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $('#save-address-submit-btn').val('Please Wait...').attr('disabled', true);
        },
        success: function (result) {
            csrfName = result.csrfName;
            csrfHash = result.csrfHash;
            if (result.error == false) {
                $('#save-address-result').html("<div class='alert alert-success'>" + result.message + "</div>").delay(1500).fadeOut();
                $('#add-address-form')[0].reset();
                $('#address_list_table').bootstrapTable('refresh');
            } else {
                $('#save-address-result').html("<div class='alert alert-danger'>" + result.message + "</div>").delay(1500).fadeOut();
            }
            $('#save-address-submit-btn').val('Save').attr('disabled', false);
        }
    })
})

$(document).on('click', '.delete-address', function (e) {
    e.preventDefault();
    if (confirm('Are you sure ? You want to delete this address?')) {
        $.ajax({
            type: 'POST',
            data: {
                'id': $(this).data('id'),
                [csrfName]: csrfHash,
            },
            url: base_url + 'my-account/delete-address',
            dataType: 'json',
            success: function (result) {
                csrfName = result.csrfName;
                csrfHash = result.csrfHash;
                if (result.error == false) {
                    $('#address_list_table').bootstrapTable('refresh');
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: result.message
                    });
                }
            }
        })
    }
});
$('#edit_city').on('change', function (e, data) {
    e.preventDefault();
    $.ajax({
        type: 'POST',
        data: {
            'city_id': $(this).val(),
            [csrfName]: csrfHash,
        },
        url: base_url + 'seller/Pickup_location/get_areas',
        dataType: 'json',
        success: function (result) {
            csrfName = result.csrfName;
            csrfHash = result.csrfHash;
            if (result.error == false) {
                var html = '';
                $.each(result.data, function (i, e) {
                    html += '<option value=' + e.id + '>' + e.name + '</option>';
                });
                $('#edit_area').html(html);
            } else {
                Toast.fire({
                    icon: 'error',
                    title: result.message
                });
                $('#edit_area').html('');
            }
        }
    })
});

$('#edit-address-form').on('submit', function (e) {
    e.preventDefault();
    var formdata = new FormData(this);
    formdata.append(csrfName, csrfHash);
    $.ajax({
        type: 'POST',
        data: formdata,
        url: $(this).attr('action'),
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $('#edit-address-submit-btn').val('Please Wait...').attr('disabled', true);
        },
        success: function (result) {
            csrfName = result.csrfName;
            csrfHash = result.csrfHash;
            if (result.error == false) {
                $('#edit-address-result').html("<div class='alert alert-success'>" + result.message + "</div>").delay(1500).fadeOut();
                $('#edit-address-form')[0].reset();
                $('#address_list_table').bootstrapTable('refresh');
                setTimeout(function () {
                    $('#address-modal').modal('hide');
                }, 2000)
            } else {
                $('#edit-address-result').html("<div class='alert alert-danger'>" + result.message + "</div>").delay(1500).fadeOut();
            }
            $('#edit-address-submit-btn').val('Save').attr('disabled', false);
        }
    })
})

$('#add-new-language-form').on('submit', function (e) {
    e.preventDefault();
    var formdata = new FormData(this);
    formdata.append(csrfName, csrfHash);
    $.ajax({
        type: 'POST',
        data: formdata,
        url: $(this).attr('action'),
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $('#submit_btn').val('Please Wait...').attr('disabled', true);
        },
        success: function (result) {
            csrfName = result.csrfName;
            csrfHash = result.csrfHash;
            if (result.error == false) {
                $('#result').show().removeClass('msg_error').addClass('msg_success').html(result.message).delay(1500).fadeOut();
                $('#add-new-language-form')[0].reset();
                setTimeout(function () {
                    $('#language-modal').modal('hide');
                    location.reload();
                }, 2000)
            } else {
                $('#result').show().removeClass('msg_success').addClass('msg_error').html(result.message).delay(1500).fadeOut();
            }
            $('#submit_btn').val('Save').attr('disabled', false);
        }
    })
})

// jQuery to handle the button click event
$(document).on('click', '.select-language', function () {
    var id = $(this).data('id');
    window.location.href = base_url + 'admin/language?id=' + id;
});

// jQuery to handle the "Delete" button click event
$(document).on('click', '.delete-language', function () {
    var id = $(this).data('id');

    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/language/delete_language',
                    type: 'POST',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });

});

$(document).ready(function () {
    // Event listener for the change event of the dropdown
    $('#is_default_for_web').on('change', function () {
        // Get the selected language ID
        var languageId = $(this).val();
        // Send AJAX request to update database
        $.ajax({
            url: base_url + 'admin/language/set_default_for_web',
            method: 'POST',
            data: {
                is_default: '1',
                language_id: languageId
            },
            success: function (response) {
                // Handle success response
                var response = JSON.parse(response);
                if (response.error == false) {
                    iziToast.success({
                        message: response.message,
                    });
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                } else {
                    iziToast.error({
                        message: response.message,
                    });
                }
            }
        });
    });
});


$('#update-language-form').on('submit', function (e) {
    e.preventDefault();
    var formdata = new FormData(this);
    formdata.append(csrfName, csrfHash);
    $.ajax({
        type: 'POST',
        data: formdata,
        url: $(this).attr('action'),
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $('#update_btn').val('Please Wait...').attr('disabled', true);
        },
        success: function (result) {
            csrfName = result.csrfName;
            csrfHash = result.csrfHash;
            if (result.error == false) {
                $('#update-result').show().removeClass('msg_error').addClass('msg_success').html(result.message).delay(1500).fadeOut();
            } else {
                $('#update-result').show().removeClass('msg_success').addClass('msg_error').html(result.message).delay(1500).fadeOut();
            }
            $('#update_btn').val('Save').attr('disabled', false);
        }
    })
})

function product_rating_query_params(p) {
    return {
        'product_id': $('#product_id').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

$('#area_wise_delivery_charge').on('change', function (event, state) {

    // if (state == false) {
    if (!$('#area_wise_delivery_charge').is(':checked')) {
        if ($(".delivery_charge").hasClass("d-none")) {
            $(".delivery_charge").removeClass("d-none")
        }
        if ($(".min_amount").hasClass("d-none")) {
            $(".min_amount").removeClass("d-none")
        }
        if ($(".area_wise_delivery_charge").hasClass("col-md-6")) {
            $(".area_wise_delivery_charge").removeClass("col-md-6")
            $(".area_wise_delivery_charge").addClass("col-md-4")
        }
    } else {
        if (!$(".delivery_charge").hasClass("d-none")) {
            $(".delivery_charge").addClass("d-none")
        }
        if (!$(".min_amount").hasClass("d-none")) {
            $(".min_amount").addClass("d-none")
        }
        if ($(".area_wise_delivery_charge").hasClass("col-md-4")) {
            $(".area_wise_delivery_charge").removeClass("col-md-4")
            $(".area_wise_delivery_charge").addClass("col-md-6")
        }


    }
});

$('#bulk_upload_form').on('submit', function (e) {
    e.preventDefault();
    var type = $('#type').val();
    if (type != '') {
        var formdata = new FormData(this);
        formdata.append(csrfName, csrfHash);
        $.ajax({
            type: 'POST',
            data: formdata,
            url: $(this).attr('action'),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#submit_btn').html('Please Wait...').attr('disabled', true);
            },
            success: function (result) {
                csrfName = result.csrfName;
                csrfHash = result.csrfHash;
                if (result.error == false) {
                    $('#upload_result').show().removeClass('msg_error').addClass('msg_success').html(result.message).delay(3000).fadeOut();
                } else {
                    $('#upload_result').show().removeClass('msg_success').addClass('msg_error').html(result.message).delay(3000).fadeOut();
                }
                $('#submit_btn').html('Submit').attr('disabled', false);
            }
        })
    } else {
        iziToast.error({
            message: 'Please select type',
        });
    }

});
$('#location_bulk_upload_form').on('submit', function (e) {
    e.preventDefault();
    var type = $('#type').val();
    var location_type = $('#location_type').val();
    if (type != '' && location_type != "" && type != "undefined" && location_type != "undefined") {
        var formdata = new FormData(this);
        formdata.append(csrfName, csrfHash);
        $.ajax({
            type: 'POST',
            data: formdata,
            url: $(this).attr('action'),
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#submit_btn').html('Please Wait...').attr('disabled', true);
            },
            success: function (result) {
                csrfName = result.csrfName;
                csrfHash = result.csrfHash;
                if (result.error == false) {
                    $('#upload_result').show().removeClass('msg_error').addClass('msg_success').html(result.message).delay(3000).fadeOut();
                } else {
                    $('#upload_result').show().removeClass('msg_success').addClass('msg_error').html(result.message).delay(3000).fadeOut();
                }
                $('#submit_btn').html('Submit').attr('disabled', false);
            }
        });
    } else {
        iziToast.error({
            message: 'Please select Type and Location Type',
        });
    }

});


// swatche js

$('#swatche_color').hide();
$('#swatche_image').hide();
$(document.body).on('change', '.swatche_type', function (e) {
    e.preventDefault();
    var swatche_type = $(this).val();
    if (swatche_type == "1") {
        $('#swatche_image').hide();
        $('#swatche_color').show();
        $('#swatche_image').val('');
    } else if (swatche_type == "2") {
        $('#swatche_color').hide();
        $('#swatche_image').show();
        $('#swatche_color').val('');
    } else {
        $('#swatche_color').hide();
        $('#swatche_image').hide();
        $('#swatche_color').val('');
        $('#swatche_image').val('');
    }
});
if ($('#google_pay_currency_code').length) {
    $('#google_pay_currency_code').on('change', function (e) {
        e.preventDefault();
        var country_code = $(this).find(':selected').data('countrycode');
        $('#google_pay_country_code').val(country_code);
    });
}

var ticket_id = "";
var scrolled = 0;
$(document).on('click', '.view_ticket', function (e, row) {
    e.preventDefault();
    scrolled = 0;
    $(".ticket_msg").data('max-loaded', false);
    ticket_id = $(this).data("id");
    var username = $(this).data("username");
    var date_created = $(this).data("date_created");
    var subject = $(this).data("subject");
    var status = $(this).data("status");
    var ticket_type = $(this).data("ticket_type");
    $('input[name="ticket_id"]').val(ticket_id);
    $('#user_name').html(username);
    $('#date_created').html(date_created);
    $('#subject').html(subject);
    $('.change_ticket_status').data('ticket_id', ticket_id);
    if (status == 1) {
        $('#status').html('<label class="badge badge-secondary ml-2">PENDING</label>');
        $('.ticket_status_footer').addClass('d-none');
    } else if (status == 2) {
        $('#status').html('<label class="badge badge-info ml-2">OPENED</label>');
        $('.ticket_status_footer').removeClass('d-none');
    } else if (status == 3) {
        $('#status').html('<label class="badge badge-success ml-2">RESOLVED</label>');
        $('.ticket_status_footer').removeClass('d-none');
    } else if (status == 4) {
        $('#status').html('<label class="badge badge-danger ml-2">CLOSED</label>');
        $('.ticket_status_footer').addClass('d-none');
    } else if (status == 5) {
        $('.ticket_status_footer').removeClass('d-none');
        $('#status').html('<label class="badge badge-warning ml-2">REOPENED</label>');
    }
    $('#ticket_type').html(ticket_type);
    $('.ticket_msg').html("");
    $('.ticket_msg').data('limit', 5);
    $('.ticket_msg').data('offset', 0);
    load_messages($('.ticket_msg'), ticket_id);
});

$(document).ready(function () {
    if ($("#element").length) {
        $("#element").scrollTop($("#element")[0].scrollHeight);
        $('#element').scroll(function () {
            if ($('#element').scrollTop() == 0) {
                load_messages($('.ticket_msg'), ticket_id);
            }
        });

        $('#element').bind('mousewheel', function (e) {
            if (e.originalEvent.wheelDelta / 120 > 0) {
                if ($(".ticket_msg")[0].scrollHeight < 370 && scrolled == 0) {
                    load_messages($('.ticket_msg'), ticket_id);
                    scrolled = 1;
                }
            }
        });
    }
});

$(document).ready(function () {
    const statusMap = {
        'bg-primary': 'received',
        'bg-info': 'processed',
        'bg-lightblue': 'shipped',
        'bg-success': 'delivered',
        'bg-danger': 'cancelled',
        'bg-secondary': 'returned'
    };

    $('.small-box').on('click', function (e) {
        e.stopPropagation();

        let boxClass = $(this).attr('class').split(' ').find(cls => statusMap[cls]);
        let status = statusMap[boxClass];

        if (status) {

            setTimeout(() => {
                $('#order-items-table').bootstrapTable('refreshOptions', {
                    url: base_url + 'seller/orders/view_order_items',
                    query: {
                        order_status: status
                    }
                });
            }, 100);

            let mainContent = document.querySelector('.main-content');
            if (mainContent) {
                mainContent.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            $('#order_status').val(status);
        } else {
            console.warn('No status mapped for class:', boxClass);
        }
    });
});

$('.table').on('load-success.bs.table', function (e, data) {
    if (data && data.total_order_sum) {
        $('#total-order-sum').text(data.total_order_sum);
    } else {
        $('#total-order-sum').text('0.00');
    }
});

$('#ticket_send_msg_form').on('submit', function (e) {
    e.preventDefault();
    var formdata = new FormData(this);
    formdata.append(csrfName, csrfHash);

    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: formdata,
        beforeSend: function () {
            $('#submit_btn').html('Sending..').attr('disabled', true);
        },
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (result) {

            csrfName = result.csrfName;
            csrfHash = result.csrfHash;
            $('#submit_btn').html('Send').attr('disabled', false);
            if (result.error == false) {
                if (result.data.id > 0) {
                    var message = result.data;
                    var is_left = (message.user_type == 'user') ? 'left' : 'right';
                    var message_html = "";
                    var atch_html = "";
                    var i = 1;
                    if (message.attachments.length > 0) {
                        message.attachments.forEach(atch => {
                            atch_html += "<div class='container-fluid image-upload-section'>" +
                                "<a class='btn btn-danger btn-xs mr-1 mb-1' href='" + atch.media + "'  target='_blank' alt='Attachment Not Found'>Attachment " + i + "</a>" +
                                "<div class='col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none'></div>" +
                                "</div>";
                            i++;
                        });
                    }
                    message_html += "<div class='direct-chat-msg " + is_left + "'>" +
                        "<div class='direct-chat-infos clearfix'>" +
                        "<span class='direct-chat-name float-" + is_left + "' id='name'>" + message.name + "</span>" +
                        "<span class='direct-chat-timestamp float-" + is_left + "' id='last_updated'>" + message.last_updated + "</span>" +
                        "</div>" +
                        "<div class='direct-chat-text' id='message'>" + message.message + "</br>" + atch_html + "</div>" +
                        "</div>";

                    $('.ticket_msg').append(message_html);
                    $("#message_input").val('');

                    $("#element").scrollTop($("#element")[0].scrollHeight);
                    $('input[name="attachments[]"]').val('');
                }
            } else {
                $("#element").data('max-loaded', true);
                iziToast.error({
                    message: '<span style="text-transform:capitalize">' + result.message + '</span> ',
                });
                return false;
            }
            iziToast.success({
                message: '<span style="text-transform:capitalize">' + result.message + '</span> ',
            });

        }
    });
});


$(document).on('click', '#delete-ticket', function () {

    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/tickets/delete_ticket',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});


$(document).on('change', '.change_ticket_status', function () {
    var status = $(this).val();
    if (status != '') {
        if (confirm("Are you sure you want to mark the ticket as " + $(".change_ticket_status option:selected").text() + "? ")) {
            var id = $(this).data('ticket_id');
            var dataString = {
                ticket_id: id,
                status: status,
                [csrfName]: csrfHash
            };
            $.ajax({
                type: 'post',
                url: base_url + 'admin/tickets/edit-ticket-status',
                data: dataString,
                dataType: 'json',
                success: function (result) {
                    csrfHash = result.csrfHash;
                    if (result.error == false) {
                        $('#ticket_table').bootstrapTable('refresh');
                        if (status == 1) {
                            $('#status').html('<label class="badge bg-secondary ml-2">PENDING</label>')
                            $('.ticket_status_footer').addClass('d-none');

                        } else if (status == 2) {
                            $('#status').html('<label class="badge bg-info ml-2">OPENED</label>')
                            $('.ticket_status_footer').removeClass('d-none');

                        } else if (status == 3) {
                            $('#status').html('<label class="badge bg-success ml-2">RESOLVED</label>')
                            $('.ticket_status_footer').removeClass('d-none');

                        } else if (status == 4) {
                            $('#status').html('<label class="badge bg-danger ml-2">CLOSED</label>')
                            $('.ticket_status_footer').addClass('d-none');

                        } else if (status == 5) {
                            $('#status').html('<label class="badge bg-warning ml-2">REOPENED</label>')
                            $('.ticket_status_footer').removeClass('d-none');

                        }
                        iziToast.success({
                            message: '<span style="text-transform:capitalize">' + result.message + '</span> ',
                        });

                    } else {
                        iziToast.error({
                            message: '<span>' + result.message + '</span> ',
                        });
                    }
                }
            });
        }
    }
});

$(document).on('click', '.delete-ticket-type', function () {
    var cat_id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/tickets/delete_ticket_type',
                    data: {
                        id: cat_id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        }

                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                        csrfName = response['csrfName'];
                        csrfHash = response['csrfHash'];
                    });
            });
        },
        allowOutsideClick: false
    });
});

function load_messages(element, ticket_id) {
    var limit = element.data('limit');
    var offset = element.data('offset');

    element.data('offset', limit + offset);
    var max_loaded = element.data('max-loaded');
    if (max_loaded == false) {
        var loader = '<div class="loader text-center"><img src="' + base_url + 'assets/pre-loader.gif" alt="Loading. please wait.. ." title="Loading. please wait.. ."></div>';
        $.ajax({
            type: 'get',
            data: 'ticket_id=' + ticket_id + '&limit=' + limit + '&offset=' + offset,
            url: base_url + 'admin/tickets/get_ticket_messages',
            beforeSend: function () {
                $('.ticket_msg').prepend(loader);
            },
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function (result) {
                if (result.error == false) {
                    if (result.error == false && result.data.length > 0) {
                        var messages_html = "";
                        var is_left = "";
                        var is_right = "";
                        var atch_html = "";
                        var i = 1;
                        result.data.reverse().forEach(messages => {
                            is_left = (messages.user_type == 'user' || messages.user_type == '') ? 'left' : 'right';
                            is_right = (messages.user_type == 'user' || messages.user_type == '') ? 'right' : 'left';
                            if (messages.attachments.length > 0) {
                                messages.attachments.forEach(atch => {
                                    atch_html += "<div class='container-fluid image-upload-section'>" +
                                        "<a class='btn btn-danger btn-xs mr-1 mb-1' href='" + atch.media + "'  target='_blank' alt='Attachment Not Found'>Attachment " + i + "</a>" +
                                        "<div class='col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none'></div>" +
                                        "</div>";
                                    i++;
                                });
                            }
                            messages_html += "<div class='direct-chat-msg " + is_left + "'>" +
                                "<div class='direct-chat-infos clearfix'>" +
                                "<span class='direct-chat-name float-" + is_left + "' id='name'>" + messages.name + "</span>" +
                                "<span class='direct-chat-timestamp float-" + is_left + "' id='last_updated'>" + messages.last_updated + "</span>" +
                                "</div>" +
                                "<div class='direct-chat-text' id='message'>" + messages.message + "</br>" + atch_html + "</div>" +
                                "</div>";
                        });
                        $('.ticket_msg').prepend(messages_html);
                        $('.ticket_msg').find('.loader').remove();
                        $(element).animate({
                            scrollTop: $(element).offset().top
                        });
                    }
                } else {
                    element.data('offset', offset);
                    element.data('max-loaded', true);
                    $('.ticket_msg').find('.loader').remove();
                    $('.ticket_msg').prepend('<div class="text-center"> <p>You have reached the top most message!</p></div>');
                }
                $('#element').scrollTop(20); // Scroll alittle way down, to allow user to scroll more
                $(element).animate({
                    scrollTop: $(element).offset().top
                });
                return false;
            }
        });

    }
}

$(document).on('click', '.edit_transaction', function (e, row) {
    e.preventDefault();
    var id = $(this).data("id");
    var txn_id = $(this).data("txn_id");
    var status = $(this).data("status");
    var message = $(this).data("message");

    $('#id').val(id);
    $('#txn_id').val(txn_id);
    $('#t_status').val(status);
    $('#message').val(message);

});
$(document).on('click', '.edit_order_tracking', function (e, rows) {
    e.preventDefault();
    var order_item_id = $(this).data("order_item_id");
    var order_id = $(this).data("order_id");
    if ($('input[type=radio][name="seller_id"]:checked').val() != undefined) {
        var seller_id = $('input[type=radio][name="seller_id"]:checked').val();
    } else {
        var seller_id = $(this).data("seller_id");
    }
    var order_item_id = $(this).data("order_item_id");
    var courier_agency = $(this).data("courier_agency");
    var tracking_id = $(this).data("tracking_id");
    var url = $(this).data("url");
    $('#order_item_id').val(order_item_id);
    $('input[name="order_id"]').val(order_id);
    $('input[name="order_item_id"]').val(order_item_id);
    $('input[type=hidden][name="seller_id"]').val(seller_id);
    $('#order_id').val(order_id);
    $('#order_item_id').val(order_item_id);
    $('#courier_agency').val(courier_agency);
    $('#tracking_id').val(tracking_id);
    $('#url').val(url);
    $('#order_tracking_table').bootstrapTable('refresh');
});


$(document).on('click', '.edit_digital_order_mails', function (e, rows) {
    e.preventDefault();
    var order_item_id = $(this).data("order_item_id");
    var order_id = $(this).data("order_id");
    $('input[name="order_item_id"]').val(order_item_id);
    $('input[name="order_id"]').val(order_id);
    $('#digital_order_mail_table').bootstrapTable('refresh');

});

$('#edit_transaction_form').on('submit', function (e) {
    e.preventDefault();
    var formdata = new FormData(this);
    formdata.append(csrfName, csrfHash);

    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: formdata,
        beforeSend: function () {
            $('#submit_btn').html('Please Wait..').attr('disabled', true);
        },
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (result) {
            csrfHash = result.csrfHash;
            $('#submit_btn').html('Update Transaction').attr('disabled', false);
            if (result.error == false) {
                $('table').bootstrapTable('refresh');
                setTimeout(function () {
                    window.location.reload();
                }, 3000);
                iziToast.success({
                    message: '<span style="text-transform:capitalize">' + result.message + '</span> ',
                });
            } else {
                iziToast.error({
                    message: '<span>' + result.message + '</span> ',
                });
            }
        }
    });
});

$('#order_tracking_form').on('submit', function (e) {
    e.preventDefault();
    var formdata = new FormData(this);
    formdata.append(csrfName, csrfHash);

    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: formdata,
        beforeSend: function () {
            $('#submit_btn').html('Please Wait..').attr('disabled', true);
        },
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (result) {
            csrfHash = result.csrfHash;
            $('#submit_btn').html('Update Transaction').attr('disabled', false);
            if (result.error == false) {
                setTimeout(function () {
                    $('.order_tracking_form').modal('hide');
                    window.location.reload();

                }, 3000);
                $('table').bootstrapTable('refresh');
                iziToast.success({
                    message: '<span style="text-transform:capitalize">' + result.message + '</span> ',
                });
            } else {
                iziToast.error({
                    message: '<span>' + result.message + '</span> ',
                });
            }
        }
    });
});

$('.delete-receipt').on('click', function () {
    var cat_id = $(this).data('id');

    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + from + '/orders/delete_receipt',
                    data: {
                        id: cat_id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                            window.location.reload();
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        }

                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                        csrfName = response['csrfName'];
                        csrfHash = response['csrfHash'];
                    });
            });
        },
        allowOutsideClick: false
    });
});

var preventClick = false;
$('.delete-receipt').click(function (e) {
    $(this)
        .css('cursor', 'default')
        .css('text-decoration', 'none')

    if (!preventClick) {
        $(this).html($(this).html() + '');
    }

    preventClick = true;

    return false;
});

$('#update_receipt_status').on('change', function (e) {
    e.preventDefault();
    var order_id = $(this).data('id');
    var user_id = $(this).data('user_id');
    var status = $(this).val();
    $.ajax({
        type: 'POST',
        data: {
            'order_id': order_id,
            'status': status,
            'user_id': user_id,
            [csrfName]: csrfHash,
        },
        url: base_url + from + '/orders/update_receipt_status',
        dataType: 'json',
        success: function (result) {
            csrfName = result.csrfName;
            csrfHash = result.csrfHash;
            if (result['error'] == false) {
                setTimeout(function () {
                    window.location.reload();

                }, 3000);
                iziToast.success({
                    message: result['message'],
                });
            } else {
                iziToast.error({
                    message: result['message'],
                });
            }
        }
    });
});

//13.City-Module
$(document).on('click', '#delete-zipcode', function () {

    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/Area/delete_zipcode',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

var searchable_zipcodes_deliveryboy = searchable_zipcodes_deliveryboy();

var $searchable_cities_deliveryboy = searchable_cities_deliveryboy(); // Assign the return value to a variable
$searchable_cities_deliveryboy.on('select2:select', function (e) {
    // Your event handling code here
    var data = e.params.data;
    if (data.link != undefined && data.link != null) {
        window.location.href = data.link;
    }
});

searchable_zipcodes_deliveryboy.on('select2:select', function (e) {
    var data = e.params.data;
    if (data.link != undefined && data.link != null) {
        window.location.href = data.link;
    }
});

var search_zipcodes = searchable_zipcodes();

search_zipcodes.on('select2:select', function (e) {
    var data = e.params.data;
    if (data.link != undefined && data.link != null) {
        window.location.href = data.link;
    }
});

var $search_cities = searchable_cities();
$search_cities.on('select2:select', function (e) {
    var data = e.params.data;
    if (data.link != undefined && data.link != null) {
        window.location.href = data.link;
    }
});

$(document).on('change', '#deliverable_type', function () {
    var type = $(this).val();
    if (type == "1" || type == "0") {
        $('#deliverable_zipcodes').prop('disabled', 'disabled');
    } else {
        $('#deliverable_zipcodes').prop('disabled', false);
    }
});
$(document).on('change', '#deliverable_zipcode_type', function () {
    var type = $(this).val();
    if (type == "1" || type == "0") {
        $('#deliverable_zipcodes').prop('disabled', 'disabled');
    } else {
        $('#deliverable_zipcodes').prop('disabled', false);
    }
});

$(document).on('change', '#deliverable_city_type', function () {
    var type = $(this).val()
    if (type == '1' || type == '0') {
        $('#deliverable_cities').prop('disabled', 'disabled')
    } else {
        $('#deliverable_cities').prop('disabled', false)
    }
})

var cat_html = "";
var count_view = 0;
$(document).on('click', '#seller_model', function (e) {
    e.preventDefault();
    cat_html = $('#cat_html').html();
    var cat_ids = $(this).data('cat_ids') + ',';
    var cat_array = cat_ids.split(",");
    cat_array = cat_array.filter(function (v) {
        return v !== ''
    });
    cat_array.sort(function (a, b) {
        return a - b;
    });

    document.getElementById("category_flag").value = "0"
    var seller_id = $(this).data('seller_id');
    if (cat_ids != "" && cat_ids != "," && cat_ids != 'undefined' && seller_id != "" && seller_id != 'undefined' && count_view == 0) {
        $.ajax({
            type: 'POST',
            data: {
                'id': seller_id,
                [csrfName]: csrfHash
            },
            url: base_url + 'admin/sellers/get_seller_commission_data',
            dataType: 'json',
            success: function (result) {
                csrfName = result.csrfName;
                csrfHash = result.csrfHash;
                if (result.error == false) {
                    var option_html = $('#cat_html').html();
                    let format = false;
                    if (result.data.length == 0) {
                        format = false
                    } else {
                        if (result.data[0].category_id == undefined) {
                            format = true;
                        }
                    }
                    $.each(result.data, function (i, e) {

                        if (format) {
                            var is_selected = cat_array.includes(e.id) ? "selected" : "";
                            if (is_selected === '') {
                            } else {
                                option_html += '<option value=' + e.id + ' ' + is_selected + '>' + e.name + '</option>';
                                load_category_section("", true, option_html, e.commission);
                            }
                        } else {
                            var is_selected = (e.category_id == cat_array[i] && e.seller_id == seller_id) ? "selected" : "";
                            if (is_selected === '') {
                                load_category_section(cat_html);
                            } else {
                                option_html += '<option value=' + e.category_id + ' ' + is_selected + '>' + e.name + '</option>';
                                load_category_section("", true, option_html, e.commission);
                            }
                        }

                    });
                } else {
                    iziToast.error({
                        message: '<span>' + result.message + '</span> ',
                    });
                }
            }
        });
        count_view = 1;
    } else {
        if (count_view == 0) {
            load_category_section(cat_html);
        }
        count_view = 1;

    }

});
$(document).on('click', '#add_category', function (e) {
    e.preventDefault();
    load_category_section(cat_html, false);
});

function load_category_section(cat_html, is_edit = false, option_html = "", commission = 0) {

    if (is_edit == true) {

        // Inside load_category_section
        var disabled = (commission != 0) ? 'disabled' : '';

        var html = ' <div class="form-group  row">' +
            '<div class="col-sm-5">' +
            '<select name="category_id" class="form-control select_multiple w-100" data-placeholder=" Select Category">' +
            '<option value="">Select Category </option>' + option_html +
            '</select>' +
            '</div>' +
            '<div class="col-sm-5">' +
            '<input type="number" step="any" min="0" max="100" class="form-control"  placeholder="Enter Commission" name="commission" required value="' + commission + '">' +
            '</div>' +
            '<div class="col-sm-2"> ' +
            '<button type="button" class="btn btn-tool remove_category_section"  ' + disabled + '> <i class="text-danger far fa-times-circle fa-2x "></i> </button>' +
            '</div>' +
            '</div>' +
            '</div>';
    } else {
        var html = ' <div class="form-group row">' +
            '<div class="col-sm-5">' +
            '<select name="category_id" class="form-control select_multiple w-100" data-placeholder="Select Category">' +
            '<option value="">Select Category </option>' + cat_html +
            '</select>' +
            '</div>' +
            '<div class="col-sm-5">' +
            '<input type="number" step="any"  min="0" max="100" class="form-control"  placeholder="Enter Commission" name="commission"  value="0">' +
            '</div>' +
            '<div class="col-sm-2"> ' +
            '<button type="button" class="btn btn-tool remove_category_section"  ' + disabled + '> <i class="text-danger far fa-times-circle fa-2x "></i> </button>' +
            '</div>' +
            '</div>' +
            '</div>';
    }
    $('#category_section').append(html);
    $('.select_multiple').each(function () {
        $('.select_multiple').select2({
            theme: 'bootstrap4',
            width: $('.select_multiple').data('width') ? $('.select_multiple').data('width') : $('.select_multiple').hasClass('w-100') ? '100%' : 'style',
            placeholder: $('.select_multiple').data('placeholder'),
            allowClear: Boolean($('.select_multiple').data('allow-clear')),
        });
    });


}

$(document).on('click', '.remove_category_section', function () {
    var $row = $(this).closest('.row');
    var commission = $row.find('input[name="commission"]').val();
    if (parseFloat(commission) !== 0) {
        // If commission is not 0, disable the button
        $(this).prop('disabled', true);
        iziToast.warning({ message: 'Cannot remove. Commission must be 0 to remove this section.' });
        return;
    }
    if ($('#category_section').children('.form-group').length > 1) {
        $row.remove();
    } else {
        alert('At least one category section must be present.');
    }
});

$("#seller_table").on("click-cell.bs.table", function (field, value, row, $el) {
    $('input[name="seller_status"]').val($el.id);
});


$('#add-seller-commission-form').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);

    var object = {};
    formData.forEach((value, key) => {
        // Reflect.has in favor of: object.hasOwnProperty(key)
        if (!Reflect.has(object, key)) {
            object[key] = value;
            return;
        }
        if (!Array.isArray(object[key])) {
            object[key] = [object[key]];
        }
        object[key].push(value);
    });
    var json = JSON.stringify(object);
    $('#cat_data').val(json);
    setTimeout(function () {
        $('#set_commission_model').modal('hide');
    }, 2000);

});

$(document).on('submit', '#verify-acount-form', function (e) {
    e.preventDefault();
    var formdata = new FormData(this);
    formdata.append(csrfName, csrfHash);

    $.ajax({
        type: 'POST',
        url: $(this).attr('action'),
        data: formdata,
        beforeSend: function () {
            $('#submit_btn1').html('Please Wait..').attr('disabled', true);
        },
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (result) {
            csrfHash = result.csrfHash;
            $('#submit_btn1').html('Verify Account').attr('disabled', false);
            if (result.error == false) {
                if (result.redirect == "3") {
                    window.location.replace(base_url + 'seller/auth/sign_up');
                } else if (result.redirect == "1") {
                    iziToast.success({
                        message: '<span style="text-transform:capitalize">' + result.message + '</span> ',
                    });
                }

            } else {
                if (result.redirect == '2') {
                    window.location.replace(base_url + 'seller/auth/sign_up');
                }
                iziToast.error({
                    message: '<span>' + result.message + '</span> ',
                });
            }
            $('#verify-acount-form').trigger("reset");
            setTimeout(function () {
                $('#has_account_model').modal('hide');
            }, 3000);
        }
    });
});

$(document).on('click', '#create-slug', function (e) {
    e.preventDefault();
    $.ajax({
        type: 'get',
        url: base_url + 'admin/sellers/create_slug',
        beforeSend: function () {
            $(this).html('Please Wait..').attr('disabled', true);
        },
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (result) {
            csrfHash = result.csrfHash;
            $(this).html('Create Seller Slug').attr('disabled', false);
            if (result.error == false) {
                iziToast.success({
                    message: result['message'],
                });
            } else {
                iziToast.error({
                    message: '<span>' + result.message + '</span> ',
                });
            }
        }
    });
});

$(document).on('click', '.update-seller-commission', function () {
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, settle commission!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/cron-job/settle-seller-commission',
                    type: 'GET',
                    data: {
                        'is_date': true
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Done!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});


$(document).on('click', '.mark-all-as-read', function () {
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, mark all notifications as read!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/Notification_settings/mark_all_as_read',
                    type: 'GET',
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Done!', response.message, 'success');
                            location.reload();
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

$(document).on('click', '.sync-zipcode-with-area', function () {
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Sync table !',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/area/table_sync',
                    type: 'GET',
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Done!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});


function cash_collection_query_params(p) {
    return {
        filter_date: $('#filter_date').val(),
        filter_status: $('#filter_status').val(),
        filter_d_boy: $('#filter_d_boy').val(),
        "start_date": $('#start_date').val(),
        "end_date": $('#end_date').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

var d_boy_cash = 0;
$('#delivery_boys_details').on('check.bs.table', function (e, row) {
    d_boy_cash = row.cash_received;
    $('#details').val("Id: " + row.id + " | Name:" + row.name + " | Mobile: " + row.mobile + " | Cash: " + row.cash_received);
    $('#delivery_boy_id').val(row.id);
});

$(document).on('click', '.edit_cash_collection_btn', function () {
    var id = $(this).data('id');
    var order_id = $(this).data('order-id');
    var amount = $(this).data('amount');
    var dboy_id = $(this).data('dboy-id');

    $('#details').val("Id: " + id + " | order id:" + order_id + " | Amount: " + amount + " | Cash: " + amount);
    $('#transaction_id').val(id);
    $('#order_id').val(order_id);
    $('#amount').val(amount);
    $('#order_amount').val(amount);
    $('#delivery_boy_id').val(dboy_id);

});

function validate_amount() {
    var cash = d_boy_cash;
    var amount = $('#amount').val();
    var details_val = $('#details').val();
    if (details_val == "") {
        iziToast.error({
            message: '<span>you have to select delivery boy to collect cash.</span> ',
        });
        $('#amount').val('');
    } else {
        if (parseInt(cash) > 0) {
            if (parseInt(amount) > parseInt(cash)) {
                iziToast.error({
                    message: '<span>You Can not enter amount greater than cash</span> ',
                });
                $('#amount').val('');
            }
            if (parseInt(amount) <= 0) {
                iziToast.error({
                    message: '<span>Amount must be greater than zero</span> ',
                });
                $('#amount').val('');
            }
        } else {
            iziToast.error({
                message: '<span>Cash must be greater than zero</span> ',
            });
            $('#amount').val('');
        }
    }
}

function idFormatter() {
    return 'Total'
}

function priceFormatter(data) {
    var field = this.field
    var store_currency = $('input[name="store_currency"]').val();

    return '<span style="color:green;font-weight:bold;font-size:large;">' + store_currency + data.map(function (row) {
        return +row[field]
    })
        .reduce(function (sum, i) {
            return sum + i
        }, 0);
}
// Feature Section Hide or Show Category Field

$(document).on('change', '.product_type', function () {
    var product_type = $('.product_type').val();
    var exclude_product_type = ["custom_products"];
    if (exclude_product_type.includes(product_type)) {
        $(".select-categories").hide();
    } else {
        $(".select-categories").show();
    }
});
$(document).on('click', '.add_promo_code_discount', function () {
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, settle Discounted!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/cron_job/settle_cashback_discount',
                    type: 'GET',
                    data: {
                        'is_date': true
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Done!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});
$(document).on('click', '.settle_referal_cashback_discount', function () {
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, settle Discounted!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/cron_job/settle_referal_cashback_discount',
                    type: 'GET',
                    data: {
                        'is_date': true
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Done!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});
$(document).on('click', '.settle_referal_cashback_discount_for_referal', function () {
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, settle Discounted!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/cron_job/settle_referal_cashback_discount_for_referal',
                    type: 'GET',
                    data: {
                        'is_date': true
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Done!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});
$('.discount_type').on('change', function () {
    if ($(this).val() == 'percentage') {
        if ($('.discount').val() > 100) {
            $('.discount').attr('max', 100);
            $('#submit_btn').attr('disabled', true);
            $('.error').html('<small class="text-danger">You cannot set percantage more then 100</small>')
        } else {
            $('.discount').removeAttr('max');
            $('#submit_btn').attr('disabled', false);
            $('.error').html('')
        }
    } else {
        $('.discount').removeAttr('max');
        $('#submit_btn').attr('disabled', false);
        $('.error').html('')
    }
});
$('.discount').keyup(function () {
    if ($('.discount_type').val() == 'percentage') {
        if ($('.discount').val() > 100) {
            $('.discount').attr('max', 100);
            $('#submit_btn').attr('disabled', true);
            $('.error').html('<small class="text-danger">You cannot set percantage more then 100</small>')
        } else {
            $('.discount').removeAttr('max');
            $('#submit_btn').attr('disabled', false);
            $('.error').html('')
        }
    } else {
        $('.discount').removeAttr('max');
        $('#submit_btn').attr('disabled', false);
        $('.error').html('')
    }
});

// select 2 js select countries
$(".country_list").select2({
    ajax: {
        url: base_url + 'admin/product/get_countries_data',
        type: "GET",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term, // search term
            };
        },
        processResults: function (response) {
            return {
                results: response
            };
        },
        cache: true
    },
    minimumInputLength: 1,
    theme: 'bootstrap4',
    placeholder: 'Search for countries',
});

$(".city_list").select2({
    ajax: {
        url: base_url + from + '/area/get_cities',
        type: "GET",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term, // search term
            };
        },
        processResults: function (response) {
            return {
                results: response
            };
        },
        cache: true
    },

    minimumInputLength: 1,
    theme: 'bootstrap4',
    placeholder: 'Search for cities',
})
$("#zipcode_list").select2({
    ajax: {
        url: base_url + from + '/area/get_zipcode_list',
        type: "GET",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term, // search term
            };
        },
        processResults: function (response) {
            return {
                results: response
            };
        },
        cache: true
    },

    minimumInputLength: 1,
    theme: 'bootstrap4',
    placeholder: 'Search for cities',
})

$(".country_list").select2({
    ajax: {
        url: base_url + 'seller/product/get_countries_data',
        type: "GET",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term, // search term
            };
        },
        processResults: function (response) {
            return {
                results: response
            };
        },
        cache: true
    },
    minimumInputLength: 1,
    theme: 'bootstrap4',
    placeholder: 'Search for countries',
});

//bonus_type
$(document).on('change', '.bonus_type', function (e, data) {
    e.preventDefault();
    var sort_type_val = $(this).val();
    if (sort_type_val == 'fixed_amount_per_order_item' && sort_type_val != ' ') {
        $('.fixed_amount_per_order').removeClass('d-none');
    } else {
        $('.fixed_amount_per_order').addClass('d-none');
    }
    if (sort_type_val == 'percentage_per_order_item' && sort_type_val != ' ') {
        $('.percentage_per_order').removeClass('d-none');
    } else {
        $('.percentage_per_order').addClass('d-none');
    }
});
//type
$(document).on('change', '.type', function (e, data) {
    e.preventDefault();
    var sort_type_val = $(this).val();
    if (sort_type_val == 'place_order' && sort_type_val != ' ') {
        $('.place_order').removeClass('d-none');
    } else {
        $('.place_order').addClass('d-none');
    }
    if (sort_type_val == 'seller_place_order' && sort_type_val != ' ') {
        $('.seller_place_order').removeClass('d-none');
    } else {
        $('.seller_place_order').addClass('d-none');
    }
    if (sort_type_val == 'delivery_boy_order_processed' && sort_type_val != ' ') {
        $('.delivery_boy_order_processed').removeClass('d-none');
    } else {
        $('.delivery_boy_order_processed').addClass('d-none');
    }
    if (sort_type_val == 'delivery_boy_return_order_assign' && sort_type_val != ' ') {
        $('.delivery_boy_return_order_assign').removeClass('d-none');
    } else {
        $('.delivery_boy_return_order_assign').addClass('d-none');
    }
    if (sort_type_val == 'settle_cashback_discount' && sort_type_val != ' ') {
        $('.settle_cashback_discount').removeClass('d-none');
    } else {
        $('.settle_cashback_discount').addClass('d-none');
    }
    if (sort_type_val == 'settle_seller_commission' && sort_type_val != ' ') {
        $('.settle_seller_commission').removeClass('d-none');
    } else {
        $('.settle_seller_commission').addClass('d-none');
    }
    if (sort_type_val == 'customer_order_received' && sort_type_val != ' ') {
        $('.customer_order_received').removeClass('d-none');
    } else {
        $('.customer_order_received').addClass('d-none');
    }
    if (sort_type_val == 'customer_order_processed' && sort_type_val != ' ') {
        $('.customer_order_processed').removeClass('d-none');
    } else {
        $('.customer_order_processed').addClass('d-none');
    }
    if (sort_type_val == 'customer_order_shipped' && sort_type_val != ' ') {
        $('.customer_order_shipped').removeClass('d-none');
    } else {
        $('.customer_order_shipped').addClass('d-none');
    }
    if (sort_type_val == 'customer_order_delivered' && sort_type_val != ' ') {
        $('.customer_order_delivered').removeClass('d-none');
    } else {
        $('.customer_order_delivered').addClass('d-none');
    }
    if (sort_type_val == 'customer_order_cancelled' && sort_type_val != ' ') {
        $('.customer_order_cancelled').removeClass('d-none');
    } else {
        $('.customer_order_cancelled').addClass('d-none');
    }
    if (sort_type_val == 'customer_order_returned' && sort_type_val != ' ') {
        $('.customer_order_returned').removeClass('d-none');
    } else {
        $('.customer_order_returned').addClass('d-none');
    }
    if (sort_type_val == 'customer_order_returned_request_approved' && sort_type_val != ' ') {
        $('.customer_order_returned_request_approved').removeClass('d-none');
    } else {
        $('.customer_order_returned_request_approved').addClass('d-none');
    }
    if (sort_type_val == 'customer_order_returned_request_decline' && sort_type_val != ' ') {
        $('.customer_order_returned_request_decline').removeClass('d-none');
    } else {
        $('.customer_order_returned_request_decline').addClass('d-none');
    }
    if (sort_type_val == 'delivery_boy_order_deliver' && sort_type_val != ' ') {
        $('.delivery_boy_order_deliver').removeClass('d-none');
    } else {
        $('.delivery_boy_order_deliver').addClass('d-none');
    }
    if (sort_type_val == 'wallet_transaction' && sort_type_val != ' ') {
        $('.wallet_transaction').removeClass('d-none');
    } else {
        $('.wallet_transaction').addClass('d-none');
    }
    if (sort_type_val == 'ticket_status' && sort_type_val != ' ') {
        $('.ticket_status').removeClass('d-none');
    } else {
        $('.ticket_status').addClass('d-none');
    }
    if (sort_type_val == 'ticket_message' && sort_type_val != ' ') {
        $('.ticket_message').removeClass('d-none');
    } else {
        $('.ticket_message').addClass('d-none');
    }
    if (sort_type_val == 'bank_transfer_receipt_status' && sort_type_val != ' ') {
        $('.bank_transfer_receipt_status').removeClass('d-none');
    } else {
        $('.bank_transfer_receipt_status').addClass('d-none');
    }
    if (sort_type_val == 'bank_transfer_proof' && sort_type_val != ' ') {
        $('.bank_transfer_proof').removeClass('d-none');
    } else {
        $('.bank_transfer_proof').addClass('d-none');
    }

});

//custom-notification-Module
$(document).on('click', '.delete_custom_notification', function () {
    var id = $(this).data('id');
    var t = this;
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/custom_notification/delete_custom_notification',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (result, textStatus) {
                        if (result['error'] == false) {
                            Swal.fire('Deleted!', result['message'], 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', result['message'], 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

//custom-sms-Module
$(document).on('click', '.delete_custom_sms', function () {
    var id = $(this).data('id');
    var t = this;
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/custom_sms/delete_custom_sms',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (result, textStatus) {
                        if (result['error'] == false) {
                            Swal.fire('Deleted!', result['message'], 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', result['message'], 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

var noti_user_id = 0;
$('#select_user_id').on('change', function () {
    noti_user_id = ($('#select_user_id').val());
});

$('.search_user').each(function () {
    $(this).select2({
        ajax: {
            url: base_url + 'admin/customer/search_user',
            type: "GET",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term, // search term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            },
            cache: true
        },
        minimumInputLength: 1,
        theme: 'bootstrap4',
        placeholder: 'Search for countries',
    });
});

$(document).on('click', '.delete-product-faq', function () {
    var faq_id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/product_faqs/delete_product_faq',
                    data: {
                        id: faq_id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                        csrfName = response['csrfName'];
                        csrfHash = response['csrfHash'];
                    });
            });
        },
        allowOutsideClick: false
    });
});
$(document).on('click', '.delete-seller-product-faq', function () {
    var faq_id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'seller/product_faqs/delete_product_faq',
                    data: {
                        id: faq_id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                        csrfName = response['csrfName'];
                        csrfHash = response['csrfHash'];
                    });
            });
        },
        allowOutsideClick: false
    });
});
$(".search_product").select2({
    ajax: {
        url: base_url + 'seller/product/get_product_data_for_faq',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term, // search term
                page: params.page
            };
        },
        processResults: function (response, params) {
            params.page = params.page || 1;

            return {
                results: response.rows,
                pagination: {
                    more: (params.page * 30) < response.total
                }
            };
        },
        cache: true
    },
    escapeMarkup: function (markup) {
        return markup;
    },
    minimumInputLength: 1,
    templateResult: formatRepo,
    templateSelection: formatRepoSelection,
    theme: 'bootstrap4',
    placeholder: 'Search for products'
});
searchable_zipcodes();
searchable_cities();
setTimeout(function () {
    $('.edit-modal-lg').unblock();
}, 2000);

function faqParams(p) {
    return {
        "user_id": $('#user_id').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}
// custom_notification_message
function initializeInputFiller(spanSelector, inputelement) {
    const inputField = inputelement;
    const fillerSpans = document.querySelectorAll(spanSelector);

    fillerSpans.forEach(function (span) {
        span.addEventListener('click', function () {
            const text = this.textContent;
            const startPos = inputField.selectionStart;
            const endPos = inputField.selectionEnd;
            const currentValue = inputField.value;
            const newValue =
                currentValue.substring(0, startPos) +
                text +
                currentValue.substring(endPos);
            inputField.value = newValue;
            inputField.focus();
            inputField.setSelectionRange(startPos + text.length, startPos + text.length);
        });
    });
}


$(document).on('show.bs.modal', '#product-faqs-modal', function (event) {
    var triggerElement = $(event.relatedTarget);
    current_selected_image = triggerElement;
    var id = $(current_selected_image).data('id');
    var existing_url = $(this).find('#product-faqs-table').data('url');

    if (existing_url.indexOf('?') > -1) {
        var temp = $(existing_url).text().split('?');
        var new_url = temp[0] + '?product_id=' + id;
    } else {
        var new_url = existing_url + '?product_id=' + id;
    }
    $('#product-faqs-table').bootstrapTable('refreshOptions', {
        url: new_url,
    });
});

$(document).on('click', '.edit_order_refund', function () {
    var order_item_id = $(this).data('order_item_id');
    var txn_id = $(this).data('txn_id');
    var txn_amount = $(this).data('txn_amount');
    $('#transaction_id').val(txn_id);
    $('#txn_amount').val(txn_amount);
    $('#item_id').val(order_item_id);
});

$('#refund_form').on('click', function (e) {
    e.preventDefault();
    var txn_id = $('#transaction_id').val();
    var txn_amount = $('#txn_amount').val();
    var item_id = $('#item_id').val();
    $.ajax({
        type: 'POST',
        data: {
            'txn_id': txn_id,
            'txn_amount': txn_amount,
            'item_id': item_id,
            [csrfName]: csrfHash,
        },
        url: base_url + 'admin/orders/refund_payment',
        dataType: 'json',
        success: function (result) {
            csrfName = result.csrfName;
            csrfHash = result.csrfHash;
            if (result['error'] == false) {
                iziToast.success({
                    message: result['message'],
                });
            } else {
                iziToast.error({
                    message: result['message'],
                });
            }
        }
    });
});

function salesReport(index, row) {
    var html = []
    var indexs = 0

    $.each(row, function (key, value) {
        var columns = $("th:eq(" + (indexs + 1) + ")").data("field")
        if (columns != undefined) {
            html.push('<p><b>' + columns + ' :</b> ' + row[columns] + '</p>')
            indexs++;
        }
    })
    return html;
}

// google translate

function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'en'
    }, 'google_translate_element');
}

$(document).ready(function () {
    googleTranslateElementInit();
});


if (window.location.href.indexOf('admin') > -1) {
    // send admin notification
    $(document).ready(function () {
        setInterval(function () {
            $.ajax({
                type: 'GET',
                url: base_url + 'admin/home/get_notification',
                dataType: 'json',
                success: function (result) {
                    var html = '';
                    html += '<a href="javascript:void(0);" id="notification_count" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg"><i class="fas fa-bell fa-2x"></i><span class="badge badge-danger navbar-badge order_notification mt-1">' + result.count_notifications + '</span></a>';
                    $('#refresh_notification').html(html);
                }
            });
        }, 3000);
    });

}

$(document).on('click', '#notification_count', function (e, rows) {
    e.preventDefault();
    $('#list').toggle();
    if ($('#list').is(":visible")) {
        // Display a "Please Wait" message or spinner while waiting for the response
        $('#list').html('<div class="loading-message">Please Wait...</div>').addClass("show");

        $.ajax({
            type: 'GET',
            url: base_url + 'admin/home/new_notification_list',
            dataType: 'json',
            success: function (result) {
                var html = '';
                var beep;
                var seconds_ago;
                var time;

                $.each(result.notifications, function (i, a) {
                    beep = (a.read_by && a.read_by == 0) ? '<span><i class="fa fa-certificate ml-3 text-orange text-sm"></i></span>' : "";
                    seconds_ago = a.date_sent;

                    html += '  <a href="' + base_url + 'admin/orders/edit_orders' + '?edit_id=' + a.type_id + '&noti_id=' + a.id + '" class="dropdown-item">\
                            <div class="media">\
                                <div class="media-body">\
                                    <h3 class="dropdown-item-title mb-2">' + a.title + beep + '</h3>\
                                    <p class="text-sm mb-2">' + a.message + '</p>\
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>' + seconds_ago + '</p>\
                                </div>\
                            </div>\
                        </a>\
                <div class="dropdown-divider"></div>';
                });

                if (!(result.notifications.length === 0)) {
                    html += '<a href="javascript:void(0);" class="dropdown-item dropdown-footer mark-all-as-read">Mark All As Read</a><div class="dropdown-divider"></div>';
                } else {
                    html += '<div class="dropdown-footer mt-2">No New Notifications</div>';
                }

                html += '<a href="' + base_url + 'admin/Notification_settings/manage_system_notifications' + '" class="dropdown-item dropdown-footer">See All Notifications</a>';

                $('#list').html(html);
            }
        });
    }
});



$(document).ready(function () {

    $('#ManageOrderSendMailModal').on('shown.bs.modal', function (e) {

        tinymce.init({
            selector: '.sendMail',
            plugins: [
                'a11ychecker', 'advlist', 'advcode', 'advtable', 'autolink', 'checklist', 'export',
                'lists', 'link', 'image', 'charmap', 'preview', 'code', 'anchor', 'searchreplace', 'visualblocks',
                'powerpaste', 'fullscreen', 'formatpainter', 'insertdatetime', 'media', 'image', 'directionality', 'fullscreen', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | image media | code fullscreen| formatpainter casechange blocks fontsize | bold italic forecolor backcolor | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist checklist outdent indent | removeformat | ltr rtl |a11ycheck table help',

            font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
            image_uploadtab: false,
            images_upload_url: base_url + "admin/media/upload",
            relative_urls: false,
            remove_script_host: false,
            file_picker_types: 'image media',
            media_poster: false,
            media_alt_source: false,

            file_picker_callback: function (callback, value, meta) {
                if (meta.filetype == "media" || meta.filetype == "image") {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/* audio/* video/*');

                    input.addEventListener('change', (e) => {
                        const file = e.target.files[0];

                        var reader = new FileReader();
                        var fd = new FormData();
                        var files = file;
                        fd.append("documents[]", files);
                        fd.append('filetype', meta.filetype);

                        var filename = "";
                        // AJAX
                        jQuery.ajax({
                            url: base_url + "admin/media/upload",
                            type: "post",
                            data: fd,
                            contentType: false,
                            processData: false,
                            async: false,
                            success: function (response) {
                                var response = jQuery.parseJSON(response)
                                filename = response.file_name;
                            }
                        });

                        reader.onload = function (e) {
                            callback(base_url + "uploads/media/2022/" + filename);
                        };
                        reader.readAsDataURL(file);
                    });
                    input.click();
                }
            },
            setup: function (editor) {
                editor.on("change keyup", function (e) {
                    editor.save(); // updates this instance's textarea
                    $(editor.getElement()).trigger('change'); // for garlic to detect change
                });
            }
        });
    });




    $('.editSendMailOrders').on('shown.bs.modal', function (e) {
        if ($(".editSendMailOrders").length > 0) {
            tinymce.init({
                selector: '.sendMail',
                plugins: [
                    'a11ychecker', 'advlist', 'advcode', 'advtable', 'autolink', 'checklist', 'export',
                    'lists', 'link', 'image', 'charmap', 'preview', 'code', 'anchor', 'searchreplace', 'visualblocks',
                    'powerpaste', 'fullscreen', 'formatpainter', 'insertdatetime', 'media', 'image', 'directionality', 'fullscreen', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | image media | code fullscreen| formatpainter casechange blocks fontsize | bold italic forecolor backcolor | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist checklist outdent indent | removeformat | ltr rtl |a11ycheck table help',

                font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
                image_uploadtab: false,
                images_upload_url: base_url + "admin/media/upload",
                relative_urls: false,
                remove_script_host: false,
                file_picker_types: 'image media',
                media_poster: false,
                media_alt_source: false,

                file_picker_callback: function (callback, value, meta) {
                    if (meta.filetype == "media" || meta.filetype == "image") {
                        const input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/* audio/* video/*');

                        input.addEventListener('change', (e) => {
                            const file = e.target.files[0];

                            var reader = new FileReader();
                            var fd = new FormData();
                            var files = file;
                            fd.append("documents[]", files);
                            fd.append('filetype', meta.filetype);

                            var filename = "";
                            // AJAX
                            jQuery.ajax({
                                url: base_url + "admin/media/upload",
                                type: "post",
                                data: fd,
                                contentType: false,
                                processData: false,
                                async: false,
                                success: function (response) {
                                    var response = jQuery.parseJSON(response)
                                    filename = response.file_name;
                                }
                            });

                            reader.onload = function (e) {
                                callback(base_url + "uploads/media/2022/" + filename);
                            };
                            reader.readAsDataURL(file);
                        });
                        input.click();
                    }
                },
                setup: function (editor) {
                    editor.on("change keyup", function (e) {
                        editor.save(); // updates this instance's textarea
                        $(editor.getElement()).trigger('change'); // for garlic to detect change
                    });
                }
            });
        }
    });



    $('.editSendMail').on('shown.bs.modal', function (e) {

        if ($(".textarea").length > 0) {
            tinymce.init({
                selector: '.textarea',
                plugins: [
                    'a11ychecker', 'advlist', 'advcode', 'advtable', 'autolink', 'checklist', 'export',
                    'lists', 'link', 'image', 'charmap', 'preview', 'code', 'anchor', 'searchreplace', 'visualblocks',
                    'powerpaste', 'fullscreen', 'formatpainter', 'insertdatetime', 'media', 'image', 'directionality', 'fullscreen', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | image media | code fullscreen| formatpainter casechange blocks fontsize | bold italic forecolor backcolor | ' +
                    'alignleft aligncenter alignright alignjustify | ' +
                    'bullist numlist checklist outdent indent | removeformat | ltr rtl |a11ycheck table help',

                font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
                image_uploadtab: false,
                images_upload_url: base_url + "admin/media/upload",
                relative_urls: false,
                remove_script_host: false,
                file_picker_types: 'image media',
                media_poster: false,
                media_alt_source: false,

                file_picker_callback: function (callback, value, meta) {
                    if (meta.filetype == "media" || meta.filetype == "image") {
                        const input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/* audio/* video/*');

                        input.addEventListener('change', (e) => {
                            const file = e.target.files[0];

                            var reader = new FileReader();
                            var fd = new FormData();
                            var files = file;
                            fd.append("documents[]", files);
                            fd.append('filetype', meta.filetype);

                            var filename = "";
                            // AJAX
                            jQuery.ajax({
                                url: base_url + "admin/media/upload",
                                type: "post",
                                data: fd,
                                contentType: false,
                                processData: false,
                                async: false,
                                success: function (response) {
                                    var response = jQuery.parseJSON(response)
                                    filename = response.file_name;
                                }
                            });

                            reader.onload = function (e) {
                                callback(base_url + "uploads/media/2022/" + filename);
                            };
                            reader.readAsDataURL(file);
                        });
                        input.click();
                    }
                },
                setup: function (editor) {
                    editor.on("change keyup", function (e) {
                        editor.save(); // updates this instance's textarea
                        $(editor.getElement()).trigger('change'); // for garlic to detect change
                    });
                }
            });
        }
    });

    tinymce.init({
        selector: '.addr_editor',
        menubar: true,
        plugins: [
            'a11ychecker', 'advlist', 'advcode', 'advtable', 'autolink', 'checklist', 'export',
            'lists', 'link', 'image', 'charmap', 'preview', 'code', 'anchor', 'searchreplace', 'visualblocks',
            'powerpaste', 'fullscreen', 'formatpainter', 'insertdatetime', 'media', 'image', 'directionality', 'fullscreen', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | image media | code fullscreen| formatpainter casechange blocks fontsize | bold italic forecolor backcolor | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist checklist outdent indent | removeformat | ltr rtl |a11ycheck table help',

        font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
        image_uploadtab: false,
        images_upload_url: base_url + "admin/media/upload",
        relative_urls: false,
        remove_script_host: false,
        file_picker_types: 'image media',
        media_poster: false,
        media_alt_source: false,

        file_picker_callback: function (callback, value, meta) {
            if (meta.filetype == "media" || meta.filetype == "image") {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/* audio/* video/*');

                input.addEventListener('change', (e) => {
                    const file = e.target.files[0];

                    var reader = new FileReader();
                    var fd = new FormData();
                    var files = file;
                    fd.append("documents[]", files);
                    fd.append('filetype', meta.filetype);
                    fd.append(csrfName, csrfHash);

                    var filename = "";
                    // AJAX
                    jQuery.ajax({
                        url: base_url + "admin/media/upload",
                        type: "post",
                        data: fd,
                        contentType: false,
                        processData: false,
                        async: false,
                        success: function (response) {
                            var response = jQuery.parseJSON(response)
                            filename = response.file_name;
                        }
                    });

                    reader.onload = function (e) {
                        const imageUrl = base_url + "uploads/media/" + currentYear + "/" + filename;
                        callback(imageUrl.replace(/&quot;/g, ''));
                    };
                    reader.readAsDataURL(file);

                });
                input.click();
            }
        },
        setup: function (editor) {
            editor.on("change keyup", function (e) {
                editor.save(); // updates this instance's textarea
                $(editor.getElement()).trigger('change'); // for garlic to detect change
            });
        }
    });
});

// select 2 js select brands
$(".admin_brand_list").select2({
    ajax: {
        url: base_url + from + '/product/get_brands_data',
        type: "GET",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term, // search term
            };
        },
        processResults: function (response) {
            return {
                results: response
            };
        },
        cache: true
    },
    minimumInputLength: 1,
    theme: 'bootstrap4',
    placeholder: 'Search for brands',
});

// select 2 js select brands
$(".brand_list").select2({
    ajax: {
        url: base_url + 'seller/product/get_brands_data',
        type: "GET",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term, // search term
            };
        },
        processResults: function (response) {
            return {
                results: response
            };
        },
        cache: true
    },
    minimumInputLength: 1,
    theme: 'bootstrap4',
    placeholder: 'Search for brands',
});


$(document).on('click', '.delete-brand', function () {
    var brand_id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: base_url + 'admin/brand/delete_brand',
                    data: {
                        id: brand_id
                    },
                    dataType: 'json'
                })
                    .done(function (response, textStatus) {
                        if (response.error == true) {
                            Swal.fire('Deleted!', 'Brand deleted successfully', 'success');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        } else {
                            Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                            $('table').bootstrapTable('refresh');
                            csrfName = response['csrfName'];
                            csrfHash = response['csrfHash'];
                        }

                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                        csrfName = response['csrfName'];
                        csrfHash = response['csrfHash'];
                    });
            });
        },
        allowOutsideClick: false
    });
});

$(document).on('click', '#add_attribute_value', function (e) {
    e.preventDefault();
    load_attribute_section();

});

$(document).on('change', '.swatche_type', function () {
    if ($(this).val() == '1') {
        $(this).siblings('.color_picker').show();
        $(this).siblings('.upload_media').hide();
        $(this).siblings('.grow').hide();
    }
    if ($(this).val() == "2") {
        $(this).siblings(".color_picker").hide();
        $(this).siblings(".color_picker").attr('name', null);
        $(this).siblings(".upload_media").show();
        $(this).siblings(".grow").show();
    }
    if ($(this).val() == "0") {
        $(".color_picker").hide();
        $(".upload_media").hide();
        $('.grow').hide();
    }
});

function load_attribute_section() {

    var html = ' <div class="form-group  row">' +
        '<div class="col-sm-4">' +
        '<input type="text" step="any"  class="form-control"  placeholder="Enter Attribute Value" name="attribute_value[]" >' +
        '</div>' +
        '<div class="col-sm-4">' +
        '<select class="form-control swatche_type"  name="swatche_type[]">' +
        '<option value="0"> Default </option>' +
        '<option value="1"> Color </option >' +
        '<option value="2"> Image </option >' +
        '</select >' +
        '<input type="color" class="form-control color_picker" id="swatche_value" name="swatche_value[]" style="display: none;">' +
        '<a style="display: none;" class="uploadFile img btn btn-primary text-white btn-sm upload_media" data-input="swatche_value[]" name="attribute_img[]" data-isremovable="0" data-is-multiple-uploads-allowed="0" data-toggle="modal" data-target="#media-upload-modal" value="Upload Photo"><i class="fa fa-upload"></i> Upload</a></div>' +
        '<div class="col-sm-2"> ' +
        '<button type="button" class="btn btn-tool remove_attribute_section" > <i class="text-danger far fa-times-circle fa-2x "></i> </button>' +
        '</div>' +
        '<div class="container-fluid row image-upload-section">' +
        '<div style="display: none;" class="shadow p-3 mb-5 bg-white rounded m-4 text-center grow">' +
        '<div class="image-upload-div"><img class="img-fluid mb-2 image" src="" alt="Image Not Found"></div>' +
        '<input type="hidden" value="">' +
        '</div>' +
        '</div>' +
        '</div>';

    $('#attribute_section').append(html);

    $('.swatche_type').each(function () {
        $('.swatche_type').select2({
            theme: 'bootstrap4',
            width: $('.swatche_type').data('width') ? $('.swatche_type').data('width') : $('.swatche_type').hasClass('w-100') ? '100%' : 'style',
            placeholder: $('.swatche_type').data('placeholder'),
            allowClear: Boolean($('.swatche_type').data('allow-clear')),
        });
    });
}

$(document).on('click', '.remove_attribute_section', function () {
    $(this).closest('.row').remove();
});

((window, document, Math) => {
    const ctx = document.createElement("canvas").getContext("2d");
    const currentColor = {
        r: 0,
        g: 0,
        b: 0,
        h: 0,
        s: 0,
        v: 0,
        a: 1,
    };
    let picker,
        colorArea,
        colorAreaDims,
        colorMarker,
        colorPreview,
        colorValue,
        clearButton,
        hueSlider,
        hueMarker,
        alphaSlider,
        alphaMarker,
        currentEl,
        currentFormat,
        oldColor;

    // Default settings
    const settings = {
        el: "[data-coloris]",
        parent: null,
        theme: "light",
        wrap: true,
        margin: 2,
        format: "hex",
        formatToggle: false,
        swatches: [],
        alpha: true,
        clearButton: {
            show: false,
            label: "Clear",
        },
        a11y: {
            open: "Open color picker",
            close: "Close color picker",
            marker: "Saturation: {s}. Brightness: {v}.",
            hueSlider: "Hue slider",
            alphaSlider: "Opacity slider",
            input: "Color value field",
            format: "Color format",
            swatch: "Color swatch",
            instruction: "Saturation and brightness selector. Use up, down, left and right arrow keys to select.",
        },
    };

    /**
     * Configure the color picker.
     * @param {object} options Configuration options.
     */
    function configure(options) {
        if (typeof options !== "object") {
            return;
        }

        for (const key in options) {
            switch (key) {
                case "el":
                    bindFields(options.el);
                    if (options.wrap !== false) {
                        wrapFields(options.el);
                    }
                    break;
                case "parent":
                    settings.parent = document.querySelector(options.parent);
                    if (settings.parent) {
                        settings.parent.appendChild(picker);
                    }
                    break;
                case "theme":
                    picker.className = `clr-picker clr-${options.theme
                        .split("-")
                        .join(" clr-")}`;
                    break;
                case "margin":
                    options.margin *= 1;
                    settings.margin = !isNaN(options.margin) ?
                        options.margin :
                        settings.margin;
                    break;
                case "wrap":
                    if (options.el && options.wrap) {
                        wrapFields(options.el);
                    }
                    break;
                case "format":
                    settings.format = options.format;
                    break;
                case "formatToggle":
                    getEl("clr-format").style.display = options.formatToggle ?
                        "block" :
                        "none";
                    if (options.formatToggle) {
                        settings.format = "auto";
                    }
                    break;
                case "swatches":
                    if (Array.isArray(options.swatches)) {
                        const swatches = [];

                        options.swatches.forEach((swatch, i) => {
                            swatches.push(
                                `<button id="clr-swatch-${i}" aria-labelledby="clr-swatch-label clr-swatch-${i}" style="color: ${swatch};">${swatch}</button>`
                            );
                        });

                        if (swatches.length) {
                            getEl("clr-swatches").innerHTML = `<div>${swatches.join(
                                ""
                            )}</div>`;
                        }
                    }
                    break;
                case "alpha":
                    settings.alpha = !!options.alpha;
                    picker.setAttribute("data-alpha", settings.alpha);
                    break;
                case "clearButton":
                    let display = "none";

                    if (options.clearButton.show) {
                        display = "block";
                    }

                    if (options.clearButton.label) {
                        clearButton.innerHTML = options.clearButton.label;
                    }

                    clearButton.style.display = display;
                    break;
                case "a11y":
                    const labels = options.a11y;
                    let update = false;

                    if (typeof labels === "object") {
                        for (const label in labels) {
                            if (labels[label] && settings.a11y[label]) {
                                settings.a11y[label] = labels[label];
                                update = true;
                            }
                        }
                    }

                    if (update) {
                        const openLabel = getEl("clr-open-label");
                        const swatchLabel = getEl("clr-swatch-label");

                        openLabel.innerHTML = settings.a11y.open;
                        swatchLabel.innerHTML = settings.a11y.swatch;
                        colorPreview.setAttribute("aria-label", settings.a11y.close);
                        hueSlider.setAttribute("aria-label", settings.a11y.hueSlider);
                        alphaSlider.setAttribute("aria-label", settings.a11y.alphaSlider);
                        colorValue.setAttribute("aria-label", settings.a11y.input);
                        colorArea.setAttribute("aria-label", settings.a11y.instruction);
                    }
            }
        }
    }

    /**
     * Bind the color picker to input fields that match the selector.
     * @param {string} selector One or more selectors pointing to input fields.
     */
    function bindFields(selector) {
        // Show the color picker on click on the input fields that match the selector
        addListener(document, "click", selector, (event) => {
            const parent = settings.parent;
            const coords = event.target.getBoundingClientRect();
            const scrollY = window.scrollY;
            let reposition = {
                left: false,
                top: false,
            };
            let offset = {
                x: 0,
                y: 0,
            };
            let left = coords.x;
            let top = scrollY + coords.y + coords.height + settings.margin;

            currentEl = event.target;
            oldColor = currentEl.value;
            currentFormat = getColorFormatFromStr(oldColor);
            picker.classList.add("clr-open");

            const pickerWidth = picker.offsetWidth;
            const pickerHeight = picker.offsetHeight;

            // If the color picker is inside a custom container
            // set the position relative to it
            if (parent) {
                const style = window.getComputedStyle(parent);
                const marginTop = parseFloat(style.marginTop);
                const borderTop = parseFloat(style.borderTopWidth);

                offset = parent.getBoundingClientRect();
                offset.y += borderTop + scrollY;
                left -= offset.x;
                top -= offset.y;

                if (left + pickerWidth > parent.clientWidth) {
                    left += coords.width - pickerWidth;
                    reposition.left = true;
                }

                if (top + pickerHeight > parent.clientHeight - marginTop) {
                    top -= coords.height + pickerHeight + settings.margin * 2;
                    reposition.top = true;
                }

                top += parent.scrollTop;

                // Otherwise set the position relative to the whole document
            } else {
                if (left + pickerWidth > document.documentElement.clientWidth) {
                    left += coords.width - pickerWidth;
                    reposition.left = true;
                }

                if (
                    top + pickerHeight - scrollY >
                    document.documentElement.clientHeight
                ) {
                    top = scrollY + coords.y - pickerHeight - settings.margin;
                    reposition.top = true;
                }
            }

            picker.classList.toggle("clr-left", reposition.left);
            picker.classList.toggle("clr-top", reposition.top);
            picker.style.left = `${left}px`;
            picker.style.top = `${top}px`;
            colorAreaDims = {
                width: colorArea.offsetWidth,
                height: colorArea.offsetHeight,
                x: picker.offsetLeft + colorArea.offsetLeft + offset.x,
                y: picker.offsetTop + colorArea.offsetTop + offset.y,
            };

            setColorFromStr(oldColor);
            colorValue.focus({
                preventScroll: true,
            });
        });

        // Update the color preview of the input fields that match the selector
        addListener(document, "input", selector, (event) => {
            const parent = event.target.parentNode;

            // Only update the preview if the field has been previously wrapped
            if (parent.classList.contains("clr-field")) {
                parent.style.color = event.target.value;
            }
        });
    }

    /**
     * Wrap the linked input fields in a div that adds a color preview.
     * @param {string} selector One or more selectors pointing to input fields.
     */
    function wrapFields(selector) {
        document.querySelectorAll(selector).forEach((field) => {
            const parentNode = field.parentNode;

            if (!parentNode.classList.contains("clr-field")) {
                const wrapper = document.createElement("div");

                wrapper.innerHTML = `<button aria-labelledby="clr-open-label"></button>`;
                parentNode.insertBefore(wrapper, field);
                wrapper.setAttribute("class", "clr-field");
                wrapper.style.color = field.value;
                wrapper.appendChild(field);
            }
        });
    }

    /**
     * Close the color picker.
     * @param {boolean} [revert] If true, revert the color to the original value.
     */
    function closePicker(revert) {
        if (currentEl) {
            // Revert the color to the original value if needed
            if (revert && oldColor !== currentEl.value) {
                currentEl.value = oldColor;

                // Trigger an "input" event to force update the thumbnail next to the input field
                currentEl.dispatchEvent(
                    new Event("input", {
                        bubbles: true,
                    })
                );
            }

            if (oldColor !== currentEl.value) {
                currentEl.dispatchEvent(
                    new Event("change", {
                        bubbles: true,
                    })
                );
            }

            picker.classList.remove("clr-open");
            currentEl.focus({
                preventScroll: true,
            });
            currentEl = null;
        }
    }

    /**
     * Set the active color from a string.
     * @param {string} str String representing a color.
     */
    function setColorFromStr(str) {
        const rgba = strToRGBA(str);
        const hsva = RGBAtoHSVA(rgba);

        updateMarkerA11yLabel(hsva.s, hsva.v);
        updateColor(rgba, hsva);

        // Update the UI
        hueSlider.value = hsva.h;
        picker.style.color = `hsl(${hsva.h}, 100%, 50%)`;
        hueMarker.style.left = `${(hsva.h / 360) * 100}%`;

        colorMarker.style.left = `${(colorAreaDims.width * hsva.s) / 100}px`;
        colorMarker.style.top = `${colorAreaDims.height - (colorAreaDims.height * hsva.v) / 100
            }px`;

        alphaSlider.value = hsva.a * 100;
        alphaMarker.style.left = `${hsva.a * 100}%`;
    }

    /**
     * Guess the color format from a string.
     * @param {string} str String representing a color.
     * @return {string} The color format.
     */
    function getColorFormatFromStr(str) {
        const format = str.substring(0, 3).toLowerCase();

        if (format === "rgb" || format === "hsl") {
            return format;
        }

        return "hex";
    }

    /**
     * Copy the active color to the linked input field.
     * @param {number} [color] Color value to override the active color.
     */
    function pickColor(color) {
        if (currentEl) {
            currentEl.value = color !== undefined ? color : colorValue.value;
            currentEl.dispatchEvent(
                new Event("input", {
                    bubbles: true,
                })
            );
        }
    }

    /**
     * Set the active color based on a specific point in the color gradient.
     * @param {number} x Left position.
     * @param {number} y Top position.
     */
    function setColorAtPosition(x, y) {
        const hsva = {
            h: hueSlider.value * 1,
            s: (x / colorAreaDims.width) * 100,
            v: 100 - (y / colorAreaDims.height) * 100,
            a: alphaSlider.value / 100,
        };
        const rgba = HSVAtoRGBA(hsva);

        updateMarkerA11yLabel(hsva.s, hsva.v);
        updateColor(rgba, hsva);
        pickColor();
    }

    /**
     * Update the color marker's accessibility label.
     * @param {number} saturation
     * @param {number} value
     */
    function updateMarkerA11yLabel(saturation, value) {
        let label = settings.a11y.marker;

        saturation = saturation.toFixed(1) * 1;
        value = value.toFixed(1) * 1;
        label = label.replace("{s}", saturation);
        label = label.replace("{v}", value);
        colorMarker.setAttribute("aria-label", label);
    }

    //
    /**
     * Get the pageX and pageY positions of the pointer.
     * @param {object} event The MouseEvent or TouchEvent object.
     * @return {object} The pageX and pageY positions.
     */
    function getPointerPosition(event) {
        return {
            pageX: event.changedTouches ? event.changedTouches[0].pageX : event.pageX,
            pageY: event.changedTouches ? event.changedTouches[0].pageY : event.pageY,
        };
    }

    /**
     * Move the color marker when dragged.
     * @param {object} event The MouseEvent object.
     */
    function moveMarker(event) {
        const pointer = getPointerPosition(event);
        let x = pointer.pageX - colorAreaDims.x;
        let y = pointer.pageY - colorAreaDims.y;

        if (settings.parent) {
            y += settings.parent.scrollTop;
        }

        x = x < 0 ? 0 : x > colorAreaDims.width ? colorAreaDims.width : x;
        y = y < 0 ? 0 : y > colorAreaDims.height ? colorAreaDims.height : y;

        colorMarker.style.left = `${x}px`;
        colorMarker.style.top = `${y}px`;

        setColorAtPosition(x, y);

        // Prevent scrolling while dragging the marker
        event.preventDefault();
        event.stopPropagation();
    }

    /**
     * Move the color marker when the arrow keys are pressed.
     * @param {number} offsetX The horizontal amount to move.
     * * @param {number} offsetY The vertical amount to move.
     */
    function moveMarkerOnKeydown(offsetX, offsetY) {
        const x = colorMarker.style.left.replace("px", "") * 1 + offsetX;
        const y = colorMarker.style.top.replace("px", "") * 1 + offsetY;

        colorMarker.style.left = `${x}px`;
        colorMarker.style.top = `${y}px`;

        setColorAtPosition(x, y);
    }

    /**
     * Update the color picker's input field and preview thumb.
     * @param {Object} rgba Red, green, blue and alpha values.
     * @param {Object} [hsva] Hue, saturation, value and alpha values.
     */
    function updateColor(rgba = {}, hsva = {}) {
        let format = settings.format;

        for (const key in rgba) {
            currentColor[key] = rgba[key];
        }

        for (const key in hsva) {
            currentColor[key] = hsva[key];
        }

        const hex = RGBAToHex(currentColor);
        const opaqueHex = hex.substring(0, 7);

        colorMarker.style.color = opaqueHex;
        alphaMarker.parentNode.style.color = opaqueHex;
        alphaMarker.style.color = hex;
        colorPreview.style.color = hex;

        // Force repaint the color and alpha gradients as a workaround for a Google Chrome bug
        colorArea.style.display = "none";
        colorArea.offsetHeight;
        colorArea.style.display = "";
        alphaMarker.nextElementSibling.style.display = "none";
        alphaMarker.nextElementSibling.offsetHeight;
        alphaMarker.nextElementSibling.style.display = "";

        if (format === "mixed") {
            format = currentColor.a === 1 ? "hex" : "rgb";
        } else if (format === "auto") {
            format = currentFormat;
        }

        switch (format) {
            case "hex":
                colorValue.value = hex;
                break;
            case "rgb":
                colorValue.value = RGBAToStr(currentColor);
                break;
            case "hsl":
                colorValue.value = HSLAToStr(HSVAtoHSLA(currentColor));
                break;
        }

        // Select the current format in the format switcher
        document.querySelector(`.clr-format [value="${format}"]`).checked = true;
    }

    /**
     * Set the hue when its slider is moved.
     */
    function setHue() {
        const hue = hueSlider.value * 1;
        const x = colorMarker.style.left.replace("px", "") * 1;
        const y = colorMarker.style.top.replace("px", "") * 1;

        picker.style.color = `hsl(${hue}, 100%, 50%)`;
        hueMarker.style.left = `${(hue / 360) * 100}%`;

        setColorAtPosition(x, y);
    }

    /**
     * Set the alpha when its slider is moved.
     */
    function setAlpha() {
        const alpha = alphaSlider.value / 100;

        alphaMarker.style.left = `${alpha * 100}%`;
        updateColor({
            a: alpha,
        });
        pickColor();
    }

    /**
     * Convert HSVA to RGBA.
     * @param {object} hsva Hue, saturation, value and alpha values.
     * @return {object} Red, green, blue and alpha values.
     */
    function HSVAtoRGBA(hsva) {
        const saturation = hsva.s / 100;
        const value = hsva.v / 100;
        let chroma = saturation * value;
        let hueBy60 = hsva.h / 60;
        let x = chroma * (1 - Math.abs((hueBy60 % 2) - 1));
        let m = value - chroma;

        chroma = chroma + m;
        x = x + m;
        m = m;

        const index = Math.floor(hueBy60) % 6;
        const red = [chroma, x, m, m, x, chroma][index];
        const green = [x, chroma, chroma, x, m, m][index];
        const blue = [m, m, x, chroma, chroma, x][index];

        return {
            r: Math.round(red * 255),
            g: Math.round(green * 255),
            b: Math.round(blue * 255),
            a: hsva.a,
        };
    }

    /**
     * Convert HSVA to HSLA.
     * @param {object} hsva Hue, saturation, value and alpha values.
     * @return {object} Hue, saturation, lightness and alpha values.
     */
    function HSVAtoHSLA(hsva) {
        const value = hsva.v / 100;
        const lightness = value * (1 - hsva.s / 100 / 2);
        let saturation;

        if (lightness > 0 && lightness < 1) {
            saturation = Math.round(
                ((value - lightness) / Math.min(lightness, 1 - lightness)) * 100
            );
        }

        return {
            h: hsva.h,
            s: saturation || 0,
            l: Math.round(lightness * 100),
            a: hsva.a,
        };
    }

    /**
     * Convert RGBA to HSVA.
     * @param {object} rgba Red, green, blue and alpha values.
     * @return {object} Hue, saturation, value and alpha values.
     */
    function RGBAtoHSVA(rgba) {
        const red = rgba.r / 255;
        const green = rgba.g / 255;
        const blue = rgba.b / 255;
        const xmax = Math.max(red, green, blue);
        const xmin = Math.min(red, green, blue);
        const chroma = xmax - xmin;
        const value = xmax;
        let hue = 0;
        let saturation = 0;

        if (chroma) {
            if (xmax === red) {
                hue = (green - blue) / chroma;
            }
            if (xmax === green) {
                hue = 2 + (blue - red) / chroma;
            }
            if (xmax === blue) {
                hue = 4 + (red - green) / chroma;
            }
            if (xmax) {
                saturation = chroma / xmax;
            }
        }

        hue = Math.floor(hue * 60);

        return {
            h: hue < 0 ? hue + 360 : hue,
            s: Math.round(saturation * 100),
            v: Math.round(value * 100),
            a: rgba.a,
        };
    }

    /**
     * Parse a string to RGBA.
     * @param {string} str String representing a color.
     * @return {object} Red, green, blue and alpha values.
     */
    function strToRGBA(str) {
        const regex =
            /^((rgba)|rgb)[\D]+([\d.]+)[\D]+([\d.]+)[\D]+([\d.]+)[\D]*?([\d.]+|$)/i;
        let match, rgba;

        // Default to black for invalid color strings
        ctx.fillStyle = "#000";

        // Use canvas to convert the string to a valid color string
        ctx.fillStyle = str;
        match = regex.exec(ctx.fillStyle);

        if (match) {
            rgba = {
                r: match[3] * 1,
                g: match[4] * 1,
                b: match[5] * 1,
                a: match[6] * 1,
            };
        } else {
            match = ctx.fillStyle
                .replace("#", "")
                .match(/.{2}/g)
                .map((h) => parseInt(h, 16));
            rgba = {
                r: match[0],
                g: match[1],
                b: match[2],
                a: 1,
            };
        }

        return rgba;
    }

    /**
     * Convert RGBA to Hex.
     * @param {object} rgba Red, green, blue and alpha values.
     * @return {string} Hex color string.
     */
    function RGBAToHex(rgba) {
        let R = rgba.r.toString(16);
        let G = rgba.g.toString(16);
        let B = rgba.b.toString(16);
        let A = "";

        if (rgba.r < 16) {
            R = "0" + R;
        }

        if (rgba.g < 16) {
            G = "0" + G;
        }

        if (rgba.b < 16) {
            B = "0" + B;
        }

        if (settings.alpha && rgba.a < 1) {
            const alpha = (rgba.a * 255) | 0;
            A = alpha.toString(16);

            if (alpha < 16) {
                A = "0" + A;
            }
        }

        return "#" + R + G + B + A;
    }

    /**
     * Convert RGBA values to a CSS rgb/rgba string.
     * @param {object} rgba Red, green, blue and alpha values.
     * @return {string} CSS color string.
     */
    function RGBAToStr(rgba) {
        if (!settings.alpha || rgba.a === 1) {
            return `rgb(${rgba.r}, ${rgba.g}, ${rgba.b})`;
        } else {
            return `rgba(${rgba.r}, ${rgba.g}, ${rgba.b}, ${rgba.a})`;
        }
    }

    /**
     * Convert HSLA values to a CSS hsl/hsla string.
     * @param {object} hsla Hue, saturation, lightness and alpha values.
     * @return {string} CSS color string.
     */
    function HSLAToStr(hsla) {
        if (!settings.alpha || hsla.a === 1) {
            return `hsl(${hsla.h}, ${hsla.s}%, ${hsla.l}%)`;
        } else {
            return `hsla(${hsla.h}, ${hsla.s}%, ${hsla.l}%, ${hsla.a})`;
        }
    }

    /**
     * Init the color picker.
     */
    //Custom header colour
    function init() {
        // Render the UI
        picker = document.createElement("div");
        picker.setAttribute("id", "clr-picker");
        picker.className = "clr-picker";
        picker.innerHTML =
            `<input id="clr-color-value" class="clr-color" type="text" value="" aria-label="${settings.a11y.input}">` +
            `<div id="clr-color-area" class="clr-gradient" role="application" aria-label="${settings.a11y.instruction}">` +
            '<div id="clr-color-marker" class="clr-marker" tabindex="0"></div>' +
            "</div>" +
            '<div class="clr-hue">' +
            `<input id="clr-hue-slider" type="range" min="0" max="360" step="1" aria-label="${settings.a11y.hueSlider}">` +
            '<div id="clr-hue-marker"></div>' +
            "</div>" +
            '<div class="clr-alpha">' +
            `<input id="clr-alpha-slider" type="range" min="0" max="100" step="1" aria-label="${settings.a11y.alphaSlider}">` +
            '<div id="clr-alpha-marker"></div>' +
            "<span></span>" +
            "</div>" +
            '<div id="clr-format" class="clr-format">' +
            '<fieldset class="clr-segmented">' +
            `<legend>${settings.a11y.format}</legend>` +
            '<input id="clr-f1" type="radio" name="clr-format" value="hex">' +
            '<label for="clr-f1">Hex</label>' +
            '<input id="clr-f2" type="radio" name="clr-format" value="rgb">' +
            '<label for="clr-f2">RGB</label>' +
            '<input id="clr-f3" type="radio" name="clr-format" value="hsl">' +
            '<label for="clr-f3">HSL</label>' +
            "<span></span>" +
            "</fieldset>" +
            "</div>" +
            '<div id="clr-swatches" class="clr-swatches"></div>' +
            `<button id="clr-clear" class="clr-clear">${settings.clearButton.label}</button>` +
            `<button id="clr-color-preview" class="clr-preview" aria-label="${settings.a11y.close}"></button>` +
            `<span id="clr-open-label" hidden>${settings.a11y.open}</span>` +
            `<span id="clr-swatch-label" hidden>${settings.a11y.swatch}</span>`;

        // Append the color picker to the DOM
        document.body.appendChild(picker);

        // Reference the UI elements
        colorArea = getEl("clr-color-area");
        colorMarker = getEl("clr-color-marker");
        clearButton = getEl("clr-clear");
        colorPreview = getEl("clr-color-preview");
        colorValue = getEl("clr-color-value");
        hueSlider = getEl("clr-hue-slider");
        hueMarker = getEl("clr-hue-marker");
        alphaSlider = getEl("clr-alpha-slider");
        alphaMarker = getEl("clr-alpha-marker");

        // Bind the picker to the default selector
        bindFields(settings.el);
        wrapFields(settings.el);

        addListener(picker, "mousedown", (event) => {
            picker.classList.remove("clr-keyboard-nav");
            event.stopPropagation();
        });

        addListener(colorArea, "mousedown", (event) => {
            addListener(document, "mousemove", moveMarker);
        });

        addListener(colorArea, "touchstart", (event) => {
            document.addEventListener("touchmove", moveMarker, {
                passive: false,
            });
        });

        addListener(colorMarker, "mousedown", (event) => {
            addListener(document, "mousemove", moveMarker);
        });

        addListener(colorMarker, "touchstart", (event) => {
            document.addEventListener("touchmove", moveMarker, {
                passive: false,
            });
        });

        addListener(colorValue, "change", (event) => {
            setColorFromStr(colorValue.value);
            pickColor();
        });

        addListener(clearButton, "click", (event) => {
            pickColor("");
            closePicker();
        });

        addListener(colorPreview, "click", (event) => {
            pickColor();
            closePicker();
        });

        addListener(document, "click", ".clr-format input", (event) => {
            currentFormat = event.target.value;
            updateColor();
            pickColor();
        });

        addListener(picker, "click", ".clr-swatches button", (event) => {
            setColorFromStr(event.target.textContent);
            pickColor();
        });

        addListener(document, "mouseup", (event) => {
            document.removeEventListener("mousemove", moveMarker);
        });

        addListener(document, "touchend", (event) => {
            document.removeEventListener("touchmove", moveMarker);
        });

        addListener(document, "mousedown", (event) => {
            picker.classList.remove("clr-keyboard-nav");
            closePicker();
        });

        addListener(document, "keydown", (event) => {
            if (event.key === "Escape") {
                closePicker(true);
            } else if (event.key === "Tab") {
                picker.classList.add("clr-keyboard-nav");
            }
        });

        addListener(document, "click", ".clr-field button", (event) => {
            event.target.nextElementSibling.dispatchEvent(
                new Event("click", {
                    bubbles: true,
                })
            );
        });

        addListener(colorMarker, "keydown", (event) => {
            const movements = {
                ArrowUp: [0, -1],
                ArrowDown: [0, 1],
                ArrowLeft: [-1, 0],
                ArrowRight: [1, 0],
            };

            if (Object.keys(movements).indexOf(event.key) !== -1) {
                moveMarkerOnKeydown(...movements[event.key]);
                event.preventDefault();
            }
        });

        addListener(colorArea, "click", moveMarker);
        addListener(hueSlider, "input", setHue);
        addListener(alphaSlider, "input", setAlpha);
    }

    /**
     * Shortcut for getElementById to optimize the minified JS.
     * @param {string} id The element id.
     * @return {object} The DOM element with the provided id.
     */
    function getEl(id) {
        return document.getElementById(id);
    }

    /**
     * Shortcut for addEventListener to optimize the minified JS.
     * @param {object} context The context to which the listener is attached.
     * @param {string} type Event type.
     * @param {(string|function)} selector Event target if delegation is used, event handler if not.
     * @param {function} [fn] Event handler if delegation is used.
     */
    function addListener(context, type, selector, fn) {
        const matches =
            Element.prototype.matches || Element.prototype.msMatchesSelector;

        // Delegate event to the target of the selector
        if (typeof selector === "string") {
            context.addEventListener(type, (event) => {
                if (matches.call(event.target, selector)) {
                    fn.call(event.target, event);
                }
            });

            // If the selector is not a string then it's a function
            // in which case we need regular event listener
        } else {
            fn = selector;
            context.addEventListener(type, fn);
        }
    }

    /**
     * Call a function only when the DOM is ready.
     * @param {function} fn The function to call.
     * @param {array} args Arguments to pass to the function.
     */
    function DOMReady(fn, args) {
        args = args !== undefined ? args : [];

        if (document.readyState !== "loading") {
            fn(...args);
        } else {
            document.addEventListener("DOMContentLoaded", () => {
                fn(...args);
            });
        }
    }

    // Polyfill for Nodelist.forEach
    if (
        NodeList !== undefined &&
        NodeList.prototype &&
        !NodeList.prototype.forEach
    ) {
        NodeList.prototype.forEach = Array.prototype.forEach;
    }

    // Expose the color picker to the global scope
    window.Coloris = (() => {
        const methods = {
            set: configure,
            wrap: wrapFields,
            close: closePicker,
        };

        function Coloris(options) {
            DOMReady(() => {
                if (options) {
                    if (typeof options === "string") {
                        bindFields(options);
                    } else {
                        configure(options);
                    }
                }
            });
        }

        for (const key in methods) {
            Coloris[key] = (...args) => {
                DOMReady(methods[key], args);
            };
        }

        return Coloris;
    })();

    // Init the color picker when the DOM is ready
    DOMReady(init);
})(window, document, Math);
Coloris({
    el: ".coloris",
});


$('.get_blog_category').select2({
    ajax: {
        url: base_url + 'admin/blogs/get_blog_category',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term // search term
            }
        },
        processResults: function (response) {
            return {
                results: response
            }
        },
        cache: true
    },
    minimumInputLength: 1,
    theme: 'bootstrap4',
    placeholder: 'Type to search blog categories'
})

//forgot password
$(document).ready(function () {
    var telInput = $("#forgot_password_number");

    // Set defaultCountry before calling intlTelInput
    telInput.intlTelInput({
        allowExtensions: true,
        formatOnDisplay: true,
        autoFormat: true,
        autoHideDialCode: true,
        autoPlaceholder: true,
        defaultCountry: "in",
        ipinfoToken: "yolo",
        nationalMode: false,
        numberType: "MOBILE",
        preferredCountries: ["in", "ae", "qa", "om", "bh", "kw", "ma"],
        preventInvalidNumbers: true,
        separateDialCode: true,
        geoIpLookup: function (callback) {
            $.get("https://ipinfo.io", function () { }, "jsonp").always(function (resp) {
                var countryCode = (resp && resp.country) ? resp.country : "";
                callback(countryCode);
            });
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"
    });
});
$(document).ready(function () {
    var telInput = $("#seller_mobile");

    // Set defaultCountry before calling intlTelInput
    telInput.intlTelInput({
        allowExtensions: true,
        formatOnDisplay: true,
        autoFormat: true,
        autoHideDialCode: true,
        autoPlaceholder: true,
        defaultCountry: "in",
        ipinfoToken: "yolo",
        nationalMode: false,
        numberType: "MOBILE",
        preferredCountries: ["in", "ae", "qa", "om", "bh", "kw", "ma"],
        preventInvalidNumbers: true,
        separateDialCode: true,
        geoIpLookup: function (callback) {
            $.get("https://ipinfo.io", function () { }, "jsonp").always(function (resp) {
                var countryCode = (resp && resp.country) ? resp.country : "";
                callback(countryCode);
            });
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"
    });
});
$(document).ready(function () {
    var telInput = $("#delivery_boy_mobile");

    // Set defaultCountry before calling intlTelInput
    telInput.intlTelInput({
        allowExtensions: true,
        formatOnDisplay: true,
        autoFormat: true,
        autoHideDialCode: true,
        autoPlaceholder: true,
        defaultCountry: "in",
        ipinfoToken: "yolo",
        nationalMode: false,
        numberType: "MOBILE",
        preferredCountries: ["in", "ae", "qa", "om", "bh", "kw", "ma"],
        preventInvalidNumbers: true,
        separateDialCode: true,
        geoIpLookup: function (callback) {
            $.get("https://ipinfo.io", function () { }, "jsonp").always(function (resp) {
                var countryCode = (resp && resp.country) ? resp.country : "";
                callback(countryCode);
            });
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"
    });
});

$(document).on('click', "#forgot_password_link", function (e) {
    e.preventDefault();
    $('.auth-modal').find('header a').removeClass('active');
    $('#forgot_password_div').removeClass('hide');
    if ($('#recaptcha-container-2').length) {
        $('#recaptcha-container-2').html('');
        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container-2');
        window.recaptchaVerifier.render().then(function (widgetId) {
            grecaptcha.reset(widgetId);
        });
    }
    var telInput = $("#forgot_password_number");

    // Set defaultCountry before calling intlTelInput
    telInput.intlTelInput({
        allowExtensions: true,
        formatOnDisplay: true,
        autoFormat: true,
        autoHideDialCode: true,
        autoPlaceholder: true,
        defaultCountry: "in",
        ipinfoToken: "yolo",
        nationalMode: false,
        numberType: "MOBILE",
        preferredCountries: ["in", "ae", "qa", "om", "bh", "kw", "ma"],
        preventInvalidNumbers: true,
        separateDialCode: true,
        geoIpLookup: function (callback) {
            $.get("https://ipinfo.io", function () { }, "jsonp").always(function (resp) {
                var countryCode = (resp && resp.country) ? resp.country : "";
                callback(countryCode);
            });
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"
    });

});


function is_user_exist(phone_number = '') {
    if (phone_number == '') {
        var phoneNumber = $('#phone-number').val();
    } else {
        var phoneNumber = phone_number;
    }
    var forgot_password_value = $('#forget_password_val').val();
    var country_code = $(".selected-dial-code").text();

    var from_seller = $('#from_seller').val();
    var from_admin = $('#from_admin').val();
    var from_delivery_boy = $('#from_delivery_boy').val();

    var response;
    $.ajax({
        type: 'POST',
        async: false,
        url: base_url + 'auth/verify_user',
        data: {
            mobile: phoneNumber,
            country_code: country_code,
            [csrfName]: csrfHash,
            forget_password_val: forgot_password_value,
            from_seller: from_seller,
            from_admin: from_admin,
            from_delivery_boy: from_delivery_boy
        },
        dataType: 'json',
        success: function (result) {
            csrfName = result['csrfName'];
            csrfHash = result['csrfHash'];
            response = result
        }
    });
    return response;
}


$(document).ready(function () {

    if ($('#recaptcha-container-2').length) {
        $('#recaptcha-container-2').html('');
        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container-2');
        window.recaptchaVerifier.render().then(function (widgetId) {
            grecaptcha.reset(widgetId);
        });
    }
    var telInput = $("#forgot_password_number"),
        errorMsg = $("#error-msg"),
        validMsg = $("#valid-msg");

    // initialise plugin
    telInput.intlTelInput({

        allowExtensions: true,
        formatOnDisplay: true,
        autoFormat: true,
        autoHideDialCode: true,
        autoPlaceholder: true,
        defaultCountry: "in",
        ipinfoToken: "yolo",

        nationalMode: false,
        numberType: "MOBILE",
        preferredCountries: ['in', 'ae', 'qa', 'om', 'bh', 'kw', 'ma'],
        preventInvalidNumbers: true,
        separateDialCode: true,
        initialCountry: "auto",
        geoIpLookup: function (callback) {
            $.get("https://ipinfo.io", function () { }, "jsonp").always(function (resp) {
                var countryCode = (resp && resp.country) ? resp.country : "";
                callback(countryCode);
            });
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"
    });

    var reset = function () {
        telInput.removeClass("error");
        errorMsg.addClass("hide");
        validMsg.addClass("hide");
    };
});

$(document).on('submit', '#send_forgot_password_otp_form', function (e) {
    e.preventDefault();
    var send_otp_btn = $('#forgot_password_send_otp_btn').html();
    $('#forgot_password_send_otp_btn').html('Please Wait...').attr('disabled', true);
    var phoneNumber = $('.selected-dial-code').html() + $('#forgot_password_number').val();
    var appVerifier = window.recaptchaVerifier;
    var formdata = new FormData(this);


    var response = is_user_exist($('#forgot_password_number').val());
    if (response.error == true) {
        $('#forgot_password_send_otp_btn').html(send_otp_btn).attr('disabled', true);
        iziToast.error({
            message: response.message
        });
        setTimeout(function () {
            window.location.reload();
        }, 2000)

    } else {
        if (auth_settings == "firebase") {
            firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier).then(function (confirmationResult) {
                resetRecaptcha();
                $('#verify_forgot_password_otp_form').removeClass('d-none');
                $('#send_forgot_password_otp_form').hide();
                $('#forgot_pass_error_box').html(response.message);
                $('#forgot_password_send_otp_btn').html(send_otp_btn).attr('disabled', false);
                $(document).on('submit', '#verify_forgot_password_otp_form', function (e) {
                    e.preventDefault();
                    var reset_pass_btn_html = $('#reset_password_submit_btn').html();
                    var code = $('#forgot_password_otp').val();
                    var formdata2 = new FormData(this);
                    var url = base_url + "admin/home/reset-password";
                    $('#reset_password_submit_btn').html('Please Wait...').attr('disabled', true);
                    confirmationResult.confirm(code).then(function (result) {
                        formdata2.append(csrfName, csrfHash);
                        formdata2.append('mobile', $('#forgot_password_number').val());
                        formdata2.append('password', $('#password').val());
                        $.ajax({
                            type: 'POST',
                            url: url,
                            data: formdata2,
                            processData: false,
                            contentType: false,
                            cache: false,
                            dataType: 'json',
                            beforeSend: function () {
                                $('#reset_password_submit_btn').html('Please Wait...').attr('disabled', true);
                            },
                            success: function (result) {
                                csrfName = result.csrfName;
                                csrfHash = result.csrfHash;
                                $('#reset_password_submit_btn').html(reset_pass_btn_html).attr('disabled', false);
                                $("#set_password_error_box").html(result.message).show();
                                if (result.error == false) {
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 2000)
                                } else {
                                    $('#reset_password_submit_btn').html(reset_pass_btn_html).attr('disabled', false);
                                    setTimeout(function () {
                                        iziToast.error({
                                            message: e.message,
                                        });
                                    }, 2000)
                                }
                            }
                        });
                    }).catch(function (error) {
                        $('#reset_password_submit_btn').html(reset_pass_btn_html).attr('disabled', false);
                        $("#set_password_error_box").html("Invalid OTP. Please Enter Valid OTP").show();
                    });
                });
            }).catch(function (error) {
                $("#forgot_pass_error_box").html(error.message).show();
                $('#forgot_password_send_otp_btn').html(send_otp_btn).attr('disabled', false);
                resetRecaptcha();
            });

        }
    }
})
if (auth_settings == "sms") {
    $(document).on("click", ".forgot-send-otp-btn", function (e) {
        e.preventDefault();
        var forgot_password_number = $('#forgot_password_number').val();
        var forget_password_val = $('#forget_password_val').val();
        var country_code = $(".selected-dial-code").text();

        $.ajax({
            type: "POST",
            async: !1,
            url: base_url + "auth/verify_user",
            data: {
                mobile: forgot_password_number,
                country_code: country_code,
                forget_password_val: forget_password_val,
                [csrfName]: csrfHash
            },
            dataType: "json",
            success: function (e) {
                if (e.error == false) {
                    csrfName = e.csrfName,
                        csrfHash = e.csrfHash,
                        resetRecaptcha(),
                        $('#verify_forgot_password_otp_form').removeClass('d-none');
                    $('#send_forgot_password_otp_form').hide();

                    $("#verify-otp-form").removeClass("d-none");

                } else {
                    iziToast.error({
                        message: e.message,
                    });
                }
            }
        })
    });

    $(document).on('submit', '#verify_forgot_password_otp_form', function (e) {
        e.preventDefault();
        var reset_pass_btn_html = $('#reset_password_submit_btn').html();
        var code = $('#forgot_password_otp').val();
        var formdata = new FormData(this);
        var url = base_url + "admin/home/reset-password";
        $('#reset_password_submit_btn').html('Please Wait...').attr('disabled', true);
        formdata.append(csrfName, csrfHash);
        formdata.append('mobile', $('#forgot_password_number').val());
        $.ajax({
            type: 'POST',
            url: url,
            data: formdata,
            processData: false,
            contentType: false,
            cache: false,
            dataType: 'json',
            beforeSend: function () {
                $('#reset_password_submit_btn').html('Please Wait...').attr('disabled', true);
            },
            success: function (result) {
                csrfName = result.csrfName;
                csrfHash = result.csrfHash;
                $('#reset_password_submit_btn').html(reset_pass_btn_html).attr('disabled', false);
                $("#set_password_error_box").html(result.message).show();
                if (result.error == false) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000)
                }
            }
        });
    });

}

function resetRecaptcha() {
    return window.recaptchaVerifier.render().then(function (widgetId) {
        grecaptcha.reset(widgetId);
    });
}
$(".auth-modal").on('click', 'header a', function (event) {
    event.preventDefault();
    window.signingIn = true;
    var index = $(this).index();
    $(this).addClass('active').siblings('a').removeClass('active');
    $(this).parents("div").find("section").eq(index).removeClass('hide').siblings('section').addClass('hide');

    if ($(this).index() === 0) {
        $(".auth-modal .iziModal-content .icon-close").css('background', '#ddd');
    } else {
        $(".auth-modal .iziModal-content .icon-close").attr('style', '');
    }
});

function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    var cls = document.getElementsByClassName('print-section');
    document.body.innerHTML = printContents;
    Array.prototype.forEach.call(cls, (item) => item.setAttribute("id", 'section-to-print'));
    setTimeout(function () { window.print(); }, 600);
    setTimeout(() => { document.body.innerHTML = originalContents; }, 1000);
}
$('.check_create_order').on('change', function (e) {
    e.preventDefault()
    if ($(this).is(':checked')) {
        $('.create_shiprocket_order').attr('disabled', false)
        var pickup_location = $(this).attr('id');
        var seller_id = $(this).data('id');
        $('#pickup_location').attr('value', pickup_location);
        $('input[type=hidden][name="shiprocket_seller_id"]').val(seller_id);
    } else {
        $('.create_shiprocket_order').attr('disabled', true)
    }
})

$(document).on('submit', '#shiprocket_order_parcel_form', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var fromAdmin = $('#fromadmin').val();
    var fromSeller = $('#fromseller').val();

    formData.append(csrfName, csrfHash);
    if (fromSeller != 'undefined' && fromSeller == 1) {
        var url = base_url + 'seller/orders/create_shiprocket_order';
    }
    if (fromAdmin != 'undefined' && fromAdmin == 1) {
        var url = base_url + 'admin/orders/create_shiprocket_order';
    }
    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            csrfName = result['csrfName'];
            csrfHash = result['csrfHash'];
            if (result.error == false) {
                iziToast.success({
                    message: result.message,
                });
                $('#consignment_status_modal').modal("hide");
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                iziToast.error({
                    message: result.message,
                });
            }
        }
    });
});

$('.generate_awb').on('click', function (e) {
    e.preventDefault()

    var shipment_id = $(this).attr('id')
    var fromSeller = $(this).data('fromseller');
    var fromAdmin = $(this).data('fromadmin');
    if (fromSeller != 'undefined' && fromSeller == 1) {
        var url = base_url + 'seller/orders/generate_awb';
    }
    if (fromAdmin != 'undefined' && fromAdmin == 1) {
        var url = base_url + 'admin/orders/generate_awb';
    }
    Swal.fire({
        title: 'Are You Sure !',
        text: 'you want to generate AWb!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, generate AWB!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        shipment_id: shipment_id,
                        [csrfName]: csrfHash
                    },
                    dataType: 'json'
                })
                    .done(function (result, textStatus) {
                        if (result['error'] == false) {
                            Swal.fire('AWB Generated!', result['message'], 'success')
                            location.reload()
                        } else {
                            Swal.fire('Oops...', result['message'], 'warning')
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error')
                    })
            })
        },
        allowOutsideClick: false
    })
})

$('.send_pickup_request').on('click', function (e) {
    e.preventDefault()
    var shipment_id = $(this).attr('name')
    var fromSeller = $(this).data('fromseller');
    var fromAdmin = $(this).data('fromadmin');
    if (fromSeller != 'undefined' && fromSeller == 1) {
        var url = base_url + 'seller/orders/send_pickup_request';
    }
    if (fromAdmin != 'undefined' && fromAdmin == 1) {
        var url = base_url + 'admin/orders/send_pickup_request';
    }
    Swal.fire({
        title: 'Are You Sure !',
        text: 'you want to send pickup request!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, send request!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        shipment_id: shipment_id,
                        [csrfName]: csrfHash
                    },
                    dataType: 'json'
                })
                    .done(function (result, textStatus) {
                        if (result['error'] == false) {
                            Swal.fire('Request send!', result['message'], 'success')
                            location.reload()
                        } else {
                            Swal.fire('Oops...', result['message'], 'warning')
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error')
                    })
            })
        },
        allowOutsideClick: false
    })
})

$('.generate_label').on('click', function (e) {
    e.preventDefault()
    var shipment_id = $(this).attr('name')
    var fromSeller = $(this).data('fromseller');
    var fromAdmin = $(this).data('fromadmin');
    if (fromSeller != 'undefined' && fromSeller == 1) {
        var url = base_url + 'seller/orders/generate_label';
    }
    if (fromAdmin != 'undefined' && fromAdmin == 1) {
        var url = base_url + 'admin/orders/generate_label';
    }
    Swal.fire({
        title: 'Are You Sure !',
        text: 'you want to generate label!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, generate label!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        shipment_id: shipment_id,
                        [csrfName]: csrfHash
                    },
                    dataType: 'json'
                })
                    .done(function (result, textStatus) {
                        if (result['error'] == false) {
                            Swal.fire('Label generated!', result['message'], 'success')
                            location.reload()
                        } else {
                            Swal.fire('Oops...', result['message'], 'warning')
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error')
                    })
            })
        },
        allowOutsideClick: false
    })
})

$('.generate_invoice').on('click', function (e) {
    e.preventDefault()
    var order_id = $(this).attr('name')
    var fromSeller = $(this).data('fromseller');
    var fromAdmin = $(this).data('fromadmin');
    if (fromSeller != 'undefined' && fromSeller == 1) {
        var url = base_url + 'seller/orders/generate_invoice';
    }
    if (fromAdmin != 'undefined' && fromAdmin == 1) {
        var url = base_url + 'admin/orders/generate_invoice';
    }
    Swal.fire({
        title: 'Are You Sure !',
        text: 'you want to generate invoice!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, generate invoice!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: {
                        order_id: order_id,
                        [csrfName]: csrfHash
                    },
                    dataType: 'json'
                })
                    .done(function (result, textStatus) {
                        if (result['error'] == false) {
                            Swal.fire('Invoice generated!', result['message'], 'success')
                            location.reload()
                        } else {
                            Swal.fire('Oops...', result['message'], 'warning')
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error')
                    })
            })
        },
        allowOutsideClick: false
    })
})

$('.cancel_shiprocket_order').on('click', function (e) {
    e.preventDefault()
    let shiprocket_order_id = $('#shiprocket_order_id').val()
    if (shiprocket_order_id == undefined || shiprocket_order_id == null || shiprocket_order_id == "") {
        iziToast.error({
            message: "Shiprocket Order Id Not Found",
        });
        return
    }

    Swal.fire({
        title: 'Are You Sure !',
        text: 'you want to cancel order!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, cancel it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: base_url + from + '/orders/cancel_shiprocket_order',
                    data: {
                        shiprocket_order_id: shiprocket_order_id,
                        [csrfName]: csrfHash
                    },
                    dataType: 'json'
                })
                    .done(function (result, textStatus) {
                        if (result['error'] == false) {
                            Swal.fire('Order cancelled !', result['message'], 'success')
                            location.reload()
                        } else {
                            Swal.fire('Oops...', result['message'], 'warning')
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error')
                    })
            })
        },
        allowOutsideClick: false
    })
})

$('input[type=radio][name=status]').change(function () {

    var status = $('input[type=radio][name="status"]:checked').val();
    if (status == 1) {
        $('#return_request_delivery_by').removeClass('d-none');
    } else {
        $('#return_request_delivery_by').addClass('d-none');

    }

});

$(document).on('click', '#edit_return_request', function () {
    var order_item_id = $(this).data('id');
    $.ajax({
        type: 'GET',
        url: base_url + 'admin/return-request/get_seller_id/' + order_item_id,
        dataType: 'json',

        success: function (result) {

            csrfName = result['csrfName'];
            csrfHash = result['csrfHash'];
            var delivery_boy_id = $('#delivery_boy_id').val();

            // Append new options from the result data
            $.each(result.data, function (index, deliveryBoy) {
                var option = $('<option></option>')
                    .attr('value', deliveryBoy.id)
                    .text(deliveryBoy.name);

                // Set selected if the delivery boy matches the delivery_boy_id
                if (deliveryBoy.id == delivery_boy_id) {
                    option.attr('selected', 'selected');
                }

                $('#deliver_by').append(option);
            });

        }
    });
});

// When the modal is closed
$('#request_rating_modal').on('hide.bs.modal', function () {
    // Clear the select options except for the first one
    $('#deliver_by').val(''); // Set the select to default empty value
    $('#deliver_by').find('option:not(:first)').remove(); // Remove all options except the placeholder
});

// 23.Whatsapp status

$(document).ready(function () {
    const $whatsappStatus = $('#whatsapp_status');
    const $whatsappNumberDiv = $('#whatsapp_number_div');

    // Function to toggle display based on checkbox status
    function toggleWhatsappNumberDiv() {
        $whatsappNumberDiv.toggleClass('d-none', !$whatsappStatus.is(':checked'));
    }

    // Initial display based on checkbox status
    toggleWhatsappNumberDiv();

    // Event listener to toggle display on checkbox change
    $whatsappStatus.change(toggleWhatsappNumberDiv);
});

var sms_data = $("#sms_gateway_data").val() ? $("#sms_gateway_data").val() : [];


if (sms_data.length != 0) {
    var sms_data = JSON.parse(sms_data);

}


// body data
$(document).on('click', '#add_sms_body', function (e) {
    e.preventDefault();
    load_sms_body_section(cat_html, false);
});

function load_sms_body_section(cat_html, is_edit = false, body_keys = [], body_values = []) {
    var body_keys = sms_data.body_key;
    var body_values = sms_data.body_value;

    if (is_edit == true) {

        var html = ''; // Initialize the HTML

        if (Array.isArray(body_keys)) {
            for (var i = 0; i < body_keys.length; i++) {
                html += '<div class="form-group row key-value-pair">';
                html += '<div class="col-sm-5">';
                html += '<label for="body_key" class="form-label"> Key </label>';
                html += '<input type="text" class="form-control" placeholder="Enter Key" name="body_key[]" value="' + body_keys[i] + '" id="body_key">';
                html += '</div>';
                html += '<div class="col-sm-5">';
                html += '<label for="body_value" class="form-label"> Value </label>';
                html += '<input type="text" class="form-control" placeholder="Enter Value" name="body_value[]" value="' + body_values[i] + '" id="body_value">';
                html += '</div>';
                html += '<div class="col-sm-2">';
                html += '<button type="button" class="btn btn-tool remove_keyvalue_section"> <i class="text-danger far fa-times-circle fa-2x "></i> </button>';
                html += '</div>';
                html += '</div>';
            }
        }
    } else {
        var html = '<div class="form-group row key-value-pair">' +
            '<div class="col-sm-5">' +
            '<label for="body_key" class="form-label"> Key </label>' +
            '<input type="text" class="form-control"  placeholder="Enter Key" name="body_key[]"  value="" id="body_key">' +
            '</div>' +
            '<div class="col-sm-5">' +
            '<label for="body_value" class="form-label"> Value </label>' +
            '<input type="text" class="form-control"  placeholder="Enter Key" name="body_value[]"  value="" id="body_value">' +
            '</div>' +
            '<div class="col-sm-2"> ' +
            '<button type="button" class="btn btn-tool remove_keyvalue_section" > <i class="text-danger far fa-times-circle fa-2x "></i> </button>' +
            '</div>' +
            '</div>' +
            '</div>';
    }
    var test = $('#formdata_section').append(html);
}

$(document).on('click', '.remove_keyvalue_section', function () {
    $(this).closest('.row').remove();
});

// header data
$(document).on('click', '#add_sms_header', function (e) {
    e.preventDefault();
    load_sms_header_section(cat_html, false);
});

function load_sms_header_section(cat_html, is_edit = false, key_headers = [], value_headers = []) {

    var key_headers = sms_data.header_key;
    var value_headers = sms_data.header_value;
    if (is_edit == true) {

        var html = '';

        if (Array.isArray(key_headers)) {
            for (var i = 0; i < key_headers.length; i++) {
                html += '<div class="form-group row">';
                html += '<div class="col-sm-5">';
                html += '<label for="header_key" class="form-label"> Key </label>';
                html += '<input type="text" class="form-control" placeholder="Enter Key" name="header_key[]" value="' + key_headers[i] + '" id="header_key">';
                html += '</div>';
                html += '<div class="col-sm-5">';
                html += '<label for="header_value" class="form-label"> Value </label>';
                html += '<input type="text" class="form-control" placeholder="Enter Value" name="header_value[]" value="' + value_headers[i] + '" id="header_value">';
                html += '</div>';
                html += '<div class="col-sm-2">';
                html += '<button type="button" class="btn btn-tool remove_keyvalue_section"> <i class="text-danger far fa-times-circle fa-2x "></i> </button>';
                html += '</div>';
                html += '</div>';
            }
        }
    } else {
        var html = '<div class="form-group row">' +
            '<div class="col-sm-5">' +
            '<label for="header_key" class="form-label"> Key </label>' +
            '<input type="text" class="form-control"  placeholder="Enter Key" name="header_key[]"  value="" id="header_key">' +
            '</div>' +
            '<div class="col-sm-5">' +
            '<label for="header_value" class="form-label"> Value </label>' +
            '<input type="text" class="form-control"  placeholder="Enter value" name="header_value[]" id="header_value"  value="">' +
            '</div>' +
            '<div class="col-sm-2"> ' +
            '<button type="button" class="btn btn-tool remove_keyvalue_header_section" > <i class="text-danger far fa-times-circle fa-2x "></i> </button>' +
            '</div>' +
            '</div>' +
            '</div>';
    }
    $('#formdata_header_section').append(html);
}

$(document).on('click', '.remove_keyvalue_header_section', function () {
    $(this).closest('.row').remove();
});

// paramas data
$(document).on('click', '#add_sms_params', function (e) {
    e.preventDefault();
    load_sms_params_section(cat_html, false);
});

function load_sms_params_section(cat_html, is_edit = false, key_params = [], value_params = []) {

    var key_params = sms_data.params_key;
    var value_params = sms_data.params_value;
    var key = $().val();
    if (is_edit == true) {
        var html = '';


        if (Array.isArray(key_params)) {
            for (var i = 0; i < key_params.length; i++) {
                html += '<div class="form-group row">';
                html += '<div class="col-sm-5">';
                html += '<label for="params_key" class="form-label"> Key </label>';
                html += '<input type="text" class="form-control" placeholder="Enter Key" name="params_key[]" value="' + key_params[i] + '" id="params_key">';
                html += '</div>';
                html += '<div class="col-sm-5">';
                html += '<label for="params_value" class="form-label"> Value </label>';
                html += '<input type="text" class="form-control" placeholder="Enter Value" name="params_value[]" value="' + value_params[i] + '" id="params_value">';
                html += '</div>';
                html += '<div class="col-sm-2">';
                html += '<button type="button" class="btn btn-tool remove_keyvalue_section"> <i class="text-danger far fa-times-circle fa-2x "></i> </button>';
                html += '</div>';
                html += '</div>';
            }
        }
    } else {
        var html = '<div class="form-group row">' +
            '<div class="col-sm-5">' +
            '<label for="params_key" class="form-label"> Key </label>' +
            '<input type="text" class="form-control"  placeholder="Enter Key" name="params_key[]"  value="" id="params_key">' +
            '</div>' +
            '<div class="col-sm-5">' +
            '<label for="params_value" class="form-label"> Value </label>' +
            '<input type="text" class="form-control"  placeholder="Enter value" name="params_value[]" id="params_value"  value="">' +
            '</div>' +
            '<div class="col-sm-2"> ' +
            '<button type="button" class="btn btn-tool remove_keyvalue_paramas_section" > <i class="text-danger far fa-times-circle fa-2x "></i> </button>' +
            '</div>' +
            '</div>' +
            '</div>';
    }
    $('#formdata_params_section').append(html);
}

$(document).on('click', '.remove_keyvalue_paramas_section', function () {
    $(this).closest('.row').remove();
});

$(document).ready(function () {
    load_sms_header_section(cat_html, true, sms_data.header_key, sms_data.header_value);
    load_sms_body_section(cat_html, true, sms_data.body_key, sms_data.body_value);
    load_sms_params_section(cat_html, true, sms_data.params_key, sms_data.params_value)
});

$(document).ready(function () {
    $("#sms_gateway_submit").click(function (event) {
        event.preventDefault();

        var form = document.getElementById("smsgateway_setting_form"); // Get the form DOM element
        var formData = new FormData(form); // Initialize FormData object with form DOM element

        var csrfName = $('input[name="csrfname"]').val();
        var csrfHash = $('input[name="csrfhash"]').val();

        formData.append(csrfName, csrfHash);
        // return
        $.ajax({
            type: $(form).attr("method"),
            url: base_url + 'admin/Sms_gateway_settings/add_sms_data',
            data: formData,
            contentType: false, // Important: false prevents jQuery from setting Content-Type header
            processData: false,
            success: function (response) {
                var response = jQuery.parseJSON(response);
                csrfName = response.csrfName;
                csrfHash = response.csrfHash;

                if (response.error == false) {
                    iziToast.success({
                        message: response.message,
                    });
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                } else {
                    iziToast.error({
                        message: response.message,
                    });
                }
            },
        });
        return;
    });
});

$(document).ready(function () {
    // Define the function for handling authentication method
    function handleAuthenticationMethod() {
        var firebaseRadio = $('input[type=radio][id="firebaseRadio"]:checked').val();
        var smsRadio = $('input[type=radio][id="smsRadio"]:checked').val();

        if (firebaseRadio == 'firebase') {
            $('.firebase_config').removeClass('d-none');
            $('.sms_gateway').addClass('d-none');
        } else if (smsRadio == 'sms') {
            $('.sms_gateway').removeClass('d-none');
            $('.firebase_config').addClass('d-none');
        }
    }

    // Run the function on page load
    handleAuthenticationMethod();

    // Also run it on radio button change
    $('input[type=radio][name=authentication_method]').change(function () {
        handleAuthenticationMethod();
    });
});

$(document).ready(function () {
    $('#product-body-tab').on('click', function (event) {
        event.preventDefault();
        $('#product-text').addClass('show');
        $('#product-text').addClass('active');
        $('#product-formdata').addClass('show');
    });

    $('#product-header-tab').click(function (event) {
        event.preventDefault();
        if ($('#product-formdata').hasClass('show')) {
            $('#product-formdata').removeClass('active');
            $('#product-formdata').removeClass('show');
        } if ($('#product-text').hasClass('show')) {
            $('#product-text').removeClass('active');
            $('#product-text').removeClass('show');
        }

    });
    $('#product-params-tab').click(function (event) {
        event.preventDefault();
        if ($('#product-formdata').hasClass('show')) {
            $('#product-formdata').removeClass('active');
            $('#product-formdata').removeClass('show');
        } if ($('#product-text').hasClass('show')) {
            $('#product-text').removeClass('active');
            $('#product-text').removeClass('show');
        }

    });

});

function validateNumberInput(input) {
    // Remove any non-numeric characters from the input value
    input.value = input.value.replace(/\D/g, '');
}

function createHeader() {
    const username = document.getElementById("converterInputAccountSID").value;
    const password = document.getElementById("converterInputAuthToken").value;

    if (username && password) {
        const stringToEncode = `${username}:${password}`;
        document.getElementById("basicToken").innerText = `Authorization: Basic ${btoa(stringToEncode)}`;
    } else {
        // Handle the case where either username or password is empty
        alert("Please provide both account SID and Auth Token.");
    }
}

$('.add_delivery_boy').on('submit', function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append(csrfName, csrfHash);

    $.ajax({
        type: 'POST',
        url: base_url + "admin/delivery_boys/add_delivery_boy",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
            csrfName = response.csrfName,
                csrfHash = response.csrfHash
            if (response.error == false) {
                iziToast.success({
                    message: response.message,
                });
                setTimeout(function () {
                    location.reload();
                }, 1000);
            } else {
                iziToast.error({
                    message: response.message,
                });
            }
        }
    });
});

$('.add_slider_form').on('submit', function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append(csrfName, csrfHash);

    $.ajax({
        type: 'POST',
        url: base_url + "admin/slider/add_slider",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
            csrfName = response.csrfName,
                csrfHash = response.csrfHash
            if (response.error == false) {
                iziToast.success({
                    message: response.message,
                });
                setTimeout(function () {
                    location.reload();
                }, 1000);
            } else {
                iziToast.error({
                    message: response.message,
                });
                $('#submit_btn').attr('disabled', false).html('Save Slider');
            }
        }
    });
});

$('.add_offer_form').on('submit', function (e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append(csrfName, csrfHash);

    $.ajax({
        type: 'POST',
        url: base_url + "admin/offer/add_offer",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
            csrfName = response.csrfName,
                csrfHash = response.csrfHash
            if (response.error == false) {
                iziToast.success({
                    message: response.message,
                });
                setTimeout(function () {
                    location.reload();
                }, 1000);
            } else {
                iziToast.error({
                    message: response.message,
                });
                $('#submit_btn').attr('disabled', false).html('Save Offer');
            }
        }
    });
});
$('.add_promocode_form').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrfName, csrfHash);

    $.ajax({
        type: 'POST',
        url: base_url + "admin/promo_code/add_promo_code",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
            csrfName = response.csrfName,
                csrfHash = response.csrfHash
            if (response.error == false) {
                iziToast.success({
                    message: response.message,
                });
                setTimeout(function () {
                    location.reload();
                }, 1000);
            } else {
                iziToast.error({
                    message: response.message,
                });
                $('#submit_btn').attr('disabled', false).html('Add Promo Code');
            }
        }
    });
});
$('.add_return_reason_form').on('submit', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    formData.append(csrfName, csrfHash);

    $.ajax({
        type: 'POST',
        url: base_url + "admin/return_reasons/add_return_reasons",
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
            csrfName = response.csrfName,
                csrfHash = response.csrfHash
            if (response.error == false) {
                iziToast.success({
                    message: response.message,
                });
                setTimeout(function () {
                    location.reload();
                }, 1000);
            } else {
                iziToast.error({
                    message: response.message,
                });
                $('#submit_btn').attr('disabled', false).html('Add Promo Code');
            }
        }
    });
});

$(document).on('click', '.add_offer_btn', function (e) {
    e.preventDefault();
    $('.modal-title').text('Add Offer');
    $('.save_offer').text('Add Offer');

    $('#category_parent').val('').trigger('change');
    $('#offer_type').val('').trigger('change');
    $('#product_offer_id').val('').trigger('change');
    $('.edit_offer_upload_image_note').text('');
    $('.image-upload-section').addClass('d-none');
    $('.offer-categories').addClass('d-none');
    $('.offer-products').addClass('d-none');
    $('.offer-url').addClass('d-none');
})
$(document).on('click', '.add_slider_btn', function (e) {
    e.preventDefault();
    $('.modal-title').text('Add Slider');
    $('.save_slider').text('Add Slider');

    $('#category_parent').val('').trigger('change');
    $('.slider-categories').addClass('d-none');
    $('.slider-products').addClass('d-none');
    $('.slider-url').addClass('d-none');

    $('#slider_type').val('').trigger('change');
    $('.edit_slider_upload_image_note').text('');
    $('.image-upload-section').addClass('d-none');

})

$(document).on('click', '.add_promocode_btn', function (e) {
    $('.modal-title').text('Add Promo Code');
    $('.save_promocode').text('Add Promo Code');
    $('.edit_promo_upload_image_note').text('');
    $('.reset_promo_code').trigger('click');
});

$(document).on('click', '.add_return_reason_btn', function (e) {
    $('.modal-title').text('Add Reaturn Reason');
    $('.save_return_reason').text('Add Return Reason');
    $('.edit_promo_upload_image_note').text('');
    $('.reset_return_reason').trigger('click');
});

$(document).on('click', '.edit_slider', function (e) {
    e.preventDefault();
    var slider_id = $(this).data('id');
    var slider_url = $(this).attr('href');
    var urlParams = new URLSearchParams(slider_url.split('?')[1]);
    var edit_id = urlParams.get('slider_edit_id');
    $.ajax({
        type: 'POST',
        url: slider_url,
        data: {
            edit_id: edit_id,
            [csrfName]: csrfHash
        },
        dataType: 'json',
        success: function (response) {
            csrfName = response.csrfName,
                csrfHash = response.csrfHash
            $('.modal-title').text('Edit Slider');
            $('.save_slider').text('Update Slider');

            $('.img-fluid').attr('id', 'slider_uploaded_image');
            $('.image-upload-section').addClass('d-none');
            $('#slider_uploaded_image').attr('src', '');
            $('.image-upload-section').attr('src', '');
            response = response.fetched_data;
            $('#add_slider').val('');
            $('#slider_type').val(response[0].type);
            $('#edit_slider').val(response[0].id);
            if (response[0].type == "default") {
                $('.slider-url').addClass('d-none');
                $('.slider-products').addClass('d-none');
                $('.slider-categories').addClass('d-none');
                if (response[0].image != '') {
                    $('.image-upload-section').removeClass('d-none');
                    $('#slider_uploaded_image').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('.image-upload-section').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('#uploaded_slider_uploaded_image').val(response[0].image);
                } else {
                    $('.image-upload-section').addClass('d-none');
                }
            }
            if (response[0].type == "categories") {
                $('.slider-categories').removeClass('d-none');
                $('.slider-url').addClass('d-none');
                $('.slider-products').addClass('d-none');
                var typeID = response[0].type_id;
                // Update the Select2 element with the selected user
                $('#category_parent').val(typeID).trigger('change');
                if (response[0].image != '') {
                    $('.image-upload-section').removeClass('d-none');
                    $('#slider_uploaded_image').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('.image-upload-section').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('#uploaded_slider_uploaded_image').val(response[0].image);
                } else {
                    $('.image-upload-section').addClass('d-none');
                }
            }
            if (response[0].type == "products") {
                $('.slider-products').removeClass('d-none');
                $('.slider-url').addClass('d-none');
                $('.slider-categories').addClass('d-none');
                var typeId = response[0].type_id;
                // Find the option element with the matching value
                var $option = $('#product_select_id option[value="' + typeId + '"]');
                // If the option exists, update the Select2 value
                if ($option.length) {
                    $("#product_select_id").val(typeId).trigger('change');
                } else {
                    // Handle the case when the option doesn't exist
                    console.log("Option with value " + typeId + " not found in the select element.");
                }
                if (response[0].image != '') {
                    $('.image-upload-section').removeClass('d-none');
                    $('#slider_uploaded_image').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('.image-upload-section').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('#uploaded_slider_uploaded_image').val(response[0].image);
                } else {
                    $('.image-upload-section').addClass('d-none');
                }
            }
            if (response[0].type == "slider_url") {
                $('.slider-url').removeClass('d-none');
                $('.slider-products').addClass('d-none');
                $('.slider-categories').addClass('d-none');
                $('#slider_url_val').val(response[0].link);
                if (response[0].image != '') {
                    $('.image-upload-section').removeClass('d-none');
                    $('#slider_uploaded_image').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('.image-upload-section').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('#uploaded_slider_uploaded_image').val(response[0].image);
                } else {
                    $('.image-upload-section').addClass('d-none');
                }
            }
        }
    });
})
$(document).on('click', '.edit_offer', function (e) {
    e.preventDefault();
    var offer_url = $(this).attr('href');
    var urlParams = new URLSearchParams(offer_url.split('?')[1]);
    var edit_id = urlParams.get('edit_id');
    $.ajax({
        type: 'POST',
        url: offer_url,
        data: {
            edit_id: edit_id,
            [csrfName]: csrfHash
        },
        dataType: 'json',
        success: function (response) {
            csrfName = response.csrfName;
            csrfHash = response.csrfHash;
            $('.modal-title').text('Edit Offer');
            $('.save_offer').text('Update Offer');
            $('.image-upload-section').removeClass('d-none');
            $('.img-fluid').attr('id', 'offer_uploaded_image');
            $('.image-upload-section').addClass('d-none');
            $('#offer_uploaded_image').attr('src', '');
            $('.image-upload-section').attr('src', '');
            response = response.fetched_data;
            $('#add_offer').val('');
            $('#offer_type').val(response[0].type);
            $('#edit_offer').val(response[0].id);
            if (response[0].type == "default") {
                $('.offer-url').addClass('d-none');
                $('.offer-products').addClass('d-none');
                $('.offer-categories').addClass('d-none');
                if (response[0].image != '') {
                    $('.image-upload-section').removeClass('d-none');
                    $('#offer_uploaded_image').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('.image-upload-section').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('#uploaded_offer_uploaded_image').val(response[0].image);
                } else {
                    $('.image-upload-section').addClass('d-none');
                }
            }
            if (response[0].type == "categories") {
                $('.offer-categories').removeClass('d-none');
                $('.offer-url').addClass('d-none');
                $('.offer-products').addClass('d-none');
                var typeID = response[0].type_id;
                // Update the Select2 element with the selected user
                $('#category_parent').val(typeID).trigger('change');
                if (response[0].image != '') {
                    $('.image-upload-section').removeClass('d-none');
                    $('#offer_uploaded_image').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('#uploaded_offer_uploaded_image').val(response[0].image);
                } else {
                    $('.image-upload-section').addClass('d-none');
                }
            }
            if (response[0].type == "products") {
                $('.offer-products').removeClass('d-none');
                $('.offer-url').addClass('d-none');
                $('.offer-categories').addClass('d-none');
                var typeId = response[0].type_id;
                // Find the option element with the matching value
                var $option = $('#product_offer_id option[value="' + typeId + '"]');
                // If the option exists, update the Select2 value
                if ($option.length) {
                    $("#product_offer_id").val(typeId).trigger('change');
                } else {
                    // Handle the case when the option doesn't exist
                    console.log("Option with value " + typeId + " not found in the select element.");
                }
                if (response[0].image != '') {
                    $('.image-upload-section').removeClass('d-none');
                    $('#offer_uploaded_image').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('#uploaded_offer_uploaded_image').val(response[0].image);
                } else {
                    $('.image-upload-section').addClass('d-none');
                }
            }
            if (response[0].type == "offer_url") {
                $('.offer-url').removeClass('d-none');
                $('.offer-products').addClass('d-none');
                $('.offer-categories').addClass('d-none');
                $('#offer_url_val').val(response[0].link);
                if (response[0].image != '') {
                    $('.image-upload-section').removeClass('d-none');
                    $('#offer_uploaded_image').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                    $('#uploaded_offer_uploaded_image').val(response[0].image);
                } else {
                    $('.image-upload-section').addClass('d-none');
                }
            }
        }
    });
})
$(document).on('click', '.edit_promocode', function (e) {
    e.preventDefault();

    $('.edit-modal-lg .modal-body').removeClass('view');
    var offer_id = $(this).data('id');

    var offer_url = $(this).attr('href');

    var urlParams = new URLSearchParams(offer_url.split('?')[1]);
    var edit_id = urlParams.get('promocode_edit_id');

    $.ajax({
        type: 'POST',
        url: offer_url,
        data: {
            edit_id: edit_id,
            [csrfName]: csrfHash
        },
        dataType: 'json',
        success: function (response) {
            csrfName = response.csrfName,
                csrfHash = response.csrfHash
            response = response.fetched_data;

            $('.modal-title').text('Edit Promo Code');
            $('.save_promocode').text('Update Promo Code');
            $('.image-upload-section').removeClass('d-none');
            $('#add_promocode').val('');
            $('#edit_promo_code').val(response[0].id);
            $('#promo_code').val(response[0].promo_code);
            $('#message').val(response[0].message);
            $('#start_date').val(response[0].start_date);
            $('#end_date').val(response[0].end_date);
            $('#no_of_users').val(response[0].no_of_users);
            $('#minimum_order_amount').val(response[0].minimum_order_amount);
            $('#discount').val(response[0].discount);
            $('#max_discount_amount').val(response[0].max_discount_amount);
            $('#no_of_repeat_usage').val(response[0].no_of_repeat_usage);
            $('#uploaded_image_here_val').val(response[0].image);

            $('#discount_type_select option').each(function () {
                if ($(this).val() === response[0].discount_type) {
                    $(this).prop('selected', true);
                }
            });
            $('#status option').each(function () {
                if ($(this).val() === response[0].status) {
                    $(this).prop('selected', true);
                }
            });
            $('#repeat_usage option').each(function () {
                if ($(this).val() === response[0].repeat_usage) {
                    $(this).prop('selected', true);
                }
            });
            if (response[0].is_cashback == 1) {
                $('#is_cashback').bootstrapSwitch('state', true);

            }
            if (response[0].list_promocode == '1') {
                $('#list_promocode').bootstrapSwitch('state', true);
            }
            $('#status option').each(function () {
                if ($(this).val() === response[0].status) {
                    $(this).prop('selected', true);
                }
            });
            if (response[0].repeat_usage == "1") {
                $('#repeat_usage_html').removeClass('d-none');
            }
            if (response[0].image != '') {
                $('.image-upload-section').removeClass('d-none');
                $('#slider_uploaded_image').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                $('.image-upload-section').attr('src', base_url + response[0].image.replace(/\/\//g, '/'));
                $('#uploaded_slider_uploaded_image').val(response[0].image);
            } else {
                $('.image-upload-section').addClass('d-none');
            }
            $('#uploaded_image_here').attr('src', base_url + '/' + response[0].image);


        }
    });
})
$(document).on('click', '.edit_return_reason', function (e) {
    e.preventDefault();

    $('.edit-modal-lg .modal-body').removeClass('view');
    var offer_id = $(this).data('id');

    var offer_url = $(this).attr('href');

    var urlParams = new URLSearchParams(offer_url.split('?')[1]);
    var edit_id = urlParams.get('promocode_edit_id');

    $.ajax({
        type: 'POST',
        url: offer_url,
        data: {
            edit_id: edit_id,
            [csrfName]: csrfHash
        },
        dataType: 'json',
        success: function (response) {
            csrfName = response.csrfName,
                csrfHash = response.csrfHash
            response = response.fetched_data;

            $('.modal-title').text('Edit Retrun Reason');
            $('.save_promocode').text('Update Return Reason');
            $('.image-upload-section').removeClass('d-none');
            $('#add_promocode').val('');
            $('#edit_return_reason_id').val(response[0].id);
            $('#return_reason').val(response[0].return_reason);
            $('#message').val(response[0].message);

            $('#uploaded_image_here_val').val(response[0].image);


            $('#uploaded_image_here').attr('src', base_url + '/' + response[0].image);


        }
    });
})

$(".tax_list").select2({

    ajax: {
        url: base_url + from + '/taxes/get_taxes',
        type: "GET",
        dataType: "json",
        delay: 250,
        data: function (params) {
            return {
                search: params.term, // search term
            };
        },
        processResults: function (response) {

            return {
                results: response,
            };
        },
        cache: true,
    },

    placeholder: "Search for taxes...",
});

$('#seller-select').select2({
    ajax: {
        url: base_url + 'admin/product/get_sellers_data',
        type: "GET",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term, // search term
            };
        },
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.name,
                        id: item.id
                    }
                })
            };
        },
        cache: true
    },
    minimumInputLength: 1,
    theme: 'bootstrap4',
    placeholder: 'Select a seller',
    dropdownParent: $("#save-product"),
});

$('#seller-select').on('change', function () {
    var seller_id = $(this).val();

    var edit_id = $('input[name="category_id"]').val();
    var ignore_status = $.isNumeric(edit_id) && edit_id > 0 ? 1 : 0;
    if ($.isNumeric(seller_id) && seller_id > 0) {

        get_seller_categories(seller_id, ignore_status, edit_id, from);
    }
});

$('#seller_filter').select2({
    ajax: {
        url: base_url + 'admin/product/get_sellers_data',
        type: "GET",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                search: params.term, // search term
            };
        },
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.name,
                        id: item.id
                    }
                })
            };
        },
        cache: true
    },
    minimumInputLength: 1,
    theme: 'bootstrap4',
    placeholder: 'Select a seller',
});


$(document).on('submit', "#add_dboy_form", function (e) {
    e.preventDefault();

    var data = new FormData(this);
    data.append(csrfName, csrfHash);
    data.append("country_code", $(".selected-dial-code").text());
    $.ajax({
        type: "POST",
        url: base_url + 'delivery_boy/login/create_delivery_boy',
        data: data,
        processData: !1,
        contentType: !1,
        cache: !1,
        dataType: "json",

        success: function (response) {

            csrfName = response.csrfName,
                csrfHash = response.csrfHash;
            if (response.error == true) {
                iziToast.error({
                    message: response.message,
                });

            } else {
                iziToast.success({
                    message: response.message,
                });
                setTimeout(function () {
                    window.location.href = base_url + 'delivery_boy/login';
                }, 3000)

            }
        }
    })
});
$(document).on('submit', "#add_seller_form", function (e) {
    e.preventDefault();
    var data = new FormData(this);
    let categories = $('#seller-register-category-field').val().join(",")
    data.append("categories", categories)
    data.append(csrfName, csrfHash);
    data.append("country_code", $(".selected-dial-code").text());
    $.ajax({
        type: "POST",
        url: base_url + 'seller/auth/create-seller',
        data: data,
        processData: !1,
        contentType: !1,
        cache: !1,
        dataType: "json",

        success: function (response) {

            csrfName = response.csrfName,
                csrfHash = response.csrfHash;
            if (response.error == true) {
                iziToast.error({
                    message: response.message,
                });

            } else {
                iziToast.success({
                    message: response.message,
                });
                setTimeout(function () {
                    window.location.href = base_url + 'seller/login';
                }, 3000)

            }
        }
    })
});

function consignment_query_params(p) {

    return {
        order_id: $('#order_id').val(),
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search
    };
}

function consignmentModal(seller_id = null) {
    if (from == "admin") {
    }

    let shiprocket_order = $("#is_shiprocket_order_check").val() == "1";

    let productVariantIds = []
    let productName = []
    let orderItemId = []
    let orderPickupLocation = []
    let orderItemIds = []

    $('.product_variant_id').each(function () {
        productVariantIds.push($(this).val());
    });
    $('.product_name').each(function () {
        productName.push($(this).val());
    });
    productVariantIds.map(function (value) {
        orderItemIds.push(JSON.parse($("#product_variant_id_" + value).text())["id"])
        // orderItemIds.push(JSON.parse($("#product_variant_id_"+value).text())["id"]);
        orderPickupLocation.push(JSON.parse($("#product_variant_id_" + value).text())["pickup_location"]);
    });
    let pickupLoationSet = [...new Set(orderPickupLocation)];

    let options = pickupLoationSet.map(function (value) {
        return {
            value: value, text: value
        }
    });

    $("#parcel_pickup_locations").empty(); // Clear existing options
    $("#parcel_pickup_locations").append(new Option("Select Option", "")); // Add default option
    options.forEach(option => {
        $("#parcel_pickup_locations").append(new Option(option.text, option.value));
    });

    var modalBody = document.getElementById('product_details');
    if (modalBody == null) {
        return iziToast.error({
            message: "Order status is still awaiting. You cannot create a parcel."
        });
    }

    modalBody.innerHTML = '';

    for (var i = 0; i < productVariantIds.length; i++) {
        const data = JSON.parse($("#product_variant_id_" + productVariantIds[i]).html());

        const quantity = parseInt(data.quantity);
        const unit_price = parseInt(data.unit_price);
        const delivered_quantity = parseInt(data.delivered_quantity);
        if (delivered_quantity != quantity && data.active_status != "cancelled" && data.active_status != "delivered") {
            $('#empty_box_body').addClass("d-none");
            $('#modal-body').removeClass("d-none");
            let row = "<tr id='parcel_row_" + productVariantIds[i] + "' data-pickup='" + orderPickupLocation[i] + "' >" +
                "<th scope='row'>" + orderItemIds[i] + "</th>" +
                "<td>" + productName[i] + "</td>" +
                "<td>" + productVariantIds[i] + "</td>" +
                "<td>" + quantity + "</td>" +
                "<td>" + unit_price + "</td>" +
                `<td><label for="checkbox-${productVariantIds[i]}"><input type="checkbox" data-item-id="${orderItemIds[i]}" name="checkbox-${productVariantIds[i]}" id="checkbox-${productVariantIds[i]}" class="form-control product-to-ship"></label></td>`
            "</tr>";

            modalBody.innerHTML += row;
        }
    }
    if (modalBody.innerHTML == "") {
        $('#empty_box_body').removeClass("d-none");
        $('#modal-body').addClass("d-none");

        let empty_box_body = document.getElementById('empty_box_body');
        empty_box_body.innerHTML = "";
        let row = "<h5 class='text-center'>Items Are Already Shipped.</h5>";
        empty_box_body.innerHTML += row;
    }


    // Add event listener for dropdown change
    $("#parcel_pickup_locations").on("change", function () {
        const selectedPickupLocation = $(this).val();

        // Uncheck all checkboxes
        $(".product-to-ship").prop("checked", false);

        if (selectedPickupLocation === "" && !shiprocket_order) {
            // Hide all rows if no option is selected
            $("tr[id^='parcel_row_']").hide();
        } else {
            // Show rows that match the selected location and hide the others
            $("tr[id^='parcel_row_']").each(function () {
                const rowPickupLocation = $(this).data("pickup");
                if (rowPickupLocation === selectedPickupLocation) {
                    $(this).show(); // Show rows that match
                } else {
                    $(this).hide(); // Hide rows that don't match
                }
                if (!shiprocket_order) {
                    $(this).show()
                }
            });
        }
    });

    $("#parcel_pickup_locations").change()

}

function delete_consignment(id) {
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: "post",
                    url: base_url + from + "/orders/delete_consignment",
                    data: {
                        id
                    },
                    dataType: "json",
                    success: function (response) {
                        if (response.error == true) {
                            Swal.fire('error', response.message, 'error');
                        } else {
                            response.data.map(val => {
                                $("#product_variant_id_" + val.product_variant_id).html(JSON.stringify(val))
                            })
                            iziToast.success({
                                message: response.message
                            })
                            Swal.fire('Success', 'Consignment Deleted !', 'success');
                        }
                        $("#consignment_table").bootstrapTable('refresh')
                    }
                });
            });
        },
        allowOutsideClick: false
    })
}

$(document).on('click', '#ship_parcel_btn', function (e) {
    e.preventDefault();
    let product_to_ship = $('.product-to-ship:checked');
    let consignment_title = $('#consignment_title').val();
    let order_id = $('#order_id').val();

    let selected_items = [];
    product_to_ship.each(function () {
        selected_items.push($(this).data("item-id"));
    });
    $.ajax({
        type: "POST",
        url: base_url + from + "/orders/create_consignment",
        data: {
            consignment_title,
            selected_items,
            order_id,
            [csrfName]: csrfHash,
        },
        success: function (response) {
            response = (JSON.parse(response));
            csrfName = response['csrfName'];
            csrfHash = response['csrfHash'];
            if (response.error == false) {
                response.data.map(val => {
                    $("#product_variant_id_" + val.product_variant_id).html(JSON.stringify(val))
                })
                $("#consignment_table").bootstrapTable('refresh')
                $("#create_consignment_modal").modal('hide')
                iziToast.success({
                    message: response.message
                })
            } else {
                iziToast.error({
                    message: response.message
                })
            }
        }
    });
})

$('#view_consignment_items_modal').on('show.bs.modal', function (event) {
    let triggerElement = $(event.relatedTarget);
    current_selected_image = triggerElement;
    let consignment_items = $(current_selected_image).data('items');
    let modalBody = document.getElementById('consignment_details');
    modalBody.innerHTML = '';
    let count = 1
    consignment_items.forEach(element => {
        var row = "<tr>" +
            "<th scope='row'>" + count + "</th>" +
            "<td>" + element.product_name + "</td>" +
            `<td><a href='${element.image}' class="image-box-100" data-toggle='lightbox' data-gallery='order-images'> <img src='${element.image}' alt="${element.product_name}"></a></td>` +
            "<td>" + element.quantity + "</td>" +
            "</tr>";

        modalBody.innerHTML += row;
        count++
    });
});

$(document).on('hide.bs.modal', '#consignment_status_modal', function () {

    $("#consignment-items-container").empty()
    $("#tracking_box").empty()
    $("#tracking_box_old").empty()
    $('.shiprocket_order_box').removeClass('d-none');
    $('.manage_shiprocket_box').addClass('d-none');

})
$(document).on('show.bs.modal', '#consignment_status_modal', function (event) {
    let triggerElement = $(event.relatedTarget);
    current_selected_image = triggerElement;

    let consignment_items = $(current_selected_image).data('items');
    let order_tracking = $('#order_tracking').val();
    if (order_tracking != undefined) {
        order_tracking = JSON.parse(order_tracking);
    }

    $('#consignment_data').val(JSON.stringify(consignment_items));
    const container = document.getElementById('consignment-items-container');
    const tracking_box = document.getElementById('tracking_box');
    const tracking_box_old = document.getElementById('tracking_box_old');
    $('.shiprocket_field_box').addClass('d-none');
    $('#pickup_location_product').val(consignment_items[0]['pickup_location']);
    if (order_tracking != undefined) {
        order_tracking.forEach(tracking => {

            if (tracking.consignment_id == consignment_items[0].consignment_id) {

                if (tracking.is_canceled == 0) {
                    $('.shiprocket_order_box').addClass('d-none');
                    $('.manage_shiprocket_box').removeClass('d-none');
                    $('#' + tracking.shipment_id + '_shipment_id').removeClass('d-none');

                    let div = document.createElement('div');

                    div.innerHTML = `
                        <h5><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/></svg> Shiprocket Order Details</h5>
                        <p class="mb-0 text-bold"><span class="text-black-50">Shiprocket Order Id:</span> ${tracking.shiprocket_order_id}</p>
                        <p class="m-0 text-bold"><span class="text-black-50">Shiprocket Tracking Id:</span> ${tracking.tracking_id}</p>
                        <p class="m-0 text-bold"><span class="text-black-50">Shiprocket Tracking Url:</span> <a href="${tracking.url}" target="_blank" class="text-primary">${tracking.url}</a></p>
                        <input type="hidden" name="shiprocket_tracking_id" id="shiprocket_tracking_id" value="${tracking.tracking_id}">
                        <input type="hidden" name="shiprocket_order_id" id="shiprocket_order_id" value="${tracking.shiprocket_order_id}">
                        `;
                    tracking_box.appendChild(div);
                } else {

                    let div = document.createElement('div');


                    div.innerHTML = `
                        <hr><h5>Cancelled Shiprocket Order Details</h5>
                        <p class="mb-0 text-bold"><span class="text-black-50">Shiprocket Order Id:</span> ${tracking.shiprocket_order_id}</p>
                        <p class="m-0 text-bold"><span class="text-black-50">Shiprocket Tracking Id:</span> ${tracking.tracking_id}</p>
                        <p class="m-0 text-bold"><span class="text-black-50">Shiprocket Tracking url:</span> <a href="${tracking.url}" target="_blank" class="text-primary">${tracking.url}</a></p><hr>
                        `;
                    tracking_box_old.appendChild(div);
                }
            }
        });
    }
    const card = document.createElement('div');
    card.className = 'card p-3 border';
    let count = 1;
    card.innerHTML = `
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Image</th>
                <th scope="col">Quantity</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
`;
    const tbody = card.querySelector('tbody');

    consignment_items.forEach(element => {
        $('#consignment_id').val(element.consignment_id);
        $('#deliver_by').val(element.delivery_boy_id);
        $('.consignment_status').val(element.active_status);
        $('.consignment_status').change();
        tbody.innerHTML += `
        <tr>
            <td>${count++}</td>
            <td>${element.product_name}</td>
            <td><a href='${element.image}' class="image-box-100" data-toggle='lightbox' data-gallery='order-images'> <img src='${element.image}' alt="${element.product_name}"></a></td>
            <td>${element.quantity}</td>
        </tr>
    `;
    });
    container.appendChild(card);
});
$(document).on('click', '.refresh_shiprocket_status', function (e) {
    let tracking_id = $('#shiprocket_tracking_id').val();
    if (tracking_id == undefined || tracking_id == "" || tracking_id == null) {
        iziToast.error({
            message: "Tracking Id is Required",
        });
        return false
    }
    $.ajax({
        type: "POST",
        url: base_url + from + '/orders/update_shiprocket_order_status',
        data: { tracking_id },
        dataType: "json",
        success: function (response) {
            if (response.error == false) {
                $("#consignment_table").bootstrapTable('refresh')
                iziToast.success({
                    message: response.message,
                });
                response.data.forEach(element => {
                    $('.status-' + element['order_item_id']).addClass('badge-info').html(element['status'])
                });
                $('#consignment_status_modal').modal("hide");

                return
            }
            iziToast.error({
                message: response.message,
            });
            return false
        }
    });

});
$(document).on('change', '[name="create_shiprocket_button"]', function () {
    if ($(this).prop('checked')) {
        $('.shiprocket_order_box').removeClass('d-none')
    } else {
        $('.shiprocket_order_box').addClass('d-none')
    }
});
$(document).on('click', '.consignment_order_status_update', function (e) {
    let consignment_id = $("#consignment_id").val();
    let status = $(".consignment_status").val();
    let parcel_otp = $("#parcel-otp").val();
    let delivery_boy_otp_system = $('#delivery_boy_otp_system').val();


    if (status == "" || status == null) {

        iziToast.error({
            message: "Please Select Status",
        });
        return false
    }
    if (status === "delivered" && delivery_boy_otp_system == 1 && parcel_otp == "") {
        iziToast.error({
            message: "Parcel OTP is Required.",
        });
        return false
    }
    let deliver_by = $('#deliver_by').val();
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: base_url + from + '/orders/update_order_status',
                    data: {
                        consignment_id,
                        status,
                        parcel_otp,
                        deliver_by,
                        delivery_boy_otp_system,
                        [csrfName]: csrfHash
                    },

                    dataType: 'json',
                    success: function (result) {

                        csrfName = result['csrfName'];
                        csrfHash = result['csrfHash'];
                        if (result['error'] == false) {

                            $("#consignment_table").bootstrapTable('refresh')
                            iziToast.success({
                                message: result['message'],
                            });
                            result.data.forEach(element => {
                                $('.status-' + element['order_item_id']).addClass('badge-info').html(element['status'])
                            });
                        } else {
                            iziToast.error({
                                message: result['message'],
                            });
                        }
                        setTimeout(function () { location.reload(); }, 1000);
                        swal.close();
                    }
                });
            });
        },
        allowOutsideClick: false
    });
});
$(document).on('click', '.digital_order_status_update', function (e) {
    let status = $('.digital_order_status').val();
    let order_id = $('#order_id').val();
    if (status == "" || status == null) {
        iziToast.error({
            message: "Please Select Status",
        });
        return false
    }
    Swal.fire({
        title: 'Are You Sure!',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'POST',
                    url: base_url + from + '/orders/update_order_status',
                    data: {
                        order_id,
                        status,
                        type: "digital",
                        [csrfName]: csrfHash
                    },

                    dataType: 'json',
                    success: function (result) {
                        csrfName = result['csrfName'];
                        csrfHash = result['csrfHash'];
                        if (result['error'] == false) {

                            $("#consignment_table").bootstrapTable('refresh')
                            iziToast.success({
                                message: result['message'],
                            });
                            result.data.forEach(element => {
                                $('.status-' + element['order_item_id']).addClass('badge-info').html(element['status'])
                            });
                        } else {
                            iziToast.error({
                                message: result['message'],
                            });
                        }
                        swal.close();
                    }
                });
            });
        },
        allowOutsideClick: false
    });
});

$('#transaction_modal').on('shown.bs.modal', function (e) {
    let button = $(e.relatedTarget);
    let consignment_id = button.data('id');
    let tracking_data = button.data('tracking-data');

    $('.consignment_id').val(consignment_id);
    if (tracking_data != [] && tracking_data.length > 0) {
        $('#courier_agency').val(tracking_data[0]['courier_agency']);
        $('#tracking_id').val(tracking_data[0]['tracking_id']);
        $('#url').val(tracking_data[0]['url']);
    } else {
        $('#courier_agency').val('');
        $('#tracking_id').val('');
        $('#url').val('');
    }
});

$(document).ready(function () {
    // Initialize Bootstrap Switch
    $("input[data-bootstrap-switch]").bootstrapSwitch();

    $('#update_seller_flow').on('switchChange.bootstrapSwitch', function (event, state) {
        if (state) { // If switch is turned ON

            $.ajax({
                url: base_url + 'admin/setting/update_seller_flow',
                type: 'POST',
                data: { update_seller_flow: 1, csrfName: csrfHash },
                success: function (response) {

                    response = JSON.parse(response);

                    if (response['error'] == false) {

                        iziToast.success({
                            message: response['message'],
                        });
                    } else {
                        iziToast.error({
                            message: response['message'],
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX request failed:", xhr.responseText, "Status:", status, "Error:", error);
                }
            });
        }
    });
});


document.querySelectorAll('.togglePassword').forEach(function (toggle) {
    toggle.addEventListener('click', function () {
        const input = this.previousElementSibling; // Find the input just before the button
        const icon = this.querySelector('i'); // Get the eye icon
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});


if ($("#seller-register-category-field")) {
    $("#seller-register-category-field").select2();
}

function itemsReadMoreFormatter(value, row, index) {
    // if (!value) return '';
    // var plain = value.replace(/(<([^>]+)>)/gi, ""); // Remove HTML tags if any
    var shortText = value.substring(0, 30) + (value.length > 30 ? '...' : '');
    var html = `
        <div class="read-more-container">
            <span class="read-more-short">${shortText}</span>
            <span class="read-more-full" style="display:none;">${value.replace(/\n/g, '<br>')}</span>
            ${value.length > 20 ? '<button type="button" class="btn btn-link btn-xs read-more-toggle">Read Morae</button>' : ''}
        </div>
    `;
    return html;
}

// Toggle logic
$(document).on('click', '.read-more-toggle', function () {
    var $container = $(this).closest('.read-more-container');
    var $short = $container.find('.read-more-short');
    var $full = $container.find('.read-more-full');
    if ($full.is(':visible')) {
        $full.hide();
        $short.show();
        $(this).text('Read More');
    } else {
        $full.show();
        $short.hide();
        $(this).text('Read Less');
    }
});


// affiliate system code
// affiliate marketing stepper
let currentAffiliateFormStep = 1;

function updateStepperUI(step) {
    // Show only the current step page
    document.querySelectorAll('.step-page').forEach(p => p.classList.add('d-none'));
    const currentPage = document.getElementById(`page${step}`);
    if (currentPage) currentPage.classList.remove('d-none');

    // Update stepper UI
    document.querySelectorAll('.affiliate_step').forEach((el, index) => {
        el.classList.remove('active', 'completed', 'primary');
        const circle = el.querySelector('.circle');
        circle.innerHTML = '';

        const stepIndex = index + 1;
        if (stepIndex < step) {
            el.classList.add('completed');
            circle.innerHTML = '✓';
        }
        if (stepIndex === step) {
            el.classList.add('active', 'primary');
        }
    });
}
function validateAffiliateStep(step) {
    let errors = [];
    let firstInvalidField = null;
    let stepToGo = step;

    // URL regex (simple version)
    const urlPattern = /^(https?:\/\/)[^\s/$.?#].[^\s]*$/i;

    if (step === 1) {
        const fullName = document.getElementById('full_name');
        const email = document.getElementById('email');
        const mobile = document.getElementById('mobile');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const address = document.getElementById('address');
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Remove previous highlights
        // [fullName, email, mobile, password, confirmPassword, address].forEach(el => el.classList.remove('is-invalid'));
        [fullName, email, mobile, password, confirmPassword, address].forEach(el => {
            if (el) el.classList.remove('is-invalid');
        });

        if (!fullName.value.trim()) {
            errors.push('Full Name is required');
            fullName.classList.add('is-invalid');
            if (!firstInvalidField) firstInvalidField = fullName;
        }
        if (!email.value.trim()) {
            errors.push('Email is required');
            email.classList.add('is-invalid');
            if (!firstInvalidField) firstInvalidField = email;
        } else if (!emailPattern.test(email.value.trim())) {
            errors.push('Please enter a valid email address');
            email.classList.add('is-invalid');
            if (!firstInvalidField) firstInvalidField = email;
        }
        if (!mobile.value.trim()) {
            errors.push('Mobile is required');
            mobile.classList.add('is-invalid');
            if (!firstInvalidField) firstInvalidField = mobile;
        }
        if (password && !password.value.trim()) {
            errors.push('Password is required');
            password.classList.add('is-invalid');
            if (!firstInvalidField) firstInvalidField = password;
        }

        if (confirmPassword && !confirmPassword.value.trim()) {
            errors.push('Confirm Password is required');
            confirmPassword.classList.add('is-invalid');
            if (!firstInvalidField) firstInvalidField = confirmPassword;
        }

        if (password && confirmPassword && password.value !== confirmPassword.value) {
            errors.push('Password and Confirm Password do not match');
            password.classList.add('is-invalid');
            confirmPassword.classList.add('is-invalid');
            if (!firstInvalidField) firstInvalidField = confirmPassword;
        }

        if (!address.value.trim()) {
            errors.push('Address is required');
            address.classList.add('is-invalid');
            if (!firstInvalidField) firstInvalidField = address;
        }
    }

    if (step === 2) {
        // Step 2: Website and Mobile App
        const website = document.getElementById('my_website');
        const app = document.getElementById('my_app');
        const urlPattern = /^(https?:\/\/)[^\s/$.?#].[^\s]*$/i;

        if (website) website.classList.remove('is-invalid');
        if (app) app.classList.remove('is-invalid');

        // Website validation
        if (!website || !website.value || !website.value.trim()) {
            errors.push('Website URL is required');
            if (website) website.classList.add('is-invalid');
            if (!firstInvalidField && website) firstInvalidField = website;
        } else if (!urlPattern.test(website.value.trim())) {
            errors.push('Please enter a valid Website URL');
            if (website) website.classList.add('is-invalid');
            if (!firstInvalidField && website) firstInvalidField = website;
        }

        // App validation
        if (!app || !app.value || !app.value.trim()) {
            errors.push('Mobile App URL is required');
            if (app) app.classList.add('is-invalid');
            if (!firstInvalidField && app) firstInvalidField = app;
        } else if (!urlPattern.test(app.value.trim())) {
            errors.push('Please enter a valid Mobile App URL');
            if (app) app.classList.add('is-invalid');
            if (!firstInvalidField && app) firstInvalidField = app;
        }
        stepToGo = 2;
    }
    if (step === 3) {
        const statusChecked = document.querySelector('input[name="status"]:checked');
        if (!statusChecked) {
            errors.push('Status is required');
            stepToGo = 3;
        }
    }

    if (errors.length > 0) {
        errors.forEach(msg => {
            iziToast.error({ title: 'Error', message: msg, position: 'topRight' });
        });
        // Go to the step with the error
        if (stepToGo !== step) goToStep(stepToGo);
        // Focus the first invalid field
        if (firstInvalidField) firstInvalidField.focus();
        return false;
    }
    return true;
}
function nextStep(step) {
    // Validate current step before moving
    if (!validateAffiliateStep(currentAffiliateFormStep)) {
        return; // Stop if validation fails
    }
    currentAffiliateFormStep = step;
    updateStepperUI(step);
}

function prevStep(step) {
    currentAffiliateFormStep = step;
    updateStepperUI(step);
}

function goToStep(step) {
    // Only allow forward navigation if current step is valid
    if (step > currentAffiliateFormStep) {
        if (!validateAffiliateStep(currentAffiliateFormStep)) {
            return; // Stop if validation fails
        }
    }
    currentAffiliateFormStep = step;
    updateStepperUI(step);
}


window.onload = () => updateStepperUI(currentAffiliateFormStep);



function renderCategoryOptions(categories, selectedId = null) {
    let html = '';

    function buildOptions(cats, level = 0) {
        cats.forEach(category => {
            const indent = '\u00A0'.repeat(level * 4); // non-breaking spaces
            const selected = selectedId == category.id ? 'selected' : '';
            html += `<option value="${category.id}" ${selected}>${indent}${category.name}</option>`;
            if (category.children && category.children.length > 0) {
                buildOptions(category.children, level + 1);
            }
        });
    }

    buildOptions(categories);
    return html;
}

function initializeSelect2() {
    $('.category_parent:not(.select2-hidden-accessible)').select2();
    refreshDisabledOptions();
}

function refreshDisabledOptions() {
    let selectedValues = [];

    $('.category_parent').each(function () {
        let val = $(this).val();
        if (val !== '') {
            selectedValues.push(val);
        }
    });

    $('.category_parent').each(function () {
        let currentSelect = $(this);
        let currentVal = currentSelect.val();

        currentSelect.find('option').each(function () {
            let optionVal = $(this).val();
            if (!optionVal) return;

            if (selectedValues.includes(optionVal) && optionVal !== currentVal) {
                $(this).attr('disabled', true);
            } else {
                $(this).attr('disabled', false);
            }
        });
    });

    $('.category_parent').select2(); // Refresh UI
}

$(document).ready(function () {
    initializeSelect2();

    $('#add-more').on('click', function () {
        const optionsHtml = renderCategoryOptions(categoriesData);

        const item = `
            <div class="repeater-item col-md-8">
                <div class="d-flex mb-3">
                    <select name="category_parent[]" class="form-control mx-3 category_parent w-100">
                        <option value="">Select Category</option>
                        ${optionsHtml}
                    </select>
                    <input type="text" class="form-control mx-3" name="commission[]" placeholder="Commission">
                    <a type="button" class="remove-btn"><i class="fa-2x fa-times-circle fas text-danger"></i></a>
                </div>
            </div>
        `;

        $('#repeater').append(item);
        initializeSelect2();
    });

    $(document).on('click', '.remove-btn', function () {
        $(this).closest('.repeater-item').remove();
        refreshDisabledOptions();
    });

    $(document).on('change', '.category_parent', function () {
        refreshDisabledOptions();
    });
});


$(document).on('click', '.open-affiliate-modal', function () {
    const id = $(this).data('id');
    const name = $(this).data('name');
    const isInAffiliate = $(this).data('is_in_affiliate');

    $('#modal_product_id').val(id);
    $('#modal_product_name').val(name);
    $('#modal_is_in_affiliate').val(isInAffiliate);
});

$(document).on('click', '.affiliateFormSave', function (e) {
    e.preventDefault();

    var product_id = $('#modal_product_id').val();
    var product_name = $('#modal_product_name').val();
    var is_in_affiliate = $('#modal_is_in_affiliate').val();

    $.ajax({
        url: base_url + from + '/product/update_affiliate_settings',
        method: 'POST',
        data: {
            product_id: product_id,
            product_name: product_name,
            is_in_affiliate: is_in_affiliate
        },
        success: function (response) {

            response = JSON.parse(response);

            iziToast.success({
                message: response.message,
            });

            $('#product-affiliate-modal').modal('hide');
            $('table').bootstrapTable('refresh');
            // Optionally reload table or update UI
        },
        error: function () {
            iziToast.error({
                message: 'Failed to update!',
            });
        }
    });
});

$(document).ready(function () {
    // Open modal on button click
    $('#openBulkModal').on('click', function () {
        $('#bulkAffiliateModal').modal('show');
    });


    // Handle bulk form submit
    $('#bulkAffiliateForm').on('submit', function (e) {
        e.preventDefault();
        // var product_ids = [];
        var product_ids = $.map($('#products_affiliate_table').bootstrapTable('getSelections'), function (row) {
            return row.id;
        });

        var is_in_affiliate = $('#bulk_affiliate_status').val();
        if (is_in_affiliate === "") {
            iziToast.error({ message: "Please select affiliate status." });
            return;
        }

        $.ajax({
            url: base_url + from + '/product/bulk_update_affiliate',
            method: 'POST',
            data: {
                product_ids: product_ids,
                is_in_affiliate: is_in_affiliate
            },
            success: function (response) {

                response = JSON.parse(response);
                iziToast.success({
                    message: response.message
                });
                $('#bulkAffiliateModal').modal('hide');
                $('table').bootstrapTable('refresh');

            },
            error: function () {
                iziToast.error({ message: 'Update failed.' });
            }
        });
    });
});

// Open affiliate link modal and generate token

$(document).on('click', '.copy-affiliate-link-btn', function () {
    const btn = $(this);
    const productId = btn.data('product_id');
    const productSlug = btn.data('slug');
    const userId = btn.data('user_id');
    const productName = btn.data('name');
    const categoryId = btn.data('category_id');
    const affiliateCommission = btn.data('affiliate_commission');

    // First, check if a token exists or generate one
    $.ajax({
        url: base_url + 'affiliate/product/get_or_generate_token',
        method: 'POST',
        dataType: 'json',
        data: {
            product_id: productId,
            product_name: productName,
            user_id: userId,
            category_id: categoryId,
            affiliate_commission: affiliateCommission
        },
        success: function (res) {
            if (!res.error) {
                const token = res.token;
                const url = `${base_url}products/details/${productSlug}?ref=${token}`;

                // Copy to clipboard
                const tempInput = document.createElement('input');
                tempInput.style.position = 'absolute';
                tempInput.style.left = '-9999px';
                tempInput.value = url;
                document.body.appendChild(tempInput);
                tempInput.select();
                tempInput.setSelectionRange(0, 99999);
                const copied = document.execCommand('copy');
                document.body.removeChild(tempInput);

                if (copied) {
                    iziToast.success({ message: 'Affiliate link copied!' });
                } else {
                    iziToast.warning({ message: 'Could not copy. Please try manually.' });
                }

                // Optional: also show modal if needed
                $('#share-url').val(url);
                const shareModal = new bootstrap.Modal(document.getElementById('shareAffiliateModal'));
                shareModal.show();

            } else {
                iziToast.error({ message: res.message || 'Failed to get token.' });
            }
        },
        error: function () {
            iziToast.error({ message: 'Server error. Please try again.' });
        }
    });
});


$('#copy-url-btn').on('click', function () {
    const urlField = document.getElementById("share-url");
    urlField.select();
    urlField.setSelectionRange(0, 99999); // for mobile
    if (document.execCommand("copy")) {
        iziToast.success({ message: 'URL copied!' });
        $('#shareAffiliateModal').modal('hide');

    }
});

//  show affiliate token based on category
// $('#product_category_tree_view_html').on('changed.jstree', function (e, data) {
//     var affiliate_categories = $('#affiliate_categories').val();
//     console.log(affiliate_categories);

//     var selectedId = data.selected[0];
//     console.log(selectedId);

//     if (affiliate_categories.includes(parseInt(selectedId))) {
//         $('.is_in_affiliate').removeClass('d-none');
//     } else {
//         $('.is_in_affiliate').addClass('d-none');
//     }
// });




$(document).on('click', '.update-affiliate-commission', function () {
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, settle commission!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/cron-job/settle_affiliate_commission?is_date=true',
                    type: 'GET',
                    data: {
                        'is_date': true
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            Swal.fire('Done!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});

$('#product-search-form').on('submit', function (e) {
    e.preventDefault();

    let search = $('#search-input').val().trim();

    $.ajax({
        url: base_url + "affiliate/product",
        type: "GET",
        data: { search: search },
        success: function (response) {
            $('#product-list').html(response);
            $('#clear-search-btn').toggleClass('d-none', !search);
        }
    });
});

// Add click animation to cards
$('.status-card').on('click', function () {
    $(this).addClass('shadow-lg').removeClass('shadow-sm');
    setTimeout(() => {
        $(this).removeClass('shadow-lg').addClass('shadow-sm');
    }, 200);
});


// Function to fetch sales data and render the charts
$(document).ready(function () {

    if (window.location.href.indexOf('affiliate/home') > -1) {
        var fetch_sales_url = base_url + "affiliate/home/fetch_sales"
        var most_selling_affiliate_categories_url = base_url + "affiliate/home/most_selling_affiliate_categories"
    } else {
        var fetch_sales_url = base_url + "admin/affiliate/fetch_sales"
        var most_selling_affiliate_categories_url = base_url + "admin/affiliate/most_selling_affiliate_categories"

    }

    // Function to fetch sales data and render the charts
    function fetchAndRenderAffiliateCharts() {
        $.ajax({
            url: fetch_sales_url,
            type: "GET",
            dataType: "json",
            success: function (response) {
                // Assuming response data structure as you provided
                let monthlyData = response[0];
                let weeklyData = response[1];
                let dailyData = response[2];

                const data = {
                    Monthly: {
                        series: [{
                            name: 'Monthly Earning',
                            data: monthlyData.total_sale || []
                        }],
                        categories: monthlyData.month_name || [],
                        colors: ['#1E90FF']

                    },
                    Weekly: {
                        series: [{
                            name: 'Weekly Earning',
                            data: weeklyData.total_sale || []
                        }],
                        categories: weeklyData.week || [],
                        colors: ['#32CD32']
                    },
                    Daily: {
                        series: [{
                            name: 'Daily Earning',
                            data: dailyData.total_sale || []
                        }],
                        categories: dailyData.day || [],
                        colors: ['#990099']

                    }
                };

                let chartData = data['Monthly'];


                const options = {
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        },
                    },
                    series: chartData.series,
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: chartData.categories
                    },
                    yaxis: {
                        labels: {
                            formatter: function (value) {
                                return (value / 100) +
                                    '00'; // Divide by 100 to convert to thousands and then add '00k'
                            }
                        }
                    },
                    fill: {
                        opacity: 1,
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                var currencySymbol = "<?php echo $currency_symbol; ?>";
                                return currencySymbol + val;
                            }
                        }
                    }
                };


                const chart = new ApexCharts(document.querySelector(".affiliate-chart-container"), options);
                chart.render();

                $(".chart-height li a").on("click", function () {
                    $(".chart-height li a").removeClass('active');
                    $(this).addClass('active');

                    chartData = data[$(this).attr("href").replace('#', '')];

                    chart.updateOptions({
                        series: chartData.series,
                        xaxis: {
                            categories: chartData.categories
                        }
                    });
                });
            },
            error: function (error) {
                console.error("Error fetching data: ", error);
            }
        });
    }


    // Initial chart rendering
    fetchAndRenderAffiliateCharts();
    // Function to fetch sales data and render the charts
    function fetchAndRenderCategoryCharts() {
        $.ajax({
            url: most_selling_affiliate_categories_url,
            type: "GET",
            dataType: "json",
            success: function (response) {
                // Assuming response data structure as you provided
                console.log('AJAX Success:', response);

                var options = {
                    series: response.sales.map(Number),  // Convert to numbers
                    chart: {
                        width: 500,
                        type: 'donut',
                    },
                    plotOptions: {
                        pie: {
                            startAngle: -90,
                            endAngle: 270
                        }
                    },
                    fill: {
                        type: 'gradient',
                    },
                    legend: {
                        formatter: function (val, opts) {
                            return val + " - " + opts.w.globals.series[opts.seriesIndex]
                        }
                    },
                    labels: response.category,
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };


                var chartCategory = new ApexCharts(document.querySelector("#piechart_3d_affiliate"), options);
                chartCategory.render();

                $(".chart-height li a").on("click", function () {
                    $(".chart-height li a").removeClass('active');
                    $(this).addClass('active');

                });
            },
            error: function (error) {
                console.error("Error fetching data: ", error);
            }
        });
    }

    // Initial chart rendering
    fetchAndRenderCategoryCharts();

});


// delete affiliate user ( inactive for some days )
$(document).on('click', '.delete-affiliate', function () {
    var id = $(this).data('id');
    if ((window.location.href.indexOf('admin') > -1)) {
        var url = base_url + 'admin/affiliate_users/remove_affiliate';
    } else {
        var url = base_url + 'affiliate/home/remove_affiliate';
    }
    Swal.fire({
        title: 'Are You Sure! All data & media will be remove related to this Affiliate',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Remove it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    type: 'GET',
                    url: url,
                    data: {
                        id: id,
                        status: 7
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result['error'] == false) {
                            Swal.fire('Deleted!', result['message']);
                            if ((window.location.href.indexOf('affiliate') > -1)) {

                                setTimeout(() => {
                                    window.location.reload();
                                }, 3000);
                            }
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', result['message'], 'error');
                        }
                    }
                });
            });
        },
        allowOutsideClick: false
    })
        .then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire(
                    'Cancelled!',
                    'Your data is  safe.',
                    'error'
                );
            }
        });
});


// affiliate categories js
// Add click functionality to category cards
$('.category-card').click(function () {
    const categoryName = $(this).find('.category-title').text() || 'Special Category';
    console.log('Category clicked:', categoryName);
    // You can add navigation or modal functionality here
});

// Add staggered animation to cards
$(document).ready(function () {
    $('.category-card').each(function (index) {
        $(this).css('animation-delay', (index * 0.1) + 's');
    });
});







// Shipping company


// Form submission for add/edit shipping company
// defensive binding: remove any previous handlers then bind
$(document).off('submit', '.add_shipping_company').on('submit', '.add_shipping_company', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    var $form = $(this);
    var $submitBtn = $form.find('button[type="submit"]'); // use local submit button
    var originalBtnHtml = $submitBtn.html();

    // disable button & show waiting state
    $submitBtn.prop('disabled', true).html('Please wait...');

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: $form.attr('action') && $form.attr('action') !== '' ? $form.attr('action') : (base_url + 'admin/shipping_companies/add_shipping_company'),
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        cache: false,
        success: function (result) {
            // re-enable button and restore text (choose appropriate label)
            var submitLabel = (result.message && result.message.indexOf('Added') !== -1) ? 'Add Shipping Company' : 'Update Shipping Company';
            $submitBtn.prop('disabled', false).html(submitLabel);

            if (result.error === false) {
                // hide modal, reset only this form, refresh table
                $form.closest('.modal').modal('hide');
                $form[0].reset();
                $('#shipping_company_data').bootstrapTable('refresh');

                // show toast (no blocking alert)
                if (typeof iziToast !== 'undefined') {
                    iziToast.success({
                        title: 'Success',
                        message: result.message || 'Success'
                    });
                } else {
                    // fallback
                    console.log('Success:', result.message);
                }
            } else {
                if (typeof iziToast !== 'undefined') {
                    iziToast.error({
                        title: 'Error',
                        message: result.message || 'Something went wrong'
                    });
                } else {
                    console.warn('Error:', result.message);
                }
            }

            // Update CSRF tokens if returned
            if (result.csrfName && result.csrfHash) {
                $('input[name="' + result.csrfName + '"]').val(result.csrfHash);
            }
        },
        error: function (xhr, status, error) {
            // restore button
            $submitBtn.prop('disabled', false).html(originalBtnHtml || 'Submit');

            if (typeof iziToast !== 'undefined') {
                iziToast.error({
                    title: 'Error',
                    message: 'An error occurred. Please try again.'
                });
            } else {
                console.error('AJAX error:', error);
            }
        }
    });

    // guarantee default submission doesn't continue
    return false;
});




$(document).on('click', '#delete-shipping-company', function () {
    var id = $(this).data('id');
    Swal.fire({
        title: 'Are You Sure !',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: base_url + 'admin/shipping_companies/delete_shipping_company',
                    type: 'GET',
                    data: {
                        'id': id
                    },
                    dataType: 'json',
                })
                    .done(function (response, textStatus) {
                        if (response.error == false) {
                            console.log(response);
                            Swal.fire('Deleted!', response.message, 'success');
                            $('table').bootstrapTable('refresh');
                        } else {
                            Swal.fire('Oops...', response.message, 'warning');
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR);
                        Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            });
        },
        allowOutsideClick: false
    });
});




// Reusable Select2 initializer for "assign_zipcode" selects
function initAssignZipcode($select, dropdownParentSelector) {
    if (!$select || $select.data('select2-initialized')) return;

    $select.select2({
        placeholder: "Select Zipcodes",
        allowClear: true,
        dropdownParent: dropdownParentSelector ? $(dropdownParentSelector) : $(document.body),
        ajax: {
            url: base_url + 'admin/shipping_companies/get_company_zipcodes',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { search: params.term };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return { id: item.id, text: item.zipcode };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 0
    });

    // Mark as initialized so we don't re-init on the same element
    $select.data('select2-initialized', true);

    // If the server provided initial selected values as JSON in data-selected attribute,
    // populate them here so they show as selected in Select2.
    // Expected format for data-selected: [{"id":"12","zipcode":"400001"}, {"id":"23","zipcode":"400002"}]
    var selectedJson = $select.attr('data-selected');
    if (selectedJson) {
        try {
            var items = JSON.parse(selectedJson);
            items.forEach(function (it) {
                // create a new Option and append to the select
                var option = new Option(it.zipcode, it.id, true, true);
                $select.append(option);
            });
            $select.trigger('change');
        } catch (e) {
            console.warn('assign_zipcode: invalid data-selected JSON', e);
        }
    }
}

// when DOM ready, initialize all matching selects present on the page
$(function () {
    // Use class selector so both modal & page instances work. -> change your HTML select id to class "assign_zipcode"
    $('.assign_zipcode').each(function () {
        // if the select is inside a modal, use the modal as dropdownParent
        var $this = $(this);
        var $modal = $this.closest('.modal');
        var parent = $modal.length ? $modal : $(document.body);
        initAssignZipcode($this, parent);
    });
});

// For dynamically loaded content (like modal body loaded via AJAX), initialize when modal shown
$(document).on('shown.bs.modal', function (e) {
    var $modal = $(e.target);
    $modal.find('.assign_zipcode').each(function () {
        initAssignZipcode($(this), $modal);
    });
});




$(document).on('submit', "#add_shipping_company_form", function (e) {
    e.preventDefault();

    var data = new FormData(this);
    data.append(csrfName, csrfHash);
    data.append("country_code", $(".selected-dial-code").text());
    $.ajax({
        type: "POST",
        url: base_url + 'shipping_company/login/create_shipping_company',
        data: data,
        processData: !1,
        contentType: !1,
        cache: !1,
        dataType: "json",

        success: function (response) {

            csrfName = response.csrfName,
                csrfHash = response.csrfHash;
            if (response.error == true) {
                iziToast.error({
                    message: response.message,
                });

            } else {
                iziToast.success({
                    message: response.message,
                });
                setTimeout(function () {
                    window.location.href = base_url + 'shipping_company/login';
                }, 3000)

            }
        }
    })
});



// cash collection and fund transfer

// JavaScript for Shipping Company Cash Collection

$(document).ready(function () {


    // Handle edit cash collection button
    $(document).on('click', '.edit_cash_collection_btn', function () {
        var id = $(this).data('id');
        var order_id = $(this).data('order-id');
        var amount = $(this).data('amount');
        var company_id = $(this).data('company-id');

        $('#transaction_id').val(id);
        $('#order_id').val(order_id);
        $('#amount').val(amount);
        $('#order_amount').val(amount);
        $('#shipping_company_id').val(company_id);

        $('#details').val('Order ID: ' + order_id + '\nAmount: ' + amount);
    });
});

// Query params for cash collection table
function cash_collection_query_params(p) {
    return {
        limit: p.limit,
        sort: p.sort,
        order: p.order,
        offset: p.offset,
        search: p.search,
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val(),
        filter_status: $('#filter_status').val(),
        filter_company: $('#filter_company').val()
    };
}

// Filter function
function status_date_wise_search() {
    var date = $('#datepicker').val();
    if (date !== '') {
        var dates = date.split(' - ');
        var start_date = dates[0].split('-').reverse().join('-');
        var end_date = dates[1].split('-').reverse().join('-');
        $('#start_date').val(start_date);
        $('#end_date').val(end_date);
    }

    $('table').bootstrapTable('refresh');
}

// Reset form on modal close
$('#cash_collection_model').on('hidden.bs.modal', function () {
    $(this).find('form')[0].reset();
    $('#transaction_id').val('');
    $('#order_id').val('');
    $('#shipping_company_id').val('');
    $('#details').val('');
});

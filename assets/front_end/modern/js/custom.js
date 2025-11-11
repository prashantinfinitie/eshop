"use strict";
var quickViewgalleryThumbs, mobile_image_swiper, quickViewgalleryTop, galleryTop, galleryThumbs, custom_url = location.href,
    is_rtl = $("#body").data("is-rtl"),
    mode = 1 == is_rtl ? "right" : "left";
const is_loggedin = $("#is_loggedin").val(),
    Toast = Swal.mixin({
        toast: !0,
        position: "top-end",
        showConfirmButton: !1,
        timer: 3e3,
        timerProgressBar: !0
    });

function queryParams(e) {
    return {
        limit: e.limit,
        sort: e.sort,
        order: e.order,
        offset: e.offset,
        search: e.search
    }
}
var currency = $('#currency').val();
var auth_settings = $('#auth_settings').val();
var allow_items_in_cart = $('#allow_items_in_cart').val();
var decimal_point = $('#decimal_point').val();
var low_stock_limit = $('#low_stock_limit').val();
var seller_low_stock_limit = $('#seller_low_stock_limit').val();

// Function to get the effective low stock limit for a product
function getEffectiveLowStockLimit(productLowStockLimit) {
    // If product has a low stock limit set and it's greater than 0, use that
    if (productLowStockLimit && parseFloat(productLowStockLimit) > 0) {
        return parseFloat(productLowStockLimit);
    }
    // Otherwise use seller's low stock limit
    return parseFloat(seller_low_stock_limit);
}

$(document).on('select2:open', () => {
    document.querySelector('.select2-search__field').focus();
});

$(document).ready(function () {
    $('.description_img img').each(function () {
        $('img').removeAttr('height');

    });
});

if (auth_settings == "firebase") {

    function onSignInSubmit(e) {
        if (e.preventDefault(), isPhoneNumberValid()) {
            $("#send-otp-button").html("Please Wait...");
            var t = is_user_exist();
            if (updateSignInButtonUI(), 1 == t.error) $("#is-user-exist-error").html(t.message), $("#send-otp-button").html("Send OTP");
            else {
                window.signingIn = !0;
                var a = getPhoneNumberFromUserInput(),
                    r = window.recaptchaVerifier;
                firebase.auth().signInWithPhoneNumber(a, r).then(function (e) {
                    $("#send-otp-button").html("Send OTP"), $(".send-otp-form").unblock(), window.signingIn = !1, updateSignInButtonUI(), resetRecaptcha(),
                        $("#send-otp-form").hide(),
                        $("#otp_div").show(),
                        $("#verify-otp-form").removeClass("d-none"),
                        $(document).on("submit", "#verify-otp-form", function (t) {
                            t.preventDefault(), $("#registration-error").html("");
                            var a = $("#otp").val(),
                                r = new FormData(this),
                                s = $(this).attr("action");
                            $("#register_submit_btn").html("Please Wait...").attr("disabled", !0), e.confirm(a).then(function (e) {
                                r.append(csrfName, csrfHash), r.append("mobile", $("#phone-number").val()), r.append("country_code", $(".selected-dial-code").text()), $.ajax({
                                    type: "POST",
                                    url: s,
                                    data: r,
                                    processData: !1,
                                    contentType: !1,
                                    cache: !1,
                                    dataType: "json",
                                    beforeSend: function () {
                                        $("#register_submit_btn").html("Please Wait...").attr("disabled", !0)
                                    },
                                    success: function (e) {
                                        csrfName = e.csrfName,
                                            csrfHash = e.csrfHash;
                                        if (e.error == true) {
                                            $("#register_submit_btn").html("Submit").attr("disabled", !1),
                                                Toast.fire({
                                                    icon: "error",
                                                    title: e.message
                                                });
                                        } else {
                                            Toast.fire({
                                                icon: "success",
                                                title: e.message
                                            });
                                            $("#register_submit_btn").html("Submit").attr("disabled", !1), $("#registration-error").html(e.message).show();
                                            $("#modal-signup").hide();
                                            $('#modal-signup').addClass('d-none');
                                            $("#modal-signin").show();
                                            $('#modal-signin').addClass('d-block show');
                                        }
                                    }
                                })
                            }).catch(function (e) {
                                $("#register_submit_btn").html("Please Wait...").attr("disabled", !0), $("#registration-error").html("Invalid OTP. Please Enter Valid OTP").show()
                            })
                        })
                }).catch(function (e) {
                    window.signingIn = !1, $("#is-user-exist-error").html(e.message).show(), $("#send-otp-button").html("Send OTP"), updateSignInButtonUI(), resetRecaptcha()
                })
            }
        }
    }

    window.onload = function () {
        document.getElementById("send-otp-form").addEventListener("submit", onSignInSubmit),
            document.getElementById("phone-number").addEventListener("keyup", updateSignInButtonUI),
            document.getElementById("phone-number").addEventListener("change", updateSignInButtonUI)
    }
    function getPhoneNumberFromUserInput() {
        return $(".selected-dial-code").html() + $("#phone-number").val()
    }

    function isPhoneNumberValid() {
        return -1 !== getPhoneNumberFromUserInput().search(/^\+[0-9\s\-\(\)]+$/)
    }
}
function resetRecaptcha() {
    return window.recaptchaVerifier.render().then(function (e) {
        grecaptcha.reset(e)
    })
}


if (auth_settings == "sms") {
    $(document).on("click", "#send-otp-button", function (e) {
        e.preventDefault();

        var t = $("#phone-number").val();
        var country_code = $(".selected-dial-code").text();

        $.ajax({
            type: "POST",
            async: !1,
            url: base_url + "auth/verify_user",
            data: {
                mobile: t,
                country_code: country_code,
                [csrfName]: csrfHash
            },
            dataType: "json",
            success: function (e) {
                csrfName = e.csrfName,
                    csrfHash = e.csrfHash;
                if (e.error == true) {
                    $("#registration-user-error").html(e.message).show();
                    Toast.fire({
                        icon: "error",
                        title: e.message
                    });
                } else {

                    $("#send-otp-form").hide(),
                        $("#verify-otp-form").removeClass("d-none");
                }
            }
        })
    });

    $(document).on("submit", "#verify-otp-form", function (t) {
        t.preventDefault(),
            $("#registration-error").html("");
        var a = $("#otp").val(),
            r = new FormData(this),
            s = $(this).attr("action");
        $("#register_submit_btn").html("Please Wait...").attr("disabled", !0);
        r.append(csrfName, csrfHash),
            r.append("mobile", $("#phone-number").val()),
            r.append("country_code", $(".selected-dial-code").text()),
            $.ajax({
                type: "POST",
                url: s,
                data: r,
                processData: !1,
                contentType: !1,
                cache: !1,
                dataType: "json",
                beforeSend: function () {
                    $("#register_submit_btn").html("Please Wait...").attr("disabled", !0)
                },
                success: function (e) {
                    csrfName = e.csrfName;
                    csrfHash = e.csrfHash;
                    if (e.error == true) {
                        $("#register_submit_btn").html("Submit").attr("disabled", !1),
                            Toast.fire({
                                icon: "error",
                                title: e.message
                            });
                    } else {
                        Toast.fire({
                            icon: "success",
                            title: e.message
                        });
                        $("#register_submit_btn").html("Submit").attr("disabled", !1),
                            $("#registration-error").html(e.message).show();
                        $("#modal-signup").hide();
                        $('#modal-signup').addClass('d-none');
                        $("#modal-signin").show();
                        $('#modal-signin').addClass('d-block show');
                        $("#modal-signup").attr("aria-hidden", "true"); // Hide modal from screen readers
                        $("#modal-signup").attr("inert", "true"); // Add inert to prevent interaction with the modal
                        $("#main-content").removeAttr("inert"); // Restore interactivity with the main content
                    }
                }
            })
        // })
    })

    $("#modal-signin").on("hidden.bs.modal", function () {
        if ($("#login_div").hasClass("hide")) {
            $("#login_div").removeClass("hide");
            $("#forgot_password_div").addClass("hide");

        }
    });

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
                county_code: country_code,
                forget_password_val: forget_password_val,
                [csrfName]: csrfHash
            },
            dataType: "json",
            success: function (e) {
                if (e.error == false) {
                    csrfName = e.csrfName,
                        csrfHash = e.csrfHash,
                        $('#verify_forgot_password_otp_form').removeClass('d-none');
                    $('#send_forgot_password_otp_form').hide();
                    $("#verify-otp-form").removeClass("d-none");
                } else {
                    Toast.fire({
                        icon: "error",
                        title: e.message
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

function updateSignInButtonUI() { }

function is_user_exist(e = "") {
    var country_code = $(".selected-dial-code").text();
    if ("" == e) var t = $("#phone-number").val();
    else t = e;
    var a;
    return $.ajax({
        type: "POST",
        async: !1,
        url: base_url + "auth/verify_user",
        data: {
            mobile: t,
            country_code: country_code,
            [csrfName]: csrfHash
        },
        dataType: "json",
        success: function (e) {
            csrfName = e.csrfName, csrfHash = e.csrfHash, a = e
        }
    }), a
}

function formatRepo(e) {

    // First part for suggestion keyword
    var s = "";
    if (e.suggestion_keyword) {
        s = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta_icon d-flex flex-row justify-content-between align-items-center'>" +
            "<a href=" + base_url + "products/search?q=" + encodeURIComponent(e.suggestion_keyword) + " class='text-dark text-decoration-none d-flex flex-row align-items-center'>" +
            "<div class='select2-result-repository__icon mx-0'><i class='fa fa-search'></i></div>" +
            "<div class='select2-result-repository__title' id='search_word'>" + e.suggestion_keyword + "</div>" +
            "</a>" +
            "<button class='select2-result-repository__icon search_btn' onclick=\"copySearchMobile('" + e.suggestion_keyword + "')\"><i class='font-weight-bold fs-20 uil uil-arrow-up-left'></i></button>" +
            "</div></div>";
    }

    // Second part for product details
    var t = "<div class='select2-result-repository clearfix'>";
    // Check if the image exists
    if (e.image_sm) {
        t += "<div class='select2-result-repository__avatar'><img src='" + e.image_sm + "' /></div>";
    }
    t += "<div class='select2-result-repository__meta'>";
    // Check if the name exists
    if (e.name) {
        t += "<div class='select2-result-repository__title'>" + e.name + "</div>";
    }
    // Check if the category exists
    if (e.category_name) {
        t += "<div class='select2-result-repository__description'> In " + e.category_name + "</div>";
    }
    // Close the meta and main container
    t += "</div></div>";
    // Combine the two parts: first keywords (s) and then products (t)
    if (e.loading) return e.text;
    return s + t;

}

function copySearchMobile(keyword) {


    // Copy the keyword to the clipboard
    navigator.clipboard.writeText(keyword).then(function () {

        // Set the copied keyword to the input field (assuming an input field with a specific class or ID)
        document.querySelector('.select2-search__field').value = keyword;

        // Optionally, trigger the search or any other actions
        document.querySelector('#search-product').dispatchEvent(new Event('input')); // Trigger an input event if needed
    }).catch(function (err) {
        console.error('Error copying keyword: ', err);
    });
}

function copySearch(keyword) {


    // Copy the keyword to the clipboard
    navigator.clipboard.writeText(keyword).then(function () {

        // Set the copied keyword to the input field (assuming an input field with a specific class or ID)
        document.querySelector('#search-product').value = keyword;

        search_product_result();

    }).catch(function (err) {
        console.error('Error copying keyword: ', err);
    });
}


function formatRepoSelection(e) {
    const displayText = e.suggestion_keyword || e.name || e.text;
    // Limit to 50 characters, for example
    return displayText.length > 40 ? displayText.substring(0, 30) + "..." : displayText;
}


$(document).on('submit', '.form-submit-event', function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    var form_id = $(this).attr("id");
    var error_box = $('#error_box', this);
    var submit_btn = $(this).find('.submit_btn');
    var btn_html = $(this).find('.submit_btn').html();
    var btn_val = $(this).find('.submit_btn').val();
    var button_text = (btn_html != '' || btn_html != 'undefined') ? btn_html : btn_val;
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
            console.log(result);

            csrfName = result['csrfName'];
            csrfHash = result['csrfHash'];
            if (result['error'] == true) {
                error_box.addClass("rounded p-3 alert alert-danger").removeClass('d-none alert-success');
                error_box.show().delay(5000).fadeOut();
                error_box.html(result['message']);
                submit_btn.html(button_text);
                submit_btn.attr('disabled', false);
            } else {
                error_box.addClass("rounded p-3 alert alert-success").removeClass('d-none alert-danger');
                error_box.show().delay(3000).fadeOut();
                error_box.html(result['message']);
                submit_btn.html(button_text);
                submit_btn.attr('disabled', false);
                $('.form-submit-event')[0].reset();
                if (form_id == 'login_form') {
                    cart_sync();
                }
                setTimeout(function () { location.reload(); }, 600);
            }
        }
    });
});

$(document).on("click", "#resend-otp", function (e) {
    e.preventDefault()
}),

    $(document).on("click", "#user_logout", function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Are You Sure!',
            text: "You won't to logout",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes!',
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: 'POST',
                        url: base_url + 'login/logout',
                        data: {
                            [csrfName]: csrfHash
                        },
                        dataType: 'json',
                        success: function (result) {
                            csrfName = result['csrfName'];
                            csrfHash = result['csrfHash'];
                            localStorage.removeItem("compare");
                            Swal.fire('Success', 'Logout successfully !', 'success');
                            setTimeout(function () {
                                window.location.reload();
                            }, 600);

                        }
                    });
                });
            },
            allowOutsideClick: false
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire('Cancelled!', 'You are logged in', 'error');
            }
        });
    }),

    $(document).on("submit", ".sign-up-form", function (e) {
        e.preventDefault();
        var t = $(".selected-dial-code").html();
        $phonenumber = $("#phone-number").val(), $username = $('input[name="username"]').val(), $email = $('input[name="email"]').val(), $passwd = $('input[name="password"]').val();
        $.ajax({
            type: "POST",
            url: base_url + "auth/register_user",
            data: {
                country_code: t,
                type: "phone",
                mobile: $phonenumber,
                name: $username,
                email: $email,
                password: $passwd,
                [csrfName]: csrfHash
            },
            dataType: "json",
            success: function (result) {
                if (result.error == true) {
                    $('#sign-up-error').html('<span class="text-danger" >' + result.message + '</span>');
                }
            }
        })
    });
var search_products_mobile = $(".search_product_mobile").select2({
    ajax: {
        url: base_url + "home/get_products",
        dataType: "json",
        delay: 250,
        data: function (e) {
            return {
                search: e.term,
                page: e.page
            }
        },
        processResults: function (e, t) {
            // Ensure both e.results and e.suggestion_keywords are defined and arrays
            var suggestion_keywords = Array.isArray(e.suggestion_keywords) ? e.suggestion_keywords : [];
            var results = Array.isArray(e.data) ? e.data : [];


            // Combine both arrays
            var combinedResults = suggestion_keywords.concat(results);
            return console.log(e), t.page = t.page || 1, {
                results: combinedResults,
                pagination: {
                    more: 30 * t.page < e.total
                }
            }
        },
        cache: !0
    },
    theme: "bootstrap-5",
    escapeMarkup: function (e) {
        return e
    },
    minimumInputLength: 1,
    templateResult: formatRepo,
    templateSelection: formatRepoSelection,
    placeholder: "Search for products, brands or categories"
});

search_products_mobile.on("select2:select", function (e) {
    var t = e.params.data;
    null != t.link && null != t.link && (window.location.href = t.link)
});


$("#leftside-navigation .sub-menu > a").click(function (e) {
    $("#leftside-navigation ul ul").slideUp(), !$("#leftside-navigation .sub-menu > a").next().is(":visible") && $("#leftside-navigation .sub-menu > a").find(".arrow").removeClass("fa-angle-down").addClass("fa-angle-left"), $(this).find(".arrow").hasClass("fa-angle-left") ? $(this).find(".arrow").removeClass("fa-angle-left").addClass("fa-angle-down") : $(this).find(".arrow").removeClass("fa-angle-down").addClass("fa-angle-left"), $(this).next().is(":visible") || $(this).next().slideDown(), e.stopPropagation()
}), $("li.has-ul").click(function () {
    $(this).children(".sub-ul").slideToggle(500), $(this).toggleClass("active"), event.preventDefault()
}),

    $(".add-to-fav-btn").on("click", function (e) {
        e.preventDefault();
        var t = new FormData,
            a = $(this).data("product-id"),
            r = $(this);
        t.append(csrfName, csrfHash), t.append("product_id", a), $.ajax({
            type: "POST",
            url: base_url + "my-account/manage-favorites",
            data: t,
            cache: !1,
            contentType: !1,
            processData: !1,
            dataType: "json",
            success: function (e) {

                csrfName = e.csrfName;
                csrfHash = e.csrfHash;
                if (e.error == true) {
                    Toast.fire({
                        icon: "error",
                        title: e.message
                    });
                } else {
                    if (r.hasClass("fa-heart-o")) {
                        r.removeClass("fa-heart-o");
                        r.addClass("fa-heart text-danger");
                        Toast.fire({
                            icon: "success",
                            title: e.message
                        });
                    } else if (r.hasClass("fa-heart")) {
                        r.removeClass("fa-heart text-danger");
                        r.addClass("fa-heart-o").css("color", "");
                        Toast.fire({
                            icon: "success",
                            title: e.message
                        });
                    }

                }
            }
        })
    }),

    $(document).on("click", "#add_to_favorite_btn", function (e) {
        e.preventDefault();
        var t = new FormData,
            a = $(this).data("product-id"),
            r = $(this),
            s = $(this).html();
        t.append(csrfName, csrfHash), t.append("product_id", a), $.ajax({
            type: "POST",
            url: base_url + "my-account/manage-favorites",
            data: t,
            cache: !1,
            contentType: !1,
            processData: !1,
            dataType: "json",
            beforeSend: function () {
                r.addClass("disabled"), r.find("span").text("Please wait")
            },
            success: function (e) {
                csrfName = e.csrfName;
                csrfHash = e.csrfHash;
                if (e.error == true) {
                    Toast.fire({
                        icon: "error",
                        title: e.message
                    });
                } else {

                    r.removeClass("disabled")
                    if (r.children().hasClass("fa-heart-o")) {
                        r.children().removeClass("fa-heart-o");
                        r.children().addClass("fa-heart").css("color", "red");
                        Toast.fire({
                            icon: "success",
                            title: e.message
                        });
                    } else if (r.children().hasClass("fa-heart")) {
                        r.children().removeClass("fa-heart");
                        r.children().addClass("fa-heart-o").css("color", "");
                        Toast.fire({
                            icon: "success",
                            title: e.message
                        });
                    }
                }
            }
        })
    }),

    $(function () {
        if ($(".auth-modal").iziModal({
            overlayClose: !1,
            overlayColor: "rgba(0, 0, 0, 0.6)"
        }),

            $("#user-review-images").length) {
            var e;
            e = $("#review-image-title").data("review-title");
            var t = $("#review-image-title").data("product-id"),
                a = "";

            $("#user-review-images").iziModal({
                overlayClose: !1,
                overlayColor: "rgba(0, 0, 0, 0.6)",
                title: e,
                arrowKeys: !1,
                fullscreen: !0,
                onOpening: function (e) {
                    e.startLoading();
                    var a = $("#review-image-title").data("review-limit"),
                        s = $("#review-image-title").data("review-offset"),
                        i = $("#review-image-title").data("reached-end");
                    $("#load_more_div").html('<div id="load_more"></div>'), 0 == i && r(t, a, s), e.stopLoading()
                },
                onOpened: function () {
                    $("div").bind("wheel", function (e) {
                        if ($("#load_more").length && $(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
                            var t = $("#review-image-title").data("product-id"),
                                a = $("#review-image-title").data("review-limit"),
                                s = $("#review-image-title").data("review-offset");
                            0 == $("#review-image-title").data("reached-end") && r(t, a, s)
                        }
                    })
                }
            })
        }

        function r(e, t, r, s = "#user_image_data") {
            $("#review-image-title").data("review-offset", r + t), $.getJSON(base_url + "products/get_rating?product_id=" + e + "&has_images=1&limit=" + t + "&offset=" + r, function (e) {
                $("#review-image-title").data("review-offset", r + t), a = "";
                var i = 0;
                if (0 == e.error)
                    for (var o = 0; o < e.data.product_rating.length; o++) {
                        i = e.data.product_rating[o];
                        for (var n = 0; n < i.images.length; n++) {
                            var c = i.images;
                            a += "<div class='review-box m-2'><a href='" + c[n] + "' data-lightbox='review-images-12345' data-title='<font >" + i.rating + " ★</font></br>" + i.user_name + "<br>" + i.comment + "'><img src='" + c[n] + "' alt='Review Image' style='height: 70px; width: 70px;'></a></div>"
                        }
                    } else $("#review-image-title").data("reached-end", "true");
                $(s).append(a)
            })
        }
        $("#seller_info").length && $("#seller_info").iziModal({
            overlayClose: !0,
            overlayColor: "rgba(0, 0, 0, 0.6)",
            title: "Sold By",
            headerColor: "#f44336c4",
            arrowKeys: !1,
            fullscreen: !0,
            onOpening: function (e) {
                e.startLoading(), e.stopLoading()
            }
        }),


            $("#quick-view").iziModal({

                overlayClose: !1,
                overlayColor: "rgba(0, 0, 0, 0.6)",
                width: 1e3,
                onOpening: function (modal) {

                    modal.startLoading();

                    $('#modal-product-tags').html('');

                    $.getJSON(base_url + 'products/get-details/' + modal.$element.data('dataProductId'), function (data) {

                        var total_images = 0;
                        $('#modal-add-to-cart-button').attr('data-product-id', data.id);
                        $('#modal-buy-now-button').attr('data-product-id', data.id);
                        $('#modal-add-to-cart-button').attr('data-product-slug', data.slug);
                        $('#modal-add-to-cart-button').attr('data-product-low-stock-limit', data.low_stock_limit);
                        $('#modal-buy-now-button').attr('data-product-slug', data.slug);
                        $('#modal-buy-now-button').attr('data-product-low-stock-limit', data.low_stock_limit);

                        if (data.type == "simple_product" || data.type == "digital_product") {
                            $('#modal-add-to-cart-button').attr('data-product-variant-id', data.variants[0].id);
                            $('#modal-buy-now-button').attr('data-product-variant-id', data.variants[0].id);
                        } else {
                            $('#modal-add-to-cart-button').attr('data-product-variant-id', '');
                            $('#modal-buy-now-button').attr('data-product-variant-id', '');
                        }
                        if (data.minimum_order_quantity != 1 && data.minimum_order_quantity != '' && data.minimum_order_quantity != 'undefined') {
                            $(".in-num").attr({
                                "data-min": data.minimum_order_quantity, // values (or variables) here
                                "value": data.minimum_order_quantity
                            });
                            $(".minus").attr({
                                "data-min": data.minimum_order_quantity, // values (or variables) here
                                "value": data.minimum_order_quantity
                            });
                            $("#modal-add-to-cart-button").attr({
                                "data-min": data.minimum_order_quantity, // values (or variables) here
                                "value": data.minimum_order_quantity
                            });
                            $("#modal-buy-now-button").attr({
                                "data-min": data.minimum_order_quantity, // values (or variables) here
                                "value": data.minimum_order_quantity
                            });

                        } else {
                            $(".in-num").attr({
                                "data-min": 1, // values (or variables) here
                                "value": 1
                            });
                            $(".minus").attr({
                                "data-min": 1, // values (or variables) here
                                "value": 1
                            });
                            $("#modal-add-to-cart-button").attr({
                                "data-min": 1 // values (or variables) here
                            });
                            $("#modal-buy-now-button").attr({
                                "data-min": 1 // values (or variables) here
                            });

                        }

                        if (data.quantity_step_size != 1 && data.quantity_step_size != '' && data.quantity_step_size != 'undefined') {
                            $(".in-num").attr({
                                "data-step": data.quantity_step_size // values (or variables) here
                            });
                            $(".minus").attr({
                                "data-step": data.quantity_step_size // values (or variables) here
                            })
                            $(".plus").attr({
                                "data-step": data.quantity_step_size // values (or variables) here
                            })

                            $("#modal-add-to-cart-button").attr({
                                "data-step": data.quantity_step_size // values (or variables) here
                            })
                            $("#modal-buy-now-button").attr({
                                "data-step": data.quantity_step_size // values (or variables) here
                            })

                        } else {
                            $(".in-num").attr({
                                "data-step": 1 // values (or variables) here
                            });
                            $(".minus").attr({
                                "data-step": 1 // values (or variables) here
                            })
                            $(".plus").attr({
                                "data-step": 1 // values (or variables) here
                            })
                            $("#modal-add-to-cart-button").attr({
                                "data-step": 1 // values (or variables) here
                            })
                            $("#modal-buy-now-button").attr({
                                "data-step": 1 // values (or variables) here
                            })

                        }

                        if (data.total_allowed_quantity != '' && data.total_allowed_quantity != 'undefined' && data.total_allowed_quantity != null) {
                            $(".in-num").attr({
                                "data-max": data.total_allowed_quantity // values (or variables) here
                            });
                            $(".plus").attr({
                                "data-max": data.total_allowed_quantity // values (or variables) here
                            })
                            $("#modal-add-to-cart-button").attr({
                                "data-max": data.total_allowed_quantity // values (or variables) here
                            })
                            $("#modal-buy-now-button").attr({
                                "data-max": data.total_allowed_quantity // values (or variables) here
                            })
                        } else {
                            $(".in-num").attr({
                                "data-max": 1 // values (or variables) here
                            });
                            $(".plus").attr({
                                "data-max": 1 // values (or variables) here
                            });
                            $("#modal-add-to-cart-button").attr({
                                "data-max": 1 // values (or variables) here
                            })
                            $("#modal-buy-now-button").attr({
                                "data-max": 1 // values (or variables) here
                            })
                        }

                        var title_slug = "";

                        if (data.name) {
                            var title_slug = '<a class="text-decoration-none" title="' + data.name + '" target="_blank" href="' + base_url + 'products/details/' + data.product_slug + '"><p class="text-dark">' + data.name + '</p></a>';
                            $('#modal-product-title').html(title_slug);
                        }
                        // $('#modal-product-short-description').text(processDescription(data.short_description, 20));
                        $('#modal-product-short-description').html(data.short_description);
                        if (data.type == 'simple_product') {
                            var product_stock = data.stock;
                        } else {
                            product_stock = data.total_stock;
                        }

                        $('#modal-product-total-stock').attr({ 'data-stock': product_stock });
                        $('#modal-product-rating').rating('update', data.rating);

                        if ((data.variants[0].special_price < data.variants[0].price) && (data.variants[0].special_price != 0)) {
                            var price = data.variants[0].special_price
                        } else {
                            var price = data.variants[0].price
                        }
                        $('#modal-product-price').html(currency + " " + data.variants[0].special_price);
                        $('#modal-product-special-price').html(currency + " " + data.variants[0].price);

                        //Quick View Product Modal Gallery Swiper

                        quickViewgalleryThumbs = new Swiper('.gallery-thumbs', {
                            spaceBetween: 10,
                            slidesPerView: 4,
                            freeMode: true,
                            watchSlidesVisibility: true,
                            watchSlidesProgress: true,
                        });

                        quickViewgalleryTop = new Swiper('.gallery-top', {
                            spaceBetween: 10,
                            navigation: {
                                nextEl: '.swiper-button-next',
                                prevEl: '.swiper-button-prev',
                            },
                            thumbs: {
                                swiper: quickViewgalleryThumbs
                            },
                            clickable: true
                        });

                        //preview-image-swiper 

                        mobile_image_swiper = new Swiper('.mobile-image-swiper', {
                            pagination: {
                                el: '.mobile-image-swiper-pagination',
                            },
                            navigation: {
                                nextEl: '.swiper-button-next',
                                prevEl: '.swiper-button-prev',
                            },
                            clickable: true
                        });

                        quickViewgalleryThumbs.removeAllSlides();
                        quickViewgalleryTop.removeAllSlides();
                        mobile_image_swiper.removeAllSlides();


                        var thumb_images = $('<div class="swiper-slide" style="height:100px; width:108px;">' +
                            '<img src="' + base_url + 'media/image?path=' + data.relative_path + '&width=400&quality=80' + '" alt="" />' +
                            '</div>');
                        $(".swiper-wrapper-thumbs").append(thumb_images);


                        var main_images = $('<div class="swiper-slide swiper-image"><div class=product-view-image-container" id="product-view-image-container">' +
                            '<img src="' + base_url + 'media/image?path=' + data.relative_path + '&width=900&quality=80' + '" class="rounded" alt="" />' +
                            '</div></div>');
                        $(".swiper-wrapper-main").append(main_images);

                        var mobile_slider_image = $('<div class="swiper-slide text-center"><img src="' + base_url + 'media/image?path=' + data.relative_path + '&width=900&quality=80' + '"></div>');
                        $(".mobile-swiper").append(mobile_slider_image);

                        var variant_relative_path = data.variants.map(function (value, index) {
                            return value.variant_relative_path;
                        });

                        $.each(variant_relative_path, function (i, images) {

                            if (images != null && images != '') {

                                $.each(images, function (i, url) {
                                    var thumb_images = $('<div class="swiper-slide" style="height:100px; width:108px;">' +
                                        '<img src="' + base_url + 'media/image?path=' + url + '&width=400&quality=80' + '" alt="" />' +
                                        '</div>');
                                    $(".swiper-wrapper-thumbs").append(thumb_images);

                                    var main_images = $('<div class="swiper-slide swiper-image"><div class=product-view-image-container">' +
                                        '<img src="' + base_url + 'media/image?path=' + url + '&width=900&quality=80' + '" class="rounded" alt="" />' +
                                        '</div></div>');
                                    $(".swiper-wrapper-main").append(main_images);

                                    mobile_slider_image = $('<div class="swiper-slide text-center"><img src="' + base_url + 'media/image?path=' + url + '&width=900&quality=80' + '"></div>');

                                    $(".mobile-swiper").append(mobile_slider_image);

                                });
                            }
                        });


                        $.each(data.other_images_relative_path, function (i, url) {

                            total_images++;

                            var thumb_images = $('<div class="swiper-slide" style="height:100px; width:108px;">' +
                                '<img src="' + base_url + 'media/image?path=' + url + '&width=400&quality=80' + '" alt="" />' +
                                '</div>');
                            $(".swiper-wrapper-thumbs").append(thumb_images);

                            var main_images = $('<div class="swiper-slide swiper-image"><div class="product-view-image-container">' +
                                '<img src="' + base_url + 'media/image?path=' + url + '&width=900&quality=80' + '" class="rounded" alt="" />' +
                                '</div></div>');
                            $(".swiper-wrapper-main").append(main_images);

                            mobile_slider_image = $('<div class="swiper-slide text-center"><img src="' + base_url + 'media/image?path=' + url + '&width=900&quality=80' + '"></div>');
                            $(".mobile-swiper").append(mobile_slider_image);

                        });

                        if (thumb_images.length > 1) {
                            quickViewgalleryThumbs.addSlide(1, thumb_images);
                        }
                        if (main_images.length > 1) {
                            quickViewgalleryTop.addSlide(1, main_images);

                        }
                        if (mobile_slider_image.length > 1) {
                            mobile_image_swiper.addSlide(1, mobile_slider_image);
                        }


                        var variant_attributes = '';

                        var is_image = 0;

                        var is_color = 0;

                        $.each(data.variant_attributes, function (i, e) {

                            var attribute_ids = e.ids.split(',');
                            var attribute_values = e.values.split(',');
                            var swatche_types = e.swatche_type.split(',');
                            var swatche_values = e.swatche_value.split(',');
                            var style = '<style> .product-page-details .btn-group>.active { border: 1px solid black;}</style>';

                            if (attribute_ids != '') {

                                variant_attributes += '<h4>' + e.attr_name + '</h4><div class="ml-1"><div class="btn-group btn-group-toggle gap-1" data-toggle="buttons">';

                                $.each(attribute_ids, function (j, id) {

                                    var color_code = "";

                                    if (swatche_types[j] == "1") {

                                        is_color = 1;

                                        color_code = 'style="background-color:' + swatche_values[j] + '";';

                                        variant_attributes += '<style> .product-page-details .btn-group>.active { border: 1px solid black;}</style>' +

                                            '<label class="btn text-center fullCircle rounded-circle p-3 h-0"' + color_code + '>' +

                                            '<input type="radio" name="' + e.attr_name + '" value="' + id + '" class="modal-product-attributes" autocomplete="off"><br>' +

                                            '</label>';

                                    } else if (swatche_types[j] == "2") {

                                        is_image = 1;

                                        variant_attributes += '<style> .product-page-details .btn-group>.active { color: #000000; border: 1px solid black;}</style>' + '<label class="btn text-center bg-transparent h-10 w-10">' +

                                            '<img class="swatche-image h-10 w-10" src="' + swatche_values[j] + '">' +

                                            '<input type="radio" name="' + e.attr_name + '" value="' + id + '" class="modal-product-attributes" autocomplete="off"><br>' +

                                            '</label>';

                                    } else {

                                        var style1 = '<style> .product-page-details .btn-group>.active { background-color: var(--primary-color);color: white!important;}</style>';

                                        variant_attributes += style1 +
                                            '<label class="btn btn-default text-center rounded-2 btn-aqua btn-sm">' +
                                            '<input type="radio" name="' + e.attr_name + '" value="' + id + '" class="modal-product-attributes" autocomplete="off">' + attribute_values[j] + '<br>' +
                                            '</label>';
                                    }
                                });
                                variant_attributes += '</div></div>';
                            }
                        });

                        var className = (data.is_deliverable == false) ? "danger" : "success";
                        var is_not = (data.is_deliverable == false) ? "not" : "";
                        var err_msg = (data.zipcode != "" && typeof data.zipcode !== 'undefined') ? '<b class="text-' + className + '">Product is ' + is_not + ' delivarable on &quot; ' + data.zipcode + ' &quot; </b>' : "";

                        if (data.check_deliverability.city_wise_deliverability == 1) {
                            if (data.type != "digital_product") {
                                variant_attributes +=
                                    '<form class="mt-2 validate_city_quick_view "   method="post" >' +
                                    '<div class="d-flex flex-nowrap input-group">' +
                                    '<div class="pl-0">' +
                                    '<input type="hidden" name="product_id" value="' + data.id + '">' +
                                    '<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">' +
                                    '<input type="text" class="form-control" id="city" placeholder="city" name="city" required value="">' +
                                    '</div>' +
                                    '<button type="submit" class="btn btn-sm ml-0 btn-primary check-availability" data-product_id="' + data.id + '"  data-city=""  id="validate_city">Check Availability</button>' +
                                    '</div>' +
                                    '<div class="mt-2" id="error_box1">' +
                                    err_msg +
                                    ' </div>' +
                                    ' </form>';
                            } else {
                                variant_attributes +=
                                    '<form class="mt-2 validate_city_quick_view "   method="post" >' +
                                    '<div class="d-flex">' +
                                    '<div class=" col-md-6 pl-0">' +
                                    '<input type="hidden" name="product_id" value="' + data.id + '">' +
                                    '<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">' +
                                    '</div>' +
                                    '</div>' +
                                    '<div class="mt-2" id="error_box1">' +
                                    err_msg +
                                    ' </div>' +
                                    ' </form>';
                            }
                        }
                        if (data.check_deliverability.pincode_wise_deliverability == 1) {

                            if (data.type != "digital_product") {
                                variant_attributes +=
                                    '<form class="mt-2 validate_zipcode_quick_view "   method="post" >' +
                                    '<div class="d-flex flex-nowrap input-group">' +
                                    '<div class="pl-0">' +
                                    '<input type="hidden" name="product_id" value="' + data.id + '">' +
                                    '<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">' +
                                    '<input type="text" class="form-control" id="zipcode" placeholder="Zipcode" name="zipcode" required value="' + data.zipcode + '">' +
                                    '</div>' +
                                    '<button type="submit" class="btn btn-sm ml-0 btn-primary check-availability" data-product_id="' + data.id + '"  data-zipcode="' + data.zipcode + '"  id="validate_zipcode">Check Availability</button>' +
                                    '</div>' +
                                    '<div class="mt-2" id="error_box1">' +
                                    err_msg +
                                    ' </div>' +
                                    ' </form>';
                            } else {
                                variant_attributes +=
                                    '<form class="mt-2 validate_zipcode_quick_view "   method="post" >' +
                                    '<div class="d-flex">' +
                                    '<div class=" col-md-6 pl-0">' +
                                    '<input type="hidden" name="product_id" value="' + data.id + '">' +
                                    '<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">' +
                                    '</div>' +
                                    '</div>' +
                                    '<div class="mt-2" id="error_box1">' +
                                    err_msg +
                                    ' </div>' +
                                    ' </form>';
                            }
                        }

                        $('#modal-product-variant-attributes').html(variant_attributes);

                        if (data.is_deliverable == false && data.zipcode != "" && typeof data.zipcode !== 'undefined') {

                            $('#modal-add-to-cart-button').attr('disabled', 'true');
                            $('#modal-buy-now-button').attr('disabled', 'true');

                        } else {

                            $('#modal-add-to-cart-button').removeAttr('disabled');
                            $('#modal-buy-now-button').removeAttr('disabled');

                        }

                        var variants = '';

                        total_images = 1;

                        $.each(data.variants, function (i, e) {

                            variants += '<input type="hidden" class="modal-product-variants" data-image-index="' + total_images + '" name="variants_ids" data-name="' + data.name + '" value="' + e.variant_ids + '" data-id="' + e.id + '" data-price="' + e.price + '" data-special_price="' + e.special_price + '" data-stock="' + e.stock + '">';
                            total_images += e.images.length;


                        });

                        $('#modal-product-variants-div').html(variants);

                        $('#add_to_favorite_btn').attr('data-product-id', data.id);

                        if (data.is_favorite == 1) {

                            $('#add_to_favorite_btn').addClass('remove-fav');

                            $('#add_to_favorite_btn').find('span').text('Remove From Favorite');

                        } else {

                            $('#add_to_favorite_btn').addClass('add-fav');

                            $('#add_to_favorite_btn').find('span').text('Add to Favorite');

                        }

                        $('#compare').attr('data-product-id', data.id);

                        if (data.type == "simple_product") {

                            $('#compare').attr('data-product-variant-id', data.variants[0].id);

                        } else {

                            $('#compare').attr('data-product-variant-id', '');

                        }

                        var compare = '';

                        $.each(data, function (i, e) {

                            compare += '<button type="button" name="compare" class="buttons btn-6-6 extra-small m-0 compare" id="compare" data-product-id="' + data.id + '" data-product-variant-id="' + data.variants.id + '"><i class="fa fa-random"></i> Compare</button>';

                        });
                        if (data.no_of_ratings >= 1) {

                            $('#modal-product-no-of-ratings').text(data.no_of_ratings);
                        } else {
                            $('#modal-product-no-of-ratings').text('No');

                        }

                        if (!$.isEmptyObject(data.tags)) {

                            var tags = 'Tags ';

                            $.each(data.tags, function (i, e) {

                                tags += '<a href="' + base_url + 'products/tags/' + e + '" target="_blank"><span class="badge badge-secondary p-1 mr-1">' + e + '</span></a>';

                            });

                            $('#modal-product-tags').html(tags);

                        }

                        var seller_info = "";
                        var brand_info = "";

                        if (data.brand) {
                            var brand_info = '<h5>Brand : </h5><a class="text-decoration-none" target="_blank" href="' + base_url + 'products?brand=' + data.brand_slug + '"><p class="text-danger">' + data.brand + '</p></a>';
                            $('#modal-product-brand').html(brand_info);
                        }

                        if (data.seller_name) {

                            var seller_info = '<p> <span class="text-secondary"> Sold by </span> <a class="text text-danger text-decoration-none" target="_blank" href="' + base_url + 'products?seller=' + data.seller_slug + '">' + data.seller_name + '</a> <span class="badge badge-success ">' + data.seller_rating + ' <i class="fa fa-star"></i></span> <small class="text-muted"> Out of</small> <b> ' + data.seller_no_of_ratings + ' </b></p>';

                            $('#modal-product-sellers').html(seller_info);

                        }

                        modal.stopLoading();
                    })

                }
            }),

            //Modal Product Variant Selection Event

            $(document).on('change', '.modal-product-attributes', function (e) {
                e.preventDefault();
                var selected_attributes = [];
                var attributes_length = "";
                var price = "";
                var is_variant_available = false;
                var variant = [];
                var prices = [];
                var variant_prices = [];
                var variants = [];
                var variant_ids = [];
                var image_indexes = [];
                var selected_image_index;

                $('.modal-product-variants').each(function () {
                    prices = {
                        price: $(this).data('price'),
                        special_price: $(this).data('special_price')
                    };

                    variant_ids.push($(this).data('id'));
                    variant_prices.push(prices);
                    variant = $(this).val().split(',');
                    variants.push(variant);
                    image_indexes.push($(this).data('image-index'));

                });

                attributes_length = variant.length;

                $('.modal-product-attributes').each(function () {
                    if ($(this).prop('checked')) {
                        selected_attributes.push($(this).val());

                        if (selected_attributes.length == attributes_length) {

                            prices = [];
                            var selected_variant_id = '';
                            var selected_stock = '';

                            $.each(variants, function (i, e) {

                                if (arrays_equal(selected_attributes, e)) {

                                    is_variant_available = true;
                                    prices.push(variant_prices[i]);
                                    selected_variant_id = variant_ids[i];
                                    selected_image_index = image_indexes[i];

                                    // Get stock for this variant
                                    selected_stock = $('.modal-product-variants').eq(i).data('stock');

                                }

                            });

                            if (is_variant_available) {
                                quickViewgalleryTop.slideTo(selected_image_index, 500, false);
                                mobile_image_swiper.slideTo(selected_image_index, 500, false);

                                // Set stock for both buttons
                                $('.add_to_cart, .buy_now').attr('data-product-stock', selected_stock);
                                $('#modal-add-to-cart-button').attr('data-product-low-stock-limit', selected_stock);
                                $('#modal-buy-now-button').attr('data-product-low-stock-limit', selected_stock);
                                $('#modal-add-to-cart-button').attr('data-product-variant-id', selected_variant_id);
                                $('#modal-buy-now-button').attr('data-product-variant-id', selected_variant_id);

                                if (prices[0].special_price < prices[0].price && prices[0].special_price != 0) {
                                    price = prices[0].special_price;
                                    $('#modal-product-price').text(currency + ' ' + price);
                                    $('#modal-product-special-price').text(currency + ' ' + prices[0].price);
                                    $('#modal-product-special-price-div').show();

                                } else {

                                    price = prices[0].price;
                                    $('#modal-product-price').html(currency + ' ' + price);
                                    $('#modal-product-special-price-div').hide();

                                }
                            } else {
                                $('#modal-product-special-price-div').hide();
                            }

                        }

                    }

                });

            });

        $("#modal-add-to-cart-button").on("click", function (event) {

            event.preventDefault();
            var quantity = $("#modal-product-quantity").val(),
                productTitle = $("#modal-product-title").text(),
                productShortDescription = $("#modal-product-short-description").text(),
                productImage = $("#product-view-image-container img").attr("src"),
                productPrice = $("#modal-product-price").text().replace(/\D/g, "");

            $("#quick-view").data("data-product-id", $(this).data("productId"));
            $("#quick-view").data("data-product-slug", $(this).data("productId"));
            var productVariantId = $(this).attr("data-product-variant-id"),
                productSlug = $(this).attr("data-product-slug");
            var minQuantity = $(this).attr("data-min"),
                maxQuantity = $(this).attr("data-max"),
                stepSize = $(this).attr("data-step"),
                totalStock = $('#modal-product-total-stock').attr("data-stock"),
                $button = $(this),
                buttonHtml = $(this).html();

            // Get stock and low stock limit
            var productLowStockLimit = parseFloat($(this).attr('data-product-low-stock-limit')) > 0 ? parseFloat($(this).attr('data-product-low-stock-limit')) : totalStock;
            var effectiveLowStockLimit = getEffectiveLowStockLimit(productLowStockLimit);


            if (!productVariantId) {
                Toast.fire({
                    icon: 'error',
                    title: "Please select variant"
                });
                return;
            }

            // If out of stock, show error and return
            if (isNaN(productLowStockLimit) || (parseFloat(productLowStockLimit) < parseFloat(effectiveLowStockLimit))) {
                Toast.fire({
                    icon: "error",
                    title: "Product is out of stock."
                });
                return;
            }

            if (is_loggedin == "1" || is_loggedin == 1) {
                // User is logged in, call AJAX
                $.ajax({
                    type: "POST",
                    url: base_url + "cart/manage",
                    data: {
                        product_variant_id: productVariantId,
                        qty: quantity,
                        is_saved_for_later: !1,
                        [csrfName]: csrfHash
                    },
                    dataType: "json",
                    beforeSend: function () {
                        $button.html("Please Wait").text("Please Wait").attr("disabled", !0)
                    },
                    success: function (response) {
                        if (csrfName = response.csrfName, csrfHash = response.csrfHash, $button.html(buttonHtml).attr("disabled", !1), 0 == response.error) {
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            }), $("#cart-count").text(response.data.cart_count);
                            var cartHtml = "";
                            $.each(response.data.items, function (index, item) {
                                var variantImage = (item.product_variants != '' && item.product_variants[0].images != '' && item.product_variants[0].images != '[]')
                                    ? JSON.parse(item.product_variants[0].images)[0]
                                    : item.image;
                                var variantValues = (item.product_variants != '' && item.product_variants[0].variant_values != '')
                                    ? item.product_variants[0].variant_values
                                    : '';
                                var displayPrice = item.special_price < item.price && 0 != item.special_price ? item.special_price : item.price;
                                cartHtml += '<div class="shopping-cart"><div class="shopping-cart-item d-flex justify-content-between" title = "' + item.name +
                                    '"><div class="d-flex flex-row gap-3"><figure class="rounded cart-img"><a href="' + base_url + 'products/details/' + item.product_slug +
                                    '"><img src="' + variantImage + '" alt="Not Found" style="object-fit: contain;"></a></figure><div class="w-100"><a href="' + base_url +
                                    'products/details/' + item.product_slug + '"><h3 class="post-title fs-16 lh-xs mb-1 title_wrap w-19" title = " ' + item.name + '">' + item.name +
                                    "</h3></a><span>" + variantValues + '</span><p class="price mb-0"><ins><span class="amount">' + currency + " " + displayPrice + '</span></ins></p><div class="product-pricing d-flex py-2 px-1 gap-2"><div class="align-items-center d-flex py-1"><input type="number" name="header_qty" class="form-control d-flex align-items-center header_qty p-1 w-11" value="' + item.qty + '" data-id="' + item.product_variant_id + '" data-price="' + item.price + '" min="' + minQuantity + '" max="' + maxQuantity + '" step="' + stepSize + '" ></div><div class="product-line-price align-self-center px-1">' + currency + (item.qty * displayPrice) + '</div></div></div></div><div class="product-sm-removal"><button class="remove-product btn btn-sm btn-danger rounded-1 p-1 py-0" data-id="' + item.product_variant_id + '"><i class="uil uil-trash-alt"></i></button></div></div></div>'
                            }),
                                $("#cart-item-sidebar").html(cartHtml)
                        } else {

                            Toast.fire({
                                icon: "error",
                                title: response.message
                            });
                        }
                    }
                });
            } else {
                // Not logged in, handle localStorage cart logic
                Toast.fire({
                    icon: "success",
                    title: "Item added to cart"
                });
                var cartProduct = {
                    product_variant_id: productVariantId.trim(),
                    title: productTitle,
                    slug: productSlug,
                    description: productShortDescription,
                    stock: totalStock,
                    qty: quantity,
                    image: productImage,
                    price: productPrice.trim(),
                    min: minQuantity,
                    step: stepSize
                };


                var cartItems = localStorage.getItem("cart");
                cartItems = null !== cartItems ? JSON.parse(cartItems) : null;

                if (null == cartItems) {
                    cartItems = [cartProduct];
                } else {
                    var productExists = false;
                    for (var j = 0; j < cartItems.length; j++) {
                        if (cartItems[j].product_variant_id == cartProduct.product_variant_id) {
                            productExists = true;
                            break;
                        }
                    }

                    if (productExists) {
                        Toast.fire({
                            icon: "error",
                            title: "Product already exists in the cart."
                        });
                    } else {
                        cartItems.push(cartProduct);
                    }
                }

                localStorage.setItem("cart", JSON.stringify(cartItems));
                display_cart(cartItems);
            }
        })

        $("#modal-buy-now-button").on("click", function (e) {

            e.preventDefault();

            var qty = $("#modal-product-quantity").val();
            var title = $('#modal-product-title').text();
            var description = $('#modal-product-short-description').text();
            var image = $('.product-view-image-container img').attr('src');
            var price = $('#modal-product-price').text().replace(/\D/g, '');

            $('#quick-view').data('data-product-id', $(this).data('productId'));
            $("#quick-view").data("data-product-slug", $(this).data("productId"));

            var product_variant_id = $(this).attr('data-product-variant-id');
            var product_type = $(this).attr('data-product-type');
            var min = $(this).attr('data-min');
            var max = $(this).attr('data-max');
            var step = $(this).attr('data-step');
            var btn = $(this);
            var btn_html = $(this).html();

            if (!product_variant_id) {
                Toast.fire({
                    icon: 'error',
                    title: "Please select variant"
                });
                return;
            }

            // Get stock and low stock limit
            var totalStock = $('#modal-product-total-stock').attr("data-stock");
            var productLowStockLimit = parseFloat($(this).attr('data-product-low-stock-limit')) > 0 ? parseFloat($(this).attr('data-product-low-stock-limit')) : totalStock;
            var effectiveLowStockLimit = getEffectiveLowStockLimit(productLowStockLimit);

            // If out of stock, show error and return
            if (isNaN(productLowStockLimit) || productLowStockLimit < effectiveLowStockLimit) {
                Toast.fire({
                    icon: "error",
                    title: "Product is out of stock."
                });
                return;
            }

            $.ajax({

                type: 'POST',

                url: base_url + 'cart/manage',

                data: {
                    'product_variant_id': product_variant_id,
                    'qty': $('#modal-product-quantity').val(),
                    'is_saved_for_later': false,
                    'buy_now': 1,
                    [csrfName]: csrfHash,
                },
                dataType: "json",

                success: function (e) {
                    if (csrfName = e.csrfName, csrfHash = e.csrfHash, u.html(p).attr("disabled", !1), 0 == e.error) {
                        Toast.fire({
                            icon: "success",
                            title: e.message
                        })
                        window.location.href = base_url + "cart";
                    } else {
                        if (0 == is_loggedin) {

                            $('.buy_now').addClass('disabled');
                        }
                        Toast.fire({
                            icon: "error",
                            title: e.message
                        })
                    }
                }
            })
        })
        $(".auth-modal").on("click", "header a", function (e) {
            e.preventDefault(), window.signingIn = !0;
            var t = $(this).index();
            $(this).addClass("active").siblings("a").removeClass("active"), $(this).parents("div").find("section").eq(t).removeClass("hide").siblings("section").addClass("hide"), 0 === $(this).index() ? $(".auth-modal .iziModal-content .icon-close").css("background", "#ddd") : $(".auth-modal .iziModal-content .icon-close").attr("style", "")
        })

        const listnerElement = document.getElementById("modal-signup")
        if (listnerElement != null) {
            document.getElementById("modal-signup").addEventListener("show.bs.modal", () => {
                if (auth_settings == "firebase") {
                    $(".send-otp-form")[0].reset(),
                        $(".send-otp-form").show(), $(".sign-up-form")[0].reset(), $(".sign-up-form").hide(),

                        $("#is-user-exist-error").html(""), $("#sign-up-error").html(""),
                        $("#recaptcha-container").html(""),
                        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier("recaptcha-container"),
                        window.recaptchaVerifier.render().then(function (e) {
                            grecaptcha.reset(e)
                        });

                }
                var e = $("#phone-number"),
                    t = $("#error-msg"),
                    a = $("#valid-msg");
                e.intlTelInput({
                    allowExtensions: !0,
                    formatOnDisplay: !0,
                    autoFormat: !0,
                    autoHideDialCode: !0,
                    autoPlaceholder: !0,
                    defaultCountry: "in",
                    ipinfoToken: "yolo",
                    nationalMode: !1,
                    numberType: "MOBILE",
                    preferredCountries: ["in", "ae", "qa", "om", "bh", "kw", "ma"],
                    preventInvalidNumbers: !0,
                    separateDialCode: !0,
                    initialCountry: "auto",
                    geoIpLookup: function (e) {
                        $.get("https://ipinfo.io", function () { }, "jsonp").always(function (t) {
                            var a = t && t.country ? t.country : "";
                            e(a)
                        })
                    },
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"
                });
                var r = function () {
                    e.removeClass("error"), t.addClass("hide"), a.addClass("hide")
                };
                e.blur(function () {
                    r(), $.trim(e.val()) && (e.intlTelInput("isValidNumber") ? a.removeClass("hide") : (e.addClass("error"), t.removeClass("hide")))
                }), e.on("keyup change", r)
            })
        }

        $("#quick-view").on("click", ".submit", function (e) {
            e.preventDefault();
            var t = "wobble",
                a = $(this).closest(".iziModal");
            a.hasClass(t) || (a.addClass(t), setTimeout(function () {
                a.removeClass(t)
            }, 1500))
        }),
            $("#quick-view").on("click", "header a", function (e) {
                e.preventDefault();
                var t = $(this).index();
                $(this).addClass("active").siblings("a").removeClass("active"), $(this).parents("div").find("section").eq(t).removeClass("hide").siblings("section").addClass("hide"), 0 === $(this).index() ? $("#quick-view .iziModal-content .icon-close").css("background", "#ddd") : $("#quick-view .iziModal-content .icon-close").attr("style", "")
            }),
            $("#quick-view").on("click", ".submit", function (e) {
                e.preventDefault();
                var t = "wobble",
                    a = $(this).closest(".iziModal");
                a.hasClass(t) || (a.addClass(t), setTimeout(function () {
                    a.removeClass(t)
                }, 1500))
            }),
            $("#quick-view").on("click", "header a", function (e) {
                e.preventDefault();
                var t = $(this).index();
                $(this).addClass("active").siblings("a").removeClass("active"), $(this).parents("div").find("section").eq(t).removeClass("hide").siblings("section").addClass("hide"), 0 === $(this).index() ? $("#quick-view .iziModal-content .icon-close").css("background", "#ddd") : $("#quick-view .iziModal-content .icon-close").attr("style", "")
            })
    }),
    function () {
        new LazyLoad({
            threshold: 0,
            callback_enter: function (e) { },
            callback_exit: function (e) { },
            callback_cancel: function (e) { },
            callback_loading: function (e) { },
            callback_loaded: function (e) { },
            callback_error: function (e) {
                "https://via.placeholder.com/440x560/?text=Error+Placeholder"
            },
            callback_finish: function () { }
        })
    }(),
    function () {
        var e = document.querySelector(".range-slider");
        if (e) {
            var t = e.querySelectorAll("input[type=range]"),
                a = e.querySelectorAll("input[type=number]");
            t.forEach(function (e) {
                e.oninput = function () {
                    var e = parseFloat(t[0].value),
                        r = parseFloat(t[1].value);
                    e > r && ([e, r] = [r, e]), a[0].value = e, a[1].value = r, custom_url = setUrlParameter(custom_url = setUrlParameter(location.href, "min-price", e), "max-price", r)
                }
            }), a.forEach(function (e) {
                e.oninput = function () {
                    var e = parseFloat(a[0].value),
                        r = parseFloat(a[1].value);
                    if (e > r) {
                        var s = e;
                        a[0].value = r, a[1].value = s
                    }
                    t[0].value = e, t[1].value = r
                }
            })
        }
    }(),
    $(document).on("change", "input.in-num", function (e) {
        e.preventDefault();
        var t = $(this);
        null != t.val() && "string" != typeof t.val() || ($.isNumeric(t.val()) ? "0" == t.val() && t.val(1) : t.val(1))
    }),
    $(document).on('focusout', '.in-num', function (e) {
        e.preventDefault();
        var value = $(this).val();
        var min = $(this).data('min');
        var step = $(this).data('step');
        var max = $(this).data('max');

        if (value < min) {
            $(this).val(min);
            Toast.fire({
                icon: 'error',
                title: 'Minimum allowed quantity is ' + min
            });
        } else if (value > max) {
            $(this).val(max);
            Toast.fire({
                icon: 'error',
                title: 'Maximum allowed quantity is ' + max
            });
        }
    });

$(document).on('click', '.num-block .num-in span', function (e) {
    e.preventDefault();
    var $input = $(this).parents('.num-block').find('input.in-num');
    if ($input.val() == null) {
        $input.val(1);
    }

    if ($(this).hasClass('minus')) {
        var step = $(this).data('step');
        var count = parseFloat($input.val()) - step;
        var min = $(this).data('min');

        if (count >= min) {
            $input.val(count);
        } else {
            $input.val(min);
            Toast.fire({
                icon: 'error',
                title: 'Minimum allowed quantity is ' + min
            });
        }
    } else {
        var step = $(this).data('step');
        var max = $(this).data('max');
        var count = parseFloat($input.val()) + step
        if (max != 0) {
            if (count <= max) {
                $input.val(count);
                if (count > 1) {
                    $(this).parents('.num-block').find(('.minus')).removeClass('dis');
                }
            } else {
                $input.val(max);
                Toast.fire({
                    icon: 'error',
                    title: 'Maximum allowed quantity is ' + max
                });
            }
        } else {
            $input.val(count);
        }
    }
    $input.change();
    return false;

});

$(document).ready(function () {

    $(".kv-fa").rating({
        theme: "krajee-fa",
        filledStar: '<i class="fas fa-star"></i>',
        emptyStar: '<i class="far fa-star"></i>',
        showClear: !1,
        showCaption: !1,
        size: "md"
    });
    var e = .05,
        t = 15,
        a = 300;

    function r() {
        var r = 0;
        $(".product").each(function () {
            r += parseFloat($(this).children(".product-line-price").text())
        });
        var s = r * e,
            i = r > 0 ? t : 0,
            o = r + s + i;
        $(".totals-value").fadeOut(a, function () {
            $("#cart-subtotal").html(r.toFixed(2)), $("#cart-tax").html(s.toFixed(2)), $("#cart-shipping").html(i.toFixed(2)), $("#cart-total").html(o.toFixed(2)), 0 == o ? $(".checkout").fadeOut(a) : $(".checkout").fadeIn(a), $(".totals-value").fadeIn(a)
        })
    }

    function s(e, t) {
        if ("cart" == e.data("page")) var s = $(e).parent().parent().parent().siblings(".total-price");
        else s = $(e).parent().parent();
        var i = t * $(e).val();
        s.children(".product-line-price").each(function () {
            $(this).fadeOut(a, function () {
                $(this).text(currency + " " + i.toFixed(2)), r(), usercartTotal(), $(this).fadeIn(a)
            })
        })
    }

    function i(e) {
        var t = $(e);
        t.slideUp(a, function () {
            t.remove(), r()
        })
    }

    $(document).on("change", ".product-quantity input,.product-sm-quantity input,.itemQty, .header_qty", function (e) {
        e.preventDefault();
        var t = $(this).data("id"),
            a = $(this).data("price"),
            r = $(this).val(),
            i = $(this);
        let o;

        o = $(this).attr("step") ? $(this).attr("step") : $(this).data("step");
        var min = $(this).attr("min");
        var max = $(this).attr("max");
        r = parseFloat(r);
        max = parseFloat(max);

        if (r >= max) {
            Toast.fire({
                icon: "error",
                title: `Maximum allow quantity is ${max} for product`
            })
        }
        if (r <= min) {
            Toast.fire({
                icon: "error",
                title: `Minimum allow quantity is ${min} for product`
            })
        }
        r <= 0 ? Toast.fire({
            icon: "error",
            title: `Oops! Please set minimum ${min} quantity for product`
        }) : r % o == 0 ? 1 == is_loggedin ? $.ajax({
            url: base_url + "cart/manage",
            type: "POST",
            data: {
                product_variant_id: t,
                qty: r,
                [csrfName]: csrfHash
            },
            dataType: "json",
            success: function (e) {
                csrfName = e.csrfName, csrfHash = e.csrfHash, 0 == e.error ? s(i, a) : Toast.fire({
                    icon: "error",
                    title: e.message
                })
            }
        }) : s(i, a) : Toast.fire({
            icon: "error",
            title: `Oops! you can only set quantity in step size of ${o}`
        })
    })
    $(document).on("click", ".product-removal button,.product-removal i,.product-sm-removal button", function (e) {
        e.preventDefault();
        var t = $(this).data("id"),
            a = void 0 !== $(this).data("is-save-for-later") && 1 == $(this).data("is-save-for-later") ? "1" : "0",
            r = $(this).parent().parent().parent();
        var currentUrl = window.location.href;
        if (confirm("Are you sure want to remove this?"))

            if (1 == is_loggedin) $.ajax({
                url: base_url + "cart/remove",
                type: "POST",
                data: {
                    product_variant_id: t,
                    is_save_for_later: a,
                    [csrfName]: csrfHash
                },
                dataType: "json",
                success: function (e) {

                    csrfName = e.csrfName,
                        csrfHash = e.csrfHash;
                    if (0 == e.error) {
                        var t = $("#cart-count").text();
                        t--, $("#cart-count").text(t), i(r);
                        if (currentUrl.includes('/cart')) {
                            window.location.reload();
                        }
                    } else Toast.fire({
                        icon: "error",
                        title: e.message
                    })
                }
            });
            else {
                i(r);
                var s = localStorage.getItem("cart");
                if (s = null !== localStorage.getItem("cart") ? JSON.parse(s) : null) {
                    var o = s.filter(function (e) {
                        return e.product_variant_id != t
                    });
                    localStorage.setItem("cart", JSON.stringify(o)), s && display_cart(o)
                }
            }
    })
}),

    jQuery(document).ready(function (e) {
        function t(e) {
            this.element = e, this.mainNavigation = this.element.find(".main-nav"), this.mainNavigationItems = this.mainNavigation.find(".has-dropdown"), this.dropdownList = this.element.find(".dropdown-list"), this.dropdownWrappers = this.dropdownList.find(".dropdown"), this.dropdownItems = this.dropdownList.find(".content"), this.dropdownBg = this.dropdownList.find(".bg-layer"), this.mq = this.checkMq(), this.bindEvents()
        }
        t.prototype.checkMq = function () {
            return window.getComputedStyle(this.element.get(0), "::before").getPropertyValue("content").replace(/'/g, "").replace(/"/g, "").split(", ")
        }, t.prototype.bindEvents = function () {
            var t = this;
            this.mainNavigationItems.mouseenter(function (a) {
                t.showDropdown(e(this))
            }).mouseleave(function () {
                setTimeout(function () {
                    0 == t.mainNavigation.find(".has-dropdown:hover").length && 0 == t.element.find(".dropdown-list:hover").length && t.hideDropdown()
                }, 50)
            }), this.dropdownList.mouseleave(function () {
                setTimeout(function () {
                    0 == t.mainNavigation.find(".has-dropdown:hover").length && 0 == t.element.find(".dropdown-list:hover").length && t.hideDropdown()
                }, 50)
            }), this.mainNavigationItems.on("touchstart", function (a) {
                var r = t.dropdownList.find("#" + e(this).data("content"));
                t.element.hasClass("is-dropdown-visible") && r.hasClass("active") || (a.preventDefault(), t.showDropdown(e(this)))
            })
        }, t.prototype.showDropdown = function (e) {
            if (this.mq = this.checkMq(), "desktop" == this.mq) {
                var t = this,
                    a = this.dropdownList.find("#" + e.data("content")),
                    r = a.innerHeight() + 18,
                    s = 180 * a.children(".content").children("ul").children("li").length;
                s > 540 && (s = 540);
                var i = parseInt(s),
                    o = e.offset().left + e.innerWidth() / 2 - i / 2,
                    n = e[0].offsetParent.offsetLeft;
                this.updateDropdown(a, parseInt(r), i, parseInt(o)), this.element.find(".active").removeClass("active"), this.element.find(".morph-dropdown-wrapper").css({
                    "-moz-transform": "translateX(-" + n + "px)",
                    "-webkit-transform": "translateX(-" + n + "px)",
                    "-ms-transform": "translateX(-" + n + "px)",
                    "-o-transform": "translateX(-" + n + "px)",
                    transform: "translateX(-" + n + "px)"
                }), a.addClass("active").removeClass("move-left move-right").prevAll().addClass("move-left").end().nextAll().addClass("move-right"), e.addClass("active"), this.element.hasClass("is-dropdown-visible") || setTimeout(function () {
                    t.element.addClass("is-dropdown-visible")
                }, 10)
            }
        }, t.prototype.updateDropdown = function (e, t, a, r) {
            this.dropdownList.css({
                "-moz-transform": "translateX(" + r + "px)",
                "-webkit-transform": "translateX(" + r + "px)",
                "-ms-transform": "translateX(" + r + "px)",
                "-o-transform": "translateX(" + r + "px)",
                transform: "translateX(" + r + "px)",
                width: a + "px",
                height: t + "px"
            }), this.dropdownBg.css({
                "-moz-transform": "scaleX(" + a + ") scaleY(" + t + ")",
                "-webkit-transform": "scaleX(" + a + ") scaleY(" + t + ")",
                "-ms-transform": "scaleX(" + a + ") scaleY(" + t + ")",
                "-o-transform": "scaleX(" + a + ") scaleY(" + t + ")",
                transform: "scaleX(" + a + ") scaleY(" + t + ")"
            })
        }, t.prototype.hideDropdown = function () {
            this.mq = this.checkMq(), "desktop" == this.mq && this.element.removeClass("is-dropdown-visible").find(".active").removeClass("active").end().find(".move-left").removeClass("move-left").end().find(".move-right").removeClass("move-right")
        }, t.prototype.resetDropdown = function () {
            this.mq = this.checkMq(), "mobile" == this.mq && this.dropdownList.removeAttr("style")
        };
        var a = [];
        if (e(".cd-morph-dropdown").length > 0) {
            e(".cd-morph-dropdown").each(function () {
                a.push(new t(e(this)))
            });
            var r = !1;

            function s() {
                a.forEach(function (e) {
                    e.resetDropdown()
                }), r = !1
            }
            s(), e(window).on("resize", function () {
                r || (r = !0, window.requestAnimationFrame ? window.requestAnimationFrame(s) : setTimeout(s, 300))
            })
        }
    }), $(".navbar-top-search-box input").on("focus", function () {
        $(".navbar-top-search-box .input-group-text").css("border-color", "#0e7dd1")
    }), $(".navbar-top-search-box input").on("blur", function () {
        $(".navbar-top-search-box .input-group-text").css("border", "1px solid #ced4da")
    });
var swiper = new Swiper(".swiper1", {
    loop: !0,
    preloadImages: !1,
    lazy: !0,
    autoplay: {
        delay: 6e3,
        disableOnInteraction: !1
    },
    pagination: {
        el: ".swiper1-pagination",
        clickable: !0
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev"
    }
}),
    swiperheader = new Swiper(".imageSliderHeader", {
        autoplay: {
            delay: 6e3
        },
        autoplay: {
            delay: 6e3,
            disableOnInteraction: !1
        },
        pagination: {
            el: ".imageSliderHeader-pagination",
            clickable: !0
        },
        loop: !0,
        grabCursor: !0
    }),
    swiperF = new Swiper(".preview-image-swiper", {
        pagination: {
            el: ".preview-image-swiper-pagination",
            clickable: !0
        },
        loop: !0,
    }),
    swiperV = new Swiper(".banner-swiper", {
        preloadImages: !1,
        lazy: !0,
        autoplay: !0,
        pagination: {
            el: ".banner-swiper-pagination"
        },
        loop: !0,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev"
        }
    });

//Gallery Swiper

galleryThumbs = new Swiper('.gallery-thumbs-1', {

    spaceBetween: 10,

    slidesPerView: 4,

    freeMode: true,

    watchSlidesVisibility: true,

    watchSlidesProgress: true,

});

galleryTop = new Swiper('.gallery-top-1', {

    spaceBetween: 10,

    navigation: {

        nextEl: '.swiper-button-next',

        prevEl: '.swiper-button-prev',

    },

    thumbs: {

        swiper: galleryThumbs

    }

});

document.querySelectorAll(".product-image-swiper").forEach(function (e) {
    new Swiper(e, {
        grabCursor: !0,
        preloadImages: !1,
        lazyLoading: !0,
        updateOnImagesReady: !1,
        lazyLoadingInPrevNextAmount: 1,
        navigation: {
            nextEl: e.nextElementSibling,
            prevEl: e.nextElementSibling.nextElementSibling
        },
        breakpoints: {
            350: {
                slidesPerView: 1,
                spaceBetweenSlides: 10
            },
            400: {
                slidesPerView: 1,
                spaceBetweenSlides: 10
            },
            499: {
                slidesPerView: 1,
                spaceBetweenSlides: 10
            },
            550: {
                slidesPerView: 1,
                spaceBetweenSlides: 10
            },
            600: {
                slidesPerView: 2,
                spaceBetweenSlides: 10
            },
            700: {
                slidesPerView: 3,
                spaceBetweenSlides: 10
            },
            800: {
                slidesPerView: 4,
                spaceBetweenSlides: 10
            },
            999: {
                slidesPerView: 5,
                spaceBetweenSlides: 10
            },
            1900: {
                slidesPerView: 6,
                spaceBetweenSlides: 10
            },
            1900: {
                slidesPerView: 6,
                spaceBetweenSlides: 10
            }
        }
    })
});
var timer, swiperH = new Swiper(".swiper2", {
    slidesPerView: "auto",
    grabCursor: !0,
    spaceBetween: 20,
    pagination: {
        el: ".swiper2-pagination",
        clickable: !0
    }
});

$(document).ready(function () {
    jQuery(document).ready(function () {
        jQuery("#jquery-accordion-menu").jqueryAccordionMenu(), jQuery(".colors a").click(function () {
            "default" != $(this).attr("class") ? ($("#jquery-accordion-menu").removeClass(), $("#jquery-accordion-menu").addClass("jquery-accordion-menu").addClass($(this).attr("class"))) : ($("#jquery-accordion-menu").removeClass(), $("#jquery-accordion-menu").addClass("jquery-accordion-menu"))
        })
    })
}),
    function (e, t, a, r) {
        var s = "jqueryAccordionMenu",
            i = {
                speed: 300,
                showDelay: 0,
                hideDelay: 0,
                singleOpen: !0,
                clickEffect: !0
            };

        function o(t, a) {
            this.element = t, this.settings = e.extend({}, i, a), this._defaults = i, this._name = s, this.init()
        }
        e.extend(o.prototype, {
            init: function () {
                this.openSubmenu(), this.submenuIndicators(), i.clickEffect && this.addClickEffect()
            },
            openSubmenu: function () {
                e(this.element).children("ul").find("li").bind("click touchstart", function (a) {
                    if (a.stopPropagation(), a.preventDefault(), e(this).children(".submenu").length > 0) {
                        if ("none" == e(this).children(".submenu").css("display")) return e(this).children(".submenu").show(i.speed), e(this).children(".submenu").siblings("a").addClass("submenu-indicator-minus"), i.singleOpen && (e(this).siblings().children(".submenu").hide(i.speed), e(this).siblings().children(".submenu").siblings("a").removeClass("submenu-indicator-minus")), !1;
                        e(this).children(".submenu").delay(i.hideDelay).hide(i.speed), e(this).children(".submenu").siblings("a").hasClass("submenu-indicator-minus") && e(this).children(".submenu").siblings("a").removeClass("submenu-indicator-minus")
                    }
                    t.location.href = e(this).children("a").attr("href")
                })
            },
            submenuIndicators: function () {
                e(this.element).find(".submenu").length > 0 && e(this.element).find(".submenu").siblings("a").append("<span class='submenu-indicator'>+</span>")
            },
            addClickEffect: function () {
                var t, a, r, s;
                e(this.element).find("a > .submenu-indicator").on("click touchstart", function (i) {
                    e(".ink").remove(), 0 === e(this).children(".ink").length && e(this).prepend("<span class='ink'></span>"), (t = e(this).find(".ink")).removeClass("animate-ink"), t.height() || t.width() || (a = Math.max(e(this).outerWidth(), e(this).outerHeight()), t.css({
                        height: a,
                        width: a
                    })), r = i.pageX - e(this).offset().left - t.width() / 2, s = i.pageY - e(this).offset().top - t.height() / 2, t.css({
                        top: s + "px",
                        left: r + "px"
                    }).addClass("animate-ink")
                })
            }
        }), e.fn[s] = function (t) {
            return this.each(function () {
                e.data(this, "plugin_" + s) || e.data(this, "plugin_" + s, new o(this, t))
            }), this
        }
    }(jQuery, window, document), document.addEventListener("DOMContentLoaded", function (e) {
        function t() {
            this.classList.add("clicked")
        }
        document.querySelectorAll(".cart-button").forEach(e => {
            e.addEventListener("click", t)
        })
    });
var compareDate = new Date;

function timeBetweenDates(e) {
    var t = e,
        a = new Date,
        r = t.getTime() - a.getTime();
    if (r <= 0) clearInterval(timer);
    else {
        var s = Math.floor(r / 1e3),
            i = Math.floor(s / 60),
            o = Math.floor(i / 60),
            n = Math.floor(o / 24);
        o %= 24, i %= 60, s %= 60, $("#days").text(n), $("#hours").text(o), $("#minutes").text(i), $("#seconds").text(s)
    }
}
compareDate.setDate(compareDate.getDate() + 7), timer = setInterval(function () {
    timeBetweenDates(compareDate)
}, 1e3), $(window).scroll(function () {
    $(this).scrollTop() > 50 ? $(".back-to-top:hidden").stop(!0, !0).fadeIn() : $(".back-to-top").stop(!0, !0).fadeOut()
}), $(function () {
    $(".scroll").click(function () {
        return $("html,body").animate({
            scrollTop: $(".sidenav").offset().top
        }, "1000"), !1
    })
}), $("#newsletter-modal").on("show.bs.modal", function (e) {
    $(e.relatedTarget).data("whatever")
});
swiper = new Swiper(".swiper-container-client", {
    loop: !0,
    loopedSlides: 10,
    autoheight: !0,
    slidesPerView: 2,
    spaceBetween: 30,
    autoplay: {
        delay: 6e3,
        disableOnInteraction: !1
    },
    breakpoints: {
        600: {
            slidesPerView: 6,
            spaceBetween: 20
        }
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: !0
    }
});

function buildUrlParameterValue(e, t, a, r = "") {
    if ("" != r) var s = getUrlParameter(e, r);
    else s = getUrlParameter(e);
    return "add" == a ? (null == s ? s = t : s += "|" + t, s) : "remove" == a ? null != s ? ((s = s.split("|")).splice($.inArray(t, s), 1), s.join("|")) : "" : void 0
}

function getUrlParameter(e, t = "") {
    if (e = e.replace(/\s+/g, "-"), "" != t) {
        if (!(t.indexOf("?") > -1)) return;
        var a = t.substring(t.indexOf("?") + 1)
    } else a = window.location.search.substring(1);
    var r, s, i = a.split("&");
    for (s = 0; s < i.length; s++)
        if ((r = i[s].split("="))[0] === e) return void 0 === r[1] || decodeURIComponent(r[1])
}

function checkUrlHasParam(e = "") {
    return "" == e && (e = window.location.href), e.indexOf("?") > -1 || void 0
}

function setUrlParameter(e, t, a) {
    if (t = t.replace(/\s+/g, "-"), null == a || "" == a) return e.replace(new RegExp("[?&]" + t + "=[^&#]*(#.*)?$"), "$1").replace(new RegExp("([?&])" + t + "=[^&]*&"), "$1");
    var r = new RegExp("\\b(" + t + "=).*?(&|#|$)");
    return e.search(r) >= 0 ? e.replace(r, "$1" + a + "$2") : (e = e.replace(/[?#]$/, "")) + (e.indexOf("?") > 0 ? "&" : "?") + t + "=" + a
}

$("#back_to_top").on("click", function () {
    $("html, body").animate({
        scrollTop: 0
    }, "slow")
}),
    $("#per_page_products a").on("click", function (e) {
        e.preventDefault();
        var t = $(this).data("value");
        $(this).parent().siblings("a.dropdown-toggle").text($(this).text()), location.href = setUrlParameter(location.href, "per-page", t)
    }),
    $("#per_page_sellers a").on("click", function (e) {
        e.preventDefault();
        var t = $(this).data("value");
        $(this).parent().siblings("a.dropdown-toggle").text($(this).text()), location.href = setUrlParameter(location.href, "per-page", t)
    }),
    $("#product_sort_by").on("change", function (e) {
        e.preventDefault();
        var t = $(this).val();
        location.href = setUrlParameter(location.href, "sort", t)
    }),

    $("#seller_search").on("focusout", function (e) {
        e.preventDefault();
        var t = $(this).val();
        location.href = setUrlParameter(location.href, "seller_search", t)
    }),
    $("#seller_search").on("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            var t = $(this).val();
            location.href = setUrlParameter(location.href, "seller_search", t);
        }
    });



$(".sub-category").on("click", function (e) {
    e.preventDefault();
    var t = $(this).data("value");
    custom_url = setUrlParameter(custom_url, "category", t),
        location.href = custom_url
}),

    $(document).on("change", ".brand", function (e) {
        e.preventDefault();
        var t = $(this).data("value");
        custom_url = setUrlParameter(custom_url, "brand", t);

        const brand_name = getUrlParameter('brand');
        var brands = $('[data-value="' + brand_name + '"]');
        $('[data-value="' + brand_name + '"]').attr('checked', true);
        var gp = $(brands).siblings();
        $(gp).removeClass('selected-brand');
    }),

    $(document).on("change", ".category", function (e) {
        e.preventDefault();
        var t = $(this).data("value");
        custom_url = setUrlParameter(custom_url, "category", t);

        const category_id = getUrlParameter('category');
        var categories = $('[data-value="' + category_id + '"]');
        $('[data-value="' + category_id + '"]').attr('checked', true);
        $(categories).removeClass('selected-category');

    }),

    $(document).on("change", ".product_attributes", function (e) {
        e.preventDefault();
        var t = $(this).data("attribute"),
            a = getUrlParameter(t = "filter-" + t),
            r = $(this).val();
        if (null == a && (a = ""), this.checked) var s = buildUrlParameterValue(t, r, "add", custom_url);
        else s = buildUrlParameterValue(t, r, "remove", custom_url);
        custom_url = setUrlParameter(custom_url, t, s)
    }),

    $(".product_filter_btn").on("click", function (e) {
        e.preventDefault(), location.href = custom_url
    });
var filters, type_url = "";

function arrays_equal(e, t) {
    if (!Array.isArray(e) || !Array.isArray(t) || e.length !== t.length) return !1;
    const a = e.concat().sort(),
        r = t.concat().sort();
    for (let e = 0; e < a.length; e++)
        if (a[e] !== r[e]) return !1;
    return !0
}

$("#reload").on("click", function (e) {
    window.location = window.location.href.split("?")[0];
});

function display_cart(e) {
    var t = e.length ? e.length : "";
    $("#cart-count").text(t);

    var a = "";
    null !== e && e.length > 0 && e.forEach(e => {

        a += '<div class="shopping-cart"><div class="shopping-cart-item d-flex justify-content-between"><div class="d-flex flex-row gap-3"  title = " ' + e.title +
            '"><figure class="rounded cart-img"><a href="' + base_url + 'products/details/' + e.slug + '"><img src="' + e.image +
            '" alt="Not Found" style="object-fit: contain;"></a></figure><div class="w-100"><a href="' + base_url + 'products/details/' + e.slug +
            '"><h3 class="post-title fs-16 lh-xs mb-1 title_wrap w-19" title = " ' + e.title + '">' + e.title +
            '</h3></a><p class="price mb-0"><ins><span class="amount">' + currency + " " + e.price +
            '</span></ins></p><div class="product-pricing d-flex py-2 px-1 gap-2"><div class="align-items-center d-flex py-1"><input type="number" name="header_qty" class="form-control d-flex align-items-center header_qty p-1 w-11" value="' + e.qty +
            '" data-id="' + e.product_variant_id + '" data-price="' + e.price + '" min="' + e.min + '" max="' + e.max + '" step="' + e.step +
            '" ></div><div class="product-line-price align-self-center px-1">' + currency + (e.qty * e.price) +
            '</div></div></div></div><div class="product-sm-removal"><button class="remove-product btn btn-sm btn-danger rounded-1 p-1 py-0" data-id="' + e.product_variant_id +
            '"><i class="uil uil-trash-alt"></i></button>   </div></div></div>'
    }),

        $("#cart-item-sidebar").html(a)
}

function cart_sync() {

    var cart = localStorage.getItem("cart");

    if (cart == null || !cart) {
        var message = "No items in cart so it will not be sync";
        return;
    }
    $.ajax({
        type: 'POST',
        url: base_url + 'cart/cart_sync',
        data: {
            [csrfName]: csrfHash,
            data: cart,
            'is_saved_for_later': false,
        },
        dataType: 'json',
        success: function (result) {
            csrfName = result.csrfName;
            csrfHash = result.csrfHash;

            if (result.error == false) {

                Toast.fire({

                    icon: 'success',

                    title: result.message

                });

                localStorage.removeItem("cart");
                return true;

            }

        }

    });

}


function transaction_query_params(e) {
    return {
        transaction_type: "transaction",
        user_id: $("#transaction_user_id").val(),
        limit: e.limit,
        sort: e.sort,
        order: e.order,
        offset: e.offset,
        search: e.search
    }
}

function customer_wallet_query_paramss(e) {
    return {
        type: "wallet",
        limit: e.limit,
        sort: e.sort,
        order: e.order,
        offset: e.offset,
        search: e.search
    }
} (type_url = setUrlParameter(custom_url, "type", null), $("#product_grid_view_btn").attr("href", type_url), type_url = setUrlParameter(custom_url, "type", "list"), $("#product_list_view_btn").attr("href", type_url), "list" == getUrlParameter("type") ? $("#product_list_view_btn").addClass("active") : $("#product_grid_view_btn").addClass("active"), $("#category_parent").each(function () {
    $(this).select2({
        theme: "bootstrap4",
        width: $(this).data("width") ? $(this).data("width") : $(this).hasClass("w-100") ? "100%" : "style",
        placeholder: $(this).data("placeholder"),
        allowClear: Boolean($(this).data("allow-clear")),
        dropdownCssClass: "test",
        templateResult: function (e) {
            if (!e.element) return e.text;
            var t = $(e.element),
                a = $("<span></span>");
            return a.addClass(t[0].className), a.text(e.text), a
        }
    })
}), $("#category_parent").on("change", function (e) {
    e.preventDefault();
    var t = $(this).val();
    location.href = setUrlParameter(location.href, "category_id", t)
}), $("#blog_search").on("keyup", function (e) {
    e.preventDefault();
    var t = $(this).val();
    location.href = setUrlParameter(location.href, "blog_search", t)
}), $(".auth_model").on("click", function (e) {
    e.preventDefault();
    var t = $(this).data("value");
    $("#forgot_password_div").addClass("hide"), "login" == t ? ($("#login_div").removeClass("hide"), $("#login").addClass("active"), $("#register_div").addClass("hide"), $("#register").removeClass("active")) : "register" == t && ($("#login_div").addClass("hide"), $("#login").removeClass("active"), $("#register_div").removeClass("hide"), $("#register").addClass("active"))
}),

    // Product Details Page.

    $('.attributes').on('change', function (e) {
        e.preventDefault();

        var selected_attributes = [];
        var attributes_length = "";
        var price = "";
        var is_variant_available = false;
        var variant = [];
        var prices = [];
        var variant_prices = [];
        var variants = [];
        var variant_ids = [];
        var image_indexes = [];
        var selected_image_index;

        $('.variants').each(function () {
            prices = {
                price: $(this).data('price'),
                special_price: $(this).data('special_price')
            };

            variant_ids.push($(this).data('id'));
            variant_prices.push(prices);
            variant = $(this).val().split(',');
            variants.push(variant);
            image_indexes.push($(this).data('image-index'));

        });

        attributes_length = variant.length;

        $('.attributes').each(function (i, e) {
            if ($(this).prop('checked')) {
                selected_attributes.push($(this).val());
                if (selected_attributes.length == attributes_length) {
                    /* compare the arrays */
                    prices = [];
                    var selected_variant_id = '';
                    var selected_stock = '';
                    $.each(variants, function (i, e) {
                        if (arrays_equal(selected_attributes, e)) {
                            is_variant_available = true;
                            prices.push(variant_prices[i]);
                            selected_variant_id = variant_ids[i];
                            selected_image_index = image_indexes[i];

                            // Get stock for this variant
                            selected_stock = $('.variants').eq(i).data('stock');
                        }
                    });
                    if (is_variant_available) {
                        $('#add_cart').attr('data-product-variant-id', selected_variant_id);
                        $('.buy_now').attr('data-product-variant-id', selected_variant_id);
                        galleryTop.slideTo(selected_image_index, 500, false);
                        swiperF.slideTo(selected_image_index, 500, false);
                        // Set stock for all add_to_cart and buy_now buttons
                        $('.add_to_cart, .buy_now').attr('data-product-stock', selected_stock);
                        $('#modal-buy-now-button').attr('data-product-low-stock-limit', selected_stock);
                        $('#modal-add-to-cart-button').attr('data-product-low-stock-limit', selected_stock);

                        if (prices[0].special_price < prices[0].price && prices[0].special_price != 0) {
                            price = prices[0].special_price;
                            $('.add_to_cart, .buy_now').attr('data-product-price', price);
                            $('#modal-buy-now-button').attr('data-product-price', price);
                            $('#modal-add-to-cart-button').attr('data-product-price', price);
                            $('#price').html(currency + ' ' + price);
                            $('#striped-price').html(currency + ' ' + prices[0].price);
                            $('#striped-price-div').show();
                            $('#add_cart').removeAttr('disabled');
                            $('.buy_now').removeAttr('disabled');
                        } else {
                            price = prices[0].price;
                            $('.add_to_cart, .buy_now').attr('data-product-price', price);
                            $('#modal-buy-now-button').attr('data-product-price', price);
                            $('#modal-add-to-cart-button').attr('data-product-price', price);
                            $('#price').html(currency + ' ' + price);
                            $('#striped-price-div').hide();
                            $('#add_cart').removeAttr('disabled');
                            $('.buy_now').removeAttr('disabled');
                        }
                    } else {
                        price = '<small class="text-danger h5">No Variant available!</small>';
                        $('#price').html(price);
                        $('#striped-price-div').hide();
                        $('#striped-price').html('');
                        $('#add_cart').attr('disabled', 'true');
                        $('.buy_now').attr('disabled', 'true');
                    }
                }
            }
        });
        variants = "";
    }),

    $(document).on("click", ".add_to_cart", function (e) {
        e.preventDefault();

        var cart_item = {
            "product_id": $(this).data('product-id'),
            "variant_id": $(this).data('product-variant-id'),
            "stock": $(this).data('product-stock'),
            "title": $(this).data('product-title'),
            "image": $(this).data('product-image'),
            "price": $(this).data('product-price'),
            "reference_id": $(this).data('product-reference_id'),
            "min": $(this).data('min'),
            "step": $(this).data('step')
        };

        // Get the effective low stock limit
        var productLowStockLimit = $(this).data('product-low-stock-limit');
        var effectiveLowStockLimit = productLowStockLimit && parseFloat(productLowStockLimit) > 0 ?
            parseFloat(productLowStockLimit) :
            parseFloat(seller_low_stock_limit);

        if (parseFloat(cart_item.stock) < effectiveLowStockLimit) {
            Toast.fire({
                icon: "error",
                title: "Product is out of stock."
            });
            return;
        }
        $("#quick-view").data("data-product-id", $(this).data("productId"));
        // var a = $(this).attr("data-product-variant-id"),
        var product_variant_id = $(this).attr("data-product-variant-id"),
            product_type = $(this).attr("data-product-type"),
            user_id = $(this).attr("data-user-id"),
            product_title = $(this).attr("data-product-title"),
            product_image = $(this).attr("data-product-image"),
            product_slug = $(this).attr("data-product-slug"),
            product_reference_id = $(this).attr("data-product-reference_id"),
            product_price = $(this).attr("data-product-price"),
            product_description = $(this).attr("data-product-description"),
            min = $(this).attr("data-min"),
            max = $(this).attr("data-max"),
            step = $(this).attr("data-step"),


            d = $(this),
            u = $(this).html(),
            izimodal_open = $(this).attr("data-izimodal-open");
        // p = $(this).attr("data-izimodal-open");

        const total_stock = $(this).attr("data-product-stock");

        if ($('[name="qty"]').val() != null) {
            var quantity_product = $('[name="qty"]').val();
        } else {
            var quantity_product = $(this).attr("data-min");
        }


        if (!product_variant_id) {
            Toast.fire({
                icon: "error",
                title: "Please select variant"
            });
            return;
        }


        if (parseFloat(total_stock) < parseFloat(getEffectiveLowStockLimit($(this).data('product-low-stock-limit')))) {

            Toast.fire({
                icon: "error",
                title: "Product is out of stock."
            });
            return;
        }

        if (is_loggedin == "1" || is_loggedin == 1) {
            $.ajax({
                type: "POST",
                url: base_url + "cart/manage",
                data: {
                    product_variant_id: product_variant_id,
                    qty: quantity_product,
                    product_reference_id: product_reference_id,
                    is_saved_for_later: !1,
                    [csrfName]: csrfHash
                },
                dataType: "json",
                beforeSend: function () {
                    d.html("Please Wait").text("Please Wait").attr("disabled", !0)
                },
                success: function (e) {

                    if (csrfName = e.csrfName, csrfHash = e.csrfHash, d.html(u).attr("disabled", !1), 0 == e.error) {
                        Toast.fire({
                            icon: "success",
                            title: e.message
                        }),
                            $("#cart-count").text(e.data.cart_count);

                        var t = "";
                        $.each(e.data.items, function (e, product_variant_id) {

                            var r = void 0 !== product_variant_id.product_variants.variant_values && null != product_variant_id.product_variants.variant_values ? product_variant_id.product_variants.variant_values : "",
                                s = product_variant_id.special_price < product_variant_id.price && 0 != product_variant_id.special_price ? product_variant_id.special_price : product_variant_id.price;

                            var number = product_variant_id.qty * s;
                            var formatprice = number.toFixed(2);

                            if (product_variant_id.product_variants != '' && product_variant_id.product_variants[0].images != '') {
                                var variant_img = JSON.parse(product_variant_id.product_variants[0].images)[0];
                            } else {
                                var variant_img = product_variant_id.image;
                            }

                            if (product_variant_id.product_variants != '' && product_variant_id.product_variants[0].variant_values != '') {
                                var variant_values = product_variant_id.product_variants[0].variant_values;
                            } else {
                                var variant_values = '';
                            }

                            t += '<div class="shopping-cart"><div class="shopping-cart-item d-flex justify-content-between" title = " ' + product_variant_id.name +
                                '"><div class="d-flex flex-row gap-3"><figure class="rounded cart-img"><a href="' + base_url + 'products/details/' + product_variant_id.product_slug +
                                '"><img src="' + variant_img + '" alt="Not Found" style="object-fit: contain;"></a></figure><div class="w-100"><a href="'
                                + base_url + 'products/details/' + product_variant_id.product_slug + '"><h3 class="post-title fs-16 lh-xs mb-1 title_wrap w-19"  title = " ' + product_variant_id.name +
                                '">' + product_variant_id.name + "</h3></a><span>" + r + '</span><span>' + variant_values + '</span><p class="price mb-0"><ins><span class="amount">'
                                + currency + " " + s + '</span></ins></p><div class="product-pricing d-flex py-2 px-1 gap-2"><div class="align-items-center d-flex py-1"><input type="number" name="header_qty" class="form-control d-flex align-items-center header_qty  p-1 w-11" value="' + product_variant_id.qty + '" data-id="' + product_variant_id.product_variant_id + '" data-price="' + product_variant_id.price + '" min="' + product_variant_id.minimum_order_quantity + '" max="' + product_variant_id.total_allowed_quantity + '" step="' + product_variant_id.quantity_step_size + '" ></div><div class="product-line-price align-self-center px-1">' + currency + (formatprice) + '</div></div></div></div><div class="product-sm-removal"><button class="remove-product btn btn-sm btn-danger rounded-1 p-1 py-0" data-id="' + product_variant_id.product_variant_id + '"><i class="uil uil-trash-alt"></i></button></div></div></div>'
                        }),
                            $("#cart-item-sidebar").html(t)
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: e.message
                        });
                    }
                },
                error: function (e) {
                    Toast.fire({
                        icon: "error",
                        title: e.message
                    })
                }
            });
        } else {
            var p = {
                product_variant_id: product_variant_id.trim(),
                title: product_title,
                slug: product_slug,
                description: product_description,
                stock: total_stock,
                qty: quantity_product,
                image: product_image,
                price: product_price.trim(),
                min: min,
                step: step,
                max: max,
            };


            var cartItems = localStorage.getItem("cart");
            cartItems = null !== cartItems ? JSON.parse(cartItems) : null;

            Toast.fire({
                icon: "success",
                title: "Item added to cart"
            });

            if (null == cartItems) {
                cartItems = [p];
            } else {
                var productExists = false;
                for (var j = 0; j < cartItems.length; j++) {
                    if (cartItems[j].product_variant_id == p.product_variant_id) {
                        productExists = true;
                        break;
                    }
                }

                if (productExists) {

                    Toast.fire({
                        icon: "error",
                        title: "Product already exists in the cart."
                    });
                } else {
                    cartItems.push(p);

                }

            }

            localStorage.setItem("cart", JSON.stringify(cartItems));
            display_cart(cartItems);
        }

    }),
    $(document).ready(function () {
        var e = localStorage.getItem("cart");

        (e = null !== localStorage.getItem("cart") ? JSON.parse(e) : null) && display_cart(e)
    }),
    $(document).on("click", "#clear_cart", function (e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
            text: "Do you really want to clear the cart?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, clear it!"
        }).then((result) => {
            console.log("outside result", result);
            if (result.value == true || result.isConfirmed == true) {
                $.ajax({
                    type: "POST",
                    data: {
                        [csrfName]: csrfHash
                    },
                    url: base_url + "cart/clear",
                    success: function (e) {
                        console.log(e);
                        // return;

                        csrfName = e.csrfName;
                        csrfHash = e.csrfHash;
                        setTimeout(function () {
                            window.location.reload()
                        }, 2000);
                    }
                });
            }
        });
    }),

    $(document).on("click", "#checkout", function (e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to proceed to checkout?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, checkout!"
        }).then((result) => {
            console.log(result);

            if (result.value == true || result.isConfirmed == true) {
                window.location.href = base_url + "cart/checkout";
            }
        });
    }),

    $(".quick-view-btn").on("click", function () {

        $('#modal-buy-now-button').attr('disabled', 'true');
        $("#quick-view").data("data-product-id", $(this).data("productId"))
    }),
    $('.save-for-later').on('click', function (e) {
        e.preventDefault();
        var formdata = new FormData();
        var product_variant_id = $(this).data('id');
        var qty = $(this).parent().siblings('.item-quantity').find('.itemQty').val();
        var product = $(this);
        formdata.append(csrfName, csrfHash);
        formdata.append('product_variant_id', product_variant_id);
        formdata.append('is_saved_for_later', 1);
        formdata.append('qty', qty);
        $.ajax({
            type: 'POST',
            url: base_url + 'cart/manage',
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (result) {

                csrfName = result.csrfName;
                csrfHash = result.csrfHash;
                if (result.error == false) {
                    window.location.reload();
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: result.message
                    });
                }
            }
        });
    }),

    $(".move-to-cart").on("click", function (e) {
        e.preventDefault();
        var t = new FormData,
            a = $(this).data("id"),
            r = $('.move-to-cart').parent().parent().siblings('.item-quantity').find('.itemQty').val();
        $(this);
        t.append(csrfName, csrfHash), t.append("product_variant_id", a), t.append("is_saved_for_later", 0), t.append("qty", r), $.ajax({
            type: "POST",
            url: base_url + "cart/manage",
            data: t,
            cache: !1,
            contentType: !1,
            processData: !1,
            dataType: "json",
            success: function (e) {
                csrfName = e.csrfName, csrfHash = e.csrfHash, 0 == e.error ? window.location.reload() : Toast.fire({
                    icon: "error",
                    title: e.message
                })
            }
        })
    }),
    $(".confirmReturn").on("click", function (e) {
        e.preventDefault();

        let itemId = $("#returnItemId").val();
        let status = $("#status").val();
        let selectedReason = $("input[name='return_reason']:checked").val();
        let otherReason = $("#otherReasonField").val();
        let returnImage = $("#return_item_image")[0].files; // Get selected image file


        if (!selectedReason) {
            alert("Please select a return reason.");
            return;
        }

        let formData = new FormData();
        formData.append("order_item_id", itemId);
        formData.append("return_reason", selectedReason);
        formData.append("status", status);
        if (selectedReason === "other") {
            formData.append("other_reason", otherReason);
        }
        // Append multiple images
        if (returnImage.length > 0) {

            for (let i = 0; i < returnImage.length; i++) {
                formData.append("return_item_images[]", returnImage[i]);
            }
        }

        formData.append(csrfName, csrfHash);

        $.ajax({
            type: "POST",
            url: base_url + "my-account/update-order-item-status",
            data: formData,
            cache: !1,
            contentType: !1,
            processData: !1,
            dataType: "json",
            beforeSend: function () {
                $("#confirmReturn").prop("disabled", true).text("Processing...");
            },
            success: function (e) {

                csrfName = e.csrfName, csrfHash = e.csrfHash, 0 == e.error ? (Toast.fire({
                    icon: "success",
                    title: e.message
                }), setTimeout(function () {
                    window.location.reload()
                }, 3e3)) : Toast.fire({
                    icon: "error",
                    title: e.message
                })
                $("#confirmReturn").prop("disabled", false).text("Confirm Return");
            }
        })
    }),
    $(".update-order").on("click", function (e) {
        e.preventDefault();
        var t = new FormData,
            a = $(this).data("order-id"),
            r = $(this).data("status"),
            s = "";
        if (s = "cancelled" == r ? "Cancel" : "Return", confirm("Are you sure you want to " + s + " this order ?")) {
            var i = $(this),
                o = i.text();
            t.append(csrfName, csrfHash), t.append("order_id", a), t.append("status", r), $.ajax({
                type: "POST",
                url: base_url + "my-account/update-order",
                data: t,
                cache: !1,
                contentType: !1,
                processData: !1,
                dataType: "json",
                beforeSend: function () {
                    i.html("Please Wait").attr("disabled", !0)
                },
                success: function (e) {
                    csrfName = e.csrfName, csrfHash = e.csrfHash, 0 == e.error ? (Toast.fire({
                        icon: "success",
                        title: e.message
                    }), setTimeout(function () {
                        window.location.reload()
                    }, 3e3)) : Toast.fire({
                        icon: "error",
                        title: e.message
                    }), i.html(o).attr("disabled", !1)
                }
            })
        }
    }),
    $(".update-order-item-cancel").on("click", function (e) {
        e.preventDefault();

        var t = new FormData,
            a = $(this).data("item-id"),
            r = $(this).data("status"),
            s = $(this),
            i = s.text();
        t.append(csrfName, csrfHash), t.append("order_item_id", a), t.append("status", r),
            Swal.fire({
                title: 'Are You Sure!',
                text: "You want to cancel this item!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!',
                showLoaderOnConfirm: true,
                preConfirm: function () {
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            type: "POST",
                            url: base_url + "my-account/update-order-item-status",
                            data: t,
                            cache: !1,
                            contentType: !1,
                            processData: !1,
                            dataType: "json",
                            beforeSend: function () {
                                s.html("Please Wait").attr("disabled", !0)
                            },
                            success: function (e) {
                                csrfName = e.csrfName, csrfHash = e.csrfHash, 0 == e.error ? (Toast.fire({
                                    icon: "success",
                                    title: e.message
                                }), setTimeout(function () {
                                    window.location.reload()
                                }, 3e3)) : Toast.fire({
                                    icon: "error",
                                    title: e.message
                                }), s.html(i).attr("disabled", !1)
                            }
                        });
                    });
                },
                allowOutsideClick: false
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire('Cancelled!', 'You are logged in', 'error');
                }
            });

    }),
    $("#add-address-form").on("submit", function (e) {
        e.preventDefault();
        var t = new FormData(this);
        var currentUrl = window.location.href;

        var pincode_test = $('#pincode option:selected').text();

        t.append(csrfName, csrfHash),
            t.append('pincode_full', pincode_test);
        $.ajax({
            type: "POST",
            data: t,
            url: $(this).attr("action"),
            dataType: "json",
            cache: !1,
            contentType: !1,
            processData: !1,

            success: function (e) {

                csrfName = e.csrfName,
                    csrfHash = e.csrfHash
                if (e.error == false) {
                    Toast.fire({
                        icon: "success",
                        title: e.message
                    }),
                        $("#add-address-form")[0].reset(),
                        $("#address_list_table").bootstrapTable("refresh");
                    $("#add-address-modal").modal('hide');
                    if (currentUrl.includes('/checkout')) {
                        window.location.reload()
                        $("#address-modal").modal('show');
                    }
                } else {
                    Toast.fire({
                        icon: "error",
                        title: e.message
                    });
                    $("#save-address-submit-btn").val("Save").attr("disabled", !1)

                }
            }
        })
    }),
    $('#pincode').on('change', function (e) {
        e.preventDefault();
        var value = $(this).val()
        if (value == 0 || value == -1) {
            $('.pincode_name').removeClass('d-none')
        } else {
            $('.pincode_name').addClass('d-none')
            $('input[name="pincode_name"]').val("");
        }
    }),
    $('#edit_pincode').on('change', function (e) {
        e.preventDefault();
        var value = $(this).val()
        if (value == 0 || value == -1) {
            $('.other_pincode').removeClass('d-none')
        } else {
            $('.other_pincode').addClass('d-none')
            $('input[name="pincode_name"]').val("");
        }
    }),
    $("#city").select2({
        ajax: {
            url: base_url + 'my-account/get_cities',
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
        containerCssClass: 'city-container',
        dropdownCssClass: 'city-dropdown',
        dropdownParent: $("#add-address-form"),

        // Set the predefined options as selected

    }),
    $('#city').on('change', function (e) {
        e.preventDefault();
        var value = $(this).val()
        if (value == 0 || value == -1) {
            $('.city_name').removeClass('d-none')
            $('.area_name').removeClass('d-none')
            $('.pincode_name').removeClass('d-none')
            $('.area').addClass('d-none')
            $('.pincode').addClass('d-none')
        } else {
            $('#edit_pincode').empty()
            $('.city').trigger('change')
            $('.city').removeClass('d-none')
            $('.area').removeClass('d-none')
            $('.pincode').removeClass('d-none')
            $('.city_name').addClass('d-none')
            $('.area_name').addClass('d-none')
            $('.pincode_name').addClass('d-none')

            $.ajax({
                type: 'POST',
                data: {
                    'city_id': $(this).val(),
                    [csrfName]: csrfHash,
                },
                url: base_url + 'my-account/get-zipcode',
                dataType: 'json',
                success: function (result) {
                    csrfName = result.csrfName;
                    csrfHash = result.csrfHash;
                    if (result.error == false) {
                        var html = '';
                        html += '<option value="">--Select Zipcode--</option>';
                        html += '<option value="0">Other</option>';
                        $.each(result.data, function (i, e) {
                            html += '<option value=' + e.zipcode + '>' + e.zipcode + '</option>';
                        });

                        $('#pincode').html(html);

                    } else {
                        var html = '';
                        html += '<option value="">--Select Zipcode--</option>';
                        html += '<option value="0">Other</option>';

                        $('#pincode').html(html);
                    }

                }

            })
        }

    }), $("#edit-address-form").on("submit", function (e) {
        e.preventDefault();
        var t = new FormData(this);
        var pincode_test = $('#edit_pincode option:selected').text();
        t.append('pincode_full', pincode_test);
        t.append(csrfName, csrfHash), $.ajax({
            type: "POST",
            data: t,
            url: $(this).attr("action"),
            dataType: "json",
            cache: !1,
            contentType: !1,
            processData: !1,
            beforeSend: function () {
                $("#edit-address-submit-btn").val("Please Wait...").attr("disabled", !0)
            },
            success: function (e) {
                csrfName = e.csrfName;
                csrfHash = e.csrfHash;
                if (e.error == false) {

                    Toast.fire({
                        icon: "success",
                        title: e.message
                    });
                    setTimeout(function () {
                        $("#address-modal").modal("hide")
                    }, 2e3);
                    $("#edit-address-submit-btn").val("Save").attr("disabled", !1)
                    $("#edit-address-form")[0].reset(),
                        $('.address_modal').modal('hide');
                    $("#address_list_table").bootstrapTable("refresh")
                } else {
                    $("#edit-address-submit-btn").val("Save").attr("disabled", !1)
                }
            }
        })
    }), $(document).on("click", ".delete-address", function (e) {
        e.preventDefault(), confirm("Are you sure ? You want to delete this address?") && $.ajax({
            type: "POST",
            data: {
                id: $(this).data("id"),
                [csrfName]: csrfHash
            },
            url: base_url + "my-account/delete-address",
            dataType: "json",
            success: function (result) {
                csrfName = result.csrfName;
                csrfHash = result.csrfHash;
                if (result.error == false) {
                    Toast.fire({
                        icon: 'success',
                        title: result.message
                    });
                    $('#address_list_table').bootstrapTable('refresh');
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: result.message
                    });
                }
            }
        })
    }), $(document).on("click", ".default-address", function (e) {
        e.preventDefault(), confirm("Are you sure ? You want to set this address as default?") && $.ajax({
            type: "POST",
            data: {
                id: $(this).data("id"),
                [csrfName]: csrfHash
            },
            url: base_url + "my-account/set-default-address",
            dataType: "json",
            success: function (e) {
                csrfName = e.csrfName, csrfHash = e.csrfHash, 0 == e.error ? ($("#address_list_table").bootstrapTable("refresh"), Toast.fire({
                    icon: "success",
                    title: e.message
                })) : Toast.fire({
                    icon: "error",
                    title: e.message
                })
            }
        })
    }),
    $(document).on("click", "#forgot_password_link", function (e) {
        e.preventDefault(),
            $(".auth-modal").find("header a").removeClass("active");
        $("#forgot_password_div").removeClass("hide").siblings("section").addClass("hide");
        if (auth_settings == "firebase") {
            $("#recaptcha-container-2").html(""),
                window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier("recaptcha-container-2"),
                window.recaptchaVerifier.render().then(function (e) {
                    grecaptcha.reset(e)
                })
        }
        $("#forgot_password_number").intlTelInput({
            allowExtensions: !0,
            formatOnDisplay: !0,
            autoFormat: !0,
            autoHideDialCode: !0,
            autoPlaceholder: !0,
            defaultCountry: "in",
            ipinfoToken: "yolo",
            nationalMode: !1,
            numberType: "MOBILE",
            preferredCountries: ["in", "ae", "qa", "om", "bh", "kw", "ma"],
            preventInvalidNumbers: !0,
            separateDialCode: !0,
            initialCountry: "auto",
            geoIpLookup: function (e) {
                $.get("https://ipinfo.io", function () { }, "jsonp").always(function (t) {
                    var a = t && t.country ? t.country : "";
                    e(a)
                })
            },
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"
        })
    }),
    $(document).on("submit", "#send_forgot_password_otp_form", function (e) {
        e.preventDefault();
        var t = $("#forgot_password_send_otp_btn").html();
        $("#forgot_password_send_otp_btn").html("Please Wait...").attr("disabled", !0);
        var a = $(".selected-dial-code").html() + $("#forgot_password_number").val(),
            r = is_user_exist($("#forgot_password_number").val());
        if (0 == r.error)
            $("#forgot_pass_error_box").html("You have not registered using this number."),
                $("#forgot_password_send_otp_btn").html(t).attr("disabled", !1);
        else {
            if (auth_settings == "firebase") {
                var s = window.recaptchaVerifier;
                firebase.auth().signInWithPhoneNumber(a, s).then(function (e) {
                    resetRecaptcha(),
                        $("#verify_forgot_password_otp_form").removeClass("d-none"),
                        $("#send_forgot_password_otp_form").hide(),
                        $("#forgot_pass_error_box").html(r.message),
                        $("#forgot_password_send_otp_btn").html(t).attr("disabled", !1),
                        $(document).on("submit", "#verify_forgot_password_otp_form", function (t) {
                            t.preventDefault();
                            var a = $("#reset_password_submit_btn").html(),
                                r = $("#forgot_password_otp").val(),
                                s = new FormData(this),
                                i = base_url + "home/reset-password";
                            $("#reset_password_submit_btn").html("Please Wait...").attr("disabled", !0), e.confirm(r).then(function (e) {
                                s.append(csrfName, csrfHash),
                                    s.append("mobile", $("#forgot_password_number").val()),
                                    $.ajax({
                                        type: "POST",
                                        url: i,
                                        data: s,
                                        processData: !1,
                                        contentType: !1,
                                        cache: !1,
                                        dataType: "json",
                                        beforeSend: function () {
                                            $("#reset_password_submit_btn").html("Please Wait...").attr("disabled", !0)
                                        },
                                        success: function (e) {
                                            csrfName = e.csrfName, csrfHash = e.csrfHash, $("#reset_password_submit_btn").html(a).attr("disabled", !1), $("#set_password_error_box").html(e.message).show(), 0 == e.error && setTimeout(function () {
                                                window.location.reload()
                                            }, 2e3)
                                        }
                                    })
                            }).catch(function (e) {
                                $("#reset_password_submit_btn").html(a).attr("disabled", !1), $("#set_password_error_box").html("Invalid OTP. Please Enter Valid OTP").show()
                            })
                        })
                }).catch(function (e) {
                    $("#forgot_pass_error_box").html(e.message).show(), $("#forgot_password_send_otp_btn").html(t).attr("disabled", !1), resetRecaptcha()
                })
            }
        }
    }), $("#contact-us-form").on("submit", function (e) {
        e.preventDefault();
        var t = $("#contact-us-submit-btn").html(),
            a = new FormData(this);
        a.append(csrfName, csrfHash), $.ajax({
            type: "POST",
            data: a,
            url: $(this).attr("action"),
            dataType: "json",
            cache: !1,
            contentType: !1,
            processData: !1,
            beforeSend: function () {
                $("#contact-us-submit-btn").html("Please Wait...").attr("disabled", !0)
            },
            success: function (e) {
                csrfName = e.csrfName, csrfHash = e.csrfHash, 0 == e.error ? (Toast.fire({
                    icon: "success",
                    title: e.message
                }), $("#contact-us-form")[0].reset()) : Toast.fire({
                    icon: "error",
                    title: e.message
                }), $("#contact-us-submit-btn").html(t).attr("disabled", !1)
            }
        })
    }), $("#product-rating-form").on("submit", function (e) {
        e.preventDefault();
        var t = $("#rating-submit-btn").html(),
            a = new FormData(this);
        a.append(csrfName, csrfHash), $.ajax({
            type: "POST",
            data: a,
            url: $(this).attr("action"),
            dataType: "json",
            cache: !1,
            contentType: !1,
            processData: !1,
            beforeSend: function () {
                $("#rating-submit-btn").html("Please Wait...").attr("disabled", !0)
            },
            success: function (e) {
                csrfName = e.csrfName, csrfHash = e.csrfHash, 0 == e.error ? (Toast.fire({
                    icon: "success",
                    title: e.message
                }), $("#product-rating-form")[0].reset(), window.location.reload()) : Toast.fire({
                    icon: "error",
                    title: e.message
                }), $("#rating-submit-btn").html(t).attr("disabled", !1)
            }
        })
    }), $("#delete_rating").on("click", function (e) {
        if (e.preventDefault(), confirm("Are you sure want to Delete Rating ?")) {
            var t = $(this).data("rating-id");
            $.ajax({
                type: "POST",
                data: {
                    [csrfName]: csrfHash,
                    rating_id: t
                },
                url: $(this).attr("href"),
                dataType: "json",
                success: function (e) {
                    csrfName = e.csrfName, csrfHash = e.csrfHash, 0 == e.error ? (Toast.fire({
                        icon: "success",
                        title: e.message
                    }), $("#delete_rating").parent().parent().parent().remove(), $("#no_ratings").text(e.data.rating[0].no_of_rating)) : Toast.fire({
                        icon: "error",
                        title: e.message
                    })
                }
            })
        }
    }), $("#edit_link").on("click", function (e) {
        e.preventDefault(), $("#rating-box").removeClass("d-none")
    }), $("#load-user-ratings").on("click", function (e) {
        e.preventDefault();
        var t = $(this).attr("data-limit"),
            a = $(this).attr("data-offset"),
            r = $(this).attr("data-product"),
            s = $(this).html(),
            i = $(this),
            o = "";
        $.ajax({
            type: "GET",
            data: {
                limit: t,
                offset: a,
                product_id: r
            },
            url: base_url + "products/get-rating",
            dataType: "json",
            beforeSend: function () {
                $(this).html("Please wait..").attr("disabled", !0)
            },
            success: function (e) {
                $(this).html(s).attr("disabled", !1), 0 == e.error ? ($.each(e.data.product_rating, function (e, t) {
                    o += '<li class="review-container"><div class="review-image"><img src="' + base_url + 'assets/front_end/modern/images/user.png" alt="" width="65" height="65"></div><div class="review-comment"><div class="rating-list"><div class="product-rating"><input type="text" class="kv-fa" value="' + t.rating + '" data-size="xs" title="" readonly></div></div><div class="review-info"><h4 class="reviewer-name">' + t.user_name + '</h4> <span class="review-date text-muted">' + t.data_added + '</span></div><div class="review-text"><p class="text-muted">' + t.comment + '</p></div><div class="row reviews">', $.each(t.images, function (e, t) {
                        o += '<div class="col-md-2"><div class="review-box"><a href="' + t + '" data-lightbox="review-images"><img src="' + t + '" alt="' + t + '"></a></div></div>'
                    }), o += "</div></div></li>"
                }), a += t, $("#review-list").append(o), $(".kv-fa").rating("create", {
                    filledStar: '<i class="fas fa-star"></i>',
                    emptyStar: '<i class="far fa-star"></i>',
                    size: "xs",
                    showCaption: !1
                }), i.attr("data-offset", a)) : Toast.fire({
                    icon: "error",
                    title: e.message
                })
            }
        })
    }),
    $("#edit_city").select2({
        ajax: {
            url: base_url + 'my-account/get_cities',
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
        dropdownParent: $("#edit-address-form"),
        placeholder: 'Search for cities',
    }),
    $('#edit_city').on('change', function (e, pincode) {

        e.preventDefault();

        var city_id = $(this).val();
        var value = $(this).val()
        if (value == 0 || value == '') {
            $('.edit_area').addClass('d-none')
            $('#edit_area').val('')
            $('.edit_pincode').addClass('d-none')
            $('.other_city').removeClass('d-none')
            $('.other_areas').removeClass('d-none')
            $('.other_pincode').removeClass('d-none')
        } else {
            $('.edit_area').removeClass('d-none')
            $('.edit_pincode').removeClass('d-none')
            $('.edit_city').removeClass('d-none')
            $('.other_city').addClass('d-none')
            $('.other_areas').addClass('d-none')
            $('.other_pincode').addClass('d-none')

            $.ajax({
                type: 'POST',
                data: {
                    'city_id': $(this).val(),
                    [csrfName]: csrfHash,
                },
                url: base_url + 'my-account/get-zipcode',
                dataType: 'json',
                success: function (result) {
                    csrfName = result.csrfName;
                    csrfHash = result.csrfHash;
                    var html = '';
                    if (result.error == false) {
                        html += '<option value="0">Other</option>';
                        $.each(result.data, function (i, e) {
                            var is_selected = (e.zipcode == pincode) ? "selected" : "";

                            html += '<option value=' + e.zipcode + ' ' + is_selected + '>' + e.zipcode + '</option>';
                        });
                        $('#edit_pincode').html(html);

                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: result.message
                        });
                        html += '<option value="0">Select Pincode</option>';
                        html += '<option value="0">Other</option>';
                        $('#edit_pincode').html(html);
                    }
                }
            })
        }
    }))

if ($('#product-filters').length) {

    if (!checkUrlHasParam()) {

        sessionStorage.setItem($('#product-filters').data('key'), $('#product-filters').val());

        var filters = sessionStorage.getItem($('#product-filters').data('key'));

        filters = filters.replace(/\\/g, "");

        print_filters(filters, 'Desktop', '#product-filters-desktop');

        print_filters(filters, 'Mobile', '#product-filters-mobile');

    } else {

        if (sessionStorage.getItem($('#product-filters').data('key')) == undefined) {

            sessionStorage.setItem($('#product-filters').data('key'), $('#product-filters').val());

        }

        var filters = sessionStorage.getItem($('#product-filters').data('key'));

        filters = filters.replace(/\\/g, "");

        print_filters(filters, 'Desktop', '#product-filters-desktop');

        print_filters(filters, 'Mobile', '#product-filters-mobile');

    }

}

function print_filters(filters, prefix = '', target) {
    var html = '';
    var attribute_values_id;
    var attribute_values;
    var new_attr_val;
    var attr_name;
    var collapse_status;
    var selected_attributes;
    var attr_checked_status;
    var e_name;

    if (filters != "") {

        $.each(JSON.parse(filters), function (i, e) {


            e_name = e.name.replace(' ', '-').toLowerCase();
            e_name = decodeURIComponent(e_name);
            attr_name = getUrlParameter('filter-' + e_name);
            collapse_status = (attr_name == undefined) ? " " : "show";
            selected_attributes = (attr_name != undefined) ? attr_name.split('|') : "";


            const brand_name = getUrlParameter('brand');
            var brands = $('[data-value="' + brand_name + '"]');
            $('[data-value="' + brand_name + '"]').attr('checked', true);
            var gp = $(brands).siblings();
            $(gp).addClass('selected-brand');


            const category_id = getUrlParameter('category');
            var categories = $('[data-value="' + category_id + '"]');
            $('[data-value="' + category_id + '"]').attr('checked', true);
            $(categories).addClass('selected-category');


            html +=
                '<div class="accordion accordion-wrapper" id="accordionSimpleExample">' +
                '<div class="card plain accordion-item">' +
                '<div class="card-header" id="h' + i + '">' +
                '<button class="accordion-button text-decoration-none text-dark h6 collapsed" data-bs-toggle="collapse" data-bs-target="#' + prefix + i + '" aria-expanded="false" aria-controls="#' + prefix + i + '" style="cursor: pointer;">' + e.name + '</button>' +
                '</div>' +
                '<div id="' + prefix + i + '" class="accordion-collapse collapse" aria-labelledby="h' + i + '" data-bs-parent="#accordionSimpleExample">' +
                '<div class="card-body-custom ml-5">';

            attribute_values_id = e.attribute_values_id.split(',');
            attribute_values = e.attribute_values.split(',');

            $.each(attribute_values, function (j, v) {
                attr_checked_status = ($.inArray(v, selected_attributes) !== -1) ? "checked" : "";
                new_attr_val = e_name + ' ' + v;
                html +=
                    '<div class="input-container d-flex">' +
                    '<input type="checkbox" name="' + v + '" value="' + v + '" class="form-check-input toggle-input product_attributes" id="' + prefix + new_attr_val + '" data-attribute="' + e_name + '" ' + attr_checked_status + '>' +
                    '<label class="form-check-label toggle checkbox" for="' + prefix + new_attr_val + '">' +
                    '<div class="toggle-inner"></div>' +
                    '</label>' +
                    '<label for="' + prefix + new_attr_val + '" class="text-label">' + v + '</label>' +
                    '</div>';
            });
            html += '</div></div></div></div>';

        });

    }
    $(target).html(html);

}

function usercartTotal() {
    var e = 0;
    $("#cart_item_table > tbody > tr > .total-price  > .product-line-price").each(function (t) {
        e = parseFloat(e) + parseFloat($(this).text().replace(/[^\d\.]/g, ""))
    }), $("#final_total").text(e.toFixed(2))
}

function shortDescriptionWordLimit(e, t = 35, a = "...") {
    return e.length > t ? e.substring(0, t - a.length) + a : e
}

$(document).ready(function () {
    $(".kv-svg").rating({
        theme: "krajee-svg",
        showClear: !1,
        showCaption: !1,
        size: "md"
    });
});

function processDescription(description, wordLimit) {

    let decodedText = description.replace(/&[^;]+;/g, "");

    let strippedText = decodedText.replace(/<\/?[^>]+(>|$)/g, "");

    let cleanText = strippedText.replace(/[[\]]/g, "");

    let normalizedText = cleanText.replace(/(\r\n|\n|\r)+/g, " ").replace(/\s+/g, " ");

    let finalText = normalizedText.replace(/ | /g, " ").trim();

    let words = finalText.split(" ").filter(word => word.length > 0);
    if (words.length > wordLimit) {
        finalText = words.slice(0, wordLimit).join(" ") + "...";
    }

    return finalText;
}


function display_compare() {
    var e = localStorage.getItem("compare");
    e = null !== localStorage.getItem("compare") ? e : null, $.ajax({
        type: "POST",
        url: base_url + "compare/add_to_compare",
        data: {
            product_id: e,
            product_variant_id: e,
            [csrfName]: csrfHash
        },
        dataType: "json",
        success: function (t) {
            csrfName = t.csrfName, csrfHash = t.csrfHash;
            var a = e.length ? e.length : "base_url()";
            $("#compare_count").text(t.data.total);
            var r = "";
            0 == t.error ? (null !== e && a > 0 && (r += '<div class="align-self-end mb-7"><div class="compare-removal"><button class="remove-compare btn btn-danger btn-sm" >Clear Compare</button></div></div></div><div class="overflow-auto"><table class="compare-table table-bordered"><tbody><tr><th class="compare-field w-19"> </th>', $.each(t.data.product, function (e, t) {
                var a = t.variants[0].special_price > 0 && "" != t.variants[0].special_price ? t.variants[0].special_price : t.variants[0].price,
                    s = t.minimum_order_quantity ? t.minimum_order_quantity : 1,
                    i = t.minimum_order_quantity && t.quantity_step_size ? t.quantity_step_size : 1,
                    o = t.total_allowed_quantity ? t.total_allowed_quantity : 1;
                if (t.type == 'simple_product') {
                    var stock_product = t.stock;
                } else {
                    var stock_product = t.total_stock;

                }
                if (r += '<td class="compare_item text-center text-justify"><div class="p-5"><div class="text-right"><a class="remove-compare-item"data-product-id="' + t.id + '" style="padding: 4px 8px border:0px !important" ><i class="fa-times fa-times-plus fa-lg fa link-color"></i></a></div><br><div class="product-grid" style="border:1px !important; padding:0 0 0px;"><div class="product-image"><div class="rounded compare-img"><a href="products/details/' + t.slug + '"><img class="pro-img" src="' + t.image + '" style="object-fit:cover;"></a></div></div><div itemscope itemtype="https://schema.org/Product">', t.rating && "" != t.no_of_rating ? r += '<div class="col-md-12 mb-3 product-rating-small" dir="ltr" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating"><meta itemprop="reviewCount" content="' + t.no_of_rating + '" /><meta itemprop="ratingValue" content="' + t.rating + '" /><input id="input" name="rating" class="kv-svg rating rating-loading d-none" data-size="xs" value="' + t.rating + '" data-show-clear="false" data-show-caption="false" readonly> <span class="my-auto mx-3"> ( ' + t.no_of_ratings + " reviews) </span></div>" : r += '<div class="col-md-12 mb-3 product-rating-small" dir="ltr"><input id="input" name="rating" class="kv-svg rating rating-loading d-none" data-size="xs" value="' + t.rating + '" data-show-clear="false" data-show-caption="false" readonly> <span class="my-auto mx-3"> ( ' + t.no_of_ratings + " reviews) </span></div>", r += "</div>", r += ' <h4 class="data-product-title" ><a class="text-decoration-none" href="products/details/' + t.slug + '">' + shortDescriptionWordLimit(t.name) + '</a></h4>   <div class="price mb-1">' + currency + ("simple_product" == t.type ? '<small style="font-size: 20px;">' + t.variants[0].price + "</small>" : '<small style="font-size: 20px;">' + t.min_max_price.max_special_price + '</small> - <small style="font-size: 20px;">' + t.min_max_price.max_price) + "</small> </div>", "simple_product" == t.type) var n = t.variants[0].id,
                    c = "";
                else n = "", c = "#quick-view";
                r += '<a href="#" class="add_to_cart btn btn-xs btn-outline-primary rounded-pill" data-product-id="' + t.id + '" data-product-variant-id="' + n +
                    '" data-product-stock="' + stock_product + '" data-izimodal-open="' + c + '" data-product-title="' + t.name + '" data-product-slug="' + t.slug +
                    '" data-product-image="' + t.image + '" data-product-description="' + processDescription(t.short_description, 20) + '"  data-product-price="' + a + '" data-min="' + s +
                    '" data-max="' + o + '" data-step="' + i + '"><i class="uil uil-shopping-bag"></i> &nbsp; Add to Cart</a>'
            }),

                r += "</tr>", r += '<tr><th class="compare-field text-dark fs-17 text-center">Description </th>', $.each(t.data.product, function (e, t) {
                    r += '<td class="text-center text-justify" data-title="Availability">' + (t.short_description ? processDescription(t.short_description, 20) : t.short_description = "-") + "</td>"
                }),

                r += "</tr>", r += '<tr><th class="compare-field text-dark fs-17 text-center">Variants </th>', $.each(t.data.product, function (e, t) {
                    var a = t.variants[0].attr_name.split(","),
                        s = t.variants[0].variant_values.split(",");
                    if ("variable_product" == t.type) {
                        r += '<td class="text-center text-justify" data-title="variants">';
                        for (e = 0; e < a.length; e++) a[e] !== s[e] && (r += a[e] + " : " + s[e] + "<br>");
                        r += "</td>"
                    } else r += '<td class="text-center text-justify" data-title="variants">-</td>'
                }),

                r += "</tr>", r += '<tr><th class="compare-field text-dark fs-17 text-center">Made In </th>', $.each(t.data.product, function (e, t) {
                    r += '<td class="text-center text-justify" data-title="made in">' + (t.made_in ? t.made_in : "-") + "</td>"
                }),

                r += "</tr>", r += '<tr><th class="compare-field text-dark fs-17 text-center">Warranty</th>', $.each(t.data.product, function (e, t) {
                    r += '<td class="text-center text-justify" data-title="warranty period">' + (t.warranty_period ? t.warranty_period : "-") + "</td>"
                }),

                r += "</tr>", r += '<tr><th class="compare-field text-dark fs-17 text-center">Guarantee</th>', $.each(t.data.product, function (e, t) {
                    r += '<td class="text-center text-justify" data-title="warranty period">' + (t.guarantee_period ? t.guarantee_period : "-") + "</td>"
                }),

                r += "</tr>", r += '<tr><th class="compare-field text-dark fs-17 text-center">Returnable</th>', $.each(t.data.product, function (e, t) {
                    r += '<td class="text-center text-justify" data-title="Returnable">' + ("1" == t.is_returnable ? t.is_returnable = "Yes" : t.is_returnable = "No") + "</td>"
                }),

                r += "</tr>", r += '<tr><th class="compare-field text-dark fs-17 text-center">Cancellation</th>', $.each(t.data.product, function (e, t) {
                    r += '<td class="text-center text-justify" data-title="cancelable">' + ("1" == t.is_cancelable ? t.is_cancelable = "Yes" : t.is_cancelable = "No") + "</td>"
                }),

                r += "</tr>", r += "</tbody></table></div>"),

                $("#compare-items").html(r),
                $(".kv-svg").rating({
                    theme: "krajee-svg",
                    showClear: !1,
                    showCaption: !1,
                    size: "md"
                })
            ) : Toast.fire({
                icon: "error",
                title: t.message
            })
        }
    })
}
$(document).on("closed", "#quick-view", function (e) {
    $("#modal-product-special-price").html("")
}), $(document).ready(function () {
    navigator.geolocation && navigator.geolocation.getCurrentPosition(function (e) {
        var t = e.coords.latitude,
            a = e.coords.longitude;
        sessionStorage.setItem("latitude", t), sessionStorage.setItem("longitude", a)
    }, function (e) {
        switch (e.code) {
            case e.PERMISSION_DENIED:
                null !== sessionStorage.getItem("latitude") && sessionStorage.removeItem("latitude"), null !== sessionStorage.getItem("longitude") && sessionStorage.removeItem("longitude");
                break;
            case e.POSITION_UNAVAILABLE:
                console.log("Location information is unavailable.");
                break;
            case e.TIMEOUT:
                console.log("The request to get user location timed out.");
                break;
            case e.UNKNOWN_ERROR:
                console.log("An unknown error occurred.")
        }
    })
}), $("#send_bank_receipt_form").on("submit", function (e) {

    e.preventDefault();
    var t = new FormData(this);
    t.append(csrfName, csrfHash), $.ajax({
        type: "POST",
        url: $(this).attr("action"),
        data: t,
        beforeSend: function () {
            $("#submit_btn").html("Please Wait..").attr("disabled", !0)
        },
        cache: !1,
        contentType: !1,
        processData: !1,
        dataType: "json",
        success: function (e) {

            csrfHash = e.csrfHash, $("#submit_btn").html("Send").attr("disabled", !1), 0 == e.error ? ($("table").bootstrapTable("refresh"), Toast.fire({
                icon: "success",
                title: e.message
            }), window.location.reload()) : Toast.fire({
                icon: "error",
                title: e.message
            })
        }
    })
}), $(document).ready(function () {
    $(".hrDiv").length && ($(".hrDiv p").addClass("hrDiv"), $("div").css({
        "font-size": "",
        font: ""
    }))
}), $("#validate-zipcode-form").on("submit", function (e) {
    e.preventDefault();
    var t = new FormData(this);
    t.append(csrfName, csrfHash), $.ajax({
        type: "POST",
        url: base_url + "products/check_zipcode",
        data: t,
        beforeSend: function () {
            $("#validate_zipcode").html("Please Wait..").attr("disabled", !0)
        },
        cache: !1,
        contentType: !1,
        processData: !1,
        dataType: "json",
        success: function (e) {
            csrfHash = e.csrfHash,
                $("#validate_zipcode").html("Check Availability").attr("disabled", !1)
            if (e.error == false) {
                $('#add_cart').removeAttr('disabled')
                $('.buy_now').removeAttr('disabled')
                $('#error_box').html(e.message)
            } else {
                $('#add_cart').attr('disabled', 'true')
                $('.buy_now').attr('disabled', 'true')
                $('#error_box').html(e.message)
            }
        }
    })
}),

    $('#validate-city-form').on('submit', function (e) {
        e.preventDefault()
        var formdata = new FormData(this)
        formdata.append(csrfName, csrfHash)

        $.ajax({
            type: 'POST',
            url: base_url + 'products/check_city',
            data: formdata,
            beforeSend: function () {
                $("#validate_city").html("Please Wait..").attr("disabled", !0)

            },
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (result) {
                csrfHash = result.csrfHash

                $('#validate_city').html('Check Availability').attr('disabled', false)
                if (result.error == false) {
                    $('#add_cart').removeAttr('disabled')
                    $('.buy_now').removeAttr('disabled')
                    $('#error_box').html(result.message)
                } else {
                    $('#add_cart').attr('disabled', 'true')
                    $('.buy_now').attr('disabled', 'true')
                    $('#error_box').html(result.message)
                }
            }
        })
    })


$(document).on("submit", ".validate_zipcode_quick_view", function (e) {
    e.preventDefault();
    var t = new FormData(this);
    t.append(csrfName, csrfHash), $.ajax({
        type: "post",
        url: base_url + "products/check-zipcode",
        data: t,
        beforeSend: function () {
            $("#validate_zipcode").html("Please Wait..").attr("disabled", !0)
        },
        cache: !1,
        contentType: !1,
        processData: !1,
        dataType: "json",
        success: function (e) {
            csrfHash = e.csrfHash, $
                ("#validate_zipcode").html("Check Availability").attr("disabled", !1)
            if (e.error == false) {
                $('#modal-add-to-cart-button').removeAttr('disabled')
                $('modal-buy-now-button').removeAttr('disabled')
                $('#error_box1').html(e.message)
            } else {
                $('#modal-add-to-cart-button').attr('disabled', 'true')
                $('modal-buy-now-button').attr('disabled', 'true')
                $('#error_box1').html(e.message)
            }
        }
    })
}),
    $(document).on("submit", ".validate_city_quick_view", function (e) {
        e.preventDefault();
        var t = new FormData(this);
        t.append(csrfName, csrfHash), $.ajax({
            type: "post",
            url: base_url + "products/check_city",
            data: t,
            beforeSend: function () {
                $("#validate_city").html("Please Wait..").attr("disabled", !0)
            },
            cache: !1,
            contentType: !1,
            processData: !1,
            dataType: "json",
            success: function (e) {
                csrfHash = e.csrfHash,
                    $("#validate_city").html("Check Availability").attr("disabled", !1)
                if (e.error == false) {
                    $('#modal-add-to-cart-button').removeAttr('disabled')
                    $('modal-buy-now-button').removeAttr('disabled')
                    $('#error_box1').html(e.message)
                } else {
                    $('#modal-add-to-cart-button').attr('disabled', 'true')
                    $('modal-buy-now-button').attr('disabled', 'true')
                    $('#error_box1').html(e.message)
                }
            }
        })
    }),
    $(".view_cart_button").click(function () {
        return 0 != is_loggedin || ($("#modal-signin").show(),
            $("#login_div").removeClass("hide"),
            $("#login").addClass("active"),
            $("#register").removeClass("active"), !1)
    }),
    $(document).ready(function () {
        if ($(location).attr('href') == base_url + "compare") {

            if (localStorage.getItem("compare")) {
                var e = localStorage.getItem("compare").length;
                (e = null !== e ? JSON.parse(e) : null) && display_compare()
            }
        }
    }), $(document).on("click", ".compare", function (e) {
        e.preventDefault();
        var t = $(this).attr("data-product-id"),
            a = $(this).attr("data-product-variant-id"),
            r = {
                product_id: t.trim(),
                product_variant_id: a.trim()
            },
            s = localStorage.getItem("compare");
        if (Toast.fire({
            icon: "success",
            title: "products added to compare list"
        }), null != (s = null !== s ? JSON.parse(s) : null)) {
            if (s.find(e => e.product_id === t)) return void Toast.fire({
                icon: "error",
                title: "This item is already present in your compare list"
            });
            s.push(r)
        } else s = [r];
        localStorage.setItem("compare", JSON.stringify(s));
        var i = s.length ? s.length : "";
        if ($("#compare_count").text(i), null !== s && i <= 1) return Toast.fire({
            icon: "warning",
            title: "Please select 1 more item to compare"
        }), !1
    }), $(document).on("click", ".remove-compare-item", function (e) {
        e.preventDefault();
        var t = $(this).attr("data-product-id");
        if (confirm("Are you sure want to remove this?")) {
            var a = $("#compare_count").text();
            a--, $("#compare_count").text(a), a < 1 ? ($(this).parent().parent().remove(), location.reload()) : $(this).parent().parent().remove();
            var r = localStorage.getItem("compare");
            if (r = null !== r ? JSON.parse(r) : null) {
                var s = r.filter(function (e) {
                    return e.product_id != t
                });
                localStorage.setItem("compare", JSON.stringify(s)), display_compare()
            }
        }
    }), $(document).on("click", ".compare-removal button", function (e) {
        e.preventDefault();
        var t = $(this).attr("data-product-id"),
            a = $(this).parent().parent().parent();
        if (confirm("Are you sure want to remove this?")) {
            localStorage.removeItem("compare"), location.reload();
            a = localStorage.getItem("compare");
            if (a = null !== localStorage.getItem("compare") ? JSON.parse(a) : null) {
                var r = a.filter(function (e) {
                    return e.id != t
                });
                localStorage.setItem("compare", JSON.stringify(r)), a && display_compare(r)
            }
        }
    }), $(document).on("submit", "#add-faqs", function (e) {
        e.preventDefault();
        var t = new FormData(this);
        t.append(csrfName, csrfHash), $.ajax({
            type: "POST",
            url: $(this).attr("action"),
            dataType: "json",
            data: t,
            processData: !1,
            contentType: !1,
            success: function (e) {
                csrfName = e.csrfName, csrfHash = e.csrfHash, 0 == e.error ? (Toast.fire({
                    icon: "success",
                    title: e.message
                }), $("#add-faqs")[0].reset()) : Toast.fire({
                    icon: "error",
                    title: e.message
                }), setTimeout(function () {
                    location.reload()
                }, 1e3)
            }
        })
    }), $(".search_faqs").select2({
        ajax: {
            url: base_url + "products/get_faqs_data",
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (e) {
                return {
                    search: e.term
                }
            },
            processResults: function (e) {
                return {
                    results: e
                }
            },
            cache: !0
        },
        minimumInputLength: 1,
        theme: "bootstrap4",
        placeholder: "Search for faqs"
    });

$(function () {
    $("#inspect_value").data("value");
    return !1
});

$(document).ready(function () {
    $("#share").jsSocials({
        showLabel: false,
        showCount: false,
        shares: ["twitter", "facebook", "whatsapp", "pinterest", "linkedin", "googleplus"]
    });
    $(document).on('click', '#googleLogin', function (e) {
        e.preventDefault();
        googleSignIn();
    });
    $(document).on('click', '#facebookLogin', function (e) {
        e.preventDefault();
        facebookSignIn();
    });
    $(document).on('click', '#googleLogout', function (e) {
        e.preventDefault();
        firebase.auth().signOut()
            .then(function () {
                // Sign-out successful.
                alert('You have been logged out.');
            })
            .catch(function (error) {
                // An error happened.
                console.error(error);
            });
    });

    function googleSignIn() {
        var provider = new firebase.auth.GoogleAuthProvider();
        provider.addScope('email');
        firebase.auth().signInWithPopup(provider).then(function (result) {


            var type = 'google';
            var name = result.user.displayName;
            if (result.user.email != null && result.user.email != '') {
                var email = result.user.email
            } else if (result.user.providerData[0].email != null && result.user.providerData[0].email != '') {
                var email = result.user.providerData[0].email
            } else {
                var email = result.additionalUserInfo.profile.email
            }
            var password = result.user.uid;
            $.ajax({
                type: 'POST',
                url: base_url + 'home/verifyUser',
                data: {
                    email: email,
                    type: type,
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function (result) {
                    csrfName = result['csrfName'];
                    csrfHash = result['csrfHash'];

                    if (result.error == true) {
                        $.ajax({
                            type: 'POST',
                            url: base_url + 'auth/register_user',
                            data: {
                                type: type,
                                name: name,
                                email: email,
                                password: password,
                                [csrfName]: csrfHash
                            },
                            dataType: 'json',
                            success: function (result) {
                                csrfName = result['csrfName'];
                                csrfHash = result['csrfHash'];
                                if (result.error == false) {
                                    $.ajax({
                                        type: 'POST',
                                        url: base_url + 'home/login',
                                        data: {
                                            identity: email,
                                            type: type,
                                            password: password,
                                            [csrfName]: csrfHash
                                        },
                                        dataType: 'json',
                                        success: function (result) {
                                            csrfName = result['csrfName'];
                                            csrfHash = result['csrfHash'];
                                            cart_sync();
                                            location.reload();

                                        }
                                    });
                                }
                            }
                        });
                    } else {
                        $.ajax({
                            type: 'POST',
                            url: base_url + 'home/login',
                            data: {
                                identity: email,
                                type: type,
                                password: password,
                                [csrfName]: csrfHash
                            },
                            dataType: 'json',
                            success: function (result) {
                                csrfName = result['csrfName'];
                                csrfHash = result['csrfHash'];
                                cart_sync();
                                location.reload();

                            }
                        });
                    }
                }
            });

        }).catch(function (error) {

        });
    }
    function facebookSignIn() {
        var provider = new firebase.auth.FacebookAuthProvider();
        provider.addScope('email');
        firebase.auth().signInWithPopup(provider).then(function (result) {

            var type = 'facebook';
            var name = result.user.displayName;
            if (result.user.email != null && result.user.email != '') {
                var email = result.user.email
            } else if (result.user.providerData[0].email != null && result.user.providerData[0].email != '') {
                var email = result.user.providerData[0].email
            } else {
                var email = result.additionalUserInfo.profile.email
            }
            var password = result.user.uid;
            $.ajax({
                type: 'POST',
                url: base_url + 'home/verifyUser',
                data: {
                    email: email,
                    type: type,
                    [csrfName]: csrfHash
                },
                dataType: 'json',
                success: function (result) {
                    csrfName = result['csrfName'];
                    csrfHash = result['csrfHash'];

                    if (result.error == true) {
                        $.ajax({
                            type: 'POST',
                            url: base_url + 'auth/register_user',
                            data: {
                                type: type,
                                name: name,
                                email: email,
                                password: password,
                                [csrfName]: csrfHash
                            },
                            dataType: 'json',
                            success: function (result) {
                                csrfName = result['csrfName'];
                                csrfHash = result['csrfHash'];
                                if (result.error == false) {
                                    $.ajax({
                                        type: 'POST',
                                        url: base_url + 'home/login',
                                        data: {
                                            identity: email,
                                            type: type,
                                            password: password,
                                            [csrfName]: csrfHash
                                        },
                                        dataType: 'json',
                                        success: function (result) {
                                            csrfName = result['csrfName'];
                                            csrfHash = result['csrfHash'];
                                            cart_sync();
                                            location.reload();
                                        }
                                    });
                                }
                            }
                        });
                    } else {
                        $.ajax({
                            type: 'POST',
                            url: base_url + 'home/login',
                            data: {
                                identity: email,
                                type: type,
                                password: password,
                                [csrfName]: csrfHash
                            },
                            dataType: 'json',
                            success: function (result) {
                                csrfName = result['csrfName'];
                                csrfHash = result['csrfHash'];
                                cart_sync();
                                location.reload();
                            }
                        });
                    }
                }
            });

        }).catch(function (error) {

        });
    }
});


swiper = new Swiper(".swiper-slide-container", {
    slidesPerView: 1,
    effect: "slide",
    pagination: {
        el: ".slide-swiper-pagination",
        dynamicBullets: true,
        clickable: !0
    },
    loop: !0,
    // autoplay: {
    //     delay: 3500
    // }, on: {
    //     init() {
    //         // ...
    //     },
    // },
});


swiper = new Swiper(".mySwiper", {
    slidesPerView: 2,
    spaceBetween: 30,
    pagination: {
        el: ".product-swiper-pagination",
        dynamicBullets: true,
        clickable: !0
    },
    breakpoints: {
        300: {
            slidesPerView: 1,
            spaceBetweenSlides: 10
        },
        350: {
            slidesPerView: 2,
            spaceBetweenSlides: 10
        },
        400: {
            slidesPerView: 2,
            spaceBetweenSlides: 10
        },
        499: {
            slidesPerView: 2,
            spaceBetweenSlides: 10
        },
        600: {
            slidesPerView: 2,
            spaceBetweenSlides: 10
        },
        800: {
            slidesPerView: 2,
            spaceBetweenSlides: 10
        },
        801: {
            slidesPerView: 3,
            spaceBetweenSlides: 10
        },
        999: {
            slidesPerView: 3,
            spaceBetweenSlides: 10
        },
        1900: {
            slidesPerView: 3,
            spaceBetweenSlides: 10
        }
    }
});
swiper = new Swiper(".mySwiper4", {
    slidesPerView: 2,
    spaceBetween: 30,
    pagination: {
        el: ".product-swiper-pagination",
        dynamicBullets: true,
        clickable: !0
    },
    breakpoints: {
        300: {
            slidesPerView: 1,
            spaceBetweenSlides: 10
        },
        350: {
            slidesPerView: 2,
            spaceBetweenSlides: 10
        },
        400: {
            slidesPerView: 2,
            spaceBetweenSlides: 10
        },
        499: {
            slidesPerView: 2,
            spaceBetweenSlides: 10
        },
        600: {
            slidesPerView: 2,
            spaceBetweenSlides: 10
        },
        800: {
            slidesPerView: 2,
            spaceBetweenSlides: 10
        },
        801: {
            slidesPerView: 3,
            spaceBetweenSlides: 10
        },
        999: {
            slidesPerView: 4,
            spaceBetweenSlides: 10
        },
        1900: {
            slidesPerView: 4,
            spaceBetweenSlides: 10
        }
    }
});

jQuery(document).ready(function ($) {
    $(".color-switcher").on("click", function () {

        $("#color-switcher").attr("href", $(this).data("url"));
        $(".logo-img").attr("src", $(this).data("image"));
        return false;
    });
    $("ul.color-style li a").click(function (e) {
        e.preventDefault();
        $(this).parent().parent().find("a").removeClass("active");
        $(this).addClass("active");
    })
    $("#colors-switcher .color-bottom a.settings").click(function (e) {
        e.preventDefault();
        var div = $("#colors-switcher");
        if (div.css(mode) === "-189px") {
            $("#colors-switcher").animate({
                [mode]: "0px"
            });
        } else {
            $("#colors-switcher").animate({
                [mode]: "-189px"
            });
        }
    })
    $("#colors-switcher").animate({
        [mode]: "-189px"
    });
});

$(document).ready(function () {
    // Show/hide chat iframe on chat button click

    $("#chat-button").on("click", function (e) {
        e.preventDefault();
        $("#chat-iframe").toggle();
        $(this).toggleClass("opened");
        $("#chat-iframe").toggleClass("opened");
    });
    $("#chat-with-button").on("click", function (e) {
        e.preventDefault();
        $("#chat-iframe").attr("src", base_url + "my-account/floating_chat_modern?user_id=" + $(this).data("id"));
        $("#chat-iframe").toggle();
        $(this).toggleClass("opened");
        $("#chat-iframe").toggleClass("opened");
    });
});

$(document).ready(function () {
    // Submit chat message to backend on form submit
    $(".reorder-btn").on("click", (event) => {
        const variants = ($(event.target).data("variants")) + ""
        const qty = ($(event.target).data("quantity")) + ""

        let html = $(event.target).html()
        $.ajax({
            type: "POST",
            url: base_url + "cart/manage",
            data: {
                product_variant_id: variants,
                qty: qty,
                is_saved_for_later: false,
                [csrfName]: csrfHash
            },
            dataType: "json",
            beforeSend: function () {
                $(event.target).text("Please Wait").attr("disabled", true)
            },
            success: function (res) {
                $(event.target).text(html).attr("disabled", false)
                window.location.href = base_url + "cart/checkout"
            }
        })

    })

});

$(document).on("click", ".buy_now", function (e) {
    e.preventDefault();
    var productId = $(this).data('product-id');
    var productTitle = $(this).data('product-title');
    var productSlug = $(this).data('product-slug');
    var productImage = $(this).data('product-image');
    var productPrice = $(this).data('product-price');
    var productDescription = $(this).data('product-description');
    var step = $(this).data('step');
    var min = $(this).data('min');
    var max = $(this).data('max');
    var d = $(this);
    if ($('[name="qty"]').val() != null) {
        var quantity_product = $('[name="qty"]').val();
    } else {
        var quantity_product = $(this).attr("data-min");
    }

    var productVariantId = $(this).data('product-variant-id');
    var data = {
        product_id: productId,
        buy_now: '1',
        product_title: productTitle,
        product_slug: productSlug,
        product_image: productImage,
        product_price: productPrice,
        product_description: productDescription,
        step: step,
        min: min,
        max: max,
        qty: quantity_product,
        is_saved_for_later: false,
        csrfName: csrfHash,
        product_variant_id: productVariantId
    }
    // Send AJAX request to add product to cart
    if (is_loggedin == "1" || is_loggedin == 1) {
        $.ajax({
            type: "POST",
            url: base_url + "cart/manage",
            data: data,

            success: function (response) {
                // Redirect to checkout page
                var res = JSON.parse(response);
                csrfName = res.csrfName;
                csrfHash = res.csrfHash;
                if (res.error == false) {
                    Toast.fire({
                        icon: "success",
                        title: res.message
                    }),
                        window.location.href = base_url + "cart";
                } else {
                    Toast.fire({
                        icon: "error",
                        title: res.message
                    });
                }
            }
        });
    } else {
        // If user is not logged in, show login modal
        $("#modal-signin").modal("show");
        // $("#modal-signin").show();
        $("#login_div").removeClass("hide");
        $("#login").addClass("active");
        $("#register").removeClass("active");
    }
});


$(document).ready(function () {
    $('.select2-container').click(function (event) {
        event.preventDefault();
        if ($('#offcanvas-search').hasClass('show')) {

            $('.select2-search--dropdown').addClass('mt-n10');
        }
    });
});

$(document).on('click', '.ticket_button', function (e) {
    if ($('.display_fields').hasClass('d-none')) {

        $('.display_fields').removeClass('d-none')
    } else {

        $('.display_fields').addClass('d-none')
    }
})

$(document).on('click', '.ask_question', function () {

    var type = $('#ticket_type').val();
    var email = $('#email').val();
    var subject = $('#subject').val();
    var description = $('#description').val();
    var id = $('#user_id').val();

    $.ajax({
        type: 'POST',
        data: {
            ticket_type_id: type,
            email: email,
            subject: subject,
            description: description,
            user_id: id,
            [csrfName]: csrfHash
        },
        dataType: 'json',
        url: base_url + 'Tickets/add_ticket',
        success: function (result) {

            csrfName = result['csrfName'];
            csrfHash = result['csrfHash'];
            if (result.error == false) {
                Toast.fire({
                    icon: 'success',
                    title: result.message
                })
                setTimeout(function () {
                    location.reload()
                }, 600)


            } else {
                Toast.fire({
                    icon: 'error',
                    title: result.message
                })
            }
        }
    })

})

// refer and earn code
function copyText() {
    /* Get the text to copy */
    const text = $("#text-to-copy").text();

    /* Create a temporary input element */
    const tempInput = $("<input>");
    tempInput.attr("type", "text");
    tempInput.val(text);
    $("body").append(tempInput);

    /* Select and copy the text */
    tempInput.select();
    document.execCommand("copy");

    /* Remove the temporary input element */
    tempInput.remove();

    /* Update the copy button text */
    const copyButton = $(".copy-button");
    copyButton.text("Copied!");
    setTimeout(function () {
        copyButton.text("Tap to copy");
    }, 1000);
}
$(document).ready(function () {
    const passwordInput = $('#passwordInput');
    const togglePassword = $('#togglePassword');

    togglePassword.click(function () {
        const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
        passwordInput.attr('type', type);

        // Change eye icon based on the input type
        togglePassword.html(type === 'password' ? '<i class="uil uil-eye"></i>' : '<i class="uil uil-eye-slash"></i>');
    });
});


var swiperS = new Swiper('.category-swiper', {
    slidesPerView: 5,
    preloadImages: false,
    updateOnImagesReady: false,
    lazyLoadingInPrevNextAmount: 0,
    pagination: {
        el: ".category-swiper-pagination",
        dynamicBullets: true,
        clickable: !0
    },
    breakpoints: {
        300: {
            slidesPerView: 3,
            spaceBetweenSlides: 10
        },
        350: {
            slidesPerView: 3,
            spaceBetweenSlides: 10
        },
        400: {
            slidesPerView: 4,
            spaceBetweenSlides: 10
        },
        499: {
            slidesPerView: 4,
            spaceBetweenSlides: 10
        },
        550: {
            slidesPerView: 5,
            spaceBetweenSlides: 10
        },
        600: {
            slidesPerView: 5,
            spaceBetweenSlides: 10
        },
        700: {
            slidesPerView: 6,
            spaceBetweenSlides: 10
        },
        800: {
            slidesPerView: 8,
            spaceBetweenSlides: 10
        },
        999: {
            slidesPerView: 8,
            spaceBetweenSlides: 10
        },
        1900: {
            slidesPerView: 8,
            spaceBetweenSlides: 10
        }
    }
});


// let timer;
const timeoutVal = 100;

const search_product_input = $('#search-product');
const search_btn = $('.search_btn');

search_product_input.on('keyup', function (e) {


    let value_enter = $('#search-product').val();

    // Check if Enter key is pressed
    if (e.keyCode === 13 || e.which === 13) {
        // Trigger the search function
        // search_product_result();
        window.location.href = `${base_url}products/search?q=${encodeURIComponent(value_enter)}`;
    }
});

search_product_input.on('keyup', search_product_result);
search_btn.on('click', search_product_result);


document.addEventListener('DOMContentLoaded', function () {
    const offcanvasSearch = document.getElementById('offcanvas-search');
    if (offcanvasSearch) {
        offcanvasSearch.addEventListener('hide.bs.offcanvas', function () {
            document.querySelectorAll('#offcanvas-search input').forEach(input => input.value = '');
            $('#search_items').html('');  // Clear search results
        });
    }
});
$(document).ready(function () {
    $('#offcanvas-search').on('shown.bs.offcanvas', function () {
        $('#search-product').focus();
        $('#search_items').html('🪄Looking for something special? Start typing and explore our recommendations!');
    });
});

// The search function
function search_product_result() {
    let value = $('#search-product').val();

    $.ajax({
        type: "GET",
        url: base_url + "home/get_products",
        data: {
            search: value,
        },
        dataType: "json",
        success: function (response) {
            var suggestion_keywords = Array.isArray(response.suggestion_keywords) ? response.suggestion_keywords : [];
            var results = Array.isArray(response.data) ? response.data : [];

            $('#header-search').attr('href', base_url + `products/search?q=${value}`);
            let html = '';
            if (response.error == false) {
                if (value != null && value != "") {
                    html += `<li class="item w-100 text-center text-muted">${response.data[0].name}</li>`;
                    suggestion_keywords.forEach(item => {
                        html +=
                            `<li class="item">
                                <div class="mini-list-item d-flex align-items-center w-100 clearfix gap-4">
                                    <a class="item-title" href="${base_url + "products/search?q=" + encodeURIComponent(item.suggestion_keyword)}">
                                        <div class="text-center"><i class='fa fa-search'></i></div>
                                    </a>
                                    <div class="align-items-center d-flex details justify-content-between w-100">
                                        <a class="item-title w-100" href="${base_url + "products/search?q=" + encodeURIComponent(item.suggestion_keyword)}">
                                            <p class="item-title m-0 text-start">${item.suggestion_keyword}</p>
                                        </a>
                                        <button class='search_btn' onclick="copySearch('${item.suggestion_keyword}')"><i class='font-weight-bold fs-20 uil uil-arrow-up-left'></i></button>
                                    </div>
                                </div>
                            </li>`;
                    });
                    results.forEach(item => {
                        if (item.product_slug !== undefined) {
                            html += `<li class="item pb-2"><div class="mini-list-item d-flex align-items-center w-100 clearfix">
                                    <div class="mini-image text-center"><a class="item-link" href="${base_url + "products/details/" + item.product_slug}"><img class="blur-up lazyload" data-src="${item.image_md}" src="${item.image_md}" alt="${item.name}" title="${item.name}" width="70" height="70" /></a></div>
                                    <div class="ms-3 details text-left">
                                    <div class="product-name"><a class="item-title" href="${base_url + "products/details/" + item.product_slug}">${item.name}</a></div>
                                    </div></div></li>`;
                        }
                    });
                } else {
                    html += `<li class="item w-100 text-center text-muted pb-2">🪄Looking for something special? Start typing and explore our recommendations!</li>`;
                }
                $('#search_items').html(html);
                return;
            }
            html += `<li class="item w-100 text-center text-muted">${response.message}</li>`;
            $('#search_items').html(html);
        }
    });
}

// SUPPORT CHAT 
var scrolled = 0;
$(document).on('click', '.view_ticket_chat', function (e, row) {
    e.preventDefault();
    $(".ticket_msg").data('max-loaded', false);
    var ticket_id = $(this).data("id");

    var username = $(this).data("username");
    var date_created = $(this).data("date_created");
    var subject = $(this).data("subject");
    var status = $(this).data("status");
    var ticket_type = $(this).data("ticket_type");
    $('input[name="ticket_id"]').val(ticket_id);
    $('#user_name').html(username);
    $('#date_created').html(date_created);
    $('#subject_chat').html(subject);
    $('.change_ticket_status').data('ticket_id', ticket_id);
    if (status == 1) {
        $('#status').html('<label class="badge badge-secondary ml-2">PENDING</label>');
    } else if (status == 2) {
        $('#status').html('<label class="badge badge-info ml-2">OPENED</label>');
    } else if (status == 3) {
        $('#status').html('<label class="badge badge-success ml-2">RESOLVED</label>');
    } else if (status == 4) {
        $('#status').html('<label class="badge badge-danger ml-2">CLOSED</label>');
    } else if (status == 5) {
        $('#status').html('<label class="badge badge-warning ml-2">REOPENED</label>');
    }
    $('#ticket_type_chat').html(ticket_type);
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
                    var is_left = (message.user_type == 'user') ? 'right' : 'left';
                    var message_html = "";
                    var atch_html = "";
                    var i = 1;
                    if (message.attachments.length > 0) {
                        message.attachments.forEach(atch => {
                            atch_html += "<div class='container-fluid image-upload-section'>" +
                                "<a class='btn btn-danger btn-xs' href='" + atch.media + "'  target='_blank' alt='Attachment Not Found'>Attachment " + i + "</a>" +
                                "<div class='col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none'></div>" +
                                "</div>";
                            i++;
                        });
                    }
                    message_html += "<div class='direct-chat-msg " + is_left + "'>" +
                        "<div class='direct-chat-infos clearfix'>" +
                        "<span class='direct-chat-name float-" + is_left + "' id='name'>" + message.name + "</span>" +
                        "<span class='direct-chat-timestamp fs-12 float-" + is_left + "' id='last_updated'>" + message.last_updated + "</span>" +
                        "</div>" +
                        "<div class='direct-chat-text' id='message'>" + message.message + "" + atch_html + "</div>" +
                        "</div>";

                    $('.ticket_msg').append(message_html);
                    $("#message_input").val('');

                    $("#element").scrollTop($("#element")[0].scrollHeight);
                    $('input[name="attachments[]"]').val('');
                }
                Toast.fire({
                    icon: 'success',
                    title: '<span style="text-transform:capitalize">' + result.message + '</span> ',
                })

            } else {
                Toast.fire({
                    icon: 'error',
                    title: '<span style="text-transform:capitalize">' + result.message + '</span> ',
                })
                $("#element").data('max-loaded', true);

                return false;
            }

        }
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
            url: base_url + 'tickets/get_ticket_messages',
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
                            is_left = (messages.user_type == 'user' || messages.user_type == '') ? 'right' : 'left';
                            is_right = (messages.user_type == 'user' || messages.user_type == '') ? 'left' : 'right';
                            if (messages.attachments.length > 0) {
                                messages.attachments.forEach(atch => {
                                    atch_html += "<div class='container-fluid image-upload-section'>" +
                                        "<a class='btn btn-danger btn-xs' href='" + atch.media + "'  target='_blank' alt='Attachment Not Found'>Attachment " + i + "</a>" +
                                        "<div class='col-md-3 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image d-none'></div>" +
                                        "</div>";
                                    i++;
                                });
                            }
                            messages_html += "<div class='direct-chat-msg " + is_left + "'>" +
                                "<div class='direct-chat-infos clearfix'>" +
                                "<span class='direct-chat-name float-" + is_left + "' id='name'>" + messages.name + "</span>" +
                                "<span class='direct-chat-timestamp fs-12 float-" + is_left + "' id='last_updated'>" + messages.last_updated + "</span>" +
                                "</div>" +
                                "<div class='direct-chat-text' id='message'>" + messages.message + "" + atch_html + "</div>" +
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

$(document).ready(function () {
    // Check if we are on the support ticket page
    if (window.location.pathname.includes('/tickets')) {
        document.getElementById('attachments').addEventListener('change', function () {
            let files = Array.from(this.files).map(f => f.name).join(', ');
            if (files) {
                if (document.getElementById('selected-files')) {
                    document.getElementById('selected-files').textContent = files;
                } else {
                    let span = document.createElement('span');
                    span.id = 'selected-files';
                    span.className = 'ml-2 text-muted';
                    span.textContent = files;
                    this.parentNode.appendChild(span);
                }
            }
        });
    }
});
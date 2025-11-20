"use strict";
var stripe1;
var fatoorah_url = '';
var currency = $('#currency').val();
var supported_locals = $('#supported_locals').val();

var addresses = [];
$(document).ready(function () {


    $('#documents').on('change', function () {
        var allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        var selectedFiles = this.files;

        for (var i = 0; i < selectedFiles.length; i++) {
            var file = selectedFiles[i];
            var extension = file.name.split('.').pop().toLowerCase();

            if (allowedExtensions.indexOf(extension) === -1) {
                alert('Invalid file format: ' + file.name);
                $('#documents').val('');
                return false;
            }
        }
    });

    function midtrans_setup(midtrans_transaction_token) {

        // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token
        window.snap.pay(midtrans_transaction_token, {
            onSuccess: function (result) {
                /* You may add your own implementation here */

                place_order().done(function (result) {
                    if (result.error == false) {
                        setTimeout(function () {
                            location.href = base_url + 'payment/success';
                        }, 3000);
                    }
                });
            },
            onPending: function (result) {
                /* You may add your own implementation here */
                alert("wating your payment!");

            },
            onError: function (result) {
                /* You may add your own implementation here */
                alert("payment failed!");
                $('#place_order_btn').attr('disabled', false).html('Place Order');

            },
            onClose: function () {
                /* You may add your own implementation here */
                $('#place_order_btn').attr('disabled', false).html('Place Order');
                alert('you closed the popup without finishing the payment');
            }
        });
    }

    function razorpay_setup(key, amount, app_name, logo, razorpay_order_id, username, user_email, user_contact) {
        var options = {
            "key": key, // Enter the Key ID generated from the Dashboard
            "amount": (amount * 100), // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
            "currency": supported_locals,
            "name": app_name,
            "description": "Product Purchase",
            "image": logo,
            "order_id": razorpay_order_id, //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
            "handler": function (response) {
                $('#razorpay_payment_id').val(response.razorpay_payment_id);
                $('#razorpay_signature').val(response.razorpay_signature);
                place_order().done(function (result) {
                    if (result.error == false) {
                        setTimeout(function () {
                            location.href = base_url + 'payment/success';
                        }, 3000);
                    }
                });
            },
            "prefill": {
                "name": username,
                "email": user_email,
                "contact": user_contact
            },
            "notes": {
                "address": app_name + " Purchase"
            },
            "theme": {
                "color": "#3399cc"
            },
            "escape": false,
            "modal": {
                "ondismiss": function () {
                }
            }
        };
        var rzp = new Razorpay(options);
        return rzp;
    }

    function paystack_setup(key, user_email, order_amount) {
        var handler = PaystackPop.setup({
            key: key,
            email: user_email,
            amount: (order_amount * 100),
            currency: "NGN",
            callback: function (response) {
                $('#paystack_reference').val(response.reference);
                if (response.status == "success") {
                    place_order().done(function (result) {
                        if (result.error == false) {
                            setTimeout(function () {
                                location.href = base_url + 'payment/success';
                            }, 3000);
                        }
                    });
                } else {
                    location.href = base_url + 'payment/cancel';
                }
            },
            onClose: function () {
                $('#place_order_btn').attr('disabled', false).html('Place Order');
            }
        });
        return handler;

    }

    function stripe_setup(key) {
        // A reference to Stripe.js initialized with a fake API key.
        // Sign in to see examples pre-filled with your key.
        var stripe = Stripe(key);
        // Disable the button until we have Stripe set up on the page
        var elements = stripe.elements();
        var style = {
            base: {
                color: "#32325d",
                fontFamily: 'Arial, sans-serif',
                fontSmoothing: "antialiased",
                fontSize: "16px",
                "::placeholder": {
                    color: "#32325d"
                }
            },
            invalid: {
                fontFamily: 'Arial, sans-serif',
                color: "#fa755a",
                iconColor: "#fa755a"
            }
        };

        var card = elements.create("card", {
            style: style
        });
        card.mount("#stripe-card-element");

        card.on("change", function (event) {
            // Disable the Pay button if there are no card details in the Element
            document.querySelector("button").disabled = event.empty;
            document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
        });
        return {
            'stripe': stripe,
            'card': card
        };
    }

    function stripe_payment(stripe, card, clientSecret) {
        // Calls stripe.confirmCardPayment
        // If the card requires authentication Stripe shows a pop-up modal to
        // prompt the user to enter authentication details without leaving your page.
        stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: card
            }
        })
            .then(function (result) {
                if (result.error) {
                    // Show error to your customer
                    var errorMsg = document.querySelector("#card-error");
                    errorMsg.textContent = result.error.message;
                    setTimeout(function () {
                        errorMsg.textContent = "";
                    }, 4000);
                    Toast.fire({
                        icon: 'error',
                        title: result.error.message
                    });
                    $('#place_order_btn').attr('disabled', false).html('Place Order');
                } else {
                    // The payment succeeded!
                    place_order().done(function (result) {
                        if (result.error == false) {
                            setTimeout(function () {
                                location.href = base_url + 'payment/success';
                            }, 1000);
                        }
                    });
                }
            });
    };


    // function flutterwave_payment() {
    //     var address_id = $("#address_id").val();
    //     var documents = $('#documents').val();
    //     var email_id = $('#digital_product_email').val();
    //     var product_type = $('#product_type').val();
    //     var download_allowed = $('#download_allowed').val(); // 0 : not downlodable | 1 : download allowed
    //     if ($('#wallet_balance').is(":checked")) {
    //         var wallet_used = 1;
    //     } else {
    //         var wallet_used = 0;
    //     }

    //     var promo_set = $('#promo_set').val();
    //     var promo_code = '';
    //     if (promo_set == 1) {
    //         promo_code = $('#promocode_input').val();
    //     }
    //     var logo = $('#logo').val();
    //     var public_key = $('#flutterwave_public_key').val();
    //     var currency_code = $('#flutterwave_currency').val();
    //     switch (currency_code) {
    //         case 'KES':
    //             var country = 'KE';
    //             break;
    //         case 'GHS':
    //             var country = 'GH';
    //             break;
    //         case 'ZAR':
    //             var country = 'ZA';
    //             break;
    //         case 'TZS':
    //             var country = 'TZ';
    //             break;

    //         default:
    //             var country = 'NG';
    //             break;
    //     }
    //     $.post(base_url + "cart/pre-payment-setup", {
    //         [csrfName]: csrfHash,
    //         'payment_method': 'Flutterwave',
    //         'wallet_used': wallet_used,
    //         'address_id': address_id,
    //         'product_type': product_type,
    //         'download_allowed': download_allowed,
    //         'email_id': email_id,
    //         'promo_code': promo_code,
    //         'documents': documents
    //     }, function (data) {
    //         csrfName = data.csrfName;
    //         csrfHash = data.csrfHash;
    //         if (data.error == false) {
    //             var amount = data.final_amount;
    //             var phone_number = $('#user_contact').val();
    //             var email = $('#user_email').val();
    //             var name = $('#username').val();
    //             var title = $('#app_name').val();
    //             var d = new Date();
    //             var ms = d.getMilliseconds();
    //             var number = Math.floor(1000 + Math.random() * 9000);
    //             var tx_ref = title + '-' + ms + '-' + number
    //             FlutterwaveCheckout({
    //                 public_key: public_key,
    //                 tx_ref: tx_ref,
    //                 amount: amount,
    //                 currency: currency_code,
    //                 country: country,
    //                 payment_options: "card,mobilemoney,ussd",
    //                 customer: {
    //                     email: email,
    //                     phone_number: phone_number,
    //                     name: name,
    //                 },
    //                 callback: function (data) { // specified callback function
    //                     console.log(data);
    //                     // return;
    //                     if (data.status == "successful" || data.status == "completed") {
    //                         $("#flutterwave_transaction_id").val(data.transaction_id);
    //                         $("#flutterwave_transaction_ref").val(data.tx_ref);
    //                         place_order().done(function (result) {
    //                             if (result.error == false) {
    //                                 setTimeout(function () {
    //                                     location.href = base_url + 'payment/success';
    //                                 }, 3000);
    //                             }
    //                         });
    //                     } else {
    //                         location.href = base_url + 'payment/cancel';
    //                     }
    //                 },
    //                 customizations: {
    //                     title: title,
    //                     description: "Payment for product purchase",
    //                     logo: logo,
    //                 },
    //             });
    //         } else {
    //             Toast.fire({
    //                 icon: 'error',
    //                 title: 'Something went wrong!'
    //             });
    //         }
    //     }, "json");
    // }

    function flutterwave_payment() {
        var address_id = $("#address_id").val();
        var documents = $('#documents').val();
        var email_id = $('#digital_product_email').val();
        var product_type = $('#product_type').val();
        var download_allowed = $('#download_allowed').val();
        var wallet_used = $('#wallet_balance').is(":checked") ? 1 : 0;
        var promo_code = $('#promo_set').val() == 1 ? $('#promocode_input').val() : '';
        var phone_number = $('#user_contact').val();
        var email = $('#user_email').val();
        var name = $('#username').val();
        var title = $('#app_name').val();
        var logo = $('#logo').val();
        var public_key = $('#flutterwave_public_key').val();
        var currency_code = $('#flutterwave_currency').val();

        var country = 'NG'; // default
        switch (currency_code) {
            case 'KES': country = 'KE'; break;
            case 'GHS': country = 'GH'; break;
            case 'ZAR': country = 'ZA'; break;
            case 'TZS': country = 'TZ'; break;
        }

        // Step 1: Place the order first
        place_order().done(function (orderResult) {
            if (orderResult.error === false) {

                console.log(orderResult);
                console.log(orderResult.data);
                console.log(orderResult.data.final_total);
                // return;

                // Get order ID or some identifier to pass with Flutterwave
                var order_id = orderResult.data.order_id || 'ORDER_' + new Date().getTime();

                var d = new Date();
                var ms = d.getMilliseconds();
                var number = Math.floor(1000 + Math.random() * 9000);
                var tx_ref = 'order' + '-' + order_id + '-' + ms + '-' + number;

                var amount = orderResult.data.final_total;

                FlutterwaveCheckout({
                    public_key: public_key,
                    tx_ref: tx_ref,
                    amount: amount,
                    currency: currency_code,
                    country: country,
                    payment_options: "card,mobilemoney,ussd",
                    customer: {
                        email: email,
                        phone_number: phone_number,
                        name: name,
                    },
                    callback: function (data) {
                        console.log(data);
                        // return;
                        if (data.status == "successful" || data.status == "completed") {
                            //     $("#flutterwave_transaction_id").val(data.transaction_id);
                            //     $("#flutterwave_transaction_ref").val(data.tx_ref);
                            //     place_order().done(function (result) {
                            // if (result.error == false) {
                            setTimeout(function () {
                                location.href = base_url + 'payment/success';
                            }, 3000);
                            // }
                            // });
                        } else {
                            location.href = base_url + 'payment/cancel';
                        }
                        // if (data.status == "successful" || data.status == "completed") {
                        //     // Confirm payment to backend
                        //     $.post(base_url + 'payment/flutterwave/confirm', {
                        //         [csrfName]: csrfHash,
                        //         transaction_id: data.transaction_id,
                        //         tx_ref: data.tx_ref,
                        //         order_id: order_id
                        //     }, function (response) {

                        //         if (!response.error) {
                        //             location.href = base_url + 'payment/success';
                        //         } else {
                        //             location.href = base_url + 'payment/failed';
                        //         }
                        //     }, 'json');
                        // } else {
                        //     location.href = base_url + 'payment/cancel';
                        // }
                    },
                    customizations: {
                        title: title,
                        description: "Payment for product purchase",
                        logo: logo,
                    },
                });

            } else {
                iziToast.error({
                    message: orderResult.message || 'Order could not be placed',
                    position: 'topRight'
                });
            }
        });
    }

    $("#checkout_form").on('submit', function (event) {
        event.preventDefault();
        var fatoorah_order_id = "";

        var address_id = $("#address_id").val();
        var email_id = $('#digital_product_email').val();
        var product_type = $('#product_type').val();
        var download_allowed = $('#download_allowed').val(); // 0 : not downlodable | 1 : download allowed
        var documents = $('#documents').val();
        if ($('#wallet_balance').is(":checked")) {
            var wallet_used = 1;
        } else {
            var wallet_used = 0;
        }

        var promo_set = $('#promo_set').val();
        var promo_code = '';
        if (promo_set == 1) {
            promo_code = $('#promocode_input').val();
        }
        var final_total = $("#final_total").text();
        final_total = final_total.replace(',', '');
        var btn_html = $('#place_order_btn').html();
        $('#place_order_btn').attr('disabled', true).html('Please Wait...');
        if (($('#is_time_slots_enabled').val() == 1 && ($('input[name="delivery_time"]').is(':checked') == false || $('input[type=hidden][id="start_date"]').val() == "") && $('#product_type').val() != 'digital_product')) {
            Toast.fire({
                icon: 'error',
                title: "Please select Delivery Date & Time."
            });
            $('#place_order_btn').attr('disabled', false).html(btn_html);
            return false;
        }
        var address_id = $('#address_id').val();
        if (product_type != 'digital_product') {
            if (address_id == null || address_id == undefined || address_id == '') {
                Toast.fire({
                    icon: 'error',
                    title: 'Please add/choose address.'
                })
                $('#place_order_btn').attr('disabled', false).html(btn_html)
                return false
            }
        }

        if (documents === "") {
            return Toast.fire({
                icon: 'error',
                title: 'Please select an Document.'
            })
        }
        var payment_methods = $("input[name='payment_method']:checked").val();
        if (payment_methods == "Stripe") {
            $.post(base_url + "cart/pre-payment-setup", {
                [csrfName]: csrfHash,
                'payment_method': 'Stripe',
                'wallet_used': wallet_used,
                'address_id': address_id,
                'product_type': product_type,
                'download_allowed': download_allowed,
                'email_id': email_id,
                'promo_code': promo_code,
                'documents': documents
            }, function (data) {
                $('#stripe_client_secret').val(data.client_secret);
                $('#stripe_payment_id').val(data.id);
                var stripe_client_secret = data.client_secret;
                stripe_payment(stripe1.stripe, stripe1.card, stripe_client_secret);
                csrfName = data.csrfName;
                csrfHash = data.csrfHash;
            }, "json");

        } else if (payment_methods == "Paystack") {
            var key = $('#paystack_key_id').val();
            var user_email = $('#user_email').val();
            $.post(base_url + "cart/pre-payment-setup", {
                [csrfName]: csrfHash,
                'payment_method': 'Paystack',
                'wallet_used': wallet_used,
                'address_id': address_id,
                'product_type': product_type,
                'download_allowed': download_allowed,
                'email_id': email_id,
                'promo_code': promo_code,
                'documents': documents
            }, function (data) {
                csrfName = data.csrfName;
                csrfHash = data.csrfHash;
                if (data.error == false) {
                    var handler = paystack_setup(key, user_email, data.final_amount);
                    handler.openIframe();
                    $('#place_order_btn').attr('disabled', false).html('Place Order');
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: data.message
                    });
                    $('#place_order_btn').attr('disabled', false).html('Place Order');
                }

            }, "json");

        } else if (payment_methods == "Razorpay") {
            $.post(base_url + "cart/pre-payment-setup", {
                [csrfName]: csrfHash,
                'payment_method': 'Razorpay',
                'wallet_used': wallet_used,
                'address_id': address_id,
                'product_type': product_type,
                'download_allowed': download_allowed,
                'email_id': email_id,
                'promo_code': promo_code,
                'documents': documents
            }, function (data) {
                csrfName = data.csrfName;
                csrfHash = data.csrfHash;
                if (data.error == false) {
                    $('#razorpay_order_id').val(data.order_id);
                    var key = $('#razorpay_key_id').val();
                    var app_name = $('#app_name').val();
                    var logo = $('#logo').val();
                    var razorpay_order_id = $('#razorpay_order_id').val();
                    var username = $('#username').val();
                    var user_email = $('#user_email').val();
                    var user_contact = $('#user_contact').val();
                    var rzp1 = razorpay_setup(key, final_total, app_name, logo, razorpay_order_id, username, user_email, user_contact);
                    rzp1.open();
                    rzp1.on('payment.failed', function (response) {
                        location.href = base_url + 'payment/cancel';
                    });
                    $('#place_order_btn').attr('disabled', false).html('Place Order');
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: data.message
                    });
                    $('#place_order_btn').attr('disabled', false).html('Place Order');

                }
            }, "json");
        } else if (payment_methods == "instamojo") {
            $.post(base_url + "cart/pre-payment-setup", {
                [csrfName]: csrfHash,
                'payment_method': 'instamojo',
                'wallet_used': wallet_used,
                'address_id': address_id,
                'product_type': product_type,
                'download_allowed': download_allowed,
                'email_id': email_id,
                'promo_code': promo_code,
                'documents': documents
            }, function (data) {
                csrfName = data.csrfName;
                csrfHash = data.csrfHash;

                if (data.error == false) {
                    $('#instamojo_order_id').val(data.order_id);
                    var instamojo_payment = instamojo_setup(data.redirect_url);
                    $('#place_order_btn').attr('disabled', false).html('Place Order');

                } else {
                    Toast.fire({
                        icon: 'error',
                        title: data.message
                    });
                    $('#place_order_btn').attr('disabled', false).html('Place Order');

                }
            }, "json");
        } else if (payment_methods == "Midtrans") {
            $.post(base_url + "cart/pre-payment-setup", {
                [csrfName]: csrfHash,
                'payment_method': 'Midtrans',
                'wallet_used': wallet_used,
                'address_id': address_id,
                'product_type': product_type,
                'download_allowed': download_allowed,
                'email_id': email_id,
                'promo_code': promo_code,
                'documents': documents
            }, function (data) {
                csrfName = data.csrfName;
                csrfHash = data.csrfHash;
                if (data.error == false) {
                    $('#midtrans_transaction_token').val(data.token);
                    $('#midtrans_order_id').val(data.order_id);
                    var key = $('#razorpay_key_id').val();
                    var app_name = $('#app_name').val();
                    var logo = $('#logo').val();
                    var midtrans_transaction_token = data.token;
                    var username = $('#username').val();
                    var user_email = $('#user_email').val();
                    var user_contact = $('#user_contact').val();
                    var midtrans_payment = midtrans_setup(midtrans_transaction_token);
                    $('#place_order_btn').attr('disabled', false).html('Place Order');
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: data.message
                    });
                    $('#place_order_btn').attr('disabled', false).html('Place Order');

                }
            }, "json");
        } else if (payment_methods == "my_fatoorah") {
            place_order().done(function (result) {

                $('#my_fatoorah_order_id').val(result.data.order_id);
                fatoorah_order_id = $('#my_fatoorah_order_id').val();

                $.post(base_url + "cart/pre-payment-setup", {
                    [csrfName]: csrfHash,
                    'payment_method': 'my_fatoorah',
                    'wallet_used': wallet_used,
                    'address_id': address_id,
                    'product_type': product_type,
                    'download_allowed': download_allowed,
                    'email_id': email_id,
                    'my_fatoorah_order_id': fatoorah_order_id,
                    'promo_code': promo_code,
                    'documents': documents

                },

                    function (data) {

                        csrfName = data.csrfName;
                        csrfHash = data.csrfHash;
                        if (data.error == false) {
                            $('#my_fatoorah_order_id').val(data.order_id);
                            fatoorah_url = data.PaymentURL;
                            var my_fatoorah_payment = my_fatoorah_setup(fatoorah_url);
                            $('#place_order_btn').attr('disabled', false).html('Place Order');
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: data.message
                            });
                            $('#place_order_btn').attr('disabled', false).html('Place Order');

                        }
                    }, "json");
            });


        } else if (payment_methods == "Paypal") {
            place_order().done(function (result) {
                $('#paypal_order_id').val(result.data.order_id);
                $('#csrf_token').val(csrfHash);
                $('#paypal_form').submit();
            });
        } else if (payment_methods == "Paytm") {

            var amount = $("#amount").val();
            var user_id = $("#user_id").val();
            var address_id = $('#address_id').val();
            if ($('#wallet_balance').is(":checked")) {
                var wallet_used = 1;
            } else {
                var wallet_used = 0;
            }

            var promo_set = $('#promo_set').val();
            var promo_code = '';
            if (promo_set == 1) {
                promo_code = $('#promocode_input').val();
            }
            $.post(base_url + "payment/initiate-paytm-transaction", {
                [csrfName]: csrfHash,
                amount: amount,
                user_id: user_id,
                address_id: address_id,
                wallet_used: wallet_used,
                promo_code: promo_code
            }, function (data) {
                if (typeof (data.data.body.txnToken) != "undefined" && data.data.body.txnToken !== null) {
                    $('#paytm_transaction_token').val(data.data.body.txnToken)
                    $('#paytm_order_id').val(data.data.order_id)
                    var txn_token = $('#paytm_transaction_token').val();
                    var order_id = $('#paytm_order_id').val();
                    var app_name = $('#app_name').val();
                    var logo = $('#logo').val();
                    var username = $('#username').val();
                    var user_email = $('#user_email').val();
                    var user_contact = $('#user_contact').val();
                    paytm_setup(txn_token, order_id, data.final_amount, app_name, logo, username, user_email, user_contact);
                    $('#place_order_btn').attr('disabled', false).html('Place Order');
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: 'Something went wrong please try again later.'
                    });
                    $('#place_order_btn').attr('disabled', false).html('Place Order');

                }
            }, "json");
        } else if (payment_methods == "Flutterwave") {
            flutterwave_payment();
        } else if (payment_methods == 'phonepe') {
            var amount = $('#amount').val()
            var user_id = $('#user_id').val()
            var address_id = $('#address_id').val()
            if ($('#wallet_balance').is(':checked')) {
                var wallet_used = 1
            } else {
                var wallet_used = 0
            }

            var promo_set = $('#promo_set').val()
            var promo_code = ''
            if (promo_set == 1) {
                promo_code = $('#promocode_input').val()
            }
            $.post(
                base_url + 'payment/phonepe', {
                [csrfName]: csrfHash,
                amount: amount,
                user_id: user_id,
                address_id: address_id,
                wallet_used: wallet_used,
                promo_code: promo_code,
            },
                function (data) {
                    console.log(data);

                    var url = (data['url']) ? data['url'] : ""
                    var message = (data['data']['message']) ? data['data']['message'] : ""
                    $("#phonepe_transaction_id").val((data['transaction_id']) ? data['transaction_id'] : "");
                    if (url != "") {
                        place_order().done(function (result) {
                            if (result.error == false) {
                                window.location.replace(url);
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: message
                                })
                            }
                        })
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: message
                        })
                    }
                },
                'json'
            )
        } else if (payment_methods == "COD" || payment_methods == "Direct Bank Transfer") {
            place_order().done(function (result) {
                if (result.error == false) {
                    setTimeout(function () {
                        location.href = base_url + 'payment/success';
                    }, 3000);
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: result.message
                    });
                }
            });
        } else if (wallet_used == 1 && final_total == '0' || final_total == '0.00') {

            place_order().done(function (result) {

                if (result.error == false) {
                    setTimeout(function () {
                        location.href = base_url + 'payment/success';
                    }, 3000);
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: result.message
                    });
                }

            });

        } else {
            $('#place_order_btn').attr('disabled', false).html(btn_html);
            return Toast.fire({
                icon: 'error',
                title: 'Please select Payment method.'
            })
        }

    });

    function my_fatoorah_setup(fatoorah_url) {
        window.location.replace(fatoorah_url)

    }
    function instamojo_setup(instamojo_redirect_url) {

        console.log(instamojo_redirect_url);
        Instamojo.open(instamojo_redirect_url)
    }
    Instamojo.configure({
        handlers: {
            onSuccess: onPaymentSuccessHandler,
            onFailure: onPaymentFailureHandler
        }
    });
    function onPaymentSuccessHandler(response) {
        console.log('Payment Success Response', response);
        $('#instamojo_payment_id').val(response.paymentId);
        if (response.status == "success") {
            place_order().done(function (result) {
                if (result.error == false) {
                    setTimeout(function () {
                        location.href = base_url + 'payment/success';
                    }, 3000);
                }
            });
        } else {
            location.href = base_url + 'payment/cancel';
        }
    }
    function onPaymentFailureHandler(response) {
        alert('Payment Failure');
        if (response.status == "failure") {
            location.href = base_url + 'payment/cancel';
        }
    }

    function place_order() {
        let myForm = document.getElementById('checkout_form');
        var formdata = new FormData(myForm);
        formdata.append(csrfName, csrfHash);
        formdata.append('promo_code', $('#promocode_input').val());
        var latitude = sessionStorage.getItem("latitude") === null ? '' : sessionStorage.getItem("latitude");
        var longitude = sessionStorage.getItem("longitude") === null ? '' : sessionStorage.getItem("longitude");
        formdata.append('latitude', latitude);
        formdata.append('longitude', longitude);

        return $.ajax({
            type: 'POST',
            data: formdata,
            url: base_url + 'cart/place-order',
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $('#place_order_btn').attr('disabled', true).html('Please Wait...');
            },
            success: function (data) {

                csrfName = data.csrfName;
                csrfHash = data.csrfHash;
                $('#place_order_btn').attr('disabled', false).html('Place Order');
                if (data.error == false) {
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: data.message
                    });
                }
            }
        })
    }

    $("input[name='payment_method']").on('change', function (e) {
        e.preventDefault();
        var payment_method = $("input[name=payment_method]:checked").val();
        if (payment_method == "Stripe") {
            stripe1 = stripe_setup($('#stripe_key_id').val());
            $('#stripe_div').slideDown();
        } else {
            $('#stripe_div').slideUp();
        }
    });

    // redeem button
    $("#redeem_btn").on('click', function (event) {
        event.preventDefault();
        var formdata = new FormData();
        formdata.append(csrfName, csrfHash);
        formdata.append('promo_code', $('#promocode_input').val());
        var address_id = $("#address_id").val();
        formdata.append('address_id', address_id);
        var wallet_used_amount = $('.wallet_used').text();
        if ($('#wallet_balance').is(':checked')) {
            var wallet_used = 1
        } else {
            var wallet_used = 0
        }
        if (wallet_used == '') {
            wallet_used_amount = 0;
        } else {
            wallet_used_amount = wallet_used_amount.replace(',', '');
        }
        $.ajax({
            type: 'POST',
            data: formdata,
            url: base_url + 'cart/validate-promo-code',
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                csrfName = data.csrfName;
                csrfHash = data.csrfHash;
                if (data.error == false) {
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });

                    var delivery_charge = $(".delivery_charge_with_cod").val();
                    if (delivery_charge == '') {
                        delivery_charge = 0;
                    } else {
                        delivery_charge = delivery_charge.replace(',', '');
                    }

                    var is_cashback = data.data[0].is_cashback;
                    var final_total = data.data[0].final_total;
                    var final_discount = parseFloat(data.data[0].final_discount);

                    if (is_cashback == 1) {
                        final_total = parseFloat(final_total) - parseFloat(wallet_used_amount) + parseFloat(delivery_charge);
                    } else {
                        final_total = parseFloat(final_total) - parseFloat(wallet_used_amount) + parseFloat(delivery_charge);
                    }

                    if (wallet_used == 0) {

                        $('.wallet_used').text(wallet_used_amount.toLocaleString(undefined, { maximumFractionDigits: 2 }));
                        $('#final_total').text(final_total.toLocaleString(undefined, { maximumFractionDigits: 2 }));
                    } else {

                        $('.wallet_used').text(wallet_used_amount.toLocaleString(undefined, { maximumFractionDigits: 2 }));
                        $('#final_total').text(final_total.toLocaleString(undefined, { maximumFractionDigits: 2 }));
                    }
                    $('#promocode_div').removeClass('d-none');
                    $('#promocode').text('(' + data.data[0].promo_code + ')');
                    $('#promocode_amount').text(final_discount.toLocaleString(undefined, { maximumFractionDigits: 2 }));
                    $('#amount').val(final_total);
                    $('#promo_is_cashback').val(is_cashback);
                    $('#clear_promo_btn').removeClass('d-none');
                    $('#redeem_btn').hide();
                    $("#promo_set").val(1);

                } else {
                    Toast.fire({
                        icon: 'error',
                        title: data.message
                    });
                    $("#promo_set").val(0);
                    $('#promocode_input').val('');
                }
            }
        })
    });

    // clear promo code
    $('#clear_promo_btn').on('click', function (event) {
        event.preventDefault()
        $('#promocode_div').addClass('d-none')
        var wallet_used_amount = $('.wallet_used').text()
        if (wallet_used == '') {
            wallet_used_amount = 0
        } else {
            wallet_used_amount = wallet_used_amount.replace(',', '')
        }

        var promocode_amount = $('#promocode_amount').text()
        if (promocode_amount == '') {
            promocode_amount = 0
        } else {
            promocode_amount = promocode_amount.replace(',', '')
        }
        var sub_total = $('.sub_total').text()
        if (sub_total == '') {
            sub_total = 0
        } else {
            sub_total = sub_total.replace(',', '')
        }
        var delivery_charge = 0;
        $('input[name="payment_method"]').each(function () {
            if ($(this).is(':checked') && $(this).val() === 'COD') {
                delivery_charge = $('.delivery_charge_with_cod').val();
            } else {
                delivery_charge = $('.delivery_charge_without_cod').val();
            }
        });
        if (isNaN(delivery_charge)) {
            delivery_charge = 0;
        }
        if (delivery_charge == '') {
            delivery_charge = 0
        } else {
            delivery_charge = delivery_charge.replace(',', '')
        }

        var new_final_total = parseFloat(sub_total) + parseFloat(delivery_charge) - parseFloat(wallet_used_amount);
        if (wallet_used == 0) {

            $('.wallet_used').text(wallet_used_amount.toLocaleString(undefined, { maximumFractionDigits: 2 }))
            $('#final_total').text(new_final_total.toLocaleString(undefined, { maximumFractionDigits: 2 }))
        } else {
            $('#final_total').text(new_final_total.toLocaleString(undefined, { maximumFractionDigits: 2 }))
            $('.wallet_used').text(wallet_used_amount.toLocaleString(undefined, { maximumFractionDigits: 2 }))

        }
        if (delivery_charge == '') {
            delivery_charge = 0
        } else {
            delivery_charge = delivery_charge.replace(',', '')
        }

        $('#amount').val(new_final_total)
        $('#clear_promo_btn').addClass('d-none')
        $('#redeem_btn').show()
        $('#promocode_input').val('')
        $('#promocode_amount').val(0)
        $('#promo_set').val(0)
    })
    /* Instantiating iziModal */
    document.getElementById("address-modal").addEventListener("show.bs.modal", () => {
        $.ajax({
            type: 'POST',
            data: {
                [csrfName]: csrfHash,
            },
            url: base_url + 'my-account/get-address/',
            dataType: 'json',
            success: function (data) {
                csrfName = data.csrfName;
                csrfHash = data.csrfHash;
                var html = '';
                if (data.error == false) {
                    var address_id = $('#address_id').val();
                    var found = 0;
                    $.each(data.data, function (i, e) {
                        var checked = '';
                        if (e.id == address_id) {
                            found = 1;
                            checked = 'checked';
                        } else if (e.is_default == 1 && found == 0) {
                            checked = 'checked';
                        }
                        addresses.push(e);

                        html += '<label for="select-address-' + e.id + '" class="form-check-label"><li class="list-group-item d-flex justify-content-between lh-condensed mt-3">' +
                            '<div class="col-md-1 h-100 my-auto">' +
                            '<input type="radio" class="select-address form-check-input m-0" ' + checked + ' name="select-address" data-index=' + i + ' id="select-address-' + e.id + '" />' +
                            '</div>' +
                            '<div class="row text-start col-11">' +
                            '<div class="text-dark"><i class="fa fa-map-marker"></i> ' + e.name + ' - ' + e.type + '</div>' +
                            '<small class="col-12 text-muted">' + e.address + ' ,' + e.area + ' , ' + e.city + ' , ' + e.state + ' , ' + e.country + ' - ' + e.pincode + '</small>' +
                            '<small class="col-12 text-muted">' + e.mobile + '</small>' +
                            '</div>' +
                            '</li></label>';
                    });

                    $('#address-list').html(html);
                }
            }
        })
    })

    $(document).on('click', '#redeem_promocode', function () {
        event.preventDefault();
        var promo_code = $(this).data('value')
        $('#promocode_input').val(promo_code);
    })

    document.getElementById("promo-code-modal").addEventListener("show.bs.modal", () => {
        var data = { [csrfName]: csrfHash };

        $.ajax({
            type: 'GET',
            url: base_url + 'my-account/get_promo_codes',
            dataType: 'json',
            success: function (data) {
                csrfName = data.csrfName;
                csrfHash = data.csrfHash;
                var html = '';
                if ((data.promo_codes).length != 0) {
                    $.each(data.promo_codes, function (i, e) {
                        var promocode_name = e.promo_code;
                        html += '<div class="card mb-2">' +
                            '<label for="promo-code-' + e.id + '">' +
                            '<li class="list-group-item d-flex align-items-center mt-3">' +
                            '<div class="promo-code-img">' +
                            '<img src="' + e.image + '" style="max-width:80px;max-height:80px;"/>' +
                            '</div>' +
                            '<div class="text-start">' +
                            '<div class="copy-promo-code d-flex gap-2 p-2 text-dark" title="Copy promocode" id="redeem_promocode" data-value="' + e.promo_code + '">' +
                            e.promo_code +
                            '<i class="fa fa-copy text-blue"></i>' +
                            '</div>' +
                            '<small class="text-muted">' + e.message + '</small>' +
                            '</div>' +
                            '</li>' +
                            '</label>' +
                            '</div>';

                    });
                } else {
                    html += '<div class="align-items-center d-flex flex-column">' +
                        '<div class="empty-compare">' +
                        '<img src="' + base_url + 'assets/front_end/modern/img/no-offer-2.jpeg" alt="Opps...No Offers Avilable">' +
                        '</div>' +
                        '<div class="h5">Opps...No Offers Avilable</div>' +
                        '</div>';
                }
                $('#promocode-list').html(html);
            }
        })
    });

    $(".address_modal").on('click', '.submit', function (event) {
        event.preventDefault();
        var index = $('input[class="select-address form-check-input m-0"][type="radio"]:checked').data('index');
        var address = addresses[index];
        var sub_total = $('#sub_total').val();
        sub_total = sub_total.replace(',', '');
        var total = $('#temp_total').val();
        var promocode_amount = $('#promocode_amount').text();
        if (promocode_amount == '') {
            promocode_amount = 0;
        } else {
            promocode_amount = promocode_amount.replace(',', '');
        }

        $('#address-name-type').html(address.name + ' - ' + address.type);
        $('#address-full').html(address.address + ' , ' + address.area + ' , ' + address.city);
        $('#address-country').html(address.state + ' , ' + address.country + ' - ' + address.pincode);
        $('#address-mobile').html(address.mobile);
        $('#address_id').val(address.id);
        $('#mobile').val(address.mobile);
        $('.address_modal').modal('hide');

        // **IMPORTANT**: Fetch shipping options (quotes or standard)
        fetchShippingQuotes(address.id);

        var address_id = $('#address_id').val();
        $.ajax({
            type: 'POST',
            data: {
                [csrfName]: csrfHash,
                'address_id': address_id,
                'total': total,
            },
            url: base_url + 'cart/get-delivery-charge',
            dataType: 'json',
            success: function (result) {

                csrfName = result.csrfName;
                csrfHash = result.csrfHash;
                var is_time_slots_enabled = 0
                var className = result.error == true ? 'danger' : 'success'
                $('#checkout_form > .row').unblock()
                $('#deliverable_status').html(
                    "<b class='text-" + className + "'>" + result.message + '</b>'
                )
                if (result.availability_data != null) {
                    result.availability_data.forEach(product => {
                        if (product.is_deliverable == false) {
                            $('#p_' + product.product_id).html(
                                "<b class='text-danger'> " +
                                (product.message ?? 'Not deliverable') +
                                '</b>'
                            )
                        } else {
                            $('#p_' + product.product_id).html('')
                        }
                        if (product.delivery_by == 'standard_shipping') {
                            is_time_slots_enabled = 0
                            $('#is_time_slots_enabled').val(is_time_slots_enabled)
                        }
                    })
                }

                $('.shipping_method').html(result.shipping_method)
                $('.delivery-charge').html(result.delivery_charge_with_cod)
                $('.delivery-charge').val(result.delivery_charge_with_cod)
                $('.delivery_charge_with_cod').html(result.delivery_charge_with_cod)
                $('.delivery_charge_with_cod').val(result.delivery_charge_with_cod)
                $('.delivery_charge_without_cod').html(result.delivery_charge_without_cod)
                $('.delivery_charge_without_cod').val(result.delivery_charge_without_cod)
                $('.estimate_date').html(result.estimate_date)
                var shipping_method = result.shipping_method
                var delivery_charge = result.delivery_charge_with_cod
                var delivery_charge_with_cod = result.delivery_charge_with_cod
                var delivery_charge_without_cod = result.delivery_charge_without_cod
                if (result.availability_data != null) {
                    result.availability_data.forEach(product => {
                        if (product.delivery_by == 'standard_shipping') {
                            $('.date-time-label').addClass('d-none')
                            $('.date-time-picker').addClass('d-none')
                            $('.time-slot').addClass('d-none')
                        } else {
                            $('.date-time-label').removeClass('d-none')
                            $('.date-time-picker').removeClass('d-none')
                            $('.time-slot').removeClass('d-none')
                        }
                    })
                }
                var final_total = parseFloat(sub_total) + parseFloat(delivery_charge);

                $('input[type=radio][name=payment_method]').change(function () {
                    var selectedPaymentMethod = $('input[type=radio][name=payment_method]:checked').val();
                    var wallet_used = $('.wallet_used').text();
                    if (wallet_used == '') {
                        wallet_used = 0;
                    } else {
                        wallet_used = wallet_used.replace(',', '');
                    }

                    var promocode_amount_in = $('#promocode_amount').text();
                    if (promocode_amount_in == '') {
                        promocode_amount_in = 0;
                    } else {
                        promocode_amount_in = promocode_amount_in.replace(',', '');
                    }
                    var delivery_charge = 0;
                    if (selectedPaymentMethod === 'COD') {
                        delivery_charge = delivery_charge_with_cod;
                    } else {
                        delivery_charge = delivery_charge_without_cod;

                    }

                    var delivery_charge = delivery_charge.toLocaleString(undefined, { maximumFractionDigits: 5 });

                    var final_total = parseFloat(sub_total) + parseFloat(delivery_charge) - parseFloat(wallet_used) - parseFloat(promocode_amount_in);
                    final_total = final_total.toLocaleString(undefined, { maximumFractionDigits: 2 });

                    $('#final_total').html(final_total);
                    var final_total = final_total.replace(',', '');

                    $('#amount').val(final_total);
                    if (final_total != 0) {
                        $('#cod').prop('required', true);
                        $('#paypal').prop('required', true);
                        $('#razorpay').prop('required', true);
                        $('#paystack').prop('required', true);
                        $('#payumoney').prop('required', true);
                        $('#flutterwave').prop('required', true);
                        $('#stripe').prop('required', true);
                        $('#paytm').prop('required', true);
                        $('#bank_transfer').prop('required', true);
                        $('.payment-methods').show();
                    }
                })
            }
        });

    });
});

$('#datepicker').attr({
    'placeholder': 'Preferred Delivery Date',
    'autocomplete': 'off'
});
$('#datepicker').on('cancel.daterangepicker', function (ev, picker) {
    $(this).val('');
    $('#start_date').val('');
});
$('#datepicker').on('apply.daterangepicker', function (ev, picker) {
    var drp = picker;
    var current_time = moment().format("HH:mm");
    if (moment(drp.startDate).isSame(moment(), 'd')) {
        $('.time-slot-inputs').each(function (i, e) {
            if ($(this).data('last_order_time') < current_time) {
                $(this).prop('checked', false).attr('required', false);
                $(this).parent().hide();
            } else {
                $(this).attr('required', true);
                $(this).parent().show();
            }
        });
    } else {
        $('.time-slot-inputs').each(function (i, e) {
            $(this).attr('required', true);
            $(this).parent().show();
        });
    }
    $('#start_date').val(drp.startDate.format('YYYY-MM-DD'));
    $('#delivery_date').val(drp.startDate.format('YYYY-MM-DD'));
    $(this).val(picker.startDate.format('MM/DD/YYYY'));
});
var mindate = '',
    maxdate = '';
if ($('#delivery_starts_from').val() != "") {
    mindate = moment().add(($('#delivery_starts_from').val() - 1), 'days');
} else {
    mindate = null;
}

if ($('#delivery_ends_in').val() != "") {
    maxdate = moment(mindate).add(($('#delivery_ends_in').val() - 1), 'days');
} else {
    maxdate = null;
}
$('#datepicker').daterangepicker({
    showDropdowns: false,
    alwaysShowCalendars: true,
    autoUpdateInput: false,
    singleDatePicker: true,
    minDate: mindate,
    maxDate: maxdate,
    locale: {
        "format": "DD/MM/YYYY",
        "separator": " - ",
        "cancelLabel": 'Clear',
        'label': 'Preferred Delivery Date'
    }
});
$(document).ready(function () {
    var address_id = $('#address_id').val();
    var sub_total = $('#sub_total').val();
    var total = $('#temp_total').val();
    $.ajax({
        type: 'POST',
        data: {
            [csrfName]: csrfHash,
            'address_id': address_id,
            'total': total,
        },
        url: base_url + 'cart/get-delivery-charge',
        dataType: 'json',
        success: function (result) {

            csrfName = result.csrfName;
            csrfHash = result.csrfHash;
            var className = result.error == true ? 'danger' : 'success'
            var is_time_slots_enabled = 0
            $('#deliverable_status').html(
                "<b class='text-" + className + "'>" + result.message + '</b>'
            )
            if (result.availability_data != null) {
                result.availability_data.forEach(product => {

                    if (product.is_deliverable == false) {
                        $('#p_' + product.product_id).html(
                            "<b class='text-danger'> " +
                            (product.message ?? 'Not deliverable') +
                            '</b>'
                        )
                    } else {
                        $('#p_' + product.product_id).html('')
                    }
                    if (product.delivery_by == 'standard_shipping') {
                        is_time_slots_enabled = 0
                        $('#is_time_slots_enabled').val(is_time_slots_enabled)
                    }
                })
            }
            $('.shipping_method').html(result.shipping_method)
            $('.delivery-charge').html(result.delivery_charge)
            $('.delivery_charge_with_cod').html(result.delivery_charge_with_cod)
            $('.delivery_charge_with_cod').val(result.delivery_charge_with_cod)
            $('.delivery_charge_without_cod').html(result.delivery_charge_without_cod)
            $('.delivery_charge_without_cod').val(result.delivery_charge_without_cod)
            $('.estimate_date').html(result.estimate_date)
            $('input[type=radio][name=payment_method]').change(function () {
                var selectedPaymentMethod = $('input[type=radio][name=payment_method]:checked').val();
                var promocode_amount_in2 = $('#promocode_amount').text();
                if (promocode_amount_in2 == '') {
                    promocode_amount_in2 = 0;
                } else {
                    promocode_amount_in2 = promocode_amount_in2.replace(',', '');
                }
                var delivery_charge = 0;
                var shipping_method = result.shipping_method
                var delivery_charge = result.delivery_charge_with_cod
                var delivery_charge_with_cod = result.delivery_charge_with_cod
                var delivery_charge_without_cod = result.delivery_charge_without_cod
                if (result.availability_data != null) {
                    result.availability_data.forEach(product => {
                        if (product.delivery_by == 'standard_shipping') {
                            $('.date-time-label').addClass('d-none')
                            $('.date-time-picker').addClass('d-none')
                            $('.time-slot').addClass('d-none')
                        } else {
                            $('.date-time-label').removeClass('d-none')
                            $('.date-time-picker').removeClass('d-none')
                            $('.time-slot').removeClass('d-none')
                        }
                    })
                }
                if (selectedPaymentMethod === 'COD') {
                    delivery_charge = parseFloat(result.delivery_charge_with_cod);
                } else {
                    delivery_charge = parseFloat(result.delivery_charge_without_cod);
                }
                if (shipping_method == 1) {
                    var final_total =
                        parseFloat(sub_total) + parseFloat(delivery_charge) - parseFloat(promocode_amount_in2);
                } else {
                    var final_total = parseFloat(sub_total) + parseFloat(delivery_charge) - parseFloat(promocode_amount_in2);
                }

                $("#amount").val(final_total);
                final_total = final_total.toLocaleString(undefined, { maximumFractionDigits: 2 });
                $('#final_total').html(final_total);

            })
        }

    })
});

//wallt balance
$(document).on('click', '#wallet_balance', function () {
    var current_wallet_balance = $('#current_wallet_balance').val();
    var wallet_balance = current_wallet_balance.replace(",", "");
    var final_total = $('#final_total').text();
    var is_cashback = $('#promo_is_cashback').val();
    final_total = final_total.replace(",", "");


    var sub_total = $("#sub_total").val();

    var delivery_charge_with_cod = $(".delivery_charge_with_cod").val();
    var delivery_charge_without_cod = $(".delivery_charge_without_cod").val();

    if (delivery_charge_without_cod != undefined) {

        if (delivery_charge_without_cod == '') {
            delivery_charge_without_cod = 0;
        } else {
            delivery_charge_without_cod = delivery_charge_without_cod.replace(',', '');
        }
    } else {
        delivery_charge_without_cod = 0;
    }
    var promo_set = $('#promo_set').val()
    var promocode_amount = ''
    if (promo_set == 1) {
        promocode_amount = $('#promocode_amount').text();
        promocode_amount = promocode_amount.replace(',', '');
    } else {
        promocode_amount = 0;
    }
    var wallet_used = $('.wallet_used').text();
    if (wallet_used == '') {
        wallet_used = 0;
    } else {
        wallet_used = wallet_used.replace(',', '');
    }
    if ($(this).is(':checked')) {
        $("#wallet_used").val(1);
        wallet_balance = parseFloat(wallet_balance.replace(',', ''));

        if (is_cashback == 1) {
            final_total = parseFloat(sub_total) + parseFloat(delivery_charge_without_cod)
        } else {
            final_total = parseFloat(sub_total) + parseFloat(delivery_charge_without_cod) - parseFloat(promocode_amount);
        }

        if (final_total - wallet_balance <= 0) {

            var available_balance = wallet_balance - final_total;
            available_balance = parseFloat(available_balance).toFixed(2);
            $(".wallet_used").html(final_total.toLocaleString(undefined, {
                maximumFractionDigits: 2
            }));
            $('#available_balance').html(available_balance.toLocaleString(undefined, {
                maximumFractionDigits: 2
            }));
            $('#final_total').html('0.00');
            $('#cod').prop('required', false);
            $('#paypal').prop('required', false);
            $('#razorpay').prop('required', false);
            $('#midtrans').prop('required', false);
            $('#my_fatoorah').prop('required', false);
            $('#paystack').prop('required', false);
            $('#payumoney').prop('required', false);
            $('#flutterwave').prop('required', false);
            $('#paytm').prop('required', false);
            $('#bank_transfer').prop('required', false);
            $('#stripe').prop('required', false);
            $('#paytm').prop('required', false);
            $('#bank_transfer').prop('required', false);
            $('.wallet-section').removeClass('d-none')
            $('.payment-methods').hide();
        } else {

            $(".wallet_used").html(current_wallet_balance);
            $('#available_balance').html('0.00');
            if (is_cashback == 1) {

                final_total = parseFloat(final_total) - parseFloat(wallet_used) + parseFloat(delivery_charge_without_cod)
            } else {
                final_total = parseFloat(final_total) - parseFloat(wallet_used) + parseFloat(delivery_charge_without_cod)
            }
            final_total = parseFloat(sub_total) - parseFloat(wallet_balance) - parseFloat(promocode_amount) + parseFloat(delivery_charge_without_cod);

            $('#final_total').html(final_total.toLocaleString(undefined, {
                maximumFractionDigits: 2
            }));
            $('#amount').val(final_total);
            $('#cod').prop('required', true);
            $('#paypal').prop('required', true);
            $('#razorpay').prop('required', true);
            $('#paystack').prop('required', true);
            $('#payumoney').prop('required', true);
            $('#flutterwave').prop('required', true);
            $('#paytm').prop('required', true);
            $('#bank_transfer').prop('required', true);
            $('#stripe').prop('required', true);
            $('#paytm').prop('required', true);
            $('#bank_transfer').prop('required', true);
            $('.payment-methods').show();
        }

    } else {
        $("#wallet_used").val(0);

        if (is_cashback == 1) {
            final_total = parseFloat(sub_total) + parseFloat(delivery_charge_with_cod)
        } else {
            final_total = parseFloat(sub_total) + parseFloat(delivery_charge_with_cod) - parseFloat(promocode_amount);
        }


        $(".wallet_used").html('0.00');
        $('#final_total').html(final_total.toLocaleString(undefined, {
            maximumFractionDigits: 2
        }));
        $('#amount').val(final_total);
        $('#available_balance').html(current_wallet_balance);
        $('.wallet-section').addClass('d-none')
        $('.payment-methods').show();
        $('#cod').prop('required', true);
        $('#paypal').prop('required', true);
        $('#razorpay').prop('required', true);
        $('#paystack').prop('required', true);
        $('#payumoney').prop('required', true);
        $('#flutterwave').prop('required', true);
        $('#paytm').prop('required', true);
        $('#bank_transfer').prop('required', true);
        $('#stripe').prop('required', true);
        $('#paytm').prop('required', true);
        $('#bank_transfer').prop('required', true);

    }
});

function paytm_setup(txnToken, orderId, amount, app_name, logo, username, user_email, user_contact) {
    var config = {
        "root": "",
        "flow": "DEFAULT",
        "merchant": {
            "name": app_name,
            "logo": logo,
            redirect: false
        },
        "style": {
            "headerBackgroundColor": "#8dd8ff",
            "headerColor": "#3f3f40"
        },
        "data": {
            "orderId": orderId,
            "token": txnToken,
            "tokenType": "TXN_TOKEN",
            "amount": amount,
            "userDetail": {
                "mobileNumber": user_contact,
                "name": username
            }
        },
        "handler": {
            "notifyMerchant": function (eventName, data) {
                if (eventName == 'SESSION_EXPIRED') {
                    alert("Your session has expired!!");
                    location.reload();
                }
                if (eventName == 'APP_CLOSED') {
                    $('#place_order_btn').attr('disabled', false).html('Place Order');
                }

            },
            transactionStatus: function (data) {
                window.Paytm.CheckoutJS.close();
                if (data.STATUS == 'TXN_SUCCESS' || data.STATUS == 'PENDING') {
                    let myForm = document.getElementById('checkout_form');
                    var formdata = new FormData(myForm);
                    formdata.append(csrfName, csrfHash);
                    formdata.append('promo_code', $('#promocode_input').val());
                    var latitude = sessionStorage.getItem("latitude") === null ? '' : sessionStorage.getItem("latitude");
                    var longitude = sessionStorage.getItem("longitude") === null ? '' : sessionStorage.getItem("longitude");
                    formdata.append('latitude', latitude);
                    formdata.append('longitude', longitude);


                    formdata.append('provider_type', $('#provider_type').val());
                    formdata.append('selected_quote_id', $('#selected_quote_id').val());
                    formdata.append('shipping_company_id', $('#shipping_company_id').val());
                    $.ajax({
                        type: 'POST',
                        data: formdata,
                        url: base_url + 'cart/place-order',
                        dataType: 'json',
                        cache: false,
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            $('#place_order_btn').attr('disabled', true).html('Please Wait...');
                        },
                        success: function (data) {
                            csrfName = data.csrfName;
                            csrfHash = data.csrfHash;
                            $('#place_order_btn').attr('disabled', false).html('Place Order');
                            if (data.error == false) {
                                Toast.fire({
                                    icon: 'success',
                                    title: data.message
                                });
                                setTimeout(function () {
                                    location.href = base_url + 'payment/success';
                                }, 3000);
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: data.message
                                });
                            }
                        }
                    })
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: 'Something went wrong please try again!'
                    });
                }

            }


        }
    };

    if (window.Paytm && window.Paytm.CheckoutJS) {
        // initialze configuration using init method
        window.Paytm.CheckoutJS.init(config).then(function onSuccess() {

            // after successfully update configuration invoke checkoutjs
            window.Paytm.CheckoutJS.invoke();
        }).catch(function onError(error) {
            console.log("Error => ", error);
        });
    }
}



$(document).ready(function () {
    // Hide bank transfer slide by default
    $('#bank_transfer_slide').hide();

    // Listen for changes on payment method radio buttons
    $('input[name="payment_method"]').on('change', function (e) {
        e.preventDefault();
        var payment_method = $(this).val();

        if (payment_method == "Direct Bank Transfer") {
            $('#account_data').show();
            $('#bank_transfer_slide').slideDown();
        } else {
            $('#account_data').hide();
            $('#bank_transfer_slide').slideUp();
        }
    });

    // Optionally, trigger change on page load to handle pre-selected value
    $('input[name="payment_method"]:checked').trigger('change');
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

var global_final_total = 0
var global_delivery_charge = 0
$('#pickup_from_store').on('change', function (e) {
    e.preventDefault()
    var final_time = $('#final_total').text()
    var delivery_charge = parseInt($('.delivery-charge').text())
    var sub_total = $('.sub_total').text()
    global_final_total = final_time
    global_delivery_charge = delivery_charge

    $('.address').hide()
    $('.address-details').hide();
    $('.all-delivery-charges').hide();
    $('.date-time-section').hide();
    $('.delivery_charge').hide()
    $('.delivery-charge').text('0.00')
    $('#final_total').text(sub_total)
})
$('#door_step').on('change', function (e) {
    e.preventDefault()
    $('.address').show()
    $('.delivery_charge').show()
    $('.address-details').show()
    $('.all-delivery-charges').show()
    $('.date-time-section').show()
    $('#time_slots').show()
    $('.delivery-charge').text(global_delivery_charge)
    $('#final_total').text(global_final_total)
})


// shipping company


// ============================================
// SHIPPING COMPANY QUOTES FUNCTIONALITY
// ============================================

/**
 * Fetch and display shipping quotes when address changes
 */
function fetchShippingQuotes(address_id) {
    if (!address_id) return;

    $.ajax({
        type: 'POST',
        url: base_url + 'cart/get_shipping_company_quotes',
        data: {
            [csrfName]: csrfHash,
            'address_id': address_id
        },
        dataType: 'json',
        beforeSend: function () {
            $('#quotes_container').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p>Loading shipping options...</p></div>');
        },
        success: function (response) {
            csrfName = response.csrfName;
            csrfHash = response.csrfHash;

            // Hide all sections first
            $('#standard_delivery_section').hide();
            $('#shipping_quotes_section').hide();
            $('#delivery_unavailable_section').hide();

            if (response.error === false && response.delivery_available) {

                $('#provider_type').val(response.provider_type);

                if (response.provider_type === 'company') {
                    // Show shipping company quotes
                    displayShippingQuotes(response.quotes);
                    $('#shipping_quotes_section').show();

                } else if (response.provider_type === 'delivery_boy') {
                    // Show standard delivery charges (existing flow)
                    $('#standard_delivery_section').show();

                    // Trigger existing delivery charge calculation
                    getStandardDeliveryCharges(address_id);
                }

            } else {
                // Delivery not available
                $('#delivery_unavailable_section').show();
                $('#place_order_btn').attr('disabled', true);
            }
        },
        error: function () {
            Toast.fire({
                icon: 'error',
                title: 'Failed to load shipping options'
            });
        }
    });
}

function displayShippingQuotes(quotes) {
    console.log(quotes);
    if (!quotes || quotes.length === 0) {
        $('#quotes_container').html('<div class="text-center py-4">No shipping quotes available</div>');
        return;
    }

    var html = '';
    quotes.forEach(function (quote, index) {
        var price = parseFloat(quote.price);
        var additional_charges = [];
        var total_additional = 0;

        // Parse additional_charges safely
        if (quote.additional_charges) {
            try {
                var parsed;
                if (Array.isArray(quote.additional_charges)) {
                    parsed = quote.additional_charges;
                } else if (typeof quote.additional_charges === 'string') {
                    parsed = JSON.parse(quote.additional_charges);
                } else {
                    parsed = quote.additional_charges;
                }

                // Handle both array and object formats
                if (Array.isArray(parsed)) {
                    additional_charges = parsed;
                } else if (typeof parsed === 'object' && parsed !== null) {
                    // Convert object to array format: {key: value} -> [{name: key, amount: value}]
                    additional_charges = Object.keys(parsed).map(function (key) {
                        return {
                            name: key,
                            amount: parsed[key]
                        };
                    });
                }
            } catch (e) {
                console.error('Error parsing additional_charges:', e);
                additional_charges = [];
            }
        }

        // Calculate total additional charges
        if (Array.isArray(additional_charges) && additional_charges.length > 0) {
            additional_charges.forEach(function (charge) {
                total_additional += parseFloat(charge.amount || 0);
            });
        }

        var total_price = price + total_additional;
        var isChecked = index === 0;
        var cardActiveClass = isChecked ? ' active' : '';

        html += '<div class="shipping-option-card' + cardActiveClass + '">';

        // Radio input (hidden)
        html += '<input type="radio" class="shipping-option-radio" name="shipping_quote" id="quote_' + quote.id + '" value="' + quote.id + '" ';
        html += 'data-company-id="' + quote.shipping_company_id + '" ';
        html += 'data-price="' + total_price.toFixed(2) + '" ';
        html += 'data-cod="' + quote.cod_available + '" ';
        html += (isChecked ? 'checked' : '') + '>';

        // Label (entire card is clickable)
        html += '<label for="quote_' + quote.id + '" class="shipping-option-label">';

        // Header: Company name and total price
        html += '<div class="shipping-option-header">';
        html += '<div class="company-name">' + quote.company_name + '</div>';
        html += '<div class="total-price">' + currency + total_price.toFixed(2) + '</div>';
        html += '</div>';

        // Details section
        html += '<div class="shipping-option-details">';

        // Left side: pricing breakdown
        html += '<div class="details-left">';

        // Delivery time
        html += '<div class="detail-row">';
        html += '<span class="detail-label">Delivery:</span>';
        html += '<span class="detail-value">' + quote.eta_text + ' days</span>';
        html += '</div>';

        // Base price
        html += '<div class="detail-row">';
        html += '<span class="detail-label">Base Price:</span>';
        html += '<span class="detail-value">' + currency + price.toFixed(2) + '</span>';
        html += '</div>';

        // Additional charges (with special styling)
        if (Array.isArray(additional_charges) && additional_charges.length > 0) {
            additional_charges.forEach(function (charge) {
                html += '<div class="detail-row additional-charge">';
                html += '<span class="detail-label">+ ' + charge.name + ':</span>';
                html += '<span class="detail-value">' + currency + parseFloat(charge.amount).toFixed(2) + '</span>';
                html += '</div>';
            });
        }

        html += '</div>';

        // Right side: COD badge
        html += '<div class="details-right">';
        if (quote.cod_available == 1) {
            html += '<span class="cod-badge available">COD Available</span>';
        } else {
            html += '<span class="cod-badge unavailable">COD Not Available</span>';
        }
        html += '</div>';

        html += '</div>'; // Close shipping-option-details
        html += '</label>';
        html += '</div>'; // Close shipping-option-card
    });

    $('#quotes_container').html(html);

    // Add click handler to update active state
    $('input[name="shipping_quote"]').on('change', function () {
        $('.shipping-option-card').removeClass('active');
        $(this).closest('.shipping-option-card').addClass('active');
    });

    // Select first quote by default
    if (quotes.length > 0) {
        var firstQuote = quotes[0];
        var firstPrice = parseFloat(firstQuote.price);
        var firstAdditional = 0;

        if (firstQuote.additional_charges) {
            try {
                var parsed;
                if (Array.isArray(firstQuote.additional_charges)) {
                    parsed = firstQuote.additional_charges;
                } else if (typeof firstQuote.additional_charges === 'string') {
                    parsed = JSON.parse(firstQuote.additional_charges);
                } else {
                    parsed = firstQuote.additional_charges;
                }

                // Handle both array and object formats
                var charges = [];
                if (Array.isArray(parsed)) {
                    charges = parsed;
                } else if (typeof parsed === 'object' && parsed !== null) {
                    charges = Object.keys(parsed).map(function (key) {
                        return {
                            name: key,
                            amount: parsed[key]
                        };
                    });
                }

                if (Array.isArray(charges)) {
                    charges.forEach(function (charge) {
                        firstAdditional += parseFloat(charge.amount || 0);
                    });
                }
            } catch (e) {
                console.error('Error parsing additional_charges for first quote:', e);
            }
        }

        selectShippingQuote(
            firstQuote.id,
            firstQuote.shipping_company_id,
            firstPrice + firstAdditional,
            firstQuote.cod_available
        );
    }
}

/**
 * Handle quote selection
 */
$(document).on('change', '.shipping-option-radio', function () {
    var $radio = $(this);
    var $card = $radio.closest('.shipping-option-card');
    var quote_id = $radio.val();
    var company_id = $radio.attr('data-company-id');      // Use attr() instead of data()
    var price = parseFloat($radio.attr('data-price'));    // Use attr() instead of data()
    var cod_available = $radio.attr('data-cod');          // Use attr() instead of data()

    console.log('Quote selected:', { quote_id, company_id, price, cod_available }); // Debug log

    // Visual feedback
    $('.shipping-option-card').removeClass('active');
    $card.addClass('active');

    selectShippingQuote(quote_id, company_id, price, cod_available);
});

// Also handle clicking anywhere on the label
$(document).on('click', '.shipping-option-label', function (e) {
    // The label click will automatically trigger the radio button
    // No additional code needed
});
// Also handle clicking anywhere on the label
$(document).on('click', '.shipping-option-label', function (e) {
    // The label click will automatically trigger the radio button
    // No additional code needed
});

/**
 * Set selected quote and update totals
 */
function selectShippingQuote(quote_id, company_id, price, cod_available) {
    console.log(price);
    $('#selected_quote_id').val(quote_id);
    $('#shipping_company_id').val(company_id);

    // Update delivery charges
    $('.delivery_charge_with_cod').text(price.toFixed(2)).val(price);
    $('.delivery_charge_without_cod').text(price.toFixed(2)).val(price);

    // Update final total
    updateFinalTotal();

    // Handle COD availability
    if (cod_available == 0) {
        $('#cod').prop('disabled', true).prop('checked', false);
        $('#cod').attr('title', 'COD not available for selected shipping option');
    } else {
        $('#cod').prop('disabled', false);
        $('#cod').attr('title', '');
    }

    // Enable place order button
    $('#place_order_btn').attr('disabled', false);
}

/**
 * Update final total amount
 */
function updateFinalTotal() {
    var sub_total = parseFloat($('#sub_total').val().replace(',', '')) || 0;
    var delivery_charge = parseFloat($('.delivery_charge_without_cod').val().replace(',', '')) || 0;
    var wallet_used = parseFloat($('.wallet_used').text().replace(',', '')) || 0;
    var promocode_amount = parseFloat($('#promocode_amount').text().replace(',', '')) || 0;

    // Check payment method
    var selected_payment = $('input[name="payment_method"]:checked').val();
    if (selected_payment === 'COD') {
        delivery_charge = parseFloat($('.delivery_charge_with_cod').val().replace(',', '')) || 0;
    }

    var final_total = sub_total + delivery_charge - wallet_used - promocode_amount;

    $('#final_total').text(final_total.toFixed(2));
    $('#amount').val(final_total);
}

/**
 * Get standard delivery charges (existing flow for delivery_boy)
 */
function getStandardDeliveryCharges(address_id) {
    var sub_total = $('#sub_total').val();
    var total = $('#temp_total').val();

    $.ajax({
        type: 'POST',
        data: {
            [csrfName]: csrfHash,
            'address_id': address_id,
            'total': total,
        },
        url: base_url + 'cart/get-delivery-charge',
        dataType: 'json',
        success: function (result) {
            csrfName = result.csrfName;
            csrfHash = result.csrfHash;

            $('.delivery_charge_with_cod').html(result.delivery_charge_with_cod).val(result.delivery_charge_with_cod);
            $('.delivery_charge_without_cod').html(result.delivery_charge_without_cod).val(result.delivery_charge_without_cod);
            $('.estimate_date').html(result.estimate_date);

            updateFinalTotal();
        }
    });
}

// ============================================
// TRIGGER ON ADDRESS CHANGE
// ============================================

// Modify existing address selection handler
var originalAddressSubmit = $(".address_modal").find('.submit').clone(true);

$(".address_modal").off('click', '.submit');
$(".address_modal").on('click', '.submit', function (event) {
    event.preventDefault();

    var index = $('input[class="select-address form-check-input m-0"][type="radio"]:checked').data('index');
    var address = addresses[index];

    // Update address display
    $('#address-name-type').html(address.name + ' - ' + address.type);
    $('#address-full').html(address.address + ' , ' + address.area + ' , ' + address.city);
    $('#address-country').html(address.state + ' , ' + address.country + ' - ' + address.pincode);
    $('#address-mobile').html(address.mobile);
    $('#address_id').val(address.id);
    $('#mobile').val(address.mobile);

    $('.address_modal').modal('hide');

    // Fetch shipping options (quotes or standard)
    fetchShippingQuotes(address.id);
});

// ============================================
// INITIALIZE ON PAGE LOAD
// ============================================

$(document).ready(function () {
    var initial_address_id = $('#address_id').val();

    if (initial_address_id && $('#product_type').val() !== 'digital_product') {
        fetchShippingQuotes(initial_address_id);
    }
});

// Update final total when payment method changes
$(document).on('change', 'input[name="payment_method"]', function () {
    updateFinalTotal();
});

// Update final total when wallet checkbox changes
$(document).on('change', '#wallet_balance', function () {
    setTimeout(updateFinalTotal, 100);
});

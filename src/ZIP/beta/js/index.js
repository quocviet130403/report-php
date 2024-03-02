var site_url = '';

$(document).ready(function () {
    $('#login_to_account, #consultant_login_to_account, #company_login_to_account, #forgot_pass_login').click(function () {
        $('#signup_form, #register_company_form, #forgot_pass_form, #pin_code_form, #signup_consultant_form').fadeOut(1, function () {
            $('#login_form').fadeIn(1000);
        });
    });
    $('#signup_now, #company_signup_now').click(function () {
        $('#login_form, #register_company_form, #forgot_pass_form, #pin_code_form, #signup_consultant_form').fadeOut(1, function () {
            $('#signup_form').fadeIn(1000);
        });
    });
    $('#register_company, #consultant_register_company').click(function () {
        $('#login_form, #signup_form, #forgot_pass_form, #pin_code_form, #signup_consultant_form').fadeOut(1, function () {
            $('#register_company_form').fadeIn(1000);
        });
    });
    $('#signup_consultant_now').click(function () {
        $('#login_form, #signup_form, #forgot_pass_form, #pin_code_form, #register_company').fadeOut(1, function () {
            $('#signup_consultant_form').fadeIn(1000);
        });
    });
    $('#forgot_password').click(function () {
        $('#login_form, #signup_form, #register_company_form, #pin_code_form, #signup_consultant_form').fadeOut(1, function () {
            $('#forgot_pass_form').fadeIn(1000);
        });
    });

    if (getCookie('cookieNotice') === 'accepted') {
        $("#cookieConsent").hide();
    }
    
    $("#closeCookieConsent, .cookieConsentOK").click(function () {
        $("#cookieConsent").fadeOut(200);
    });
    
    $("#noThanks").click(function () {
        $("#cookieConsent").fadeOut(200);
    });
    
    $("#cookieAccept").click(function () {
        setCookie('cookieNotice', 'accepted', 30);
        $("#cookieConsent").fadeOut(200);
    });
    
    function setCookie(name, value, days) {
        var expirationDate = new Date();
        expirationDate.setDate(expirationDate.getDate() + days);
        var cookieValue = name + "=" + encodeURIComponent(value) + "; expires=" + expirationDate.toUTCString() + "; path=/";
        document.cookie = cookieValue;
    }
    
    function getCookie(name) {
        var allCookies = document.cookie;
        var cookieArray = allCookies.split("; ");
        for (var i = 0; i < cookieArray.length; i++) {
            var cookie = cookieArray[i];
            var equalsIndex = cookie.indexOf("=");
            var cookieName = equalsIndex > -1 ? cookie.substr(0, equalsIndex) : cookie;
            var cookieValue = equalsIndex > -1 ? cookie.substr(equalsIndex + 1) : "";
            if (cookieName === name) {
                return decodeURIComponent(cookieValue);
            }
        }
        return "";
    }    


    if ($('#divPricing').length) {
        $('#divPricing').hide();
    }



 

    //User sing-in and validation
    $('#login_button').click(function (event) {
        event.preventDefault();
        let email_input = $('#login_email');
        let password_input = $('#login_password');

        let email = email_input.val();
        let password = password_input.val();

        let validation = true;

        if (email.length < 1 || !validateEmail(email)) {
            email_input.css('border', '2px solid rgba(255, 255, 255, 0.75)');
            validation = validation & false;
        }
        if (password.length < 1) {
            password_input.css('border', '2px solid rgba(255, 255, 255, 0.75)');
            validation = validation & false;
        }
        // window.location.href = site_url + '/user/index.php?route=dashboard';
        // return
        if (validation) {
            email_input.css('border', 'transparent');
            password_input.css('border', 'transparent');
            $.ajax({
                url: site_url + "/option_server.php",
                type: "POST",
                data: {
                    'sign': 'account_login',
                    'user_email': email,
                    'user_password': password
                }
            }).done(function (data) {
                try {
                    var datas = JSON.parse(data);
                    if (datas.message == 'success') {
                        $('#login_button').prop('disabled', true);
                        if (datas.type == 'user' || datas.type == 'company')
                            window.location.href = site_url + '/user/index.php?route=dashboard';
                        else if (datas.type == 'consultant')
                            window.location.href = site_url + '/user/index.php?route=dashboard';
                        else
                            window.location.href = site_url + '/user/index.php?route=dashboard';
                    } else if (datas.message == 'not_verified') {
                        $('#login_form, #signup_form, #register_company_form, #forgot_pass_form').fadeOut(1, function () {
                            $('#pin_code_form').fadeIn(1000);
                        });
                    }
                } catch (e) {
                    $('#login_status').text(data);
                }
            })
        }
    });

    //Submit pin code
    $('#submit_pin_code_button').click(function (event) {
        event.preventDefault();
        let pin_code_input = $('#pin_code');
        let user_type_logged_in_input = $('#user_type_logged_in');
        let user_id_logged_in_input = $('#user_id_logged_in');

        let pin_code = pin_code_input.val();
        let user_type = user_type_logged_in_input.val();
        let user_id = user_id_logged_in_input.val();

        let validation = true;

        if (pin_code.length < 1) {
            pin_code_input.css('border', '2px solid rgba(255, 255, 255, 0.75)');
            validation = validation & false;
        }

        if (validation) {
            pin_code_input.css('border', 'transparent');
            $.ajax({
                url: site_url + "/option_server.php",
                type: "POST",
                data: {
                    'verify': 'code_pin',
                    'pin_code': pin_code,
                    'user_type': user_type,
                    'user_id': user_id
                }
            }).done(function (data) {
                try {
                    var datas = JSON.parse(data);
                    if (datas.message == 'success') {
                        $('#login_button').prop('disabled', true);
                        if (datas.type == 'user' || datas.type == 'company')
                            window.location.href = site_url + '/user/index.php?route=tickets';
                        else
                            window.location.href = site_url + '/user/index.php?route=dashboard';
                    }
                } catch (e) {
                    $('#code_pin_status').text(data);
                }
            })
        }
    });

    //Forgot password and validation
    $('#forgot_pass_button').click(function (event) {
        event.preventDefault();
        let button = $(this);

        let checked = '';

        let tfa_email_check = document.getElementById('tfa_email');
        let tfa_phone_check = document.getElementById('tfa_phone');

        if (tfa_email_check.checked)
            checked = 'email';
        else if (tfa_phone_check.checked)
            checked = 'phone';



        let forgot_input = '';
        if (checked === 'email') {
            forgot_input = $('#forgot_pass_email');
        } else if (checked === 'phone') {
            forgot_input = $('#forgot_pass_phone');
        }

        let source = forgot_input.val();

        let validation = true;

        if (source.length < 1) {
            forgot_input.css('border', '1px solid red');
            validation = validation & false;
        }

        if (validation) {
            forgot_input.css('border', '1px solid white');

            button.append(' <i class="fas fa-spin fa-spinner"></i>');

            $.ajax({
                url: site_url + "/option_server.php",
                type: "POST",
                data: {
                    'sign': 'forgot_password',
                    'user_source': source,
                    'type': checked
                }
            }).done(function (data) {
                    var datas = JSON.parse(data);
                    if (datas.message == 'not_verified') {
                        $('#login_form, #signup_form, #register_company_form, #forgot_pass_form').fadeOut(1, function () {
                            $('#pin_code_form').fadeIn(1000);
                        });
                    } else {
                        $('#forget_pass_status').text(data);
                    }
            })
        }
    });

    //Password reset and validation
    $('#pass_reset_button').click(function (event) {
        event.preventDefault();
        let button = $(this);

        let new_pass_input = $('#new_pass');
        let confirm_pass_input = $('#confirm_pass');
        let new_pass = new_pass_input.val();
        let confirm_pass = confirm_pass_input.val();
        let request_id = $(this).data('request-id');

        let validation = true;

        if (new_pass.length < 1) {
            new_pass_input.css('border', '1px solid red');
            validation = validation & false;
        }
        if (confirm_pass.length < 1 || confirm_pass != new_pass) {
            confirm_pass_input.css('border', '1px solid red');
            validation = validation & false;
        }
        if (validation) {
            new_pass_input.css('border', '1px solid white');
            confirm_pass_input.css('border', '1px solid white');

            button.append(' <i class="fas fa-spin fa-spinner"></i>');

            $.ajax({
                url: site_url + "/option_server.php",
                type: "POST",
                data: {
                    'sign': 'reset_password',
                    'new_password': new_pass,
                    'request_id': request_id
                }
            }).done(function (data) {
                button.find('i').remove();
                $('#pass_reset_status').html(data);
                new_pass_input.val('');
                confirm_pass_input.val('');
            })
        }
    });

    //User Signup and validation
    $('#signup_button').click(function (event) {
        event.preventDefault();
        let button = $(this);

        let username_input = $('#signup_username');
        let email_input = $('#signup_email');
        let password_input = $('#signup_password');
        let confirm_password_input = $('#signup_confirm_password');
        let phone_input = $('#signup_phone');
        let company_input = $('#signup_company');

        let username = username_input.val();
        let email = email_input.val();
        let password = password_input.val();
        let confirm_password = confirm_password_input.val();
        let phone = phone_input.val();
        let company = company_input.val();

        let validation = true;

        if (username.length < 1) {
            username_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            username_input.css('border', '1px solid white');
        }
        if (email.length < 1 || !validateEmail(email)) {
            email_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            email_input.css('border', '1px solid white');
        }
        if (password.length < 1 || confirm_password != password) {
            password_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            password_input.css('border', '1px solid white');
        }
        if (confirm_password.length < 1 || confirm_password != password) {
            confirm_password_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            confirm_password_input.css('border', '1px solid white');
        }
        if (phone.length < 1) {
            phone_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            phone_input.css('border', '1px solid white');
        }
        if (company.length < 1) {
            company_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            company_input.css('border', '1px solid white');
        }
        if (!$('#signup_terms').is(':checked')) {
            alert($('#translation').data('main_js_phrase1'));
            validation = validation & false;
        }

        if (validation) {
            button.append(' <i class="fas fa-spin fa-spinner"></i>');
            //Server request
            $.ajax({
                url: site_url + '/option_server.php',
                type: 'POST',
                data: {
                    'sign': 'user_signup',
                    'user_name': username,
                    'user_email': email,
                    'user_password': password,
                    'user_phone': phone,
                    'user_company_id': company
                }
            }).done(function (data) {
                if (data == 'success') {
                    //window.location.href = site_url + '/user/index.php?route=profile';
					//alert("User added!");
					$('#message_success').html('<p style="color:red;text-align: center;font-size: 21px;">User added!</p>');
					$('#message_success').delay(3000).fadeOut();
                    window.location.href = site_url + '/index.php';
                } else {
                    button.find('i').remove();
                    $('#signup_status').text(data);
                }
            });
        }
    });

    //Consultant Signup and validation
    $('#consultant_signup_button').click(function (event) {
        event.preventDefault();
        let button = $(this);

        let username_input = $('#consultant_username');
        let email_input = $('#consultant_email');
        let password_input = $('#consultant_password');
        let confirm_password_input = $('#consultant_confirm_password');
        let phone_input = $('#consultant_phone');

        let username = username_input.val();
        let email = email_input.val();
        let password = password_input.val();
        let confirm_password = confirm_password_input.val();
        let phone = phone_input.val();

        let validation = true;

        if (username.length < 1) {
            username_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            username_input.css('border', '1px solid white');
        }
        if (email.length < 1 || !validateEmail(email)) {
            email_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            email_input.css('border', '1px solid white');
        }
        if (password.length < 1 || confirm_password != password) {
            password_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            password_input.css('border', '1px solid white');
        }
        if (confirm_password.length < 1 || confirm_password != password) {
            confirm_password_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            confirm_password_input.css('border', '1px solid white');
        }
        if (phone.length < 1) {
            phone_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            phone_input.css('border', '1px solid white');
        }
        if (!$('#consultant_terms').is(':checked')) {
            alert($('#translation').data('main_js_phrase1'));
            validation = validation & false;
        }

        if (validation) {
            button.append(' <i class="fas fa-spin fa-spinner"></i>');
            //Server request
            $.ajax({
                url: site_url + '/option_server.php',
                type: 'POST',
                data: {
                    'sign': 'consultant_signup',
                    'consultant_name': username,
                    'consultant_email': email,
                    'consultant_password': password,
                    'consultant_phone': phone
                }
            }).done(function (data) {
                if (data == 'success') {
                    //window.location.href = site_url + '/user/index.php?route=profile';
					//alert("Consultant added!");
					$('#message_success').html('<p style="color:red;text-align: center;font-size: 21px;">Consultant added!</p>');
					$('#message_success').delay(3000).fadeOut();
                    window.location.href = site_url + '/index.php';
                } else {
                    button.find('i').remove();
                    $('#consultant_status').text(data);
                }
            });
        }
    });

    //Company registration form package updater
    $('#register_company_employee').change(function () {
        let min_max = $(this).val();
        let selector = $(this);

        $.ajax({
            url: site_url + '/option_server.php',
            type: 'POST',
            data: {"sign": "package_updater", "min_max": min_max}
        }).done(function (data) {
            let option_array = JSON.parse(data);

            let html = '';
            for (let key in option_array) {
                html += `
                    <option value="${option_array[key]['user']}">
                    ${option_array[key]['name']} (${option_array[key]['user']} user - ${selector.data('site_currency_symbol') + option_array[key]['price'] + ' ' + selector.data('site_currency')})
                    </option>
                `
            }

            $('#register_company_size').html(html);
        })
    });

    //Company registration request and validation
    $('#register_company_button').click(function (event) {
        event.preventDefault();
        let button = $(this);

        let name_input = $('#register_company_name');
        let industry_type_input = $('#register_company_industry_type');
        let email_input = $('#register_company_email');
        let password_input = $('#register_company_password');
        let confirm_password_input = $('#register_company_confirm_password');
        let phone_input = $('#register_company_phone');

        let name = name_input.val();
        let industry_type = industry_type_input.val();
        let email = email_input.val();
        let password = password_input.val();
        let confirm_password = confirm_password_input.val();
        let phone = phone_input.val();
	

        let validation = true;

        if (name.length < 1) {
            name_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            name_input.css('border', '1px solid white');
        }
        if (!industry_type) {
            industry_type_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            industry_type_input.css('border', '1px solid white');
        }
        if (email.length < 1 || !validateEmail(email)) {
            email_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            email_input.css('border', '1px solid white');
        }
        if (password.length < 1 || confirm_password != password) {
            password_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            password_input.css('border', '1px solid white');
        }
        if (confirm_password.length < 1 || confirm_password != password) {
            confirm_password_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            confirm_password_input.css('border', '1px solid white');
        }
        if (phone.length < 1) {
            phone_input.css('border', '1px solid red');
            validation = validation & false;
        } else {
            phone_input.css('border', '1px solid white');
        }
        if (!$('#register_company_terms').is(':checked')) {
            alert($('#translation').data('main_js_phrase1'));
            validation = validation & false;
        }

        if (validation) {
            button.append(' <i class="fas fa-spin fa-spinner"></i>');
            //Server request
            $.ajax({
                url: site_url + '/option_server.php',
                type: 'POST',
                data: {
                    'sign': 'company_registration',
                    'company_name': name,
                    'company_industry_type': industry_type,
                    'company_email': email,
                    'company_password': password,
                    'company_phone': phone,
                    'company_size': 0
                }
            }).done(function (data) {
                if (data == 'success') {
                    //window.location.href = site_url + '/user/index.php?route=company_profile';
					//alert("Company added!");
					$('#message_success').html('<p style="color:red;text-align: center;font-size: 21px;">Company added!</p>');
					$('#message_success').delay(3000).fadeOut();
                    window.location.href = site_url + '/index.php';
                } else {
                    button.find('i').remove();
                    $('#register_company_status').text(data);
                }
            });
        }
    });

    //Footer language change
    $('#footer_language_selector').change(function () {
        let lang_code = $(this).val();

        console.log(lang_code);
        $.ajax({
            url: site_url + '/option_server.php',
            type: 'POST',
            data: {'sign': 'language_change', 'lang_code': lang_code}
        }).done(function (data) {
            if (data == 'success')
                window.location.reload();
            else
                alert(data);
        })
    });
});

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}


function showPricing(lblClose) {
    var htmlText = '<div class="w3-modal-content">';
    htmlText += '<div class="w3-container">';
    htmlText += '<span ';
    htmlText += 'class="w3-button w3-display-topright"></span>';
    htmlText += '<iframe class="w3-modal-iframe" src=site_url + "/pricing.php"></iframe>';
    htmlText += '<div class="w3-button-holder">';
    htmlText += '<button id="close-tc" onclick="hidePricing()" class="btn btn-info">' + lblClose + '</button><div>';
    htmlText += '</div></div></div>';

    $('#divPricing').html(htmlText).show();
}

function hidePricing() {
    $('#divPricing').hide();
}


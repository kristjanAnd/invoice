$(function() {

    //$( "#forgotPassword" ).dialog({
    //    autoOpen: false,
    //    modal: true
    //});
    //
    //$( "#forgotPasswordButton" )
    //    .click(function() {
    //        $( "#forgotPassword" ).dialog( "open" );
    //        $( "#login" ).dialog( "close" );
    //    });
    //
    //$( "#twitterLogin" ).dialog({
    //    autoOpen: false,
    //    modal: true
    //});
    //
    //$( "#twitterLoginButton" )
    //    .click(function() {
    //        $( "#twitterLogin" ).dialog( "open" );
    //    });

});

var Common = {
    language: 'et',
    setLanguage: function(language){
        Common.language = language;
    }
};

var Password = {
    forgotPasswordSuccessMessage: '',
    forgotPasswordErrorMessage: '',

    isValid: function (password) {
        password = password.toString();
        return !(password.length < 6 || password.length > 20);
    },

    initForgot: function(){
        Password.disableEnableForgotSend();
        $(document).on('blur', '#forgotEmail', function () {
            Password.disableEnableForgotSend();
        });

        $(document).on('click', '#sendPassword', function (e) {
            e.preventDefault();
            Password.disableEnableForgotSend();
            if($(this).prop('disabled') == false){
                Password.sendPasswordLink($('#forgotEmail').val());
                $(this).prop('disabled', true);
            }
        });
    },

    sendPasswordLink: function(email){
        $.get('/application/index/forgotPassword', {email: email, language: Common.language}, function (data) {
            if(data == 1){
                $('#forgotPassword').html('<p>' + Password.forgotPasswordSuccessMessage + '</p>');
            } else {
                $('#forgotPassword').html('<p>' + Password.forgotPasswordErrorMessage + '</p>');
            }
        });
    },

    disableEnableForgotSend: function(){
        var sendPasswordLink = $('#sendPassword');
        if (Password.isValidEmail()){
            sendPasswordLink.prop('disabled', false);
        } else {
            sendPasswordLink.prop('disabled', true);
        }
    },

    isValidEmail: function(){
        var email = $('#forgotEmail');
        if($.trim(email.val()).length == 0){
            return false;
        }
        var isValid = true;

        var element = $('#forgot-email-not-exists');
        $(element).parent().removeClass('has-error');
        $(element).removeClass('help-block');
        $(element).hide();

        var element1 = $('#forgot-email-error');
        $(element1).parent().removeClass('has-error');
        $(element1).removeClass('help-block');
        $(element1).hide();

        if(!Mail.isValidEmail($.trim(email.val()))){
            $(element1).parent().addClass('has-error');
            $(element1).addClass('help-block');
            $(element1).show();

            return false;
        }

        if(!Validator.checkIfEmailExists(email, 'forgot')){
            $(element).parent().addClass('has-error');
            $(element).addClass('help-block');
            $(element).show();

            return false;
        }

        return isValid;
    }

};

var Validator = {
    initRegister: function () {

        $(document).on('blur', '#email', function () {
            Validator.checkIfEmailExists($(this), 'register');
        });

        $.validator.addMethod('password', function (value, element) {
            return this.optional(element) || Password.isValid(value);
        });

        $('#registerForm').validate({
            rules: {
                "firstName": {
                    required: true
                },
                "lastName": {
                    required: true
                },
                "email": {
                    required: true,
                    email: true
                },
                "personalCode": {
                    required: true,
                    digits: true,
                    maxlength: 11,
                    minlength: 11
                },
                "phone": {
                    digits: true,
                    required: true,
                    minlength: 5,
                    maxlength: 8
                },
                "password": {
                    required: true,
                    password: true
                },
                "passwordRepeat": {
                    required: true,
                    equalTo: '#password'
                }
            },
            errorPlacement: function (error, element) {

            },
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass('has-error');
                $(element).parent().find('.help-block').show();
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass('has-error');
                $(element).parent().find('.help-block').hide();
            }
        });
    },

    initLogin: function () {
        $(document).on('submit', '#loginForm', function (e) {
            var submit = $(".submit");
            submit.prop('disabled', true);
            var loginError = $('#loginError');
            loginError.hide();
            if (Validator.checkIfEmailExists($('#identity'), 'login') && Validator.checkIfPasswordValid()){
                return;
            }
            loginError.show();
            submit.prop('disabled', false);
            return false;
        });

        $(document).on('keyup', '#identity', function () {
            var element = $('.email-not-exists');
            $(element).parent().removeClass('has-error');
            $(element).removeClass('help-block');
            $(element).hide();
        });

        $(document).on('keyup', '#credential', function () {
            var element = $('.wrong-password');
            $(element).parent().removeClass('has-error');
            $(element).removeClass('help-block');
            $(element).hide();
        });


        $.validator.addMethod('password', function (value, element) {
            return this.optional(element) || Password.isValid(value);
        });


        $('#loginForm').validate({
            ignore: [],
            rules: {
                "identity": {
                    required: true,
                    email: true
                },
                "credential": {
                    required: true,
                    password: true
                }
            },
            errorPlacement: function (error, element) {

            },
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass('has-error');
                $(element).parent().find('.help-block').show();
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass('has-error');
                $(element).parent().find('.help-block').hide();
            }
        });
    },

    initNewPassword: function () {

        $.validator.addMethod('password', function (value, element) {
            return this.optional(element) || Password.isValid(value);
        });

        $('#newPasswordForm').validate({
            rules: {
                "password": {
                    required: true,
                    password: true
                },
                "passwordRepeat": {
                    required: true,
                    equalTo: '#password'
                }
            },
            errorPlacement: function (error, element) {

            },
            highlight: function (element, errorClass, validClass) {
                $(element).parent().addClass('has-error');
                $(element).parent().find('.help-block').show();
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().removeClass('has-error');
                $(element).parent().find('.help-block').hide();
            }
        });
    },



    checkIfEmailExists: function (element, action) {
        if(action == 'register'){
            $.get('/application/index/email-exists', {email: element.val()}, function (data) {
                if (data == 1) {
                    $(element).parent().addClass('has-error');
                    $(element).parent().find('.email-exists').addClass('help-block');
                    $(element).parent().find('.email-exists').show();
                } else {
                    $(element).parent().removeClass('has-error');
                    $(element).parent().find('.email-exists').removeClass('help-block');
                    $(element).parent().find('.email-exists').hide();
                }

            });
        }
        if(action == 'login' || action == 'forgot') {
            var result = null;
            var jqXHR = $.ajax({
                url: '/application/index/email-exists',
                type: 'get',
                data: { email: element.val()} ,
                dataType: 'html',
                async: false,
                success: function(data) {
                    result = data;
                }
            });
            var emailExists = (jQuery.parseJSON(jqXHR.responseText) == 1);

            if(action == 'login') {
                element = $('.email-not-exists');
                if (!emailExists) {
                    $(element).parent().addClass('has-error');
                    $(element).addClass('help-block');
                    $(element).show();
                } else {
                    $(element).parent().removeClass('has-error');
                    $(element).removeClass('help-block');
                    $(element).hide();
                }
            }
            return emailExists;
        }
    },

    checkIfPasswordValid: function() {

        var email = $("#identity").val();
        var password = $("#credential").val();

        var result = null;
        var jqXHR = $.ajax({
            url: '/application/index/isPasswordValid',
            type: 'get',
            data: { email: email, password: password} ,
            dataType: 'html',
            async: false,
            success: function(data) {
                result = data;
            }
        });
        var passwordValid = (jQuery.parseJSON(jqXHR.responseText) == 1);

        if(!passwordValid){
            var element = $('.wrong-password');
            $(element).parent().addClass('has-error');
            $(element).addClass('help-block');
            $(element).show();
        }

        return passwordValid;
    }


};

var IdLogin = {

    authUrl: '',
    pollUrl: '',
    cancelUrl: '',
    IdRedirectUrl: '',

    pollInterval: 2000,

    messages: {
        CHECKING_CONTRACT: 'Kontrollin mobiil-ID lepingu olemasolu.',
        REQUEST_SENT: 'Päring saadetud. Kontrollkood:',
        SERVER_FAULT: 'Esines serveri poolne tõrge. Palun proovige mõne aja pärast uuesti.',
        RESPONSE_RECEIVED: 'Vastus saadud, alustan sisselogimist.'
    },

    statuses: {
        NOT_VALID: 'Tekitatud signatuur pole kehtiv.',
        EXPIRED_TRANSACTION: 'Sessioon on aegunud.',
        USER_CANCEL: 'Kasutaja poolt katkestatud.',
        MID_NOT_READY: 'Funktsionaalsus ei ole veel kasutatav. Proovi mõne aja pärast uuesti.',
        PHONE_ABSENT: 'Mobiiltelefon ei ole levis.',
        SENDING_ERROR: 'Sõnumi saatmisel tekkis viga. Proovi mõne aja pärast uuesti.',
        SIM_ERROR: 'SIM kaardi viga.',
        INTERNAL_ERROR: 'Tehniline viga. Proovi mõne aja pärast uuesti.'
    },

    firstPollAfter: 10000,

    isCanceled: false,

    init: function () {
        var hidden = 'hidden';

        this.initValidator();

        $(document).on('change', '.email', function(){
            $('.email').val($(this).val());
        });

        $(document).on('keyup', '#phone', function(){
            $(this).val($.trim($(this).val()));
        });

        $('.trigger-mid').click(function () {
            var login = $('form#loginForm'),
                midLogin = $('form#mobile-id-login');

            if (midLogin.hasClass(hidden)) {
                midLogin.find('input#emailMob').val(login.find('input#email').val());
            } else {
                login.find('input#email').val(midLogin.find('input#emailMob').val())
            }

            login.toggleClass(hidden);
            midLogin.toggleClass(hidden).find('input#phone').focus();
        });

        $('#mid-submit').click(function (e) {
            e.preventDefault();
            IdLogin.prepare();
        });

        $('form').keydown(function (e) {
            if (e.keyCode === 13) {
                if ($('input[type="password"]').parent().hasClass(hidden)) {
                    e.preventDefault();
                    IdLogin.prepare();
                }
            }
        });

        $('input.cancel').click(function (e) {
            var hidden = 'hidden',
                form = $('form#mobile-id-login');

            IdLogin.isCanceled = true;
            e.preventDefault();

            $('form#mobile-id-login, .buttons').removeClass(hidden);
            $('.mid-info').addClass(hidden);
            $('.mid-text, .mid-check').html('');
            $('.mid-warning').addClass(hidden);

            $.post(this.cancelUrl, {
                email: form.find('input[name="email"]').val(),
                phone: form.find('input[name="phone"]').val()
            });
        })
    },

    // @todo change min to 7
    initValidator: function () {
        $('form#mobile-id-login').validate({
            rules: {
                emailMob: {
                    required: true,
                    email: true
                },
                phone: {
                    digits: true,
                    required: true,
                    minlength: 5,
                    maxlength: 8
                }
            },
            errorPlacement: function (error, element) {

            },
            highlight: function (element, errorClass, validClass) {
                $(element).parent().parent().addClass('has-error');
                $(element).parent().parent().find('.help-block').show();
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().parent().removeClass('has-error');
                $(element).parent().parent().find('.help-block').hide();
            }
        });

        $('form#id-login').validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            errorPlacement: function (error, element) {

            },
            highlight: function (element, errorClass, validClass) {
                $(element).parent().parent().addClass('has-error');
                $(element).parent().parent().find('.help-block').show();
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parent().parent().removeClass('has-error');
                $(element).parent().parent().find('.help-block').hide();
            },
            submitHandler: function(form) {
                window.location.href = IdLogin.IdRedirectUrl + '?email=' + $('#idEmail').val();

            }
        });
    },

    prepare: function () {
        var midText = $('.mid-text'),
            midWarning = $('.mid-warning'),
            midInfo = $('.mid-info'),
            midCheck = $('.mid-check'),
            formError = $('p.form-error'),
            hidden = 'hidden',
            disabled = 'disabled',
            buttons = $('.buttons'),
            form = $('form#mobile-id-login'),
            mid = IdLogin;

        IdLogin.isCanceled = false;

        if (form.valid()) {

            formError.addClass(hidden).html('');
            midInfo.removeClass(hidden);
            form.addClass(hidden);
            buttons.addClass(hidden);

            midText.html(IdLogin.messages.CHECKING_CONTRACT);

            $.post(mid.authUrl, {
                email: form.find('input[name="emailMob"]').val(),
                phone: '+372' + form.find('input[name="phone"]').val()
            }, function (data) {
                if (!IdLogin.isCanceled) {
                    if (data.error === null) {
                        midText.html(IdLogin.messages.REQUEST_SENT);
                        midCheck.removeClass(hidden).html(data.code);
                        midWarning.removeClass(hidden);

                        setTimeout(function () {
                            mid.pollStatus();
                        }, mid.firstPollAfter);
                    } else {
                        form.removeClass(hidden);
                        buttons.removeClass(hidden);
                        formError.removeClass(hidden).html(data.error);
                        midInfo.addClass(hidden);
                    }
                }
            }).error(function () {
                if (!IdLogin.isCanceled) {
                    form.removeClass(hidden);
                    buttons.removeClass(hidden);
                    formError.removeClass(hidden).html(IdLogin.messages.SERVER_FAULT);
                    midInfo.addClass(hidden);
                }
            });
        }
    },

    pollStatus: function () {
        var midText = $('.mid-text'),
            midCheck = $('.mid-check'),
            hidden = 'hidden',
            midWarning = $('.mid-warning'),
            cancelButton = $('input.cancel'),
            form = $('form#mobile-id-login'),
            error = $('p.form-error');

        if (!this.isCanceled) {

            $.post(this.pollUrl, {
                email: form.find('input[name="emailMob"]').val(),
                phone: form.find('input[name="phone"]').val()
            }, function (data) {
                if (data.error === null) {
                    switch (data.status) {
                        case 'OUTSTANDING_TRANSACTION':
                            setTimeout(function () {
                                if (!IdLogin.isCanceled) {
                                    IdLogin.pollStatus();
                                }
                            }, IdLogin.pollInterval);
                            break;
                        case 'USER_AUTHENTICATED':
                            midText.html(IdLogin.messages.RESPONSE_RECEIVED);
                            midWarning.addClass(hidden);
                            midCheck.addClass(hidden);
                            $('input.cancel').addClass(hidden);

                            setTimeout(function () {
                                window.location.href = '/';
                            }, 1000);
                            break;
                        default:
                            error.removeClass(hidden).html(IdLogin.statuses[data.status]);
                            cancelButton.click();

                            setTimeout(function () {
                                window.location.href = '/user/login';
                            }, 5000);
                            break;
                    }
                } else {
                    error.removeClass(hidden).html('ERROR! ' + data.error);
                    cancelButton.click();
                }
            }).error(function () {
                cancelButton.click();
            });
        }
    }
};

var Mail = {
    isValidEmail: function(email){
        var pattern = new RegExp(/^[+a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i);
        return pattern.test(email);
    }
};
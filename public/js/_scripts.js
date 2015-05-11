$(function() {
    function reposition() {
        var modal = $(this),
            dialog = modal.find('.modal-dialog');
        modal.css('display', 'block');

        // Dividing by two centers the modal exactly, but dividing by three
        // or four works better for larger screens.
        dialog.css("margin-top", Math.max(0, ($(window).height() - dialog.height()) / 2));
        dialog.css("margin-left", Math.max(0, ($(window).width() - dialog.width()) / 2));
    }
    // Reposition when a modal is shown
    $('.modal').on('show.bs.modal', reposition);
    // Reposition when the window is resized
    $(window).on('resize', function() {
        $('.modal:visible').each(reposition);
    });
});

function log(input){
    console.log(input);
}

var Common = {
    NO_ROWS_MESSAGE: '',
    companyId: null,
    userId: null,
    language: 'us',
    locale: 'en_US',
    datepickerBaseSrc: '/lib/jquery-ui-1.10.4/development-bundle/ui/i18n/jquery.ui.datepicker-',
    datePickerLocaleMapper: {
        'us': 'en-GB',
        'ru': 'ru',
        'et': 'et'
    },
    select2LanguageMapper: {
        'us': 'en',
        'ru': 'ru',
        'et': 'et'
    },
    setNoRowsMessage: function(msg){
      Common.NO_ROWS_MESSAGE = msg;
    },
    setLanguage: function(language){
        if(language == undefined){
            language = Common.language;
        }
        Common.language = language;
        Common.initDatepickerRegion(Common.datePickerLocaleMapper[language]);
    },
    setLocale: function(locale){
        if(locale !== undefined){
            Common.locale = locale;
        }
    },
    replaceCommas: function(element) {
        var val = element.val().replace(",", ".");
        element.val(val);
    },
    replaceNonNumbersWithZero: function(elements){
        $.each(elements, function(){
            this.val(0);
        })
    },
    initDatepickerRegion: function(locale){
        $.getScript(Common.datepickerBaseSrc + locale + '.js', function () {
            $.datepicker.setDefaults($.datepicker.regional[locale]);
        });

    },
    setCompanyId: function(id){
        Common.companyId = id;
    },
    setUserId: function(id){
        Common.userId = id;
    },
    calculateAmount:function(price, qty){
        return parseFloat(price*qty);
    },
    calculateVatAmount: function(vatPercent, amount){
        return parseFloat(vatPercent/100*amount);
    },
    calculateAmountVat: function(vatPercent, amount){
        var vatAmount = vatPercent/100*amount;
        return parseFloat(amount+vatAmount);
    },
    calculateTotalAmount: function(){
        var sum = 0;
        $('.amount').each(function(){
            sum+= parseFloat($(this).val());
        });
        return sum;
    },
    calculateTotalVatAmount: function(){
        var sum = 0;
        $('.vatAmount').each(function(){
            sum+= parseFloat($(this).val());
        });
        return sum;
    },
    calculateTotalAmountVat: function(){
        var sum = 0;
        $('.amountVat').each(function(){
            sum+= parseFloat($(this).val());
        });
        return sum;
    },
    checkRowCount: function() {
        var invoiceRow = $('.invoice-row');
        if (invoiceRow.length){
            return true;
        }
        $('#no-rows-div').html(Common.getNoRowsMessage());
        return false;
    },
    getNoRowsMessage: function(){
        return "<div class='alert alert-danger alert-dismissable'> <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button>"
        + Common.NO_ROWS_MESSAGE +
        "</div>";
    },
    initObjectAddition: function(){
        $(document).on('click', '#add-vat-submit', function(e){
            e.preventDefault();
            var form = $('#vatForm');
            if(form.valid()){
                var formValues = form.serializeArray();
                var localeObject = {name: 'locale', value: Common.locale};
                formValues.push(localeObject);
                var url = '/application/article/add-vat?modal=1';
                $.post(url, formValues).done(function (data) {
                    $('#vat').html(data);
                    $('#add-modal').modal('hide');
                });
            } else {
                return false;
            }
        });
        $(document).on('click', '.add-modal-vat', function(){
            var addModal = $('#add-modal');
            $.get('/application/index/get-vat-add-form', {locale: Common.locale}, function (data) {
                $('#add-modal-content').html(data);
                addModal.modal('show');
            });
        });
        $(document).on('click', '#add-client-submit', function(e){
            e.preventDefault();
            var form = $('#clientForm');
            if(form.valid()){
                var formValues = form.serializeArray();
                var localeObject = {name: 'locale', value: Common.locale};
                formValues.push(localeObject);
                var url = '/application/client/add-client?modal=1';
                $.post(url, formValues).done(function (data) {
                    $('#client-select').html(data.html);
                    $("#client").select2({
                        language: Common.select2LanguageMapper[Common.language]
                    });
                    Invoice.setClientFieldValues(data);
                    $('#add-modal').modal('hide');
                });
            } else {
                return false;
            }
        });
        $(document).on('click', '.add-modal-client', function(){
            var addModal = $('#add-modal');
            $.get('/application/index/get-client-add-form', {locale: Common.locale}, function (data) {
                $('#add-modal-content').html(data);
                addModal.modal('show');
            });
        });
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
                "language": {
                    required: true
                },
                "role": {
                    required: true
                },
                "status": {
                    required: true
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

    initUserForm: function (userId) {

        $(document).on('blur', '#email', function () {
            Validator.checkIfEmailExists($(this), 'register', userId);
        });

        $(document).on('click', '#changePass', function () {
            $("#isP").val(1);
            $(this).hide();
            $('#passwordDiv').show();
            $('#cancelPass').show();
        });

        $(document).on('click', '#cancelPass', function () {
            $("#isP").val(0);
            $(this).hide();
            $('#passwordDiv').hide();
            $('#changePass').show();
        });

        $.validator.addMethod('password', function (value, element) {
            return this.optional(element) || Password.isValid(value);
        });

        $('#userForm').validate({
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
                "language": {
                    required: true
                },
                "role": {
                    required: true
                },
                "status": {
                    required: true
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
                    required: {
                        depends: function(element) {
                            return $("#isP").val() == 1;
                        }
                    },
                    password: true
                },
                "passwordRepeat": {
                    required: {
                        depends: function(element) {
                            return $("#isP").val() == 1;
                        }
                    },
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

    initUnitForm: function () {
        $('#unitForm').validate({
            rules: {
                "code": {
                    required: true
                },
                "value": {
                    required: true,
                    number: true
                },
                "status": {
                    required: true
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

    initVatForm: function () {
        $('#vatForm').validate({
            rules: {
                "code": {
                    required: true
                },
                "value": {
                    required: true,
                    number: true,
                    min: 0,
                    max: 100
                },
                "status": {
                    required: true
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

    initCompanyForm: function () {
        $('#companyForm').validate({
            rules: {
                "name": {
                    required: true
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

    initInvoiceSettingForm: function () {
        $(document).on('change', '#delayPercent', function(){
            Common.replaceCommas($(this));
        });
        $('#invoiceSettingForm').validate({
            rules: {
                "deadlineDays": {
                    digits: true
                },
                "nextNumber": {
                    digits: true
                },
                "delayPercent": {
                    number: true,
                    min: 0,
                    max: 100
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

    initArticleSettingForm: function () {
        $(document).on('click', '#add-unit-submit', function(e){
            e.preventDefault();
            var form = $('#unitForm');
            if(form.valid()){
                var formValues = form.serializeArray();
                var localeObject = {name: 'locale', value: Common.locale};
                formValues.push(localeObject);
                var url = '/application/article/add-unit?modal=1';
                $.post(url, formValues).done(function (data) {
                    $('#item-unit').html(data);
                    $('#service-unit').html(data);
                    $('#add-modal').modal('hide');
                });
            } else {
                return false;
            }
        });
        $(document).on('click', '#add-vat-submit', function(e){
            e.preventDefault();
            var form = $('#vatForm');
            if(form.valid()){
                var formValues = form.serializeArray();
                var localeObject = {name: 'locale', value: Common.locale};
                formValues.push(localeObject);
                var url = '/application/article/add-vat?modal=1';
                $.post(url, formValues).done(function (data) {
                    $('#item-vat').html(data);
                    $('#service-vat').html(data);
                    $('#add-modal').modal('hide');
                });
            } else {
                return false;
            }
        });
        $(document).on('change', '.quantity', function(){
            Common.replaceCommas($(this));
        });
        $(document).on('click', '.add-modal-unit', function(){
            var addModal = $('#add-modal');

            $.get('/application/index/get-unit-add-form', {locale: Common.locale}, function (data) {
                $('#add-modal-content').html(data);
                addModal.modal('show');
            });
        });
        $(document).on('click', '.add-modal-vat', function(){
            var addModal = $('#add-modal');

            $.get('/application/index/get-vat-add-form', {locale: Common.locale}, function (data) {
                $('#add-modal-content').html(data);
                addModal.modal('show');
            });
        });

        $('#itemSettingForm').validate({
            rules: {
                "quantity": {
                    number: true,
                    required: true
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
        $('#serviceSettingForm').validate({
            rules: {
                "quantity": {
                    number: true,
                    required: true
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

    initClientForm: function () {
        $(document).on('change', '#delayPercent', function(){
            Common.replaceCommas($(this));
        });
        $('#clientForm').validate({
            rules: {
                "name": {
                    required: true
                },
                "clientUser": {
                    required: true
                },
                "status": {
                    required: true
                },
                "deadlineDays": {
                    digits: true
                },
                "delayPercent": {
                    number: true,
                    min: 0,
                    max: 100
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

    initInvoiceForm: function () {
        $(document).on('change', '#delayPercent', function(){
            Common.replaceCommas($(this));
        });

        $.validator.addMethod('rowCount', function (value, element) {
            return this.optional(element) || Common.checkRowCount();
        });
        $('#invoiceForm').validate({
            ignore: "not:hidden",
            rules: {
                "row-count": {
                    rowCount: true
                },
                "user": {
                    required: true
                },
                "language": {
                    required: true
                },
                "dateFormat": {
                    required: true
                },
                "documentDate": {
                    required: true
                },
                "deadlineDate": {
                    required: true
                },
                "subjectName": {
                    required: true
                },
                "vat": {
                    required: true
                },
                "names[]": {
                    required: true
                },
                "quantities[]": {
                    required: true
                },
                "units[]": {
                    required: true
                },
                "prices[]": {
                    required: true
                },
                "vats[]": {
                    required: true
                },
                "vatAmounts[]": {
                    required: true
                },
                "amounts[]": {
                    required: true
                },
                "amountVats[]": {
                    required: true
                }


            },
            errorPlacement: function (error, element) {

            },
            highlight: function (element, errorClass, validClass) {
                if(element.id !== 'row-count'){
                    $(element).parent().addClass('has-error');
                    $(element).parent().find('.help-block').show();
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                if(element.id !== 'row-count'){
                    $(element).parent().removeClass('has-error');
                    $(element).parent().find('.help-block').hide();
                }
            }
        });
    },

    initArticleForm: function () {
        $(document).on('change', '#salePrice', function(){
            Common.replaceCommas($(this));
        });
        $(document).on('change', '.quantity', function(){
            Common.replaceCommas($(this));
        });

        $(document).on('click', '.add-modal-unit', function(){
            var addModal = $('#add-modal');

            $.get('/application/index/get-unit-add-form', {locale: Common.locale}, function (data) {
                $('#add-modal-content').html(data);
                addModal.modal('show');
            });
        });
        $(document).on('click', '.add-modal-vat', function(){
            var addModal = $('#add-modal');

            $.get('/application/index/get-vat-add-form', {locale: Common.locale}, function (data) {
                $('#add-modal-content').html(data);
                addModal.modal('show');
            });
        });
        $(document).on('click', '.add-modal-category', function(){
            var addModal = $('#add-modal');

            $.get('/application/index/get-category-add-form', {locale: Common.locale}, function (data) {
                $('#add-modal-content').html(data);
                addModal.modal('show');
            });
        });
        $(document).on('click', '.add-modal-brand', function(){
            var addModal = $('#add-modal');
            $.get('/application/index/get-brand-add-form', {locale: Common.locale}, function (data) {
                $('#add-modal-content').html(data);
                addModal.modal('show');
            });
        });


        $(document).on('click', '#add-unit-submit', function(e){
            e.preventDefault();
            var form = $('#unitForm');
            if(form.valid()){
                var formValues = form.serializeArray();
                var localeObject = {name: 'locale', value: Common.locale};
                formValues.push(localeObject);
                var url = '/application/article/add-unit?modal=1';
                $.post(url, formValues).done(function (data) {
                    $('#unit').html(data);
                    $('#add-modal').modal('hide');
                });
            } else {
                return false;
            }
        });
        $(document).on('click', '#add-vat-submit', function(e){
            e.preventDefault();
            var form = $('#vatForm');
            if(form.valid()){
                var formValues = form.serializeArray();
                var localeObject = {name: 'locale', value: Common.locale};
                formValues.push(localeObject);
                var url = '/application/article/add-vat?modal=1';
                $.post(url, formValues).done(function (data) {
                    $('#vat').html(data);
                    $('#add-modal').modal('hide');
                });
            } else {
                return false;
            }
        });
        $(document).on('click', '#add-category-submit', function(e){
            e.preventDefault();
            var form = $('#categoryForm');
            if(form.valid()){
                var formValues = form.serializeArray();
                var localeObject = {name: 'locale', value: Common.locale};
                formValues.push(localeObject);
                var url = '/application/article/add-category?modal=1';
                $.post(url, formValues).done(function (data) {
                    $('#category').html(data);
                    $('#add-modal').modal('hide');
                });
            } else {
                return false;
            }
        });
        $(document).on('click', '#add-brand-submit', function(e){
            e.preventDefault();
            var form = $('#brandForm');
            if(form.valid()){
                var formValues = form.serializeArray();
                var localeObject = {name: 'locale', value: Common.locale};
                formValues.push(localeObject);
                var url = '/application/article/add-brand?modal=1';
                $.post(url, formValues).done(function (data) {
                    $('#brand').html(data);
                    $('#add-modal').modal('hide');
                });
            } else {
                return false;
            }
        });

        $('#articleForm').validate({
            rules: {
                "name": {
                    required: true
                },
                "unit": {
                    required: true
                },
                "vat": {
                    required: true
                },
                "salePrice": {
                    number: true
                },
                "status": {
                    required: true
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

    initCategoryForm: function () {
        $('#categoryForm').validate({
            rules: {
                "name": {
                    required: true
                },
                "status": {
                    required: true
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

    initBrandForm: function () {
        $('#brandForm').validate({
            rules: {
                "name": {
                    required: true
                },
                "status": {
                    required: true
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

    initRoleForm: function () {
        $('#roleForm').validate({
            rules: {
                "name": {
                    required: true
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



    checkIfEmailExists: function (element, action, userid) {
        if(action == 'register'){
            $('#submit').prop('disabled', true);
            $.get('/application/index/email-exists', {email: element.val(), exclude: userid}, function (data) {
                if (data == 1) {
                    $(element).parent().addClass('has-error');
                    $(element).parent().find('.email-exists').addClass('help-block');
                    $(element).parent().find('.email-exists').show();
                } else {
                    $('#submit').prop('disabled', false);
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
                    maxlength: 7
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

var Unit = {
    init: function(){
        $(document).on('click', '.editUnit', function () {
            Unit.editUnit($(this));
        });

        $(document).on('click', '.saveUnit', function () {
            Unit.saveUnit($(this));
        });

        $(document).on('keyup', '.code, .status', function () {
            Unit.preValidate($(this));
        });
    },

    editUnit: function(element){
        var id = element.data('value');
        element.hide();
        element.parent().find('.saveUnit').show();
        $('.value-' + id).hide();
        $('.input-' + id).show();
    },

    saveUnit: function(element){
        var id = element.data('value');
        if(Unit.validateEdit(id)){
            var code = $('#code-input-'+id).val();
            var status = $('#status-input-'+id).val();

            $('#status-value-'+id).html($("#status-input-" + id + " option:selected").text());
            $('#code-value-'+id).html(code);

            element.hide();
            element.parent().find('.editUnit').show();
            $('.value-' + id).show();
            $('.input-' + id).hide();

            $.get('/application/article/edit-unit', {id: id, code: code, status: status}, function (data) {

            });
        }
    },

    preValidate: function(element) {
        $(element).parent().removeClass('has-error');
        if(!element.val().length > 0){
            $(element).parent().addClass('has-error');
            return false;
        }
        if(element.hasClass('value')){
            Common.replaceCommas(element);
            if(!$.isNumeric(element.val())){
                $(element).parent().addClass('has-error');
                return false;
            }
        }
        return true;
    },

    validateEdit: function(id){
        var breakOut;
        var elements = [
            $('#code-input-'+id),
            $('#status-input-'+id),
        ];
        $.each(elements, function(){
            if(!Unit.preValidate($(this))){
                breakOut = true;
                return false;
            }
        });
        if(breakOut) {
            breakOut = false;
            return false;
        }
        return true;
    }
};

var Vat = {
    init: function(){
        $(document).on('click', '.editVat', function () {
            Vat.editVat($(this));
        });

        $(document).on('click', '.saveVat', function () {
            Vat.saveVat($(this));
        });

        $(document).on('keyup', '.code, .status', function () {
            Vat.preValidate($(this));
        });
    },

    editVat: function(element){
        var id = element.data('value');
        element.hide();
        element.parent().find('.saveVat').show();
        $('.value-' + id).hide();
        $('.input-' + id).show();
    },

    saveVat: function(element){
        var id = element.data('value');
        if(Vat.validateEdit(id)){
            var code = $('#code-input-'+id).val();
            var value = $('#value-input-'+id).val();
            var status = $('#status-input-'+id).val();

            $('#status-value-'+id).html($("#status-input-" + id + " option:selected").text());
            $('#code-value-'+id).html(code);
            $('#value-value-'+id).html(value);

            element.hide();
            element.parent().find('.editVat').show();
            $('.value-' + id).show();
            $('.input-' + id).hide();

            $.get('/application/article/edit-vat', {id: id, code: code, value: value, status: status}, function (data) {

            });
        }
    },

    preValidate: function(element) {
        $(element).parent().removeClass('has-error');
        if(!element.val().length > 0){
            $(element).parent().addClass('has-error');
            return false;
        }
        if(element.hasClass('value')){
            Common.replaceCommas(element);
            if(!$.isNumeric(element.val())){
                $(element).parent().addClass('has-error');
                return false;
            }
        }
        return true;
    },

    validateEdit: function(id){
        var breakOut;
        var elements = [
            $('#code-input-'+id),
            $('#value-input-'+id),
            $('#status-input-'+id),
        ];
        $.each(elements, function(){
            if(!Vat.preValidate($(this))){
                breakOut = true;
                return false;
            }
        });
        if(breakOut) {
            breakOut = false;
            return false;
        }
        return true;
    }
};

var Category = {
    init: function(){
        $(document).on('click', '.editCategory', function () {
            Category.editCategory($(this));
        });

        $(document).on('click', '.saveCategory', function () {
            Category.saveCategory($(this));
        });

        $(document).on('keyup', '.name', function () {
            Category.preValidate($(this));
        });
    },

    editCategory: function(element){
        var id = element.data('value');
        element.hide();
        element.parent().find('.saveCategory').show();
        $('.value-' + id).hide();
        $('.input-' + id).show();
    },

    saveCategory: function(element){
        var id = element.data('value');
        if(Category.validateEdit(id)){
            var name = $('#name-input-'+id).val();
            var code = $('#code-input-'+id).val();
            var description = $('#description-input-'+id).val();
            var status = $('#status-input-'+id).val();

            $('#status-value-'+id).html($("#status-input-" + id + " option:selected").text());
            $('#description-value-'+id).html(description);
            $('#code-value-'+id).html(code);
            $('#name-value-'+id).html(name);

            element.hide();
            element.parent().find('.editCategory').show();
            $('.value-' + id).show();
            $('.input-' + id).hide();

            $.get('/application/article/edit-category', {id: id, name: name, code: code, description: description, status: status}, function (data) {

            });
        }
    },

    preValidate: function(element) {
        $(element).parent().removeClass('has-error');
        if(!element.val().length > 0){
            $(element).parent().addClass('has-error');
            return false;
        }
        return true;
    },

    validateEdit: function(id){
        var breakOut;
        var elements = [
            $('#name-input-'+id),
            $('#status-input-'+id),
        ];
        $.each(elements, function(){
            if(!Category.preValidate($(this))){
                breakOut = true;
                return false;
            }
        });
        if(breakOut) {
            breakOut = false;
            return false;
        }
        return true;
    }
};

var Brand = {
    init: function(){
        $(document).on('click', '.editBrand', function () {
            Brand.editBrand($(this));
        });

        $(document).on('click', '.saveBrand', function () {
            Brand.saveBrand($(this));
        });

        $(document).on('keyup', '.name', function () {
            Brand.preValidate($(this));
        });
    },

    editBrand: function(element){
        var id = element.data('value');
        element.hide();
        element.parent().find('.saveBrand').show();
        $('.value-' + id).hide();
        $('.input-' + id).show();
    },

    saveBrand: function(element){
        var id = element.data('value');
        if(Brand.validateEdit(id)){
            var name = $('#name-input-'+id).val();
            var status = $('#status-input-'+id).val();
            $('#name-value-'+id).html(name);
            $('#status-value-'+id).html($("#status-input-" + id + " option:selected").text());

            element.hide();
            element.parent().find('.editBrand').show();
            $('.value-' + id).show();
            $('.input-' + id).hide();

            $.get('/application/article/edit-brand', {id: id, name: name, status: status}, function (data) {

            });
        }
    },

    preValidate: function(element) {
        $(element).parent().removeClass('has-error');
        if(!element.val().length > 0){
            $(element).parent().addClass('has-error');
            return false;
        }
        return true;
    },

    validateEdit: function(id){
        var breakOut;
        var elements = [
            $('#name-input-'+id),
            $('#status-input-'+id),
        ];
        $.each(elements, function(){
            if(!Category.preValidate($(this))){
                breakOut = true;
                return false;
            }
        });
        if(breakOut) {
            breakOut = false;
            return false;
        }
        return true;
    }
};

var Role = {
    defaultRoute: 'dashboard',
    excludeId: null,
    init: function(excludeId){
        Role.excludeId = excludeId;

        $(document).on('keyup, focusout, change', '#name', function () {
            $('#submit').prop('disabled', true);
            Role.checkIfRoleExists($(this));
        });

        $(document).on('change', '#redirectRoute', function () {
            if($(this).val() !== Role.defaultRoute){
                $('#' + $(this).val()).prop('checked', true);
            }
        });

        $(document).on('change', '.isAllowed', function () {
            var id = $(this).prop('id');
            if(!$(this).is(':checked') && id == $('#redirectRoute').val()){
                $('#redirectRoute').val(Role.defaultRoute)
            }

        });
    },

    checkIfRoleExists: function(element){
        $.get('/application/admin/if-role-exists', {name: element.val(), exclude: Role.excludeId}, function (data) {
            if(data == 1){
                $('#submit').prop('disabled', false);
                $(element).parent().removeClass('has-error');
                $(element).parent().find('.role-exists').removeClass('help-block');
                $(element).parent().find('.role-exists').hide();
            } else {
                $(element).parent().addClass('has-error');
                $(element).parent().find('.role-exists').addClass('help-block');
                $(element).parent().find('.role-exists').show();
            }
        });
    }
};

var FilterForm = {
    init: function(){
        FilterForm.initDates();
        $(document).on('click', '#openFilter', function () {
            $(this).hide();
            $('#closeFilter').show();
            $('#filterBody').show();
        });

        $(document).on('click', '#closeFilter', function () {
            $(this).hide();
            $('#openFilter').show();
            $('#filterBody').hide();
        });
    },
    initDates: function(){
        var fromDate = $('#fromDate');
        var toDate = $('#toDate');
        var datepickerParams = {
            inline: true,
            //nextText: '&rarr;',
            //prevText: '&larr;',
            showOtherMonths: true
            //dateFormat: 'dd MM yy',
            //showOn: "button",
            //buttonImage: "img/calendar-blue.png",
            //buttonImageOnly: true,
        };
        if(fromDate.length){
            fromDate.datepicker(datepickerParams);
        }
        if(toDate.length){
            toDate.datepicker(datepickerParams);
        }
    }
};

var AddArticle = {
    type_item: 'item',
    type_service: 'service',
    type_select: $('#articleType'),
    category_select: $('#add-category'),
    brand_select: $('#add-brand'),
    article_select: $('#add-article'),

    init: function(){
        $(document).on('change', '#articleType, #add-category, #add-brand', function () {
            if($(this).prop('id') == 'articleType'){
                $('#add-category').select2('val', '');
                $('#add-brand').select2('val', '');
            }
            AddArticle.populateArticleSelect();
        });
    },

    populateArticleSelect: function(){
        var type = $('#articleType').val();
        var category = $('#add-category').val();
        var brand = $('#add-brand').val();

        $.get('/application/article/get-article-select', {type: type, category: category, brand: brand, locale: Common.locale}, function (data) {
            $('#add-article').html(data);
        });
    }
};

var Invoice = {
    invoiceId: null,
    index: 1,
    init: function(invoiceId){

        Invoice.invoiceId = invoiceId;
        Invoice.initSelect2();
        Invoice.initDates();

        $(document).on('click', '#add-article-row', function(){
            Invoice.addArticle();
        });
        $(document).on('click', '#add-empty-row', function(){
            Invoice.addEmptyRow();
        });
        $(document).on('click', '.delete-row', function(){
            var rowId = $(this).data('id');
            if(rowId > 0){
                log('must delete');
            }
            $(this).closest('tr').remove();
            Invoice.setInvoiceTotalAmounts();
        });
        $(document).on('change', '#client', function(){
            Invoice.setClientData($(this).val());
        });
        $(document).on('change', '#deadlineDays', function(){
            Invoice.setDeadlineDate($(this).val());
        });
        $(document).on('change', '.vat, .price, .quantity', function(){
            var nonNumberElements = [];
            var row = $(this).closest('tr');
            var qtyElement = row.find('.quantity');
            var priceElement = row.find('.price');
            var vatElement = row.find('.vat');
            var amountElement = row.find('.amount');
            var vatAmountElement = row.find('.vatAmount');
            var amountVatElement = row.find('.amountVat');

            Common.replaceCommas(priceElement);
            Common.replaceCommas(qtyElement);
            if(!$.isNumeric(qtyElement.val())){
                nonNumberElements.push(qtyElement);
            }
            if(!$.isNumeric(priceElement.val())){
                nonNumberElements.push(priceElement);
            }
            Common.replaceNonNumbersWithZero(nonNumberElements);
            var qty = $.isNumeric(qtyElement.val()) ? parseFloat(qtyElement.val()).toFixed(2) : 0;
            var price = $.isNumeric(priceElement.val()) ? parseFloat(priceElement.val()).toFixed(2) : 0;
            var vat = $.isNumeric(vatElement.val()) ? parseFloat(row.find('.vat').val()).toFixed(2) : 0;

            var amount = Common.calculateAmount(price, qty);
            var vatAmount = Common.calculateVatAmount(vat, amount);
            var amountVat = Common.calculateAmountVat(vat, amount);

            amountElement.val(amount.toFixed(3));
            vatAmountElement.val(vatAmount.toFixed(3));
            amountVatElement.val(amountVat.toFixed(3));

            Invoice.setInvoiceTotalAmounts();

        });
        $(document).on('focusin', '.amount, .quantity, .amountVat', function(){
            $(this).select();
        });
    },

    setDeadlineDate: function(days){
        var deadlineDate = $('#deadlineDate');
        var date = $('#documentDate').datepicker('getDate');
        date.setDate(date.getDate() + parseInt(days));
        deadlineDate.datepicker('setDate', date);
        $('#ui-datepicker-div').remove();
    },

    setInvoiceTotalAmounts: function(){
        $('#amount').val(Common.calculateTotalAmount().toFixed(3));
        $('#vatAmount').val(Common.calculateTotalVatAmount().toFixed(3));
        $('#amountVat').val(Common.calculateTotalAmountVat().toFixed(3));
    },

    setClientData: function(id){
        $.get('/application/index/get-client-data', {id: id}, function (data) {
            Invoice.setClientFieldValues(data);
        });
    },
    setClientFieldValues: function(data){
        $('#subjectName').val(data && data.name ? data.name : '');
        $('#subjectEmail').val(data && data.email ? data.email : '');
        $('#subjectRegNo').val(data && data.regNo ? data.regNo : '');
        $('#subjectVatNo').val(data && data.vatNo ? data.vatNo : '');
        $('#referenceNumber').val(data && data.refNo ? data.refNo : '');
        if(data && data.delayPercent){
            $('#delayPercent').val(data.delayPercent);
        }
        if(data && data.deadlineDays){
            $('#deadlineDays').val(data.deadlineDays).trigger('change');
        }
    },
    addArticle: function(){
        var articleId = $('#add-article').val();
        if(articleId > 0){
            $.get('/application/invoice/add-article', {invoiceId: Invoice.invoiceId, articleId: articleId, locale: Common.locale}, function (data) {
                $('#invoice-rows').append(data);
                Invoice.setInvoiceTotalAmounts();
            });
        }
    },

    addEmptyRow: function(){
        $.get('/application/invoice/add-article', {invoiceId: Invoice.invoiceId, articleId: null, locale: Common.locale}, function (data) {
            $('#invoice-rows').append(data);
            Invoice.setInvoiceTotalAmounts();
        });
    },

    initDates: function(){
        var fromDate = $('#documentDate');
        var toDate = $('#deadlineDate');
        var datepickerParams = {
            inline: true,
            //nextText: '&rarr;',
            //prevText: '&larr;',
            showOtherMonths: true
            //dateFormat: 'dd MM yy',
            //showOn: "button",
            //buttonImage: "img/calendar-blue.png",
            //buttonImageOnly: true,
        };
        if(fromDate.length){
            fromDate.datepicker(datepickerParams);
        }
        if(toDate.length){
            toDate.datepicker(datepickerParams);
        }
    },

    initSelect2: function(){
        $("#client, #articleType, #add-category, #add-brand, #add-article").select2({
            language: Common.select2LanguageMapper[Common.language]
        });
    }
}
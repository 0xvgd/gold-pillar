

$(() => {
    $('.carousel.booking-carousel').carousel({
        interval: false,
    })
});
const baseUrl = $('#bath_path').val();
const loginUrl = $('#login_path').val();
const signUrl = $('#sign_path').val();
const checkUrl = $('#check_path').val();
const authUrl = $('#book_path').val();
//booking system
var app = angular.module('myApp', []);
app.controller('bookCtrl', function ($scope, $http, $compile) {

    $scope.currentStep = 'assigned';
    $scope.authType = '';
    $scope.bookingTimes = [];
    $scope.monthValue = '';
    $scope.bookingDateTxt = ''
    $scope.bookingMoveDates = [];
    $scope.bookingDateValue = '';
    $scope.bookingTimeValue = '';
    $scope.bookingMoveValue = '';
    $scope.bookingCount = 1;
    angular.element(document).ready(function () {
        let cItem = $('#bookCarousel .carousel-item.active')[0].dataset.value;
        $scope.$apply(function () {
            $scope.monthValue = cItem;
        })

    });

    $(document).on('click', '#bookCarousel .carousel-action', function () {
        let cItem = $('#bookCarousel .carousel-item.active')[0].dataset.value;
        $scope.$apply(function () {
            $scope.monthValue = cItem;
        })
    });

    $(document).on('click', '#bookCarousel .carousel-item.active .item>div', function () {
        let val = $(this)[0].dataset.value;
        let txt = $(this)[0].dataset.text;
        $('#bookCarousel .carousel-item .item>div').removeClass('active');
        $(this).addClass('active');
        $scope.$apply(function () {
            $scope.bookingDateValue = val;
            $scope.bookingDateTxt = txt;
            $scope.bookingTimeValue = '';
            $scope.bookingMoveValue = '';
        });
        $scope.getAvailableItems(val);
    });

    $(document).on('click', '#bookCarousel_hour .carousel-item.active .item:not(.disabled)', function () {
        let val = $(this)[0].dataset.value;
        $('#bookCarousel_hour .carousel-item .item').removeClass('active');
        $(this).addClass('active');
        $scope.$apply(function () {
            $scope.bookingTimeValue = val;
        });
    });

    $scope.getAvailableItems = function (val) {

        $http({
            method: 'get',
            url: `${baseUrl}/${val}`,
            dataType: 'json',
            headers: {"Content-Type": "application/json;charset=utf-8"}
        }).then(function successCallback(resp) {
            $scope.bookingTimes = resp.data.times;
            $scope.bookingMoveDates = resp.data.moves;
        });
    }

    $(document).on('click', '#bookCarousel_date .carousel-item.active .item>div', function () {
        let val = $(this)[0].dataset.value;
        $('#bookCarousel_date .carousel-item .item>div').removeClass('active');
        $(this).addClass('active');
        $scope.$apply(function () {
            $scope.bookingMoveValue = val;
        });
    });

    $scope.saveBookingAuth = function(){
        let param = $scope.getBookingInfo();
        $.post(authUrl,param,function (resp) {
            if(resp.result === true){
                alert('booking set successfully');
                window.location.href = '/';
            } else {
                alert('booking failed' + resp.result);
            }
        },'json')
    }

    //login setup
    $scope.setAuthType = function (val) {

        $scope.authType = val;
    }

    $scope.getBookingInfo = function () {
        if ($scope.bookingDateValue === '' ||
            $scope.bookingTimeValue === '' ||
            $scope.bookingMoveValue === '' ||
            $scope.bookingCount <= 0)
            return false;
        return {
            bookingDate: $scope.bookingDateValue, bookingTime: $scope.bookingTimeValue,
            bookingMove: $scope.bookingMoveValue, bookingCount: $scope.bookingCount
        }
    }

    $scope.bookingWithLogin = function () {
        let param = $scope.getBookingInfo();
        let email = $('#login_email').val();
        let password = $('#login_pass').val();
        if(param === false || email === '' || password === '')
            return false;
        param.email = email;
        param.password = password;
        $.post(loginUrl,param,function (resp) {
            if(resp.result === true){
                alert('booking set successfully');
                window.location.href = '/';
            } else {
                alert('booking failed' + resp.result);
            }
        },'json')


    }

    $scope.bookingSignCheck = function () {
        let forms = $('#sign_form_1 .form-control');
        forms.each(function (i,j) {
            if($(j).val() == ''){
                $(j).addClass('has-error');
            } else
                $(j).removeClass('has-error');
        });
        if($('#reg_pass').val().length < 6)
            $('#reg_pass').addClass('has-error');
        if($('#reg_pass').val() !== $('#reg_conf').val())
            $('#reg_conf').addClass('has-error');
        let valid = $('#sign_form_1 .form-control.has-error').length;
        if(valid > 0)
            return false;
        let email = $('#reg_email').val();
        $http({
            method: 'get',
            url: `${checkUrl}/${email}`,
            dataType: 'json',
            headers: {"Content-Type": "application/json;charset=utf-8"}
        }).then(function successCallback(resp) {
            if(resp.data.result === true)
                $scope.authType = 'location';
            $('#reg_email').addClass('has-error');
        });

    }

    $scope.bookingWithSign = function () {
        let forms = $('#sign_form_2 .form-control');
        forms.each(function (i,j) {
            if($(j).val() == ''){
                $(j).addClass('has-error');
            } else
                $(j).removeClass('has-error');
        });
        let valid = $('#sign_form_2 .form-control.has-error').length;
        if(valid > 0)
            return false;
        let param = $scope.getBookingInfo();
        param.name = $('#reg_name').val();
        param.email = $('#reg_email').val();
        param.phone = $('#reg_phone').val();
        param.password = $('#reg_pass').val();
        param.address1 = $('#registration_address_addressLine1').val();
        param.address2 = $('#registration_address_addressLine2').val();
        param.postcode = $('#registration_address_postcode').val();
        param.city = $('#registration_address_city').val();
        param.country = $('#registration_address_country').val();
        $.post(signUrl,param,function (resp) {
            if(resp.result === true){
                alert('booking set successfully');
                window.location.href = '/';
            } else {
                alert('booking failed' + resp.result);
            }
        },'json')
    }

});
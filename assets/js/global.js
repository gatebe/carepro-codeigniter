$(document).ready(function () {

    // $('#logs').DataTable();
    //Enable sidebar toggle


    /* Smooth Scroll to Top
    * ====================== */
    $("#totop").click(function () {
        $("html, body").animate({
            scrollTop: 0
        }, 300);
        return false;
    });
    //notices
    setTimeout(function () {
        $('#msg').slideUp('slow');
    }, 6000);

    //charges
    $('.pay-charge-form').hide();
    $('.pay-charge-btn').click(function () {
        $('.pay-charge-form').hide('fast');
        $(this).closest('tr').next('tr').show('fast');
    });

    //tooltips
    $('.send-mail,.show-pin,.show-tip').tooltip();

    // // add a hash to the URL when the user clicks on a tab
    $('a[data-toggle="tab"]').on('click', function (e) {
        history.pushState(null, null, $(this).attr('href'));
    });
    /*begin persistent tabs*/
    if (location.hash !== '') {
        $('a[href="' + location.hash + '"]').tab('show');
        return $('a[data-toggle="tab"]').on('shown', function (e) {
            return location.hash = $(e.target).attr('href').substr(1);
        });
    }
    /*end persistent tabs*/

    // $('[data-toggle="popover"]').popover();
    //Editors

    // $('#editor').trumbowyg();
    $('#attendances').DataTable({
        buttons: [
            'pdf'
        ]
    });
    $('#datatable').DataTable();
    $('#users').DataTable({
        buttons: [
            'pdf'
        ]
    });
    //lockscreen
    if (lockScreenTimer === undefined || lockScreenTimer === "")
        lockScreenTimer = 5;

    var lockTimer = 1320000 * lockScreenTimer;

    $('.lock-screen').click(function () {
        startLockscreen();
    });

    setTimeout(function () {
        startLockscreen();
    }, lockTimer);


    //news articles
    $('.news-sidebar>li>a').click(function () {
        var article_id = $(this).attr('id');
        var page = site_url + 'view_article/' + article_id;
        $('.news-article').html('loading <img src="../assets/img/loader.gif"/>').load(page);
    });

    //delete news article
    $('.del-article-btn').click(function () {
        var article_id = $(this).attr('id');
        if (confirm('Are you sure you want to delete this?')) {
            window.location.href = site_url + 'news/delete/' + article_id;
        }
    });


    $('.reportsBtn').popover({
        title: lang['reports'],
        html: true,
        placement: 'bottom',
        content: function () {
            return $('#daily-report').html();
        }
    });

    $('.delete').click(function (e) {

        e.preventDefault();

        var loc = $(this).attr('href');
        swal({
            title: lang['confirm_delete_title'],
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: lang['confirm_delete_btn'],
            closeOnConfirm: false,
            backdrop: false,
            allowOutsideClick: false
        }, function () {
            swal('processing...');
            if (loc !== undefined)
                window.location.href = loc;
        });
    });
});
/*jslint browser: true*/
/*global $, jQuery, alert*/

$(document).ready(function () {

    "use strict";

    $('.chat-list').slimScroll({
        height: '100%',
        position: 'right',
        size: "0px",
        color: '#dcdcdc',
        // scrollTo:$('.chat-list').scrollHeight
    });

    $('.chat-left-inner > .chatonline').slimScroll({
        height: '100%',
        position: 'right',
        size: "0px",
        color: '#dcdcdc',
        start: 'bottom',
    });
    $(function () {
        $(window).on("load", function () { // On load
            $('.chat-list').css({
                'height': (($(window).height()) - 450) + 'px'
            });
        });
        $(window).on("resize", function () { // On resize
            $('.chat-list').css({
                'height': (($(window).height()) - 450) + 'px'
            });
        });
    });

    // this is for the left-aside-fix in content area with scroll

    $(function () {
        $(window).on("load", function () { // On load
            $('.chat-left-inner').css({
                'height': (($(window).height()) - 240) + 'px'
            });
        });
        $(window).on("resize", function () { // On resize
            $('.chat-left-inner').css({
                'height': (($(window).height()) - 240) + 'px'
            });
        });
    });


    $(".open-panel").on("click", function () {
        $(".chat-left-aside").toggleClass("open-pnl");
        $(".open-panel i").toggleClass("ti-angle-left");
    });

    $("#newChatUser").on('keyup', function () {
        var user = $(this).val();

        if (user.length >= 3) {

            $.ajax({
                url: site_url + '/messaging/get_users',
                data: { user: user }, //$('form').serialize(),
                type: 'POST',
                success: function (response) {
                    var users = JSON.parse(response);
                    $('#newChatUsers').html('');

                    $.each(users, function (index, user) {
                        $('#newChatUsers').append('<li><a href="?m=' + user.id + '">' + user.name + '</a></li>')
                    })
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }
    })
    //child checkin
    $('.checkin-btn').click(function () {
        var child_id = $(this).attr('id');
        $('.modals-loader').load(site_url + 'child/checkInOut/' + child_id + '/checkin').modal('show');
    });
    $('.checkout-btn').click(function () {
        var child_id = $(this).attr('id');
        $('.modals-loader').load(site_url + 'child/checkInOut/' + child_id).modal('show');
    });
    $('.assign-parent-btn').click(function () {
        var id = $(this).attr('id');
        $('.modals-loader').load(site_url + '/parents/parents/' + id).modal('show')
    });
    $(".child-assign-room").click(function () {
        var child_id = $(this).data("child-id");
        $.ajax({
            type: 'POST',
            url: 'assign_room',
            dataType: 'json',
            ContentType: 'application/json; charset=utf-8',
            data: { 'child_id': child_id },
            success: function (data) {
                var rooms_length = data.all_rooms.length;
                var selected_length = data.selected_rooms.length;
                var room_ids = [];
                $("#child_id").val(child_id);
                if (rooms_length > 0) {
                    for (var i = 0; i < rooms_length; i++) {
                        var create_options = "<option value='" + data.all_rooms[i].id + "' class='form-control'>" + data.all_rooms[i].name + "</option>";
                        if ($('#assign_room option[value="' + data.all_rooms[i].id + '"]').length === 0) {
                            $("#assign_room").append(create_options);
                            $("#assign_room").selectpicker('refresh');
                        }
                    }
                }
                if (selected_length > 0) {
                    for (var i = 0; i < selected_length; i++) {
                        var id = data.selected_rooms[i].room_id;
                        room_ids.push(id);
                    }
                    $('#assign_room').selectpicker('val', room_ids);
                    $('#assign_room').selectpicker('refresh');
                }
            }
        })
    });
    $("#AssignRoomModal").on('hidden.bs.modal', function () {
        $("#assign_room").val('default');
        $("#assign_room").selectpicker("refresh");
    });
    new List('conversations', { valueNames: ['name'], page: 10, pagination: true });

    new List('checkedout-children',
        { valueNames: ['name', 'born', 'nid'], page: 10, pagination: true }
    );

    new List('room-staff', { valueNames: ['staffname'] });

    new List('room-children', { valueNames: ['childname'] });

    new List('room-notes', {
        valueNames: ['room-note', 'room-note-date'],
        page: 10,
        pagination: true
    });
    new List('parents', {
        valueNames: ['parent-name', 'child-name'],
        page: 10,
        pagination: true
    });
});

//meds
$(document).ready(function () {
    $('.adminMedModal').click(function () {
        var med_id = $(this).attr('data-medId');
        var modal = $('#medAdminModal');
        modal.find('input[name=med_id]').val(med_id);
        modal.find('.medName').text($(this).attr('data-name'));
        modal.find('.medNotes').text($(this).attr('data-desc'));
    });

    $('.medHistory').click(function () {
        var med_id = $(this).attr('id');

        $('#med-modal').load(site_url + 'meds/history/' + med_id, function () {
            $(this).find('.modal').modal('show');

            $('.deleteHistory').click(function (e) {
                e.preventDefault();

                var btn = $(this)
                var id = btn.attr('id');

                swal({
                    title: lang['confirm_delete_title'],
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#DD6B55',
                    confirmButtonText: lang['confirm_delete_btn'],
                    closeOnConfirm: false,
                    backdrop: false,
                    allowOutsideClick: false
                }, function () {
                    var url = site_url + 'meds/deleteHistory/' + id;
                    $.ajax({
                        url: url,
                        data: { id: id }, //$('form').serialize(),
                        type: 'POST',
                        success: function (response) {
                            swal({
                                type: 'success',
                                text: response.message
                            });
                            btn.closest('tr').remove();
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });
                });
            })
        });
    });

    $('#medImagesModalBtn').click(function () {
        $('#med-modal').load(site_url + 'meds/medImages', function () {
            $(this).find('.modal').modal('show')
        });
    });

    $('.newMedModal').click(function () {
        var id = $(this).attr('id');
        $('#med-modal').load(site_url + 'meds/newMedModal', function () {
            $(this).find('input[name=child_id]').val(id);
            $(this).find('.modal').modal('show')
        });
    });

    $('.delete-med').click(function (e) {
        e.preventDefault();
        var url = site_url + 'meds/destroy/' + $(this).attr('id');
        if (confirm('Are you sure?'))
            window.location.href = url;
    });
    $(".pay_button").click(function () {
        var due_amount = $(this).data("due-amount");
        $("#invoice_amount").val(due_amount);
    });
    var $form = $(".require-validation");
    $('form.require-validation').bind('submit', function (e) {
        var $form = $(".require-validation"),
            inputSelector = ['input[type=email]', 'input[type=password]',
                'input[type=text]', 'input[type=file]',
                'textarea'
            ].join(', '),
            $inputs = $form.find('.required').find(inputSelector),
            $errorMessage = $form.find('div.error'),
            valid = true;
        $errorMessage.addClass('d-none');

        $('.has-error').removeClass('has-error');
        $inputs.each(function (i, el) {
            var $input = $(el);
            if ($input.val() === '') {
                $input.parent().addClass('has-error');
                $errorMessage.removeClass('d-none');
                e.preventDefault();
            } else {
                $('.loading_div').show();
            }
        });

        if (!$form.data('cc-on-file')) {
            e.preventDefault();
            Stripe.setPublishableKey($form.data('stripe-publishable-key'));
            Stripe.createToken({
                number: $('.card-number').val(),
                cvc: $('.card-cvc').val(),
                exp_month: $('.card-expiry-month').val(),
                exp_year: $('.card-expiry-year').val()
            }, stripeResponseHandler);
        }

    });

    function stripeResponseHandler(status, response) {
        if (response.error) {
            $('.error')
                .removeClass('d-none')
                .find('.alert')
                .text(response.error.message);
        } else {
            var token = response['id'];
            $form.find('input[type=text]').empty();
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
            $form.get(0).submit();
        }
    }
    $('#logs').DataTable({
        order: [[0, 'desc']],
    });
    // var table = $('#logs').DataTable( {
    //     processing: true,
    //     serverSide: true,
    //     dataType : 'json',
    //    "ajax": {
    //     url: "settings/logs",
    //     type: "POST",
    //     columns: [
    //         { "data": "id" },
    //         { "data": "user_id" },
    //         { "data": "daycare_id" },
    //         { "data": "event" },
    //         { "data": "date" },
    //     ]
    //    }
    //  });    
    $(".manager_status").click(function () {
        $(".error_msg").text("");
        $(".error_msg").css("display", "block").addClass('alert alert-danger alert-dismissable');
        $(".error_msg").append('<span class="fa fa-info"></span>Access denied');

        setTimeout(function () {
            $('.error_msg').slideUp('slow');
        }, 6000);
    });
})

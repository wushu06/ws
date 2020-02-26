jQuery(document).ready(function($) {

    $('#datetimepicker1, #datetimepicker2, #datetimepickerA').datetimepicker({
        format:'d-m-Y H:i:s',

    });

    runOrderAjax('#showRange');
    runOrderAjax('#page');
    runOrderAjax('#showRangeSftp', true);
    function runOrderAjax(id, sftp = false) {
        $(id).live('click', function (ev) {

            ev.preventDefault();
            var url = $(this).data('url');
            var after = $('#datetimepicker1').val()
            var before = $('#datetimepicker2').val()
            var page = $(this).data('page')
            $.ajax({
                url:url,
                data:{sftp: sftp, page: page, after: after, before: before, action: 'orderajaxrange'}, // form data
                type:'POST', // POST
                beforeSend:function(xhr){
                    $('#loadRange').html('Loading...');
                },
                error:function (e) {
                    $('#loadRange').empty();
                },
                success:function(data){
                    console.log(data);
                    $('#loadRange').empty().html(data);
                }
            });
            return;
        });
    }

    $(".custom-invoice").live("click", function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var urla = $(this).data('urla');
        var all = $(this).data('all');
        var id = $(this).data('id');
        var printWindow = window.open('', '', 'height=700,width=1200');

        $.ajax({
            url:urla,
            data:{post_id: id, all: all, action: 'invoiceprint'}, // form data
            type:'POST', // POST

            error:function (data) {
                console.log('ERROR');
            },
            success:function(data){
                printWindow.document.write(data);
                console.log(data);
                printWindow.document.close();
                printWindow.print();


            }
        });
        return;

    });
});
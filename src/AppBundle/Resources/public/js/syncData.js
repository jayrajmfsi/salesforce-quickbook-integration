import moment from 'moment';
$(document).ready(function() {
    var start = moment().subtract(29, 'days');
    var end = moment();


    function cb(startDate, endDate) {
        start = startDate;
        end = endDate;
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        showDropdowns: true,
        endDate: end,
        minYear: 2010,
        maxYear: 2050,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

    $(".sync-data-form").on('submit', function (event) {
        console.log(start.format('YYYY-MM-DD'));
        event.preventDefault();
        event.stopPropagation();
        $.ajax({
            url: '/fetch-salesforce-contacts',
            type: 'post',
            beforeSend: function(request) {
                request.setRequestHeader("Authorization", 'Oauth ' + localStorage.getItem('sf_refresh_token'));
            },
            dataType: 'json',
            contentType: 'application/json',
            success: function (data) {
                if (!data.sf_ids) {
                    Swal.fire({
                        text: 'No Records Found in the provided date range',
                        type: 'info',
                        timer: 2000,
                        position: "top"
                    });
                } else if (data.reasonCode === '0') {
                    let sfIds = data.sf_ids;
                    Swal.fire({
                        text: 'Customers Fetched from Salesforce Successfully.',
                        type: 'success',
                        timer: 1500,
                        position: "top"
                    });
                    window.location.href = "/update-quickbooks-contacts?update=1&sf_ids="+ sfIds;
                } else {
                    Swal.fire({
                     text: data.error.text,
                     type: 'error',
                     position: "top"
                    });
                }
            },
            data: JSON.stringify({ fromDate: start.format('YYYY-MM-DD'), toDate: end.format('YYYY-MM-DD') })
        });
    });
});

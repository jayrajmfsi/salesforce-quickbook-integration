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
            url: '/app_dev.php/fetch-salesforce-contacts',
            type: 'post',
            beforeSend: function(request) {
                request.setRequestHeader("Authorization", 'Oauth ' + localStorage.getItem('sf_refresh_token'));
            },
            dataType: 'json',
            contentType: 'application/json',
            success: function (data) {
                if (!data.sf_ids) {
                    alert('No Records Found in the provided date range');
                } else if (data.reasonCode === '0') {
                    let sfIds = data.sf_ids;
                    alert("Customers Fetched from Salesforce Successfully.");
                    window.location.href = "/app_dev.php/update-quickbooks-contacts?update=1&sf_ids="+ sfIds;
                } else {
                    alert(data.error.text);
                }
            },
            data: JSON.stringify({ fromDate: start.format('YYYY-MM-DD'), toDate: end.format('YYYY-MM-DD') })
        });
    });
});

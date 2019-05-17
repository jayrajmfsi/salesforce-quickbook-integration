import moment from 'moment';
$(document).ready(function() {
    $('input[name="show-daterange"]').daterangepicker({
        opens: 'right',
        startDate: moment(),
        showDropdowns: true,
        minYear: 2010,
        maxYear: 2050,
        endDate: moment().add(1, 'years')
    }, function(start, end, label) {
        console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        $(".sync-data-form").on('submit', function (event) {
            console.log('right');
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
                    if (data.reasonCode === '0') {
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

});

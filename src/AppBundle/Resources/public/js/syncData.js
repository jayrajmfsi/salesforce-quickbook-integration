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
    });
    $("#sync-data").click(function () {
        $.ajax({
            url: 'sync-data',
            type: 'post',
            dataType: 'json',
            contentType: 'application/json',
            success: function (data) {
                if (data.reasonCode === '0') {
                    alert('Sync Done Successfully');
                } else {
                    alert(data.error.text);
                }
            },
            data: JSON.stringify({ name: 'jka', age: 24 })
        });
    });
});

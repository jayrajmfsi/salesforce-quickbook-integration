$(document).ready(function() {
    $("#quickbooks-connect").click(function () {
        console.log('in');
        $.ajax({
            url: 'connect-to-quickbooks',
            type: 'post',
            dataType: 'json',
            contentType: 'application/json',
            success: function (data) {
                if (data.reasonCode === '0') {
                    window.location.href = '/app_dev.php/sync-data';
                    console.log(window.location.href);
                } else {
                    alert(data.error.text);
                }
            },
            data: JSON.stringify({ name: 'jka', age: 24 })
        });
    });
});

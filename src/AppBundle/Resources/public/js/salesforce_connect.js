$(document).ready(function() {
    $("#salesforce-connect").click(function () {
        $.ajax({
            url: 'connect-to-salesforce',
            type: 'post',
            dataType: 'json',
            contentType: 'application/json',
            success: function (data) {
                if (data.reasonCode === '0') {
                    window.location.href = '/app_dev.php/quickbooks-connect';
                } else {
                    alert(data.error.text);
                }
            },
            data: JSON.stringify({ name: 'jka', age: 24 })
        });
    });
});

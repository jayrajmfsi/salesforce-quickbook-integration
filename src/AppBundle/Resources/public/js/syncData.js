$(document).ready(function() {
    $("#sync-data").click(function () {
        console.log('in');
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

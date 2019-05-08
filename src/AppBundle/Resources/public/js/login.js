(function() {
    "use strict";
    window.addEventListener("load", function() {
        $("#needs-validation").on("submit",function(event) {
            $("#needs-validation").addClass("was-validated");
            event.preventDefault();
            event.stopPropagation();
            let username = $("#needs-validation").find('input[name="username"]').val();
            let password = $("#needs-validation").find('input[name="password"]').val();
            var person = {
                username,
                password,
            };

            $.ajax({
                url: 'check-credentials',
                type: 'post',
                dataType: 'json',
                contentType: 'application/json',
                success: function (data) {
                    if (data.reasonCode === '0') {
                        window.location.href = '/app_dev.php/';
                    } else {
                        alert(data.error.text);
                    }
                },
                data: JSON.stringify(person)
            });
        });
    }, false);
}());

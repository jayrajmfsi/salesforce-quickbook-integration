$(document).ready(function() {
    let form  = $("#needs-validation");
    form.validate({
        errorElement: 'div',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            error.insertAfter(element);
        },
        rules: {
            username: {
              required: true,
              minlength: 3
            },
            password: {
                required: true,
                minlength: 5
            }
        },
        messages: {
            username: {
                required: "Please provide an username",
                minlength: "Your password must be atleast 3 characters long."
            },
            password: {
                required: "Please provide a password",
                minlength: "Your password must be atleast 5 characters long."
            },
        }
    });

    form.on("submit",function(event) {
        event.preventDefault();
        event.stopPropagation();
        if (form.valid() !== false) {
            form.addClass("was-validated");
            let username = form.find('input[name="username"]').val();
            let password = form.find('input[name="password"]').val();
            let user = {
                UserRequest: {
                    username,
                    password,
                }
            };

            $.ajax({
                url: 'check-credentials',
                type: 'post',
                dataType: 'json',
                contentType: 'application/json',
                success: function (data) {
                    if (data.reasonCode === '0') {
                        localStorage.setItem('sf_oauth_data', JSON.stringify(data.UserResponse));
                        window.location.href = '/app_dev.php/salesforce-connect';
                    } else {
                        alert(data.reasonText);
                    }
                },
                error: function(xhr, status, error) {
                    var errorResult = JSON.parse(xhr.responseText);
                    alert(errorResult.Response.error.text);
                },
                data: JSON.stringify(user)
            });
        }
    });
});

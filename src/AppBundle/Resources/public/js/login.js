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
                url: 'api-check-credentials',
                type: 'post',
                dataType: 'json',
                contentType: 'application/json',
                success: function (data) {
                    if (data.reasonCode === '0') {
                        localStorage.setItem('oauth_credentials', JSON.stringify(data.UserResponse));
                        window.location.href = '/salesforce-connect';
                    } else {
                        Swal.fire({
                            text: data.reasonText,
                            type: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    var errorResult = JSON.parse(xhr.responseText);
                    Swal.fire({
                        text: errorResult.Response.error.text,
                        type: 'error'
                    });
                },
                data: JSON.stringify(user)
            });
        }
    });
});

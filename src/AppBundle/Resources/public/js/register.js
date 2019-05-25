$(document).ready(function() {

    let form  = $("#needs-validation-for-register");
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
            },
            confirmPassword: {
                required: true,
                minlength: 5,
                equalTo: "#password"
            },
            email: {
                required: true,
                email: true
            },
            sf_account_id: "required",
            sf_client_id: "required",
            sf_client_secret: "required",
            sf_redirect_uri: {
                required: true,
            },
            qb_client_id: "required",
            qb_client_secret: "required",
            qb_redirect_uri: {
                required: true,
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
            email: {
                required: "Please provide an email.",
                email: "Email id format is incorrect."
            },
            confirmPassword: {
                required: "Please provide the password again",
                minlength: "Your confirm password field must be atleast 5 characters long.",
                equalTo: "Passwords do not match"
            },
            sf_account_id: "Please provide a valid SF account id.",
            sf_client_id: "Please provide a valid SF Client Id.",
            sf_client_secret: "Please provide a valid SF Client Secret.",
            sf_redirect_uri: {
                username: "Please provide a valid SF Redirect URI.",
            },
            qb_client_id: "Please provide a valid QB Client Id.",
            qb_client_secret: "Please provide a valid QB Client Secret.",
            qb_redirect_uri: {
                username: "Please provide a valid QB Redirect URI.",
            }
        }
    });

    form.on("submit",function(event) {
        event.preventDefault();
        event.stopPropagation();
        if (form.valid() !== false) {
            form.addClass("was-validated");
            let username = form.find('input[name="username"]').val();
            let password = form.find('input[name="password"]').val();
            let email_id = form.find('input[name="email"]').val();
            let confirm_password = form.find('input[name="confirmPassword"]').val();
            let sf_account_id = form.find('input[name="sf_account_id"]').val();
            let sf_client_id = form.find('input[name="sf_client_id"]').val();
            let sf_client_secret = form.find('input[name="sf_client_secret"]').val();
            let sf_redirect_uri = form.find('input[name="sf_redirect_uri"]').val();
            let qb_client_id = form.find('input[name="qb_client_id"]').val();
            let qb_client_secret = form.find('input[name="qb_client_secret"]').val();
            let qb_redirect_uri = form.find('input[name="qb_redirect_uri"]').val();
            let user = {
                UserRequest: {
                    username,
                    password,
                    confirm_password,
                    email_id,
                    sf_account_id,
                    sf_client_id,
                    sf_client_secret,
                    sf_redirect_uri,
                    qb_client_id,
                    qb_client_secret,
                    qb_redirect_uri
                }
            };

            $.ajax({
                url: 'register',
                type: 'post',
                dataType: 'json',
                contentType: 'application/json',
                success: function (data) {
                    if (data.reasonCode === '0') {
                        window.location.href = "/login";
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

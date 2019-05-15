$(document).ready(function() {
    $("#salesforce-connect").on('click', function () {
        console.log('11111');
        oauth = JSON.parse(localStorage.getItem('oauth_credentials'));
        client_id = oauth.sf_oauth.client_id;
        client_secret = oauth.sf_oauth.client_secret;
        redirect_uri = oauth.sf_oauth.redirect_uri;

        window.location.href = `/app_dev.php/salesforce-connect-action?client_id=${client_id}&client_secret=${client_secret}&redirect_uri=${redirect_uri}`;
    });
});

$(document).ready(function() {
    $("#quickbooks-connect").click(function () {
        console.log('11111');
        oauth = JSON.parse(localStorage.getItem('oauth_credentials'));
        client_id = oauth.qb_oauth.client_id;
        client_secret = oauth.qb_oauth.client_secret;
        redirect_uri = oauth.qb_oauth.redirect_uri;

        window.location.href = `/app_dev.php/quickbooks-connect-action?client_id=${client_id}&client_secret=${client_secret}&redirect_uri=${redirect_uri}`;
    });
});

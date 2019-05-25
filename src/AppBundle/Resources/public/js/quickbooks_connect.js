$(document).ready(function() {
    $("#quickbooks-connect").click(function () {
        let oauth = JSON.parse(localStorage.getItem('oauth_credentials'));
        let client_id = oauth.qb_oauth.client_id;
        let client_secret = oauth.qb_oauth.client_secret;
        let redirect_uri = oauth.qb_oauth.redirect_uri;

        window.location.href = `/quickbooks-connect-action?client_id=${client_id}&client_secret=${client_secret}&redirect_uri=${redirect_uri}`;
    });
});

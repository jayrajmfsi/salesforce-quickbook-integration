$(document).ready(function() {
    $("#salesforce-connect").on('click', function () {
        let oauth = JSON.parse(localStorage.getItem('oauth_credentials'));
        let client_id = oauth.sf_oauth.client_id;
        let client_secret = oauth.sf_oauth.client_secret;
        let redirect_uri = oauth.sf_oauth.redirect_uri;
        window.location.href = `/salesforce-connect-action?client_id=${client_id}&client_secret=${client_secret}&redirect_uri=${redirect_uri}`;
    });
});

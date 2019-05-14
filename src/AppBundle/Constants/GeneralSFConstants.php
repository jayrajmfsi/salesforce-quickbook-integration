<?php


namespace AppBundle\Constants;


class GeneralSFConstants
{
    const GrantType = 'authorization_code';
    const RefreshType = 'refresh_token';
    const SF_AUTH_URI = 'https://login.salesforce.com/services/oauth2/token';
    const REQUEST_AUTHORIZATION_TYPE = 'Oauth';
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_URL_ENCODED = 'application/x-www-form-urlencoded';
    const NEW_TOKEN = 'NewAccessToken';
}
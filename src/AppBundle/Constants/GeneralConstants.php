<?php
/**
 *  General Constants
 *  @category Constants
 *  @author Jayraj Arora<jayraja@mindfiresolutions.com>
 */
namespace AppBundle\Constants;

class GeneralConstants
{
    const GRANT_TYPE = 'authorization_code';
    const REFRESH_TYPE = 'refresh_token';
    const SF_AUTH_URI = 'https://login.salesforce.com/services/oauth2/token';
    const REQUEST_AUTHORIZATION_TYPE = 'Oauth';
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_URL_ENCODED = 'application/x-www-form-urlencoded';
    const NEW_TOKEN = 'NewAccessToken';
    const OAUTH_MODE = 'oauth2';
    const DUMMY_REFRESH_TOKEN = 'abcdef';
    const QUICKBOOKS_SUCCESS_MESSAGE = 'Connected to Quickbooks Successfully';
    const SALESFORCE_SUCCESS_MESSAGE = 'Connected to Salesforce Successfully';
    const ACCOUNT_CREATE_SUCCESS_MESSAGE = 'Account Created Successfully. Kindly Login.';
}

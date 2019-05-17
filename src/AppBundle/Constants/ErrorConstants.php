<?php

namespace AppBundle\Constants;


class ErrorConstants
{
    const CLIENT_NOT_FOUND = 'Account not registered';
    const BAD_CONNECT_REQUEST = 'Client details not found';
    const BAD_FETCH_HEADERS = 'INVALIDHEADERS';
    const CONTENT_NOT_FOUND = 'CONTENTNOTFOUND';
    const UNAUTHORIZED_MESSAGE = 'Session expired or invalid';
    const UNAUTHORIZED_CODE = 'INVALID_SESSION_ID';
    const INVALID_AUTHORIZATION = 'INVALIDAUTHORIZATION';
    const DISABLEDUSER = 'DISABLEDUSER';
    const INVALID_CONFIRM_PASS = 'INVALIDCONFIRMPASSWORD';
    const USERNAME_EXISTS = 'USERNAMEPREEXIST';
    const EMAIL_EXISTS = 'EMAILPREEXISTS';
    const INVALID_EMAIL_FORMAT = 'INVALIDEMAILFORMAT';
    const INVALID_USERNAME = 'INVALIDUSERNAME';
    const INVALID_REQ_DATA = 'INVALIDREQDATA';
    const INVALID_CONTENT_TYPE = 'INVALIDCONTENTTYPE';
    const INVALID_CONTENT_LENGTH = 'INVALIDCONTENTLEN';
    const METHOD_NOT_ALLOWED = 'METHODNOTALLOWED';
    const REQ_TIME_OUT = 'REQTIMEOUT';
    const SERVICE_UNAVAIL = 'SERVICEUNAVAIL';
    const INTERNAL_ERR = 'INTERNALERR';
    const INVALID_CRED = 'INVALIDCRED';
    const RESOURCE_NOT_FOUND = 'NORESOURCEFOUND';
    const GATEWAY_TIMEOUT = 'GATEWAYTIMEOUT';
    const INVALID_NEW_PASS_FORMAT = 'INVALIDNEWPASSFORMAT';

    public static $errorCodeMap = [
        self::INVALID_AUTHORIZATION => ['code' => '403', 'message' => 'api.response.error.request_unauthorized'],
        self::RESOURCE_NOT_FOUND => ['code' => '404', 'message' => 'api.response.error.resource_not_found'],
        self::METHOD_NOT_ALLOWED => ['code' => '405', 'message' => 'api.response.error.request_method_not_allowed'],
        self::REQ_TIME_OUT => ['code' => '408', 'message' => 'api.response.error.request_timed_out'],
        self::INTERNAL_ERR => ['code' => '500', 'message' => 'api.response.error.internal_error'],
        self::SERVICE_UNAVAIL => ['code' => '503', 'message' => 'api.response.error.service_unavailable'],
        self::GATEWAY_TIMEOUT => ['code' => '504', 'message' => 'api.response.error.gateway_timeout'],
        self::INVALID_REQ_DATA => ['code' => '1002', 'message' => 'api.response.error.invalid_request_data'],
        self::INVALID_CONTENT_TYPE => ['code' => '1005', 'message' => 'api.response.error.invalid_content_type'],
        self::INVALID_CONTENT_LENGTH => ['code' => '1006', 'message' => 'api.response.error.invalid_content_length'],
        self::INVALID_EMAIL_FORMAT => ['code' => '1012', 'message' => 'api.response.error.invalid_email_data'],
        self::INVALID_USERNAME => ['code' => '1013', 'message' => 'api.response.error.invalid_username'],
        self::USERNAME_EXISTS => ['code' => '1017', 'message' => 'api.response.error.username_exists'],
        self::INVALID_CRED => ['code' => '1014', 'message' => 'api.response.error.invalid_credentials'],
        self::EMAIL_EXISTS => ['code' => '1018', 'message' => 'api.response.error.email_exists'],
        self::DISABLEDUSER => ['code' => '1020', 'message' => 'api.response.error.disabled_user'],
        self::INVALID_CONFIRM_PASS => ['code' => '1021', 'message' => 'api.response.error.invalid_confirm_password'],
        self::INVALID_NEW_PASS_FORMAT =>
            ['code' => '1019', 'message' => 'api.response.error.invalid_newpass_format'],
        self::UNAUTHORIZED_CODE => [
            'code' => '1020', 'message' => 'api.response.salesforce.failure.unauthorized'
        ],
        self::BAD_FETCH_HEADERS => [
            'code' => '1021', 'message' => 'api.response.failure.bad_headers'
        ],
        self::CONTENT_NOT_FOUND => [
            'code' => '1022', 'message' => 'api.response.error.content_not_found'
        ]
    ];
}

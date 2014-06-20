<?php
namespace api\helpers;

/**
 * Enumaration for the API response codes.
 */
class ErrorCode
{
    /**
     * Response codes for the API.
     */
    const ERROR_CODE_SUCCESS    = 1000;
    const ERROR_CODE_VALIDATION = 1100;
    const ERROR_CODE_UNKNOWN    = 1900;
}

<?php
namespace api\rest;

use api\helpers\ErrorCode;

/**
 * Api serializer
 */
class Serializer extends \yii\rest\Serializer
{
    /**
     * Ensures that the response is an error containing a 'response' and 'error'
     * message.
     */
    public function serialize($data)
    {
        if (!is_array($data) || !isset($data['response']) || !isset($data['error'])) {
            $data = [
                'response' => parent::serialize($data),
                'error' => ['code' => ErrorCode::ERROR_CODE_SUCCESS, 'status' => ''],
            ];

            return $data;
        } else {
            $data['response'] = parent::serialize($data['response']);
        }

        return $data;
    }
}

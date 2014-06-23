<?php
namespace api\rest;

use api\helpers\ErrorCode;

/**
 * Api serializer
 */
class Serializer extends \yii\rest\Serializer
{
    /**
     * Ensures that the result is an error containing a 'result' and 'error'
     * message.
     */
    public function serialize($data)
    {
        if (!is_array($data) || !isset($data['result']) || !isset($data['error'])) {
            $data = [
                'result' => parent::serialize($data),
                'error' => ['code' => ErrorCode::ERROR_CODE_SUCCESS, 'message' => ''],
            ];

            return $data;
        } else {
            $data['result'] = parent::serialize($data['result']);
        }

        return $data;
    }
}

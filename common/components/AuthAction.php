<?php
namespace common\components;

use Yii;
use yii\helpers\Url;
use common\components\LibreIdAuthClient;

class AuthAction extends \yii\authclient\AuthAction
{
    /**
     * @inheritdoc
     */
    protected function auth($client)
    {
        if ($client instanceof LibreIdAuthClient) {
            return $this->authLibreId($client);
        } else {
            return parent::auth($client);
        }
    }

    /**
     * Performs LibreID auth flow.
     * @param LibreIdAuthClient $client auth client instance.
     * @return Response action response.
     * @throws Exception on failure.
     * @throws HttpException on failure.
     */
    protected function authLibreId($client)
    {
        if (isset($_POST['response'])) {
            //$this->authSuccess($client);
            $response = Yii::$app->libreidapi->validate_and_decrypt($_POST['response']);
        	var_dump($response);
            $response_decoded = json_decode($response);
            var_dump($response_decoded);
		//	var_dump(Yii::$app->libreidapi->getloginstatus($response_decoded["data"]["ticket"]));
        //    var_dump(Yii::$app->libreidapi->getdata($response_decoded["data"]["ticket"], 'first_name,last_name')); die;
        } else {
            $returnUrl = Url::to(['', $this->clientIdGetParamName => $_GET[$this->clientIdGetParamName], 'login' => 1], 'https');
            $authUrl = $client->buildAuthUrl($returnUrl);
            $this->controller->redirect($authUrl);
        }

        //
        /*if (!empty($_REQUEST['openid_mode'])) {
            switch ($_REQUEST['openid_mode']) {
                case 'id_res':
                    if ($client->validate()) {
                        return $this->authSuccess($client);
                    } else {
                        throw new HttpException(400, 'Unable to complete the authentication because the required data was not received.');
                    }
                    break;
                case 'cancel':
                    $this->redirectCancel();
                    break;
                default:
                    throw new HttpException(400);
                    break;
            }
        } else {
            $url = $client->buildAuthUrl();
            return Yii::$app->getResponse()->redirect($url);
        }

        return $this->redirectCancel();*/
    }
}

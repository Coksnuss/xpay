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

        //$_GET['message'] = 'AUOtR6JuYO5Tk9I2s5w5GcNVGDZxEuMcYhZgER4QyChkLkT7t5QQvGMr5cPPEdOIiYlNCawj83LbCgB2htnGtiZcaieWfcN1fnygsx1AvoH/yxWHVsr2f48FYZAw7WXxW9bipdplR9t2h8dqPD4Xwy5Pi3X8Cobfjhftcc9ySedeQjdqkF2tn27d2aR5AvKknElR52BzRqGRKctVId2fuY2fgbia3/0sESZEN2LRT6XBuCSsulg/6JU/KfghdjSfp0ra1jZjpJsXjqu6m65i/OE8gWCk4qbodUTtwW5w1RyfaHkleGcFoWKbqRtcg+ZZd3Py72k5Y5n2FEOOV2FPSiScTQrLZxA0wkF1KeWLSjRj2yd3v7tuTk6TP2arlla3CkGgMupa5Aa7l/PukZEH0SLK/XkpN9Dvt2Wm/nkwJPoB4cA6VVcP6AIWsb5GXa5W+JOsuKK4sd+VCfLiEf68XdxMF7HWnqdIBL63Fbi3nM10iHNt4gcusg6Pc6OcR9lHsuIxTNn7X1k25L1krBJLU7wfYWMO71e0BBs7xTe3xsa3oNC0kQTQSPzVt0YW8FTafzQPXfOmpV5fHQFazzcyamxiDPEZSbIkwLhtusLaalUx8uRu4n2iJGNdb/ZUKw73cEyqd/ti2c8TprK3l83tP3I=';
        if (isset($_GET['message'])) {
            //$this->authSuccess($client);
            var_dump($_POST);
            var_dump(Yii::$app->libreidapi->getdata($_GET['message'], 'first_name,last_name'));
            die;
        } else {
            $returnUrl = Url::to(['', $this->clientIdGetParamName => $_GET[$this->clientIdGetParamName], 'login' => 1], 'https');
           // $returnUrl = str_replace('xpay.yii.dev', 'xpay.wsp.lab.sit.cased.de', $returnUrl);
           // $returnUrl = str_replace('site/auth', 'site/test', $returnUrl);
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

<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\BaseUrl;
use common\models\User;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($action->id === 'libre-id-login') {
            $this->enableCsrfValidation = false;
        }

       return parent::beforeAction($action);
    }

    /**
     * TODO: DOC
     */
    public function actionLibreIdLogin()
    {
    	if (!\Yii::$app->user->isGuest) {
    		return $this->goHome();
    	}
    	
    	if(isset($_POST['response'])) {
    		$response = json_decode(Yii::$app->libreidapi->validate_and_decrypt($_POST['response']), true);
    		if(isset($response) && $response["status"] == "success") {
    			$data = Yii::$app->libreidapi->getdata($response["data"]["ticket"],'email_address,first_name,last_name');
    			if(isset($data) && $data["status"] == "success") {
    				$model = new LoginForm();
    				$model->email = $data["data"]["email_address"];
    				if($model->login(false,true)) {
    					return $this->goHome();
    				} else {
    					//signup
    					$signupForm = new SignupForm();
    					$signupForm->email = $data["data"]["email_address"];
						$signupForm->firstName = $data["data"]["first_name"];
						$signupForm->lastName = $data["data"]["last_name"];
    					$signupForm->password = Yii::$app->security->generateRandomKey(10);
    					if ($user = $signupForm->signup()) {
    						if (Yii::$app->getUser()->login($user)) {
    							//signup successful, send email for fallback password reset
    							$user->generatePasswordResetToken();
    							if ($user->save()) {
    								$emailSend = \Yii::$app->mail->compose('setFallbackPassword', ['user' => $user])
    								->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
    								->setTo($model->email)
    								->setSubject('Password reset for ' . \Yii::$app->name)
    								->send();
									if($emailSend) {
										return $this->goHome();
									} else {
										throw new BadRequestHttpException('Error sending email for setting fallback password. Please use the <a href="/site/request-password-reset">reset function</a>.');
									}
    							} else {
    								throw new BadRequestHttpException('Error sending email for setting fallback password. Please use the <a href="/site/request-password-reset">reset function</a>.');
								}	
    						} else {
    							throw new BadRequestHttpException('Error logging user in after signing up. Please try again or came back later.');
    						}
    					} else {
						throw new BadRequestHttpException('Error signing up user. Login failed. Please try again or came back later.');
						}
    				}
    			} else {
    				throw new BadRequestHttpException('Error getting user information from LibreID. Login failed. Please try again or came back later.');
    			}
    		} else {
    			throw new BadRequestHttpException('Error processing response from LibreID. Login failed. Please try again or came back later.');
    		}
    	} else {
		$url = BaseUrl::base('https');
	        return $this->render('libre-login', [
	            'message' => Yii::$app->libreidapi->get_login_message($url."/site/libre-id-login/"),
	            'returnUrl' => $url."/site/libre-id-login/",
	        ]);
    	}
    }
    
    

    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        $model = User::findOne(['id'=>Yii::$app->user->identity->id]);
        $model->last_login_time = date('Y-m-d H:i:s');
        $model->last_login_ip = Yii::$app->request->userIP;
        $model->save(false);
    	Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}

<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true]],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['logout' => ['post']],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => ['class' => \yii\web\ErrorAction::class],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Homepage
     */
    public function actionIndex()
    {
        $path = Yii::$app->request->pathinfo;
        $state = $this->getAppInitialState();
        $this->registerJsAppDefaults($path, $state);

        return $this->render('index');
    }

    /**
     * Sign up redirect with token
     */
    public function actionSignupCheck($token)
    {
        if ($token !== Yii::$app->params['signupToken']) {
            return $this->redirect(['/404']);
        }

        // uloží cookie pro signupEnabled, přežije 2 requesty
        $this->multiFlashSet('signupEnabled', true, 1);

        return $this->redirect(['/sign-up']);
    }

    /**
     * Registrace JS appConfig pro frontend
     */
    private function registerJsAppDefaults($path, $state)
    {
        $appConfig = [
            'baseUrl' => Url::to('/'),
            'apiBaseUrl' => Url::to('api/'),
            'currentPath' => $path,
            'appName' => Yii::$app->name,
            'state' => $state,
            'debug' => YII_ENV_DEV ? true : false,
            'flashData' => [
                'signupEnabled' => $this->multiFlashGet('signupEnabled'),
            ],
        ];

        $appConfigJson = json_encode($appConfig, JSON_UNESCAPED_UNICODE);

        $this->getView()->registerJs("
            window.appConfig = $appConfigJson;
        ", \yii\web\View::POS_HEAD);
    }

    /**
     * Inicializace počátečního stavu pro React app
     */
    private function getAppInitialState()
    {
        $state = [
            'account' => [
                'loginStatus' => false,
                'profile' => null,
                'wallet' => null,
            ],
        ];

        $user = !Yii::$app->user->isGuest ? User::getCurrent() : null;
        if ($user && $user->isPublicUser()) {
            $state['account']['loginStatus'] = true;
            $state['account']['profile'] = $user->getProfile();
            $state['account']['wallet'] = $user->getWallet();
        }

        return $state;
    }

    // =============================
    // Cookie-based Multi-Flash
    // =============================
    private function multiFlashSet(string $key, $value, int $count = 1)
    {
        $data = json_encode(['value' => $value, 'count' => $count]);
        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => $key,
            'value' => $data,
            'httpOnly' => false, // dostupné pro frontend JS
            'path' => '/',
        ]));
    }

    private function multiFlashGet(string $key)
    {
        $cookies = Yii::$app->request->cookies;
        if (!$cookies->has($key)) {
            return null;
        }

        $data = json_decode($cookies->getValue($key), true);
        if (!$data) {
            return null;
        }

        $value = $data['value'];
        $data['count']--;

        if ($data['count'] <= 0) {
            Yii::$app->response->cookies->remove($key);
        } else {
            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => $key,
                'value' => json_encode($data),
                'httpOnly' => false,
                'path' => '/',
            ]));
        }

        return $value;
    }
}

<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

class ApiController extends CController {

    /* It is detailed error codes, as http status codes can not cover error type detailed. Need for api client
       developer for example to know what exactly to do
     */

    const ERR_AUTH_USERNAME        = 1001;
    const ERR_NOT_FOUND_VARIANT    = 1002;
    const ERR_VALIDATION           = 1003;
    const ERR_UNDEFINED            = 1004;

    private $_contentType = 'application/json';

    /* Error explanations needed for api client developer to see also description of error */

    private static $_errMessages = [
        self::ERR_AUTH_USERNAME     => 'Not valid user name',
        self::ERR_NOT_FOUND_VARIANT => 'Not valid user name',
        self::ERR_VALIDATION        => 'Some parameters are missing or has incorrect value',
        self::ERR_UNDEFINED         => 'Not recognized error',
    ];

    /* If error causes we'll also send appropriate http-status */

    private static $_errHttpStatuses = [
        self::ERR_AUTH_USERNAME     => 401,
        self::ERR_VALIDATION        => 400,
        self::ERR_NOT_FOUND_VARIANT => 404,
        self::ERR_UNDEFINED         => 500,
    ];



    private $_format = 'json';

    /**@var CDbConnection $_db */
    private $_db = null;

    private $_modelId = null;

    private $_restData = null;

    public function init()
    {
        Yii::app()->attachEventHandler('onError', [$this,'handleError']);
        Yii::app()->attachEventHandler('onException', [$this,'handleEx']);
        $this->_db       = Yii::app()->db;
        $this->_restData = Yii::app()->request->getRestParams();
        $this->_modelId  = (int)Yii::app()->request->getQuery('id');
    }

    public function filters() {
        return array_merge(
            ['accessControl'],
            parent::filters()
        );
    }

    private $_user;

    private $_cmd;

    private function _setUser()
    {
        if (!isset($_SERVER['HTTP_USERNAME'])) {
            $this->throwException('', self::ERR_AUTH_USERNAME);
        }
        $username = $_SERVER['HTTP_USERNAME'];
        $this->_user = User::model()->find(
            'name = ?',
            [$username]
        );
        if (null === $this->_user) {
            $this->throwException('', self::ERR_AUTH_USERNAME);
        }
    }


    public function actionIndex()
    {
        $out = [
            'name' => '',
            'options' => '',
        ];
        $vote = Vote::model()->findByPk($this->_modelId);
        $out['name'] = $vote->name;

        $options = VoteOption::model()->getByVote($vote->id);

        foreach ($options as $opt) {
            $out['options'][$opt->id] = $opt;
        }
        $this->_sendResponse(200, self::_ok($out));

    }

    public function actionUpdate()
    {
        $out = [
            'options' => '',
            'msg' => ''
        ];

        $this->_setUser(); //Like auth, needed for some methods
        if (!isset($this->_restData['id_option'])) {
            $this->throwException('', self::ERR_VALIDATION);
        }
        $isVoted = VoteResult::model()->isVoted(
            $this->_modelId,
            $this->_user->id
        );

        $resObj = new VoteResult();
        if (!$isVoted) {
            $resObj->addVote($this->_modelId, $this->_user->id, $this->_restData['id_option']);
            Yii::app()->cache->set($this->getKeyCache('view_result'), $resObj->getResultByVote($this->_modelId));
        }

        $result = Yii::app()->cache->get($this->getKeyCache('view_result'));

        $out['options'] = $result;

        if (!$isVoted) {
            $selectedItem = [];
            foreach ($result as &$item) {
                if ($this->_restData['id_option'] == $item['id']) {
                    $selectedItem = $item;
                }
            }
            $out['msg'] = $this->_user->name . ", "
                . $selectedItem['percent'] ."% also likes "
                . $selectedItem['name'];
        }

        $this->_sendResponse(200, self::_ok($out));

    }

    private static function _ok($data = [])
    {
        $res = [
            'ok' => true,
            'error' => [],
            'rnd' => mt_rand()
        ];

        if (sizeof($data)) {
            $res['data'] = $data;
        }
        return $res;
    }

    private static function _error($code)
    {
        $response = [
            'ok'    => false,
            'err_code' => $code,
            'msg' => self::$_errMessages[$code],
            'rnd'   => mt_rand()
        ];
        return $response;
    }

    public function handleError(CEvent $event)
    {
        $this->_sendResponse(500, CJSON::encode($this->_error(self::ERR_UNDEFINED)));
    }

    public function handleEx(CEvent $event)
    {
        $ex = $event->exception;
        if ($ex instanceof ApiException) {
            $code = $ex->getCode();
        } else {
            $code = self::ERR_UNDEFINED;
        }

        $httpCode = self::$_errHttpStatuses[$code];

        $this->_sendResponse($httpCode, CJSON::encode($this->_error($code)));
    }


    private function throwException($errMsg, $errCode)
    {
        throw new ApiException($errMsg, $errCode);
    }

    private function _getStatusCodeMessage($status)
    {
        $codes = [
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        ];
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    /*
     * Really simple unique cache key generation
     * Minimum of unique
     * Controller - like "case" of caching model
     * @name - model should be cached.
     */
    private function getKeyCache($name)
    {
        return Yii::app()->controller->id . $name;
    }

    private function _sendResponse($status = 200, $body = [])
    {
        $statusHeader = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($statusHeader);
        header('Content-type: ' . $this->_contentType);
        echo CJSON::encode($body);
        Yii::app()->end();
    }
}
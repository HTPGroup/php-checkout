<?php
namespace App\Service;


class Api
{

    public static $isWriteLog = false;
    const API_TIMEOUT = 120;

    public static function call($function, $params, $show_bug = false, $language = '', $timeout = self::API_TIMEOUT)
    {
        if ($language == '') {
            $language = self::_getLanguage();
        }
        $error_code = 1;
        $error_message = 'Lỗi hệ thống';
        $response = array();
        //-------------
        $inputs = array(
            'fnc' => $function,
            'data' => json_encode($params),
            'language' => $language,
            'client_ip' => getIP(),
        );
        //Todo Log API Call
        //$slackLog = new SlackLog();
        $data = [
            'function' => $function,
        ];
        $result = array_merge($data, $params);
        //Send Log
   //       $slackLog->sendLog($result);


        $result = self::_call($inputs, $show_bug, $timeout);
        if ($result != false && is_array($result) && array_key_exists('error_code', $result)) {
            $error_code = $result['error_code'];
            if (self::isSuccess($error_code)) {
                $error_message = '';
            } else {
                if ($error_code == 1) {
                    $error_message = 'Lỗi hệ thống';
                } else {
                    if (isset($result['message'])){
                        $error_message = trim($result['message']);
                    }

                    if ($error_message == '') {
                        $error_message = 'Lỗi chưa được định nghĩa';
                    }
                }
            }
            $response = $result;
            unset($response['error_code']);
            unset($response['message']);
        } else {
            $error_code = 1;
            $error_message = 'Lỗi hệ thống';
        }
        //----------
        return array('error_code' => $error_code, 'error_message' => $error_message, 'response' => $response);
    }

    protected static function _getLanguage()
    {
        if (!isset($GLOBALS['language']) || empty($GLOBALS['language'])) {
            $language = Yii::app()->language;
        } else {
            $language = $GLOBALS['language'];
        }
        return $language;
    }

    protected static function _call($params, $show_bug = false, $timeout)
    {
        $id_api = self::_getIdAPI();
        $file_path = ROOT_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . date('Y-m-d') . '.txt';
        $result = false;
        try {
            $params_str = http_build_query($params);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, API_URL);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params_str);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, 'omipay@2015:sadnq%@23932KDHW326343');
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
            $result = curl_exec($ch);

            curl_close($ch);
            $log_result = $result;
            $result = json_decode($result, true);
            if (empty($result) || !array_key_exists('error_code', $result)) {
                self::_writeLog($file_path, '[' . $id_api . '][input][' . $params_str . '][result]: ' . $log_result);
            }
          } catch (Exception $e) {
            $result = false;
        }

        return $result;
    }

    protected static function _getIdAPI()
    {
        if (!isset($_SESSION['api_id']) || empty($_SESSION['api_id'])) {
            $_SESSION['api_id'] = uniqid();
        }
        return $_SESSION['api_id'];
    }

    public static function isSuccess($error_code)
    {
        return (strval($error_code) === '0');
    }

    public static function getMessage($error_code, $function)
    {
        $message = $error_code;
        if ($function != 'notificationGetListForCache') {
            if (!isset($GLOBALS['API_MESSAGE']) || empty($GLOBALS['API_MESSAGE'])) {
                self::loadMessageFromCache();
            }
            if (array_key_exists($error_code, $GLOBALS['API_MESSAGE'])) {
                $message = @$GLOBALS['API_MESSAGE'][$error_code];
            } else {
                $message = @$GLOBALS['API_MESSAGE'][2];
                self::updateMessageToCache();
            }
            if ($message == '') {
                $message = 'Lỗi hệ thống';
            }
        }
        return $message;
    }

    public static function loadMessageFromCache()
    {
        $GLOBALS['API_MESSAGE'] = array();
        $file_path = CACHE_PATH . 'api_message.cache';
        if (file_exists($file_path)) {
            $file_size = filesize($file_path);
            if ($file_size > 0) {
                $f = fopen($file_path, 'r');
                if ($f) {
                    $data = fread($f, $file_size);
                    fclose($f);
                    $GLOBALS['API_MESSAGE'] = json_decode($data, true);
                }
            }
        }
    }

    public static function updateMessageToCache()
    {
        $result = self::call('notificationGetListForCache', array());
        if ($result['error_message'] == '') {
            $data = $result['response']['data'];
            $message = array();
            if (is_array($data) && !empty($data)) {
                foreach ($data as $row) {
                    $message[$row['code']] = $row['content'];
                }
            }
            //-------------------
            $file_path = CACHE_PATH . 'api_message.cache';
            $f = fopen($file_path, 'w');
            if ($f) {
                fwrite($f, json_encode($message));
                fclose($f);
                return true;
            }
        }
        return false;
    }

    private static function _writeLog($fileName, $data)
    {
        $fp = fopen($fileName, 'a');
        if ($fp) {
            $line = date("H:i:s, d/m/Y:  ") . $data . " \n";
            fwrite($fp, $line);
            fclose($fp);
            return true;
        }
        return false;
    }

    function writeLog($data)
    {
        require_once ROOT_PATH . DS . 'protected' . DS . 'components' . DS . 'libs' . DS . 'Logs.php';
        $fileName = 'data/logs/bab/compare/' . date("Ymd", time()) . ".txt";
        $pathinfo = pathinfo($fileName);
        Logs::create($pathinfo['dirname'], $pathinfo['basename'], $data);
    }


}

?>

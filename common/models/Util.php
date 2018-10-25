<?php
/**
 * @author hbhe 57620133@qq.com
 * @version 0.01
 */

namespace common\models;

use yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/**
 * Class Util
 * @package common\models
 */
class Util
{
    const RANDOM_DIGITS = 'digits';
    const RANDOM_NONCESTRING = 'noncestr';

    /**
     * @param string $obj
     * @param string $log_file
     */
    public static function W($obj = "", $log_file = '')
    {
        if (is_array($obj))
            $str = print_r($obj, true);
        else if (is_object($obj))
            $str = print_r($obj, true);
        else
            $str = "{$obj}";

        if (empty($log_file))
            $log_file = \Yii::$app->getRuntimePath() . '/errors.log';

        $date = date("Y-m-d H:i:s");
        $log_str = sprintf("%s,%s\n", $date, $str);
        error_log($log_str, 3, $log_file);
    }

    /**
     * @param $url
     * @param array $get
     * @param array $post
     * @param string $format
     * @return mixed
     */
    public static function C($url, $get = [], $post = [], $format = 'json')
    {
        $requestUrl = $url . "?";
        foreach ($get as $k => $v) {
            $requestUrl .= "$k=" . urlencode($v) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);
        return Util::curl($requestUrl, $post);
    }

    /**
     * @param $url
     * @param array $posts
     * @param string $format
     * @return mixed
     * @throws \Exception
     */
    public static function curl($url, $posts = [], $format = 'json')
    {
        $response = self::curl_core($url, $posts);
        if ('json' === $format) {
            return json_decode($response, true);
        } else if ('xml' === $format) {
            $respObject = @simplexml_load_string($response);
            if (false !== $respObject)
                return json_decode(json_encode($respObject), true);
            else
                throw new \Exception ('XML error:' . $response);
        }
    }

    /**
     *
     * @param $url
     * @param array $posts
     * @return mixed
     * @throws \Exception
     */
    public static function curl_core($url, $posts = [])
    {
        yii::info([$url, $posts]);
        $curlOptions = [
            CURLOPT_HTTPHEADER => array(
                "Contont-Type: text/plain",
            ),
            CURLOPT_USERAGENT => 'WXTPP Client',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            //CURLOPT_POSTFIELDS => is_string($posts) ? $posts : json_encode($posts),
            CURLOPT_POSTFIELDS => $posts,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1,
        ];
        $curlResource = curl_init();
        foreach ($curlOptions as $option => $value) {
            curl_setopt($curlResource, $option, $value);
        }
        $response = curl_exec($curlResource);
        $responseHeaders = curl_getinfo($curlResource);

        // check cURL error
        $errorNumber = curl_errno($curlResource);
        $errorMessage = curl_error($curlResource);
        curl_close($curlResource);

        if ($errorNumber > 0) {
            throw new \Exception('Curl error requesting "' . $url . '": #' . $errorNumber . ' - ' . $errorMessage);
        }
        if (strncmp($responseHeaders['http_code'], '20', 2) !== 0) {
            throw new \Exception('Request failed with code: ' . $responseHeaders['http_code'] . ', message: ' . $response);
        }
        yii::info($response);
        return $response;
    }

    /**
     * @return string
     */
    public static function generateOid()
    {
        return strtoupper(uniqid());
    }

    /**
     * U::getWxUserHeadimgurl("http://wx.qlogo.cn/mmopen/17ASicSl2de5EHEpImf7IOxZ5w6MibiaWuzsThDo39s0Lq6U0ZG4Kn04AJDfK4XiaxYicCCpsXH3UxW8goFcPnEkfhv7GO2AeFAtR/0", 64);
     * @param $url
     * @param $size
     * @return string
     */
    public static function getWxUserHeadimgurl($url, $size)
    {
        if (empty($url))
            return $url;
        if (!in_array($size, [0, 46, 64, 96, 132]))
            return $url;
        $pos = strrpos($url, "/");
        $str = substr($url, 0, $pos) . "/$size";
        return $str;
    }

    /**
     * @param $mobile
     * @return bool
     */
    public static function mobileIsValid($mobile)
    {
        $pattern = '/^1\d{10}$/';
        if (preg_match($pattern, $mobile))
            return true;
        return false;
    }

    /**
     * @param null $key
     * @return array|mixed|string
     */
    public static function getYesNoOptionName($key = null)
    {
        $arr = array(
            '0' => '否',
            '1' => '是',
        );
        return $key === null ? $arr : (isset($arr[$key]) ? $arr[$key] : '');
    }

    /**
     * @param string $type
     * @param int $len
     * @return string
     */
    public static function randomString($type = self::RANDOM_DIGITS, $len = 4)
    {
        $code = '';
        switch ($type) {
            case self::RANDOM_DIGITS:
                $chars = '0123456789';
                break;
            case self::RANDOM_NONCESTRING:
                $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                break;
        }
        $chars_len = strlen($chars);
        while ($len > 0) {
            $code .= substr($chars, rand(0, 10000) % $chars_len, 1);
            $len--;
        }
        return $code;
    }

    /**
     * @param $ptime
     * @return string
     */
    public static function timeago($ptime)
    {
        $ptime = strtotime($ptime);
        $etime = time() - $ptime;
        if ($etime < 1) return '刚刚';
        $interval = array(
            12 * 30 * 24 * 60 * 60 => '年前' . ' (' . date('Y-m-d', $ptime) . ')',
            30 * 24 * 60 * 60 => '个月前' . ' (' . date('m-d', $ptime) . ')',
            7 * 24 * 60 * 60 => '周前' . ' (' . date('m-d', $ptime) . ')',
            24 * 60 * 60 => '天前',
            60 * 60 => '小时前',
            60 => '分钟前',
            1 => '秒前'
        );
        foreach ($interval as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . $str;
            }
        };
    }

    /**
     * @param int $probability, 400000 -> 40%
     * @return bool
     */
    public static function haveProbability($probability = 400000)
    {
        return mt_rand(0, 1000000) < $probability;
    }

    /**
     * qqface_convert_html("/:moon/:moon");
     * \common\wosotech\Util::qqface_convert_html(emoji_unified_to_html(emoji_softbank_to_unified($model->content)));
     * @param $text
     * @return mixed
     */
    public static function qqface_convert_html($text)
    {
        $GLOBALS['qqface_maps'] = array("/::)", "/::~", "/::B", "/::|", "/:8-)", "/::<", "/::$", "/::X", "/::Z", "/::'(", "/::-|", "/::@", "/::P", "/::D", "/::O", "/::(", "/::+", "/:--b", "/::Q", "/::T", "/:,@P", "/:,@-D", "/::d", "/:,@o", "/::g", "/:|-)", "/::!", "/::L", "/::>", "/::,@", "/:,@f", "/::-S", "/:?", "/:,@x", "/:,@@", "/::8", "/:,@!", "/:!!!", "/:xx", "/:bye", "/:wipe", "/:dig", "/:handclap", "/:&-(", "/:B-)", "/:<@", "/:@>", "/::-O", "/:>-|", "/:P-(", "/::'|", "/:X-)", "/::*", "/:@x", "/:8*", "/:pd", "/:<W>", "/:beer", "/:basketb", "/:oo", "/:coffee", "/:eat", "/:pig", "/:rose", "/:fade", "/:showlove", "/:heart", "/:break", "/:cake", "/:li", "/:bome", "/:kn", "/:footb", "/:ladybug", "/:shit", "/:moon", "/:sun", "/:gift", "/:hug", "/:strong", "/:weak", "/:share", "/:v", "/:@)", "/:jj", "/:@@", "/:bad", "/:lvu", "/:no", "/:ok", "/:love", "/:<L>", "/:jump", "/:shake", "/:<O>", "/:circle", "/:kotow", "/:turn", "/:skip", "/:oY");
        return str_replace($GLOBALS['qqface_maps'],
            array_map(array('self', 'add_img_label'), array_keys($GLOBALS['qqface_maps'])),
            htmlspecialchars_decode($text, ENT_QUOTES)
        );
    }

    /**
     * @param $v
     * @return string
     */
    public static function add_img_label($v)
    {
        return '<img src="https://res.wx.qq.com/mpres/htmledition/images/icon/emotion/' . $v . '.gif" width="24" height="24">';
    }

    /**
     * @param $str
     * @param $len
     * @param string $suffix
     * @return string
     */
    public static function getShortString($str, $len, $suffix = '...')
    {
        return StringHelper::truncate($str, $len);
    }

    public static function short($string, $length = 20)
    {
        return StringHelper::truncate($string, $length);
    }

    public static function sendVerifycodeAjax($params)
    {
        $mobile = ArrayHelper::getValue($params, 'mobile');
        $content = ArrayHelper::getValue($params, 'content', '');
        $template = ArrayHelper::getValue($params, 'template', '');  // SMS_001
        if (empty($mobile)) {
            return Json::encode(['code' => 1, 'msg' => '手机号不能空号!']);
        }

        if (!preg_match('/^1\d{10}$/', $mobile)) {
            return Json::encode(['code' => 1, 'msg' => '无效的手机号!']);
        }

        $verifyCode = self::randomString();
        Yii::info("$mobile verifyCode = $verifyCode");
        try {
            $results = Yii::$app->sm->send($mobile, [
                'content'  => $content,
                'template' => $template,
                'data' => [
                    'code' => $verifyCode,
                ],
            ]);

        } catch(\Exception $e) {
            Yii::error(['send verify failed', __METHOD__, __LINE__, $e->getExceptions()]);
            return Json::encode(['code' => 1, "msg" => '短信发送失败，请稍后再试']);
        }

        Yii::$app->cache->set('SMS-VERIFY-CODE' . $mobile, $verifyCode, YII_DEBUG ? 24 * 3600 : 5 * 60);
        // Yii::info(['set ok', 'SMS-VERIFY-CODE' . $mobile, \Yii::$app->cache->get('SMS-VERIFY-CODE' . $mobile)]);
        return Json::encode(['code' => 0]);
    }

    public static function checkVerifyCode($mobile, $verifyCode)
    {
        $verifyCodeCache = \Yii::$app->cache->get('SMS-VERIFY-CODE' . $mobile);
        //Yii::info(['checkVerifyCode, compare...', __METHOD__, __LINE__, $verifyCodeCache, $verifyCode]);
        return YII_DEBUG ? true : $verifyCodeCache == $verifyCode;
        //return $verifyCodeCache == $verifyCode;
    }

    public static function getIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = empty($_SERVER['REMOTE_ADDR']) ? '' : $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /*
        [
            'code' => 0,
            'data' => [
                'ip' => '223.75.1.199',
                'country' => '中国',
                'area' => '',
                'region' => '湖北',
                'city' => '武汉',
                'county' => 'XX',
                'isp' => '移动',
                'country_id' => 'CN',
                'area_id' => '',
                'region_id' => '420000',
                'city_id' => '420100',
                'county_id' => 'xx',
                'isp_id' => '100025',
            ],
        ],
    */

    /**
     * @param null $ip
     * @return AreaCode
     */
    public static function getCurrentAreaCode($ip = null)
    {
        $city_id = Yii::$app->request->get('area_id') ?: Yii::$app->session->get('area_id');
        if (($model = AreaCode::findOne($city_id)) !== null) {
            Yii::$app->session->set('area_id', $city_id);
            return $model;
        }

        if (null === $ip) {
            $ip = YII_ENV_DEV ? '223.75.1.199' : self::getIpAddr();
        }

        $key = [__METHOD__, $ip];
        $arr = Yii::$app->cache->get($key);
        if ($arr === false) {
            $arr = self::C('http://ip.taobao.com/service/getIpInfo.php', ['ip' => $ip]);
            Yii::$app->cache->set($key, $arr, 30 * 24 * 3600);
        }

        $city_id = ArrayHelper::getValue($arr, 'data.city_id', '0');
        $region_id = ArrayHelper::getValue($arr, 'data.region_id', '0');
        if (($model = AreaCode::findOne($city_id)) !== null) {
            return $model;
        }
        if (($model = AreaCode::findOne(['parent_id' => $region_id])) !== null) {
            return $model;
        }
        Yii::info([__METHOD__, __LINE__, $arr, $model->toArray()]);
        return $model;
    }

    public static function saveUrl()
    {
        Yii::$app->session->set('previousRoute', Yii::$app->controller->getRoute());
        Yii::$app->session->set('previousParams', Yii::$app->request->get());
    }

    public static function getPreviousUrl(array $params = [], $scheme = false)
    {
        $parentParams = Yii::$app->session->get('previousParams');
        $parentParams[0] = '/' . Yii::$app->session->get('previousRoute');
        $route = ArrayHelper::merge($parentParams, $params);

        return Url::toRoute($route, $scheme);
    }

    // $url = "http://m.site.com/register.html?pid={$id}";
    public function getQrCodeImageUrl($url)
    {
        $fileName = md5($url) . '.png';
        $filePath = Yii::getAlias("@storage/web/images/qrcode/{$fileName}");
        if (file_exists($filePath)) {
            return Yii::getAlias("@storageUrl/images/qrcode/{$fileName}");
        }
        require_once Yii::getAlias('@vendor/phpqrcode/phpqrcode.php');
        \QRcode::png($url, $filePath); // save as file, \QRcode::png($url, Yii::getAlias('@runtime/xxx.png'));
        return Yii::getAlias("@storageUrl/images/qrcode/{$fileName}");
    }

    /**
     * 获取图片的base64编码
     *
     * @param  string $imgPath 可以使用路径别名表示的图片路径
     * @return string          base64编码后的图片
     */
    public static function getBase64Img($imgPath)
    {
        $mimeType = FileHelper::getMimeType(Yii::getAlias($imgPath));
        $fileContent = base64_encode(file_get_contents($filePath));

        return 'data:' . $mimeType . ';base64,' . $fileContent;
    }

    /**
     * getRandomWeightedElement()
     * Utility function for getting random values with weighting.
     * Pass in an associative array, such as array('A'=>5, 'B'=>45, 'C'=>50)
     * An array like this means that "A" has a 5% chance of being selected, "B" 45%, and "C" 50%.
     * The return value is the array key, A, B, or C in this case.  Note that the values assigned
     * do not have to be percentages.  The values are simply relative to each other.  If one value
     * weight was 2, and the other weight of 1, the value with the weight of 2 has about a 66%
     * chance of being selected.  Also note that weights should be integers.
     *
     * @param array $weightedValues
     */
    static public function getRandomWeightedElement(array $weightedValues) {
        $rand = mt_rand(1, (int) array_sum($weightedValues));

        foreach ($weightedValues as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }
    }

}


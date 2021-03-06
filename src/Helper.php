<?php

namespace App;
use App\Models\User;
use Facebook\Facebook;
use phpFastCache\CacheManager;
use phpFastCache\Core\phpFastCache;

class Helper
{
    public static $app;
    public function __construct($app)
    {
        self::$app = $app;
    }
    public static function time_elapsed_string($datetime, $full = false) {

        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
            unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    /**
     * Return a diff between strings
     * @param $old
     * @param $new
     * @return array
     */
//    public static function htmlDiff($old, $new){
//        $ret = '';
//        $diff = self::diff(preg_split("/[\s]+/", $old), preg_split("/[\s]+/", $new));
//        foreach($diff as $k){
//            if(is_array($k)) {
//                $ret .= (!empty($k['d']) ? "<del>" . implode(' ', $k['d']) . "</del> " : '') .
//                    (!empty($k['i']) ? "<ins>" . implode(' ', $k['i']) . "</ins> " : '');
//            }
//            else $ret .= ' ';
//        }
//        return $ret;
//    }

    public static function htmlDiff($diffs){
        foreach ($diffs as $diff) {
            foreach ($diff as $d) {
                if($d['tag'] == 'replace') {

                }
            }
        }
    }

    private static function diff($old, $new){
        $matrix = array();
        $maxlen = 0;
        foreach($old as $oindex => $ovalue){
            $nkeys = array_keys($new, $ovalue);
            foreach($nkeys as $nindex){
                $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                    $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                if($matrix[$oindex][$nindex] > $maxlen){
                    $maxlen = $matrix[$oindex][$nindex];
                    $omax = $oindex + 1 - $maxlen;
                    $nmax = $nindex + 1 - $maxlen;
                }
            }
        }
        if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
        return array_merge(
            self::diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
            array_slice($new, $nmax, $maxlen),
            self::diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
    }

    /**
     * Moves the uploaded file to the upload directory and assigns it a unique name
     * to avoid overwriting an existing uploaded file.
     *
     * @param string $directory directory to which the file is moved
     * @param  $uploaded file uploaded file to move
     * @return string filename of moved file
     */
    public static function moveUploadedFile($directory, $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boole $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source https://gravatar.com/site/implement/images/php/
     */
    public static function  get_gravatar( $email, $s = 80, $d = 'mp', $r = 'g', $img = false, $atts = array() ) {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }

    public static function get_user_avatar($user_id, $size) {
        $user = User::find_by_id($user_id);

        if($user['fb_access_token']) {
            $longLivedAccessToken = unserialize($user['fb_access_token']);
            $cached_avatar = self::$app->instanceCache->getItem(hash('md5', $user['email']).'_avatar');
            if(!$cached_avatar->isHit()) {
                $fb = new Facebook([
                    'app_id' => getenv('FACEBOOK_APP_ID'),
                    'app_secret' => getenv('FACEBOOK_APP_SECRET'),
                    'default_graph_version' => getenv('FACEBOOK_GRAPH_VERSION')
                ]);
                $fbPictures = $fb->get('/me/picture?redirect=0&height='.$size, $longLivedAccessToken);
                $picture = $fbPictures->getGraphUser();
                $cached_avatar->set(serialize($picture))->expiresAfter(86400);
                self::$app->instanceCache->save($cached_avatar);
            } else {
                 $picture = unserialize($cached_avatar->get()) ;
            }
            return $picture['url'];
        } else {
            return self::get_gravatar($user['email'], $size);
        }
    }

    public static function get_title($url) {
        $str = file_get_contents($url);
        if(strlen($str)>0){
            $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
            preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
            return $title[1];
        }
    }

    public static function get_img_preview($url, $request) {
        if(file_exists(__DIR__ . '/../public/url-preview/'.sha1($url).'.jpg')) {
            return $request->getUri()->getBaseUrl().'/url-preview/'.sha1($url).'.jpg';
        } else {
            $fetchUrl = "https://api.thumbnail.ws/api/".getenv('THUMBNAIL_API_KEY') ."/thumbnail/get?url=".
                urlencode($url)."&width=". urlencode(getenv('THUMBNAIL_WIDHT'));
            $image = file_get_contents($fetchUrl);
            file_put_contents(__DIR__ . '/../public/url-preview/'.sha1($url).'.jpg', $image);
            return $request->getUri()->getBaseUrl().'/url-preview/'.sha1($url).'.jpg';
        }

    }

    public static function get_diff($old, $new) {
        $old_count = count($old);
        $new_count = count($new);
        $html = "<table>";
        $diff = [];
        if($old_count == $new_count) {
            // размер текста одинаковый, надо пройти построчно и сверить строки между собой
            foreach ($new as $string_number => $string_value) {
                $new_str_arr = str_split($new[$string_number]);
                $old_str_arr = str_split($old[$string_number]);
                if(count($new_str_arr) == count($old_str_arr)) {
                    // строки по длине одинаковые, но надо проверить на замену символа
                    $res = array_diff($new_str_arr, $old_str_arr);
                    echo "<pre>";
                    die(var_dump($res));

                } elseif(count($new_str_arr) > count($old_str_arr)) {
                    // в новую строку добавили символы
                    $res = array_diff($new_str_arr, $old_str_arr);
                    if(count($res) > 0) {
                        $diff['old'][] = "{$old[$string_number]}" ;
                        $diff['new'][] = str_replace(implode($res), "<ins>".implode($res)."</ins>", $new[$string_number]);
                    }
                } else {
                    // из новой строки убрали симоволы
                    $res = array_diff($old_str_arr, $new_str_arr);
                    if(count($res) > 0) {
                        $diff['old'][] = "{$old[$string_number]}" ;
                        $diff['new'][] = str_replace(implode($res), "<del>".implode($res)."</del>", $old[$string_number]);
                    }
                }
                $res = array_diff($new_str_arr, $old_str_arr);
                echo "<pre>";
                die(var_dump($res));

            }
        } elseif($old_count > $new_count) {
            // из нового текста удаляли строки
        } else {
            // в новый текст добавили строки
        }
        foreach ($new as $key => $string) {
            if($new[$key] === $old[$key]) {
                // the same strings
            } else {
              if(isset($old[$key])) {

              }
            }
        }
//        var_dump(count($old));
//        var_dump(count($new));
//        die("Asd");
    }
}
<?php

class BangumiAPI
{
    private static $bangumiAPI = null;

    private static $apiUrl = 'https://api.bgm.tv';

    private $myCollection;

    private $collectionApi = '';


    public static function GetInstance()
    {
        if (BangumiAPI::$bangumiAPI == null) {
            BangumiAPI::$bangumiAPI = new BangumiAPI();
        }
        return BangumiAPI::$bangumiAPI;
    }

    private function __construct()
    {
    }

    public function initCollectionApi($userID,$appID)
    {
        $this->collectionApi = BangumiAPI::$apiUrl . '/user/' . $userID . '/collections/anime?app_id=' . $appID . '&max_results=99';
    }

    public function getCollection($userID, $appID,$hasCache, $cacheTime)
    {
        if (empty($userID)) return false;
        $this->initCollectionApi($userID,$appID);
        $FilePath = __DIR__ . '/bgm-list.json';
        if ($hasCache && file_exists($FilePath) && time() - filemtime($FilePath) < $cacheTime) {
            $file = fopen($FilePath, 'r');
            if (!$this->verifyCollection(fread($file, filesize($FilePath)))) {
                $data = BangumiAPI::curl_get_contents($this->collectionApi);
                if (!$this->verifyCollection($data)) return false;
                file_put_contents($FilePath, $data, LOCK_EX);
            }
            fclose($file);
        } else {
            $data = BangumiAPI::curl_get_contents($this->collectionApi);
            if (!$this->verifyCollection($data)) return false;
            file_put_contents($FilePath, $data, LOCK_EX);
        }
        return true;
    }

    public function verifyCollection($data)
    {
        $content = json_decode($data,true);
        if (empty($content)) return false;
        $index = 0;
        foreach ($content as $value) {
            $this->myCollection[$index++] = $value;
            //var_dump($value);
        }
        return true;
    }

    public function printCollecion()
    {
        foreach ($this->myCollection as $value) {
            // echo '<style>.Bangumi ul{display:inline-block;vertical-align:top;margin:10px 10px 10px 0}.Bangumi ul img{width:88px;height:132px;object-fit:cover}.Bangumi ul p{width:88px;display:block;margin-block-start:1em;margin-block-end:1em;margin-inline-start:0;margin-inline-end:0;overflow-wrap:break-word}</style>';
            $watching = $value["collects"][0]["list"];//??????
            $watched = $value["collects"][1]["list"];//??????
            $wantsee = $value["collects"][2]["list"];//??????
            //$watchhold = $value["collects"][3]["list"];//??????
            //$watchdrop = $value["collects"][4]["list"];//??????
            $watchPlus = array_merge($watching,$watched);//??????25????????????????????????????????????
            array_multisort(array_column($watchPlus,'subject_id'),SORT_ASC,$watchPlus);//???subject_id??????(?????????????????????)
            array_multisort(array_column($wantsee,'subject_id'),SORT_ASC,$wantsee);//???subject_id??????(?????????????????????)
            echo "<br><b>??????</b><br>";
            foreach ($watchPlus as $value1) {
            $name = $value1["subject"]["name_cn"] ?: $value1["subject"]["name"];
            $url = $value1["subject"]["url"];
            $imgUrl = $value1["subject"]["images"]["medium"];
            $imgUrl = str_replace("http","https",$imgUrl);
            echo "<ul><a href=" . $url . " target='_blank' rel='noopener' class='bangumi'><img alt='$name'src='$imgUrl'/></a><p>$name</p><br></ul>";
            }
            echo "<br><b>??????</b><br>";
            foreach ($wantsee as $value1) {
            $name = $value1["subject"]["name_cn"] ?: $value1["subject"]["name"];
            $url = $value1["subject"]["url"];
            $imgUrl = $value1["subject"]["images"]["medium"];
            $imgUrl = str_replace("http","https",$imgUrl);
            echo "<ul><a href=" . $url . " target='_blank' rel='noopener' class='bangumi'><img alt='$name'src='$imgUrl'/></a><p>$name</p><br></ul>";
            }
        }
    }

    private static function curl_get_contents($_url)
    {
        $myCurl = curl_init($_url);
        curl_setopt($myCurl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($myCurl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($myCurl, CURLOPT_HEADER, false);
        $content = curl_exec($myCurl);
        curl_close($myCurl);
        //echo $content;
        return $content;
    }
}

class BangumiList_Action extends Widget_Abstract_Contents implements Widget_Interface_Do
{

    public function action()
    {
        $options = Helper::options();
        $userID = trim($options->plugin('BangumiList')->userID);
        $appID = $options->plugin('BangumiList')->appID;
        $hasCache = $options->plugin('BangumiList')->hasCache;
        $cacheTime = trim($options->plugin('BangumiList')->cacheTime);

        $bangumi = BangumiAPI::GetInstance();
        if ($bangumi->getCollection($userID, $appID,$hasCache, $cacheTime)) {
            $bangumi->printCollecion();
        } else {
            echo '??????????????????';
        }
    }
}

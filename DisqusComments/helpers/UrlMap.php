<?php
/**
 * Class UrlMap
 * Getting URLs for pages with comments using urlMap in CSV format
 */

class UrlMap {

    /* It is recommended to provide max string length to fgetcsv for improve executing speed http://php.net/manual/ru/function.fgetcsv.php */
    const URL_MAX_LENGTH = 1000;

    /* Name of your map stored in '/data' folder */
    public static $urlMapFileName = 'urlMap.csv';

    /**
     * Getting URLs from CSV map
     * @return array
     */
    public static function getUrlArrayFromMap()
    {
        $urlArray = array();
        $filePath = self::getFilePath();
        if(file_exists($filePath))
        {
            $file = fopen($filePath, 'r');
            if(is_resource($file))
            {
                while (($data = fgetcsv($file, self::URL_MAX_LENGTH)) !== FALSE)
                {
                    $urlArray[] = $data[0];
                }
                fclose($file);
            }
        }
        return $urlArray;
    }

    /**
     * Adding current url to map if it isn't existing
     * @param string $url
     */
    public static function updateUrlMap($url)
    {
        $urlArray = self::getUrlArrayFromMap();
        if(array_search($url, $urlArray) === false)
        {
            $urlArray[] = $url;
            $filePath = self::getFilePath();
            $file = fopen($filePath, 'a');
            if(is_resource($file))
            {
                fputcsv($file, array($url));
            }
            fclose($file);
        }

    }

    /**
     * @return string
     */
    public static function getFilePath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . self::$urlMapFileName;
    }
}
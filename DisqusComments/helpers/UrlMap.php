<?php
/**
 * Class UrlMap
 * Getting URLs for pages with comments using urlMap in CSV format
 */

class UrlMap {

    /* It is recommended to provide max string length to fgetcsv for improve executing speed http://php.net/manual/ru/function.fgetcsv.php */
    const URL_MAX_LENGTH = 1000;

    /**
     * Getting URLs from CSV map
     * @param
     * @return array
     */
    public static function getUrlArrayFromMap($filePath)
    {
        $urlArray = array();
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
        else
        {
            echo 'Can not read the file ' . $filePath;
        }
        return $urlArray;
    }

}
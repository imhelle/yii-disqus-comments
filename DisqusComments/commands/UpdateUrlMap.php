<?php

/**
 * Class UpdateUrlMap
 * The console command updates URLs in table of comments from CSV file
 */
class UpdateUrlMap extends CConsoleCommand
{

    public function actionIndex($filePath)
    {
        $startAll = microtime(true);
        $disqusComponent = Yii::app()->disqusComments; /** @var EDisqusComments $disqusComponent */
        $pageUrlArray = UrlMap::getUrlArrayFromMap($filePath);

        foreach($pageUrlArray as $url)
        {
            $disqusComments = DisqusComments::model()->cache($disqusComponent->cacheDuration)->findByAttributes(array(
                'page_url' => $url
            ));

            if(!isset($disqusComments))
            {
                $disqusComments = new DisqusComments('updateUrls');
                $disqusComments->page_url = $url;
            }
            $disqusComments->save();

        }
        echo 'updated ALL in ';
        echo microtime(true) - $startAll . " seconds. \n";
    }

}
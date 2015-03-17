<?php

/**
 * Class UpdateUrlMap
 * The console command updates URLs in table of comments from CSV file
 */
class UpdateUrlMap extends CConsoleCommand
{

    public function actionIndex()
    {
        $startAll = microtime(true);
        $discusComponent = Yii::app()->discusComments; /** @var EDisqusComments $discusComponent */
        $pageUrlArray = UrlMap::getUrlArrayFromMap();

        foreach($pageUrlArray as $url)
        {
            $disqusComments = DisqusComments::model($discusComponent->cacheDuration)->findByAttributes(array(
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
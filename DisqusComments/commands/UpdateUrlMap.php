<?php

/**
 * Class UpdateUrlMap
 * The console command updates URLs in table of comments from CSV file
 */
class UpdateUrlMap extends CConsoleCommand
{
    /* Update by URLs received from API */
    public function actionFromApi()
    {
        $startAll = microtime(true);
        $discusComponent = Yii::app()->disqusComments;
        /** @var EDisqusComments $discusComponent */
        $pageUrlArray = $discusComponent->loadUrls();

        $counter = 0;
        foreach ($pageUrlArray as $url) {
            $counter++;
            $disqusComments = DisqusComments::findByUrl($url, true, 'updateUrls');
            $disqusComments->save();

        }
        echo "updated $counter URLs by ";
        echo microtime(true) - $startAll . " seconds. \n";
    }

    /* Update from loaded CSV url map */
    public function actionFromCSV($filePath)
    {
        $startAll = microtime(true);
        $pageUrlArray = UrlMap::getUrlArrayFromMap($filePath);

        foreach ($pageUrlArray as $url) {
            $disqusComments = DisqusComments::findByUrl($url, true, 'updateUrls');
            $disqusComments->save();

        }
        echo 'updated ALL in ';
        echo microtime(true) - $startAll . " seconds. \n";
    }

}
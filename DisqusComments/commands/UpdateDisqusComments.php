<?php

/**
 * Class UpdateDisqusComments
 * The console command updates table of comments using Disqus API
 */
class UpdateDisqusComments extends CConsoleCommand
{

    public function actionIndex()
    {
        $startAll = microtime(true);
        $disqusApiComponent = Yii::app()->disqusComments; /** @var EDisqusComments $disqusApiComponent */

        $pageUrlArray = UrlMap::getUrlArrayFromMap();

        foreach($pageUrlArray as $url)
        {
            $start = microtime(true);

            $commentsFromApi = $disqusApiComponent->loadCommentsByUrl($url);
            if(is_array($commentsFromApi) && !empty($commentsFromApi))
            {
                $commentsHTML = EDisqusComments::createCommentsHTML($commentsFromApi);
                $disqusComments = DisqusComments::model()->findByAttributes(array('page_url' => $url));

                if(!isset($disqusComments))
                {
                    $disqusComments = new DisqusComments();
                    $disqusComments->page_url = $url;
                }
                $disqusComments->comments_block = $commentsHTML;
                $disqusComments->save();
            }
            echo 'generated for ' . $url . ' in ';
            echo microtime(true) - $start . " seconds. \n";
        }
        echo 'generated ALL in ';
        echo microtime(true) - $startAll . " seconds. \n";
    }

}
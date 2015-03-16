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

        $commentsPages = DisqusComments::model()->findAll();

        foreach($commentsPages as $commentsPage)
        {
            $start = microtime(true);

            $commentsPage->setScenario('syncComments');
            $commentsFromApi = $disqusApiComponent->loadCommentsByUrl($commentsPage->page_url);
            if(is_array($commentsFromApi) && !empty($commentsFromApi))
            {
                $commentsHTML = EDisqusComments::createCommentsHTML($commentsFromApi);
                $commentsPage->comments_block = $commentsHTML;
                $commentsPage->save();
            }
            echo 'generated for ' . $commentsPage->page_url . ' in ';
            echo microtime(true) - $start . " seconds. \n";
        }
        echo 'generated ALL in ';
        echo microtime(true) - $startAll . " seconds. \n";
    }

}
<?php

/**
 * Class UpdateDisqusComments
 * The console command updates table of comments using Disqus API
 */
class UpdateDisqusComments extends CConsoleCommand
{
    public function actionAll()
    {
        $startAll = microtime(true);
        $discusComponent = Yii::app()->disqusComments; /** @var EDisqusComments $discusComponent */

        $commentsPages = DisqusComments::model()->findAll();

        foreach($commentsPages as $commentsPage)
        {
            $start = microtime(true);

            $commentsPage->setScenario('syncComments');
            $commentsFromApi = $discusComponent->loadCommentsByUrl($commentsPage->page_url);
            if(is_array($commentsFromApi) && !empty($commentsFromApi))
            {
                $comments = EDisqusComments::formatComments($commentsFromApi);
                $commentsHierarchy = EDisqusComments::sortCommentsByHierarchy($comments);
                $commentsPage->comments_block = json_encode($commentsHierarchy);
                $commentsPage->update_time = time();
                $commentsPage->save();
            }
            echo 'generated for ' . $commentsPage->page_url . ' in ';
            echo microtime(true) - $start . " seconds. \n";
        }
        echo 'generated ALL in ';
        $finishAll = microtime(true);
        \Yii::app()->setGlobalState('DisqusComments', $finishAll);
        echo $finishAll - $startAll . " seconds. \n";
    }

    public function actionRecent()
    {
        $startAll = microtime(true);
        $discusComponent = Yii::app()->disqusComments; /** @var EDisqusComments $discusComponent */

        $lastUpdate = DisqusComments::getLastUpdateTime();
        $interval = DisqusInterval::getIntervalBySeconds(time() - $lastUpdate);
        $urls = $discusComponent->loadRecentThreads($interval);
        echo "searching for updates by last $interval \n";
        if(!empty($urls))
        {
            foreach($urls as $url)
            {
                $start = microtime(true);

                $commentsPage = DisqusComments::findByUrl($url, true);  /* 'true' is for create if not exist */
                $commentsFromApi = $discusComponent->loadCommentsByUrl($commentsPage->page_url);
                if(is_array($commentsFromApi) && !empty($commentsFromApi))
                {
                    $comments = EDisqusComments::formatComments($commentsFromApi);
                    $commentsHierarchy = EDisqusComments::sortCommentsByHierarchy($comments);
                    $commentsPage->comments_block = json_encode($commentsHierarchy);
                    $commentsPage->update_time = time();
                    $commentsPage->save();
                }
                echo 'generated for ' . $commentsPage->page_url . ' in ';
                echo microtime(true) - $start . " seconds. \n";
            }
            $finishAll = microtime(true);
            \Yii::app()->setGlobalState('DisqusComments', $finishAll);
            echo 'generated ALL in ';
            echo $finishAll - $startAll . " seconds. \n";
        }
        else
        {
            echo "nothing to update \n";
        }
    }

}
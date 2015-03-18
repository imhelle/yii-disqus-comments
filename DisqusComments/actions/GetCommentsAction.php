<?php

/**
 * Class GetCommentsAction
 * Action returns the comments block in JSON format for AJAX request with "page_url" parameter
 * (JSON contains comments array with "author", "date" and "text" fields)
 */
class  GetCommentsAction extends CAction
{
    public function run()
    {
        if(Yii::app()->request->isAjaxRequest)
        {
            if(isset($_POST['page_url']))
            {
                $url = $_POST['page_url'];
                $disqusComponent = Yii::app()->disqusComments; /** @var EDisqusComments $disqusComponent */
                $duration = $disqusComponent->queryCacheDuration;
                $dependency = new \CGlobalStateCacheDependency('DisqusComments');
                $comments = DisqusComments::model()->cache($duration, $dependency)->findByAttributes(array(
                    'page_url' => $url
                ));
                if(!isset($comments))
                {
                    DisqusComments::saveNewUrl($url);
                }
                echo EDisqusComments::createCommentsJSON($comments->comments_block);
            }
        }
    }
}
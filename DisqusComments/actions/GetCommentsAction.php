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
                $comments = DisqusComments::model()->cache($disqusComponent->cacheDuration)->findByAttributes(array(
                    'page_url' => $url
                ));
                echo EDisqusComments::createCommentsJSON($comments->comments_block);
            }
        }
    }
}
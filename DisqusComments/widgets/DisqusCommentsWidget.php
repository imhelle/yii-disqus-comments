<?php

/**
 * Class DisqusCommentsWidget
 * Displays the comments block from DB and loads Disqus widget
 * @property string $pageUrl
 */
class DisqusCommentsWidget extends CWidget {

    /* Current website page URL */
    public $pageUrl = '';

    public function run()
    {
        $disqusApiComponent = Yii::app()->disqusComments; /** @var EDisqusComments $disqusApiComponent */

        $disqusComments = DisqusComments::model()->findByAttributes(array(
            'page_url' => $this->pageUrl
        ));
        if(isset($disqusComments))
        {
            $commentsBlock = $disqusComments->comments_block;
        }
        else
        {
            $commentsBlock = '';
            if($disqusApiComponent->autoUpdateMap && !empty($this->pageUrl))
            {
                $disqusComments = new DisqusComments('updateUrls');
                $disqusComments->page_url = $this->pageUrl;
                $disqusComments->save();
            }
        }

        $this->render('disqusCommentsWidget', array(
            'commentsBlock' => $commentsBlock,
            'apiKey' => $disqusApiComponent->apiKey,
            'shortName' => $disqusApiComponent->shortName
        ));
    }
}
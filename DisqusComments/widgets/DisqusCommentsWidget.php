<?php

/**
 * Class DisqusCommentsWidget
 * Displays the comments block from DB and loads Disqus widget
 * @property string $pageUrl
 */
class DisqusCommentsWidget extends CWidget {

    /* Current website page URL */
    public $pageUrl;

    public function run()
    {
        $disqusApiComponent = Yii::app()->disqusComments; /** @var EDisqusComments $disqusApiComponent */

        if($disqusApiComponent->autoUpdateMap) {
            UrlMap::updateUrlMap($this->pageUrl);
        }

        $disqusComments = DisqusComments::model()->findByAttributes(array(
            'page_url' => $this->pageUrl
        ));

        $commentsBlock = (isset($disqusComments)) ? $disqusComments->comments_block : '';

        $this->render('disqusCommentsWidget', array(
            'commentsBlock' => $commentsBlock,
            'apiKey' => $disqusApiComponent->apiKey,
            'shortName' => $disqusApiComponent->shortName
        ));
    }
}
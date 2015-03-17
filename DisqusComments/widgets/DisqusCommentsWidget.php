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
        $disqusComponent = Yii::app()->disqusComments; /** @var EDisqusComments $disqusComponent */

        $commentsBlock = $disqusComponent->getCache('commentBlock_' . md5($this->pageUrl));
        if($commentsBlock === false)
        {
            $disqusComments = DisqusComments::model()->cache($disqusComponent->cacheDuration)->findByAttributes(array(
                'page_url' => $this->pageUrl
            ));
            if(isset($disqusComments))
            {
                $comments = json_decode($disqusComments->comments_block);
                $commentsBlock = EDisqusComments::createCommentsHTML($comments);
            }
            else
            {
                $commentsBlock = '';
                if($disqusComponent->autoUpdateMap && !empty($this->pageUrl))
                {
                    $disqusComments = new DisqusComments('updateUrls');
                    $disqusComments->page_url = $this->pageUrl;
                    $disqusComments->save();
                }
            }
            $disqusComponent->setCache('commentBlock_' . md5($this->pageUrl), $commentsBlock);
        }

        $this->render('disqusCommentsWidget', array(
            'commentsBlock' => $commentsBlock,
            'apiKey' => $disqusComponent->apiKey,
            'shortName' => $disqusComponent->shortName
        ));
    }
}
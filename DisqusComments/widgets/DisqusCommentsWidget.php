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
        $discusComponent = Yii::app()->disqusComments; /** @var EDisqusComments $discusComponent */

        $commentsBlock = $discusComponent->getCache('commentBlock_' . md5($this->pageUrl));
        if($commentsBlock === false)
        {
            $disqusComments = DisqusComments::model()->cache($discusComponent->cacheDuration)->findByAttributes(array(
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
                if($discusComponent->autoUpdateMap && !empty($this->pageUrl))
                {
                    $disqusComments = new DisqusComments('updateUrls');
                    $disqusComments->page_url = $this->pageUrl;
                    $disqusComments->save();
                }
            }
            $discusComponent->setCache('commentBlock_' . md5($this->pageUrl), $commentsBlock);
        }

        $this->render('disqusCommentsWidget', array(
            'commentsBlock' => $commentsBlock,
            'apiKey' => $discusComponent->apiKey,
            'shortName' => $discusComponent->shortName
        ));
    }
}
<?php

/**
 * Class EDisqusComments
 * @property string $apiKey
 * @property string $shortName
 * @property integer $commentsLimit
 * @property bool $autoUpdateMap
 */
class EDisqusComments extends CApplicationComponent {

    /* Count of comments that we want to receive from API for one request. max = 100*/
    public $commentsLimit = 100;

    /* The API Key that you can receive by registering your application on https://disqus.com/api/applications/ */
    public $apiKey;

    /* The Disqus shortname of your site. Register it on https://disqus.com/admin/create/ */
    public $shortName;

    /* true if you want to create url map automatically by using the widget. false otherwise */
    public $autoUpdateMap = true;

    public function init()
    {
        Yii::import('ext.DisqusComments.models.*');
        Yii::import('ext.DisqusComments.helpers.*');
        parent::init();
    }

    /**
     * Getting comments from Disqus API for website page using cursor for results pagination
     * @param string $pageUrl
     * @param string|null $cursor
     * @return array
     */
    public function loadCommentsByUrl($pageUrl, $cursor = null)
    {
        $disqusApiUrl = 'https://disqus.com/api/3.0/threads/listPosts.json';
        $disqusApiUrl .= '?api_key=' . $this->apiKey;
        $disqusApiUrl .= '&forum=' . $this->shortName;
        $disqusApiUrl .= '&limit=' . $this->commentsLimit;
        $disqusApiUrl .= '&order=desc';
        $disqusApiUrl .= '&thread:link=' . $pageUrl;
        if(isset($cursor))
        {
            $disqusApiUrl .= '&cursor=' . $cursor;
        }
        $connection = curl_init($disqusApiUrl);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($connection);
        curl_close($connection);

        $decodedData = json_decode($data);
        $result = $decodedData->response;

        if($decodedData->cursor->hasNext)
        {
            $result = array_merge($result, $this->loadCommentsByUrl($pageUrl, $decodedData->cursor->next));
        }
        return $result;
    }

    /**
     * Generates HTML code for comments block from Disqus API request results
     * @param array $commentsFromApi
     * @param integer|null $parentId
     * @return string
     */
    public static function createCommentsHTML($commentsFromApi, $parentId = null)
    {
        $commentsHTML = CHtml::openTag('ul');
        foreach($commentsFromApi as $commentFromApi)
        {
            if($commentFromApi->parent == $parentId)
            {
                $commentsHTML .= CHtml::openTag('li');
                $commentsHTML .= CHtml::tag('span', array(), $commentFromApi->author->username);
                $commentsHTML .= $commentFromApi->message;
                $commentsHTML .= CHtml::closeTag('li');
                $commentsHTML .= self::createCommentsHTML($commentsFromApi, (integer)$commentFromApi->id);
            }
        }
        $commentsHTML .= CHtml::closeTag('ul');
        return $commentsHTML;
    }

}
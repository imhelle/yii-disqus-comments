<?php

/**
 * Class EDisqusComments
 * @property string $apiKey
 * @property string $shortName
 * @property integer $commentsLimit
 * @property bool $autoUpdateMap
 */
class EDisqusComments extends CApplicationComponent
{

    /* Count of comments that we want to receive from API for one request. max = 100*/
    public $commentsLimit = 100;

    /* The API Key that you can receive by registering your application on https://disqus.com/api/applications/ */
    public $apiKey;

    /* The Disqus shortname of your site. Register it on https://disqus.com/admin/create/ */
    public $shortName;

    /* true if you want to create url map automatically by using the widget. false otherwise */
    public $autoUpdateMap = true;

    /* Id of cache component you want to use */
    public $cacheId = 'cache';

    /* Duration of caching in seconds */
    public $cacheDuration = 3600;

    /* Duration of query caching in seconds */
    public $queryCacheDuration = 300;

    public function init()
    {
        Yii::setPathOfAlias('disqusComments', dirname(__FILE__));
        Yii::import('disqusComments.models.*');
        Yii::import('disqusComments.helpers.*');
        Yii::import('disqusComments.actions.*');
        parent::init();
    }

    /**
     * Getting comments from Disqus API for website page using cursor for results pagination
     * @param string $pageUrl
     * @param string|null $cursor
     * @return stdClass[]
     */
    public function loadCommentsByUrl($pageUrl, $cursor = null)
    {
        $disqusApiUrl = 'https://disqus.com/api/3.0/threads/listPosts.json';
        $disqusApiUrl .= '?api_key=' . $this->apiKey;
        $disqusApiUrl .= '&forum=' . $this->shortName;
        $disqusApiUrl .= '&limit=' . $this->commentsLimit;
        $disqusApiUrl .= '&order=desc';
        $disqusApiUrl .= '&thread:link=' . $pageUrl;
        if (isset($cursor)) {
            $disqusApiUrl .= '&cursor=' . $cursor;
        }
        $decodedData = self::sendRequest($disqusApiUrl);
        $result = $decodedData->response;

        if ($decodedData->cursor->hasNext) {
            $result = array_merge($result, $this->loadCommentsByUrl($pageUrl, $decodedData->cursor->next));
        }
        return $result;
    }

    /**
     * Get from Disqus API all your thread URLs
     * @param string|null $cursor
     * @return string[]
     */
    public function loadUrls($cursor = null)
    {
        $disqusApiUrl = 'https://disqus.com/api/3.0/threads/list.json';
        $disqusApiUrl .= '?api_key=' . $this->apiKey;
        $disqusApiUrl .= '&forum=' . $this->shortName;
        $disqusApiUrl .= '&limit=' . $this->commentsLimit;
        if (isset($cursor)) {
            $disqusApiUrl .= '&cursor=' . $cursor;
        }

        $data = self::sendRequest($disqusApiUrl);
        $result = array();
        if (is_array($data->response) && !empty($data->response)) {
            foreach ($data->response as $threadFromApi) {
                $result[] = $threadFromApi->link;
            }
        }
        if ($data->cursor->hasNext) {
            $result = array_merge($result, $this->loadUrls($data->cursor->next));
        }
        return $result;
    }

    /**
     * Get from Disqus API the threads having new comments in recent interval
     * @param string $interval
     * @param string|null $cursor
     * @return string[]
     */
    public function loadRecentThreads($interval, $cursor = null)
    {
        $disqusApiUrl = 'https://disqus.com/api/3.0/threads/listPopular.json';
        $disqusApiUrl .= '?api_key=' . $this->apiKey;
        $disqusApiUrl .= '&forum=' . $this->shortName;
        $disqusApiUrl .= '&limit=' . $this->commentsLimit;
        $disqusApiUrl .= '&interval=' . $interval;
        if (isset($cursor)) {
            $disqusApiUrl .= '&cursor=' . $cursor;
        }
        $data = self::sendRequest($disqusApiUrl);
        $result = array();
        if (is_array($data->response) && !empty($data->response)) {
            foreach ($data->response as $threadFromApi) {
                $result[] = $threadFromApi->link;
            }
        }

        if ($data->cursor->hasNext) {
            $result = array_merge($result, $this->loadRecentThreads($interval, $data->cursor->next));
        }
        return $result;
    }

    /**
     * Get stored value from cache
     * @param string $id
     * @return bool|mixed
     */
    public function getCache($id)
    {
        $value = false;
        if ($this->cacheId !== false && ($cache = Yii::app()->getComponent($this->cacheId)) !== null) {
            /** @var CCache $cache */
            $value = $cache->get($id);
        }
        return $value;
    }

    /**
     * Set value to cache
     * @param string $id
     * @param mixed $value
     */
    public function setCache($id, $value)
    {
        if ($this->cacheId !== false && ($cache = Yii::app()->getComponent($this->cacheId)) !== null) {
            /** @var CCache $cache */
            $dependency = new \CGlobalStateCacheDependency('DisqusComments');
            $cache->set($id, $value, $this->cacheDuration, $dependency);
        }
    }

    /**
     * Generates HTML code for comments block from Disqus API request results
     * @param stdClass[] $comments
     * @return string
     */
    public static function createCommentsHTML($comments)
    {
        $commentsHTML = '';
        if(is_array($comments)) {
            $commentsHTML = CHtml::openTag('ul');
            foreach ($comments as $comment) {
                $commentsHTML .= CHtml::openTag('li');
                $commentsHTML .= CHtml::tag('span', array(), $comment->author);
                $commentsHTML .= $comment->text;
                $commentsHTML .= CHtml::closeTag('li');
            }
            $commentsHTML .= CHtml::closeTag('ul');
        }
        return $commentsHTML;
    }

    public static function createCommentsJSON($comments)
    {
        $commentsForJSON = array();
        foreach ($comments as $comment) {
            $commentsForJSON[] = array(
                'author' => $comment->author,
                'date' => $comment->date,
                'text' => $comment->text,
            );
        }
        return json_encode($commentsForJSON);
    }

    /**
     * @param array $comments
     * @param integer $parentId
     * @return array
     */
    public static function sortCommentsByHierarchy($comments, $parentId = null)
    {
        $sortedComments = array();
        foreach ($comments as $comment) {
            if ($comment->parent == $parentId) {
                $sortedComments[] = $comment;
                $sortedComments = array_merge($sortedComments, self::sortCommentsByHierarchy($comments, (integer)$comment->id));
            }
        }
        return $sortedComments;
    }

    /**
     * Format comment objects for storing in DB
     * @param stdClass[] $commentsFromApi
     * @return stdClass[]
     */
    public static function formatComments($commentsFromApi)
    {
        $comments = array();
        foreach ($commentsFromApi as $commentFromApi) {
            $comment = new stdClass();
            $comment->author = $commentFromApi->author->username;
            $comment->date = self::formatDate($commentFromApi->createdAt);
            $comment->text = $commentFromApi->message;
            $comment->id = $commentFromApi->id;
            $comment->parent = $commentFromApi->parent;
            $comments[] = $comment;
        }
        return $comments;
    }

    /**
     * @param $date
     * @return bool|string
     */
    public static function formatDate($date)
    {
        return date_format(date_create($date), 'Y-m-d H:i:s');
    }

    /**
     * Send CUrl request and return decoded data
     * @param string $url
     * @return stdClass
     */
    protected static function sendRequest($url)
    {
        $connection = curl_init($url);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($connection);
        curl_close($connection);

        return json_decode($data);
    }

}

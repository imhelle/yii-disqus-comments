<?php

/**
 * This is the model class for table "disqus_comments".
 *
 * The followings are the available columns in table 'disqus_comments':
 * @property integer $id
 * @property string $page_url
 * @property string $comments_block
 * @property integer $create_time
 * @property integer $update_time
 */
class DisqusComments extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'disqus_comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('create_time, update_time', 'numerical', 'integerOnly'=>true),
			array('page_url, comments_block', 'required', 'on' => 'syncComments'),
			array('page_url', 'required', 'on' => 'updateUrls'),
			array('id, page_url, comments_block, create_time, update_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'page_url' => 'Url for page using Disqus comments',
			'comments_block' => 'Comments block HTML',
			'create_time' => 'Time created',
			'update_time' => 'Time modified',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('page_url', $this->page_url, true);
		$criteria->compare('comments_block', $this->comments_block, true);
		$criteria->compare('create_time', $this->create_time);
		$criteria->compare('update_time', $this->update_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DisqusComments the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public static function saveNewUrl($url)
    {
        $model = new self('updateUrls');
        $model->page_url = $url;
        $model->save();
        \Yii::app()->setGlobalState('DisqusComments', microtime(true));
    }

    /**
     * Returns the the latest update time from comments table
     * @return int|false
     */
    public static function getLastUpdateTime()
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'max(update_time)';
        $model = self::model();
        $command = $model->commandBuilder->createFindCommand(self::model()->tableName(), $criteria);
        return $command->queryScalar();
    }

    public static function findByUrl($url, $createIfNotExist = false)
    {
        $commentsPage = self::model()->findByAttributes(array(
            'page_url' => $url
        ));
        if(!isset($commentsPage) && $createIfNotExist)
        {
            $commentsPage = new self('syncComments');
            $commentsPage->page_url = $url;
        }
        return $commentsPage;
    }

}

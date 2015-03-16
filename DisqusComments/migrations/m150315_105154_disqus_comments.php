<?php

class m150315_105154_disqus_comments extends CDbMigration
{
	public function safeUp()
	{
        $this->createTable('disqus_comments', array(
            'id'                => 'pk',
            'page_url'          => 'string DEFAULT NULL COMMENT "Url for page using Disqus comments"',
            'comments_block'    => 'text DEFAULT NULL COMMENT "Comments block HTML"',
            'create_time' => 'integer DEFAULT NULL COMMENT "Время создания"',
            'update_time' => 'integer DEFAULT NULL COMMENT "Время изменения"',
        ));

        $this->createIndex('disqus_comments_page_url', 'disqus_comments', 'page_url', true);
	}

	public function safeDown()
	{
		$this->dropIndex('disqus_comments_page_url', 'disqus_comments');
        $this->dropTable('disqus_comments');
	}

}
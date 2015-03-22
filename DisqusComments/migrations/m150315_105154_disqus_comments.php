<?php

class m150315_105154_disqus_comments extends CDbMigration
{
    public function safeUp()
    {
        $this->createTable('disqus_comments', array(
            'id'                => 'pk',
            'page_url'          => 'string DEFAULT NULL COMMENT "Url for page using Disqus comments"',
            'comments_block'    => 'text DEFAULT NULL COMMENT "Comments block JSON"',
            'create_time'       => 'integer DEFAULT NULL COMMENT "Created at"',
            'update_time'       => 'integer DEFAULT NULL COMMENT "Updated at"',
        ));

        $this->createIndex('disqus_comments_page_url', 'disqus_comments', 'page_url', true);
        $this->createIndex('disqus_comments_update_time', 'disqus_comments', 'update_time');
    }

    public function safeDown()
    {
        $this->dropIndex('disqus_comments_page_url', 'disqus_comments');
        $this->dropTable('disqus_comments');
    }

}

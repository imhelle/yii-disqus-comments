# yii-disqus-comments
Extension for adding Disqus comments (https://disqus.com) for Yii application.
Provides to use Disqus comments widget and synchronise comments from Disqus to your database (it can be useful for indexing comments by Google).

## Installation

Clone or extract the DisqusComments folder from archive under protected/extensions of your Yii application.

## Configuration
Extension need to be added in your application and console config to components array:
```php
'components' => array(
        ...
        'disqusComments' => array(
            'class' => 'ext.DisqusComments.components.EDisqusComments',
            'apiKey' => 'YOUR_API_KEY',
            'shortName' => 'YOUR_SHORT_NAME'
        ),
        ...
```
The shortname is a specified name for your site that you will get by registering it on https://disqus.com/admin/create/
The API Key that you can receive by registering your application on https://disqus.com/api/applications/, it is required for using Disqus Api

For easy use a synchronisating console command you can add it in your commandMap in console config:
```php
'commandMap' => array(
        'update_disqus_comments' => array(
            'class' => 'ext.DisqusComments.commands.UpdateDisqusComments'
        ),
    ),
```
Apply the migration for create table for storing synchronised comments:
```
php yiic.php migrate --migrationPath=ext.DisqusComments.migrations
```

## Base Usage
Add widget from extension to pages that you want add comments
```php
<?php $this->widget('ext.DisqusComments.widgets.DisqusCommentsWidget', array('pageUrl' => $pageUrl)); ?>
```
This widget can receive a URL for current website page. It needed for getting synchronised comments from DB.

To synchronise comments from Disqus run the console command
```
 php yiic.php update_disqus_comments
```

This extension provides to use URL map for synchronisation. It contains list of URLs that have a Disqus comments.
The full URL map for your site you can download from Discus administration panel on https://iconschallenge.disqus.com/admin/discussions/migrate/ ("Start URL mapper" button).
Extension also provides to create URL map automatically. If you set a $autoUpdateMap parameter as true, extension will save every URL for page using Discus to your URL map.
```php
'components' => array(
        'disqusComments' => array(
            'class' => 'ext.DisqusComments.components.EDisqusComments',
            'apiKey' => 'YOUR_API_KEY',
            'shortName' => 'YOUR_SHORT_NAME',
            'autoUpdateMap' = true // this one
        ),
```
Please note that this parameter is true by default.

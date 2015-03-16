# yii-disqus-comments
Extension for adding Disqus comments (https://disqus.com) for Yii application.
Provides to use Disqus comments widget and synchronise comments from Disqus to your database (it can be useful for indexing comments by Google).

## Installation

Clone or extract the DisqusComments folder from archive under protected/extensions of your Yii application.

## Configuration
Extension need to be added in your application and console config as a component:
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

The API Key you can receive by registering your application on https://disqus.com/api/applications/, it is required for using Disqus Api

For easy use a synchronisating console commands you can add it to your commandMap in console config:
```php
'commandMap' => array(
        'update_disqus_comments' => array(
            'class' => 'ext.DisqusComments.commands.UpdateDisqusComments'
        ),
        'update_url_map' => array(
            'class' => 'ext.DisqusComments.commands.UpdateUrlMap'
        ),
    ),
```
Apply the migration to create table for storing synchronised comments:
```
php yiic.php migrate --migrationPath=ext.DisqusComments.migrations
```

## Base Usage
Add this widget to pages that you want add comments
```php
<?php $this->widget('ext.DisqusComments.widgets.DisqusCommentsWidget', array('pageUrl' => $pageUrl)); ?>
```
This widget can receive an URL for current website page. It needed for getting synchronised comments from DB.

To synchronise comments from Disqus run the console command
```
 php yiic.php update_disqus_comments
```
You can add it in your crontab to synchronise comments automatically.

This extension provides to use URL map for synchronisation. The map is a CSV file contains list of URLs that have a Disqus comments.

To update URL map from file, put this file to \helpers\data and run the console command
```
 php yiic.php update_url_map
```

The full URL map for your site you can download from Discus administration panel on https://iconschallenge.disqus.com/admin/discussions/migrate/ ("Start URL mapper" button).

Extension also provides to update URLs in database automatically. If you set a $autoUpdateMap parameter as true, extension will synchronized every URL for page using Discus to your comments table.
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

# yii-disqus-comments
Extension for adding Disqus comments (https://disqus.com) to Yii application.
Provides to use Disqus comments widget and synchronization your comments from Disqus to your database (it can be useful for indexing comments by Google).

## Installation

To install via composer require the package in your composer.json:
```
"imhelle/yii-disqus-comments": "dev-master"
```

Also you can just clone project or extract the archive under protected/extensions directory in your Yii application.

## Configuration
Extension need to be added in your application and console configs as a component:
```php
'components' => array(
        ...
        'disqusComments' => array(
            'class' => 'application.vendor.imhelle.yii-disqus-comments.EDisqusComments', // or "ext.yii-disqus-comments.EDisqusComments" if you install it in extension folder.
            'apiKey' => 'YOUR_API_KEY',
            'shortName' => 'YOUR_SHORT_NAME'
        ),
        ...
```
The shortname is a specified name for your site that you should get after registering on https://disqus.com/admin/create/

The API Key you'll receive by registering your application on https://disqus.com/api/applications/, it's required for using Disqus Api.

For easy use a synchronization console commands you can add it to your commandMap in console config:
```php
'commandMap' => array(
        'update_disqus_comments' => array(
            'class' => 'application.vendor.imhelle.yii-disqus-comments.commands.UpdateDisqusComments'
        ),
        'update_url_map' => array(
            'class' => 'application.vendor.imhelle.yii-disqus-comments.commands.UpdateUrlMap'
        ),
    ),
```
Apply the migration to create table for storing synchronized comments:
```
php yiic.php migrate --migrationPath=application.vendor.imhelle.yii-disqus-comments.migrations
```
Extension provides the cache usage for widget.
You can set your Id of cache component you use. 
```php
'components' => array(
        'disqusComments' => array(
            'class' => 'application.vendor.imhelle.yii-disqus-comments.EDisqusComments',
            'apiKey' => 'YOUR_API_KEY',
            'shortName' => 'YOUR_SHORT_NAME',
            'cacheId' = 'cache' // you can set it here
        ),
```

## Base Usage
Add this widget to the views to get the Disqus comments there:
```php
<?php $this->widget('disqusComments.widgets.DisqusCommentsWidget', array('pageUrl' => $pageUrl)); ?>
```
This widget can receive an URL for current website page. It's necessary for getting synchronized comments from DB.

Extension has console command for synchronize comments from Disqus.

To synchronize all comments from Disqus (by URLs you have in comments table) run the console command
```
 php yiic.php update_disqus_comments all
```
Note: if you have many Disqus threads, execution of this command may take a long time.

To synchronize only the recent comments you can run this:
```
 php yiic.php update_disqus_comments recent
```
This command will get your last update time and request only the comment threads that was update by this time.
It is recommended to add this command in your crontab to synchronize comments automatically. 

To get the initial URL map from Disqus API you can run the command 
```
 php yiic.php update_url_map fromApi
```

Also you can download the map in CSV format from Discus administration panel on https://iconschallenge.disqus.com/admin/discussions/migrate/ ("Start URL mapper" button) and update URLs from it:
```
 php yiic.php update_url_map fromCSV --filePath='PATH_TO_YOUR_FILE'
```

Extension also provides the automatic updating URLs in the database. If you set a $autoUpdateMap parameter as true, extension will synchronize the Disqus comments, when the widget is loaded.
```php
'components' => array(
        'disqusComments' => array(
            'class' => 'application.vendor.imhelle.yii-disqus-comments.EDisqusComments',
            'apiKey' => 'YOUR_API_KEY',
            'shortName' => 'YOUR_SHORT_NAME',
            'autoUpdateMap' = true // this one
        ),
```
Please note that this parameter is true by default.

<?php

return [
    'feeds' => [
        'dcln' => [
            /*
             * Here you can specify which class and method will return
             * the items that should appear in the feed. For example:
             * [App\Model::class, 'getAllFeedItems']
             *
             * You can also pass an argument to that method. Note that their key must be the name of the parameter:
             * [App\Model::class, 'getAllFeedItems', 'parameterName' => 'argument']
             */
            'items' => [\App\Models\Episode::class, 'getPodcastFeedItems', 'owner' => 2],

            /*
             * The feed will be available on this url.
             */
            'url'   => '/feeds/dcln',

            'title'       => 'Deeper Christian Life Network Master Classes',
            'description' => 'The Master Classes are powerful, never-before-released audio messages by Frank that can only be found on this Network.',
            'language'    => 'en-US',

            /*
             * The image to display for the feed. For Atom feeds, this is displayed as
             * a banner/logo; for RSS and JSON feeds, it's displayed as an icon.
             * An empty value omits the image attribute from the feed.
             */
            'image'       => 'https://www.thedeeperchristianlife.com/wp-content/uploads/2015/08/DCL_logo.png',

            /*
             * The format of the feed. Acceptable values are 'rss', 'atom', or 'json'.
             */
            'format'      => 'rss',

            /*
             * The view that will render the feed.
             */
            'view'        => 'feed::rss',

            /*
             * The mime type to be used in the <link> tag. Set to an empty string to automatically
             * determine the correct value.
             */
            'type'        => '',

            /*
             * The content type for the feed response. Set to an empty string to automatically
             * determine the correct value.
             */
            'contentType' => '',
        ],
    ],
];

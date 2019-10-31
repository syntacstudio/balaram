<?php

return [
    // folder target to backup
    // Multiple folders allowed, separated with comma without whitespace
    // example: storage,public
    'target' => env('BACKUP_TARGET', 'storage'),

    // setup the telegram
    'telegram' => [
        // token your bot
        'token' => env('TELEGRAM_BOT_TOKEN'),

        // yout bot username
        'bot_username' => env('TELEGRAM_BOT_USERNAME'),

        /*
         | 1- Add the bot to the group.
         | Go to the group, click on group name, click on Add members, in the searchbox search for your | bot like this: @my_bot, select your bot and click add.
         |
         | 2- Send a dummy message to the bot.
         | You can use this example: /my_id @my_bot
         | (I tried a few messages, not all the messages work. The example above works fine. Maybe the message should start with /)
         |
         | 3- Go to following url: https://api.telegram.org/botXXX:YYYY/getUpdates
         | replace XXX:YYYY with your bot token
         | 
         | 4- Look for “chat”:{“id”:-zzzzzzzzzz,
         | -zzzzzzzzzz is your chat id (with the negative sign).
         */
        'chat_id' => env('TELEGRAM_CHAT_ID')
    ],

    'database' => [
        /**
         * Set to false if you won't backup the database
         */
        'backup' => env('BACKUP_DATABASE', true),
        /**
         * Database type
         * Supported db: MySql, PostgreSQL, SQLite, MongoDB
         * Default: MySql
         */
        'type' => env('BACKUP_DATABSE_TYPE', 'MySql')
    ]
];
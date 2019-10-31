<?php

namespace Syntac\Balaram\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Carbon\Carbon;
use \zipArchive;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;
use Spatie\DbDumper\Databases\MySql;
use Spatie\DbDumper\Databases\PostgreSql;
use Spatie\DbDumper\Databases\SQLite;
use Spatie\DbDumper\Databases\MongoDB;
use Spatie\DbDumper\Compressors\GzipCompressor;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;

class BackupRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start to backup Laravel app into telegram';

    /**
     * Supported db
     * 
     * @var array
     */
    protected $supportedDB = ['MySql', 'PostgreSQL', 'SQLite', 'MongoDB'];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Backup file name
     * 
     * @return string
     */
    protected function backupFileName(){
        return Str::slug('files-backup-'.config('app.name').'-'.Carbon::now()).'.zip';
    }

    /**
     * Database backup file name
     * 
     * @return string
     */
    protected function dbBackupFileName(){
        return Str::slug('database-backup-'.config('app.name').'-'.Carbon::now()).'.sql.gz';
    }

    /**
     * Target directory will be compressed
     * 
     * @return string
     */
    protected function targetDir()
    {
        $folders    = explode(',', config('balaram.target'));
        $target     = [];

        foreach($folders as $folder) {
            $target[$folder] = base_path($folder);
        }

        return $target;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $targets    = $this->targetDir();
        $fileBackup = $this->backupFileName();
        $dbBackup   = $this->dbBackupFileName();

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open(storage_path('app/'.$fileBackup), ZipArchive::CREATE | ZipArchive::OVERWRITE);
        
        $this->info('Starting to compress your website files');
        foreach($targets as $folder => $path) {
            // Create recursive directory iterator
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                // Skip directories (they would be added automatically)
                if (!$file->isDir()) {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($path) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $folder.'/'.$relativePath);
                }
            }
        }

        // Create the archive file
        $zip->close();
        $this->info('Your website files successfully compressed.');
        $this->info('Starting to dump your website database.');

        // dump database if enable
        if( config('balaram.database.backup') && in_array(config('balaram.database.type'), $this->supportedDB) ) {
            // dump database proccess
            switch (config('balaram.database.type')) {
                case 'MySql':
                    $dumper = MySql::create();
                    break;
                
                case 'PostgreSQL':
                    $dumper = PostgreSql::create();
                    break;
                
                case 'SQLite':
                    $dumper = SQLite::create();
                    break;
                
                case 'MongoDB':
                    $dumper = MongoDB::create();
                    break;
                
                default:
                    $dumper = MySql::create();
                    break;
            }

            $dumper->setHost(ENV('DB_HOST'))
                ->setPort(ENV('DB_PORT'))
                ->setDbName(ENV('DB_DATABASE'))
                ->setUserName(ENV('DB_USERNAME'))
                ->setPassword(ENV('DB_PASSWORD'))
                ->useCompressor(new GzipCompressor())
                ->dumpToFile(storage_path('app/'.$dbBackup));

            $this->info('Your website database successfully archived.');

            $bot_token      = config('balaram.telegram.token');
            $bot_username   = config('balaram.telegram.bot_username');

            $fileSize   = filesize(storage_path('app/'.$fileBackup));
            $dbSize     = filesize(storage_path('app/'.$dbBackup));

            try {
                // Create Telegram API object
                $telegram = new Telegram($bot_token, $bot_username);

                if($fileSize < 50000000){
                    $this->info('Starting to upload your backup files to telegram.');
            
                    Request::sendDocument([
                        'chat_id' => config('balaram.telegram.chat_id'),
                        'document' => storage_path('app/'.$fileBackup),
                        'caption' => 'Backup files for website '. config('app.name') .' ('. config('app.url') .') generated at: '. Carbon::now()
                    ]);

                    $this->info('Your backup files successfully uploaded to telegram.');
                } else {
                    $this->info('Your backup files size more than 50mb, we can\'t upload your backup files to telegram.');
                    Request::sendMessage([
                        'chat_id' => config('balaram.telegram.chat_id'),
                        'text' => 'Your backup files size more than 50mb, we can\'t upload your backup files to telegram.'
                    ]);
                }

                if($dbSize < 50000000){
                    $this->info('Starting to upload your backup database file to telegram.');

                    Request::sendDocument([
                        'chat_id' => config('balaram.telegram.chat_id'),
                        'document' => storage_path('app/'.$dbBackup),
                        'caption' => 'Backup database file for website '. config('app.name') .' ('. config('app.url') .') generated at: '. Carbon::now()
                    ]);

                    $this->info('Your backup database file successfully uploaded to telegram.');
                } else {
                    $this->info('Your backup database file size more than 50mb, we can\'t upload your backup files to telegram.');

                    Request::sendMessage([
                        'chat_id' => config('balaram.telegram.chat_id'),
                        'text' => 'Your backup database file size more than 50mb, we can\'t upload your backup files to telegram.'
                    ]);
                }

                unlink(storage_path('app/'.$fileBackup));
                unlink(storage_path('app/'.$dbBackup));
                
            } catch (Longman\TelegramBot\Exception\TelegramException $e) {
                $this->error($e->getMessage());

                Request::sendMessage([
                    'chat_id' => config('balaram.telegram.chat_id'),
                    'text' => $e->getMessage()
                ]);
            }
        }
    }
}

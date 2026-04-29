<?php

namespace App\Console\Commands;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Console\Command;
use Spatie\DbDumper\Databases\MySql;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BackupGoogleDrive extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'backup:google';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description';

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
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{


		$storagePath = storage_path('app/backups/');
		if (!is_dir($storagePath)) {
			mkdir($storagePath);
		}
		$fileName = env('DB_DATABASE') . "_" . date('YmdHis') . ".sql";
		$backupName = $storagePath . $fileName;
		// $dumpSqlCommand = "mysqldump --user=" . env('DB_USERNAME') . " --password=" . env('DB_PASSWORD') . " --result-file=" . $backupName . " --databases " . env('DB_DATABASE');
		// $dumpSqlCommand="docker exec " . env('DB_HOST') . " /usr/bin/mysqldump -u " . env('DB_USERNAME') . " --password=". env('DB_PASSWORD') ." ". env('DB_DATABASE') . " > " . $backupName;
		// $this->info($dumpSqlCommand);
		// shell_exec($dumpSqlCommand);

		MySql::create()
    ->setDbName(env('DB_DATABASE'))
    ->setUserName(env('DB_USERNAME'))
    ->setPassword(env('DB_PASSWORD'))
		->setHost(env('DB_HOST'))
    ->dumpToFile($backupName);

		$client = new Client();
		$client->setApplicationName("Test");
		$client->setAuthConfig('client_secret.json');

		$client->addScope(Drive::DRIVE);

		$accessToken = $client->refreshToken(env("REFRESH_TOKEN"));

		$client->setAccessToken($accessToken['access_token']);

		$service = new Drive($client);

		$file = new Drive\DriveFile();
		$file->setName($fileName);
		$file->setParents([env('BACKUP_FOLDER')]);
		$results = $service->files->create($file, array(
			'data' => file_get_contents($backupName),
			'mimeType' => 'application/octet-stream',
			'uploadType' => 'media'
		));

		unlink($backupName);

		$this->info('Backup criado');
		Log::info('Backup criado');

	}
}

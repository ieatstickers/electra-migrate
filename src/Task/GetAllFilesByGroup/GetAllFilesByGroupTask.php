<?php

namespace Electra\Module\Migration\Task\GetAllFilesByGroup;

use Electra\Core\Task\AbstractTask;
use Electra\Module\Migration\Data\MigrationFile;
use Electra\Utility\Arrays;
use Electra\Utility\Strings;
use Symfony\Component\Console\Output\Output;

class GetAllFilesByGroupTask extends AbstractTask
{
  /** @return string */
  public function getPayloadClass(): string
  {
    return GetAllFilesByGroupPayload::class;
  }

  /**
   * @param GetAllFilesByGroupPayload $payload
   * @return GetAllFilesByGroupResponse
   * @throws \Exception
   */
  protected function process($payload): GetAllFilesByGroupResponse
  {
    $output = $payload->output;
    $response = new GetAllFilesByGroupResponse();

    $migrationDirectories = $payload->migrationDirs;
    $migrationFilesByGroup = [];

    foreach ($migrationDirectories as $group => $migrationDirectoryConfig)
    {
      $migrationDirectoryName = Arrays::getByKey('name', $migrationDirectoryConfig);
      $migrationDirectory = Arrays::getByKey('dirPath', $migrationDirectoryConfig);

      if (!is_string($group))
      {
        $this->throwError("Config array with key 'migrationsDir' in electra.yaml must be associative", $output);
      }

      if (!$migrationDirectoryName)
      {
        $this->throwError("'name' key not set for configured migration directory: $group", $output);
      }

      if (!$migrationDirectory)
      {
        $this->throwError("'dirPath' key not set for configured migration directory: $group", $output);
      }

      if (!file_exists($migrationDirectory))
      {
        $this->throwError("Configured migration directory does not exist: $migrationDirectory", $output);
      }

      // Get all files in directory
      $dirContents = scandir($migrationDirectory);
      $groupMigrationArray = [];

      // For each one, generate the full path to it
      foreach ($dirContents as $filename)
      {
        if (
          $filename == '.'
          || $filename == '..'
          || !Strings::endsWith($filename, '.php')
        )
        {
          continue;
        }

        $fullMigrationFilePath = $migrationDirectory . '/' . $filename;
        [$year, $month, $day, $time, $className] = explode('_', $filename);
        $timestamp = implode('_', [$year, $month, $day, $time]);
        $migrationClassName = preg_replace('/\.php$/', '', $className);

        if (!isset($migrationFilesByGroup[$group]))
        {
          $migrationFilesByGroup[$group] = [];
        }

        // Read file in
        $fileContents = file_get_contents($fullMigrationFilePath);

        if (!$fileContents)
        {
          $this->throwError("Migration file empty: " . $fullMigrationFilePath, $output);
        }

        $matches = null;
        preg_match("/namespace ([^;]+);/", $fileContents, $matches);

        if (!$matches || !isset($matches[1]))
        {
          $this->throwError("Cannot find migration namespace in: " . $fullMigrationFilePath, $output);
        }

        $namespace = $matches[1];

        $migrationFile = new MigrationFile();
        $migrationFile->classname = $migrationClassName;
        $migrationFile->name = $timestamp . "_" . $migrationClassName;
        $migrationFile->filename = $filename;
        $migrationFile->filepath = $fullMigrationFilePath;
        $migrationFile->fqns = '\\' . $namespace . '\\' . $migrationClassName;
        $migrationFile->directoryKey = $group;
        $migrationFile->directoryDisplayName = $migrationDirectoryName;
        $migrationFile->timestamp = $timestamp;

        // Add to the array under the correct group
        $groupMigrationArray[rtrim($filename, '.php')] = $migrationFile;
      }

      ksort($groupMigrationArray);
      $migrationFilesByGroup[$group] = $groupMigrationArray;
    }

    $response->filesByGroup = $migrationFilesByGroup;

    return $response;
  }

  /**
   * @param string $message
   * @param Output $output
   * @throws \Exception
   */
  public function throwError(string $message, $output)
  {
    if ($output)
    {
      $output->writeln(
        "<fg=red>$message</>"
      );
      die;
    }

    throw new \Exception($message);
  }
}
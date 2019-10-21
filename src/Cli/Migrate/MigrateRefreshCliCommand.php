<?php

namespace Electra\Migrate\Cli\Migrate;

use Electra\Config\Config;
use Electra\Dal\Database\Mysql\Mysql;
use Electra\Migrate\Event\MigrateAll\MigrateAllPayload;
use Electra\Migrate\Event\MigrationEvents;
use Electra\Utility\Arrays;
use Electra\Utility\Objects;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class MigrateRefreshCliCommand extends AbstractMigrateCommand
{
  protected static $defaultName = 'migrate:refresh';

  protected function configure()
  {
    $this
      ->setDescription('Re-run all migrations')
      ->setHelp('Delete all tables and execute all migrations');
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|void|null
   * @throws \Exception
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $helper = $this->getHelper('question');
    $question = new ConfirmationQuestion('All data will be lost. Are you sure you want to refresh migrations? (y/N)' . PHP_EOL, false);

    if (!$helper->ask($input, $output, $question)) {
      $output->writeln("<fg=red>Aborted</>");
      return;
    }

    $defaultConnectionName = Mysql::connection()->getName();
    $allConnections = Config::getByPath('electra:dal:connections');

    $defaultConnectionDbName = null;

    foreach ($allConnections as $connectionName => $connectionConfig)
    {
      if ($connectionName == $defaultConnectionName)
      {
        $defaultConnectionDbName = Arrays::getByKey('database', $connectionConfig);
      }
    }

    // Drop all databases apart from one configured in default connection
    $databases = Mysql::connection()->select("
      SELECT DISTINCT SCHEMA_NAME AS `database`
      FROM information_schema.SCHEMATA
      WHERE  SCHEMA_NAME NOT IN ('information_schema', 'performance_schema', 'mysql', 'sys', '$defaultConnectionDbName')
      ORDER BY SCHEMA_NAME
    ");

    foreach ($databases as $row)
    {
      $dbName = Objects::getProperty('database', $row);

      if ($dbName)
      {
        Mysql::connection()->statement(
          "DROP DATABASE `{$dbName}`"
        );
      }
    }

    // Drop all tables in default database
    Mysql::schema()->dropAllTables();

    $output->writeln(
      "<fg=green>All databases cleared successfully</>"
    );

    $migrateAllPayload = MigrateAllPayload::create();
    $migrateAllPayload->migrationDirs = Config::getByPath("electra:migrate:migrationDirs");
    $migrateAllPayload->output = $output;
    $migrateAllResponse = MigrationEvents::migrateAll($migrateAllPayload);

    // Migrate all failed
    if (!$migrateAllResponse->success)
    {
      $output->writeln("<fg=green>An error occurred while running migrations</>");
      return;
    }

    // Successfully run migrations
    if ($migrateAllResponse->executedMigrationsCount)
    {
      $pluralMigration = $migrateAllResponse->executedMigrationsCount > 1 ? 's' : '';
      $output->writeln(
        "<fg=green>{$migrateAllResponse->executedMigrationsCount} migration{$pluralMigration} executed successfully</>"
      );
      return;
    }
  }
}
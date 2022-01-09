<?php

namespace Electra\Migrate\Cli\Migrate;

use Carbon\Carbon;
use Electra\Utility\Arrays;
use Electra\Utility\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateMakeCliCommand extends AbstractMigrateCommand
{
  protected static $defaultName = 'migrate:make';

  protected function configure()
  {
    $this
      ->setDescription('Generate a new migration')
      ->setHelp('Generate a new migration file and add it to the configured migrations directory')
      ->addArgument('name', InputArgument::REQUIRED, 'What is your migration called? e.g. CreateUserTable')
      ->addArgument('connection', InputArgument::OPTIONAL, 'What database connection will this migration use?')
      ->addArgument('table', InputArgument::OPTIONAL, 'What database table will this migration use?')
      ->addArgument('migrationDir', InputArgument::OPTIONAL, 'What migration directory should be used? e.g. billing')
    ;
  }

  /**
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|void|null
   * @throws \Exception
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $outputMigrationDirectories = $this->getContext()->getConfig()->getByPath('electra:migrate:migrationDirs');
    $migrationDirectory = null;
    $migrationName = $input->getArgument('name');
    $migrationConnection = $input->getArgument('connection');
    $migrationTable = $input->getArgument('table');
    $migrationNamespace = null;

    if (
      ($migrationConnection && !$migrationTable)
      ||
      (!$migrationConnection && $migrationTable)
    )
    {
      $output->writeln("<fg=red>Connection and table options must be provided together or not at all.</>");
      return Command::INVALID;
    }

    $inputDir = $input->getArgument('migrationDir');

    if (
      $inputDir
      && $inputDir !== 'default'
    )
    {
      $outputMigrationDirectory = $outputMigrationDirectories[$inputDir] ?? null;
      $migrationDirectory = Arrays::getByKey('dirPath', $outputMigrationDirectory);
      $migrationNamespace = Arrays::getByKey('namespace', $outputMigrationDirectory);

      if (!$migrationNamespace)
      {
        $output->writeln("<fg=red>No namespace found for migration directory: $inputDir</>");
        return Command::INVALID;
      }
    }
    else
    {
      // Find default migration directory and namespace (error if they don't exist)
      foreach ($outputMigrationDirectories as $dirKey => $outputMigrationDirectory)
      {
        if (Arrays::getByKey('default', $outputMigrationDirectory))
        {
          $migrationDirectory = Arrays::getByKey('dirPath', $outputMigrationDirectory);
          $migrationNamespace = Arrays::getByKey('namespace', $outputMigrationDirectory);

          if (!$migrationNamespace)
          {
            $output->writeln("<fg=red>No namespace found for default migration directory: $dirKey</>");
            return Command::INVALID;
          }

          break;
        }
      }
    }

    // Error if it doesn't exist
    if (!$migrationDirectory)
    {
      $output->writeln("<fg=red>Cannot use the migrate:make command without specifying a default migration directory and namespace in electra.yaml</>");
      return Command::INVALID;
    }

    $migrationFileTemplate = file_get_contents(realpath(__DIR__ . '/FileTemplate/Migration.template'));
    $methodBlockTemplate = 'Mysql::schema("{connection}")->table("{table}", function(Blueprint $table) 
    {
        
    });';
    $useStatements = ['use Electra\Migrate\Migration;'];

    $replacements = [
      '{connection}' => $migrationConnection,
      '{table}' => $migrationTable,
      '{namespace}' => $migrationNamespace,
      '{className}' => $migrationName,
      '{methodContent}' => ""
    ];

    // if connection name and table are specified
    if ($migrationConnection && $migrationTable)
    {
      // Add method block code to the replacements array
      $replacements['{methodContent}'] = Strings::replace($methodBlockTemplate, $replacements);
      // Include additional use statements required
      $useStatements[] = "use Electra\Dal\Database\Mysql\Mysql;";
      $useStatements[] = "use Illuminate\Database\Schema\Blueprint;";
    }

    $replacements['{useStatements}'] = implode(PHP_EOL, $useStatements);

    $fileContents = Strings::replace($migrationFileTemplate, $replacements);

    $now = new Carbon();
    $year = $now->year;
    $month = str_pad($now->month, 2, '0', STR_PAD_LEFT);
    $day = str_pad($now->day, 2, '0', STR_PAD_LEFT);
    $hour = str_pad($now->hour, 2, '0', STR_PAD_LEFT);
    $minutes = str_pad($now->minute, 2, '0', STR_PAD_LEFT);
    $seconds = str_pad($now->second, 2, '0', STR_PAD_LEFT);
    $filename = "{$year}_{$month}_{$day}_{$hour}{$minutes}{$seconds}_{$migrationName}";
    $outputFilePath = __DIR__ . '/../../../../../../' . $migrationDirectory . "/{$filename}.php";

    // Write file
    file_put_contents($outputFilePath, $fileContents);

    $output->writeln("<fg=green>Migration created successfully: $filename</>");

    return Command::SUCCESS;
  }
}

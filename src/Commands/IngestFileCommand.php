<?php

declare(strict_types=1);

namespace App\Commands;

use App\Service\FileIngestionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:ingest:file',
    description: 'Ingest json file into the database.',
    hidden: false,
)]
class IngestFileCommand extends Command
{
    public function __construct(
        private readonly FileIngestionService $ingestionService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('path', InputArgument::REQUIRED, 'path to the json file to ingest');
        $this->addOption('truncate', null, InputOption::VALUE_NONE, 'Truncate the table before execution');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $shouldTruncate = $input->getOption('truncate');

        if ($shouldTruncate) {
            $confirmTruncate = $io->confirm('Are you sure you want to truncate the table before ingestion?', false);

            if (!$confirmTruncate) {
                $io->warning('Ingestion operation canceled');

                return Command::FAILURE;
            }
        }

        $path = $input->getArgument('path');

        try {
            $this->ingestionService->ingestFile($path, $shouldTruncate);
        } catch (\Exception) {
            $io->error('Ingestion operation failed');

            return Command::FAILURE;
        }

        $io->success('Ingestion operation completed');

        return Command::SUCCESS;
    }
}

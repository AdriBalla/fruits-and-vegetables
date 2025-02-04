<?php

declare(strict_types=1);

namespace App\Tests\App\Command;

use App\Commands\IngestFileCommand;
use App\Service\FileIngestionService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @covers \App\Commands\IngestFileCommand
 */
class IngestFileCommandTest extends TestCase
{
    private CommandTester $commandTester;
    private FileIngestionService&MockObject $fileIngestionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileIngestionService = $this->createMock(FileIngestionService::class);

        $command = new IngestFileCommand($this->fileIngestionService);

        $this->commandTester = new CommandTester($command);
    }

    public function testExecuteSuccess(): void
    {
        $filePath = '/fake/path/to/file.json';

        $this->fileIngestionService
            ->expects($this->once())
            ->method('ingestFile')
            ->with($filePath, false);

        $exitCode = $this->commandTester->execute(['path' => $filePath]);

        $this->assertEquals(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Ingestion operation completed', $this->commandTester->getDisplay());
    }

    public function testExecuteFailure(): void
    {
        $filePath = '/fake/path/to/file.json';

        $this->fileIngestionService
            ->expects($this->once())
            ->method('ingestFile')
            ->willThrowException(new \Exception('Test Exception'));

        $exitCode = $this->commandTester->execute(['path' => $filePath]);

        $this->assertEquals(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Ingestion operation failed', $this->commandTester->getDisplay());
    }

    public function testExecuteWithTruncateConfirmed(): void
    {
        $filePath = '/fake/path/to/file.json';

        $this->fileIngestionService
            ->expects($this->once())
            ->method('ingestFile')
            ->with($filePath, true);

        $this->commandTester->setInputs(['yes']);
        $exitCode = $this->commandTester->execute(['path' => $filePath, '--truncate' => true]);

        $this->assertEquals(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Ingestion operation completed', $this->commandTester->getDisplay());
    }

    public function testExecuteWithTruncateCancelled(): void
    {
        $filePath = '/fake/path/to/file.json';

        $this->fileIngestionService
            ->expects($this->never())
            ->method('ingestFile');

        $this->commandTester->setInputs(['no']);
        $exitCode = $this->commandTester->execute(['path' => $filePath, '--truncate' => true]);

        $this->assertEquals(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Ingestion operation canceled', $this->commandTester->getDisplay());
    }
}

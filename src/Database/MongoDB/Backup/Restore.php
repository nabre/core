<?php

namespace Nabre\Database\MongoDB\Backup;

use Spatie\DbDumper\DbDumper;
use Spatie\DbDumper\Exceptions\CannotStartDump;
use Symfony\Component\Process\Process;

class Restore extends DbDumper
{
    protected int $port = 27017;

    protected ?string $collection = null;

    protected ?string $authenticationDatabase = null;

    public function restoreFromFile(string $dumpFile)
    {
        $this->guardAgainstIncompleteCredentials();
        $process = $this->getProcess($dumpFile);
        $process->run();
        //$this->checkIfDumpWasSuccessFul($process, $dumpFile);
    }

    public function guardAgainstIncompleteCredentials(): void
    {
        foreach (['dbName', 'host'] as $requiredProperty) {
            if (strlen($this->$requiredProperty) === 0) {
                throw CannotStartDump::emptyParameter($requiredProperty);
            }
        }
    }

    public function setCollection(string $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    public function setAuthenticationDatabase(string $authenticationDatabase): self
    {
        $this->authenticationDatabase = $authenticationDatabase;

        return $this;
    }

    public function getRestoreCommand(string $filename): string
    {
        $quote = $this->determineQuote();

        $command = [
            "{$quote}{$this->dumpBinaryPath}mongorestore{$quote}",
            "--db {$this->dbName}",
            "--drop",
            "--archive={$filename}",
        ];

        if ($this->userName) {
            $command[] = "--username {$quote}{$this->userName}{$quote}";
        }

        if ($this->password) {
            $command[] = "--password {$quote}{$this->password}{$quote}";
        }

        if (isset($this->host)) {
            $command[] = "--host {$this->host}";
        }

        if (isset($this->port)) {
            $command[] = "--port {$this->port}";
        }

        if (isset($this->collection)) {
            //      $command[] = "--collection {$this->collection}";
        }

        if ($this->authenticationDatabase) {
            $command[] = "--authenticationDatabase {$this->authenticationDatabase}";
        }

        return implode(' ', $command); //$this->echoToFile(implode(' ', $command), $filename);
    }

    public function getProcess(string $dumpFile): Process
    {
        $command = $this->getRestoreCommand($dumpFile);
        return Process::fromShellCommandline($command, null, null, null, $this->timeout);
    }

    function dumpToFile(string $dumpFile): void
    {
    }
}

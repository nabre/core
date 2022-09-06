<?php

namespace Nabre\Database\MongoDB\Backup;

class Execute
{
    static function dump()
    {
        $dbConfig = config("database.connections.mongodb");
        $dbDumper = Dump::create()
            ->setHost($dbConfig['host'] ?? '')
            ->setDbName($dbConfig['database'])
            ->setUserName($dbConfig['username'] ?? '')
            ->setPassword($dbConfig['password'] ?? '')
            ->setAuthenticationDatabase($dbConfig['options']['database'] ?? '');

        if (isset($dbConfig['port'])) {
            $dbDumper = $dbDumper->setPort($dbConfig['port']);
        }

        $path = \Storage::disk('backup')->path('');
        if (!\File::isDirectory($path)) {
            \File::makeDirectory($path, 0777, true, true);
        }

        $upDate = date("Ymd_His");
        $file = "{$path}{$dbConfig['database']}_{$upDate}.gz";
        $dbDumper->dumpToFile($file);

        return "saving to $file";
    }

    static function restore($file)
    {
        $dbConfig = config("database.connections.mongodb");
        $dbDumper = Restore::create()
            ->setHost($dbConfig['host'] ?? '')
            ->setDbName($dbConfig['database'])
            ->setUserName($dbConfig['username'] ?? '')
            ->setPassword($dbConfig['password'] ?? '')
            ->setAuthenticationDatabase($dbConfig['options']['database'] ?? '');

        if (isset($dbConfig['port'])) {
            $dbDumper = $dbDumper->setPort($dbConfig['port']);
        }

        $dbDumper->restoreFromFile($file);

        return "loaded from $file";
    }
}

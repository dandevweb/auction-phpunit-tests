<?php

namespace Dandevweb\Auction\Infra;

class ConnectionCreator
{
    private static $pdo = null;

    public static function getConnection(): \PDO
    {
        if (is_null(self::$pdo)) {
            $pathDb = __DIR__ . '/../../banco.sqlite';
            self::$pdo = new \PDO('sqlite:' . $pathDb);
            self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}

<?php

namespace App\Core;

use Nette\Schema\Expect;
use Nette\Schema\Schema;
use PDO;

class DatabaseConnection
{
    protected PDO $con;

    public function __construct(
        protected string $driver,
        protected string $host,
        protected string $database,
        protected string $username,
        protected string $password,
        protected array $options = [],
    ) {
    }

    public function connect(): PDO
    {
        // Crea la conexión sólo una vez
        if (!isset($this->con)) {
            $defaultOptions = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                // Para incluir en `$stmt->rowCount()` las filas no cambiadas por un UPDATE
                PDO::MYSQL_ATTR_FOUND_ROWS => true,

                // Para no cerrar la conexión al finalizar el script (probar primero)
                //PDO::ATTR_PERSISTENT => true,
            ];

            $this->con = new PDO(
                "{$this->driver}:host={$this->host};dbname={$this->database}",
                $this->username,
                $this->password,
                $defaultOptions + $this->options,
            );
            $this->con->exec('set names utf8');
        }



        return $this->con;
    }


    public static function getConfigSchema(): Schema
    {
        return Expect::structure([
            'driver' => Expect::anyOf('mysql', 'postgresql', 'sqlite')->required(),
            'host' => Expect::string()->default('localhost'),
            'database' => Expect::string()->required(),
            'username' => Expect::string()->required(),
            'password' => Expect::string()->nullable(),
        ]);
    }
}

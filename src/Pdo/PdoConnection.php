<?php

declare(strict_types=1);

namespace Tomrf\Seminorm\Pdo;

use PDO;
use RuntimeException;
use Tomrf\Seminorm\Interface\ConnectionInterface;

class PdoConnection implements ConnectionInterface
{
    protected ?PDO $pdo = null;
    // protected string|PDO $dsnOrPdo;

    /**
     * @param PDO|string          $dsnOrPdo DSN string or an existing PDO object
     * @param null|array<int,int> $options  PDO options array
     */
    public function __construct(
        protected string|PDO $dsnOrPdo,
        protected ?string $username = null,
        protected ?string $password = null,
        protected ?array $options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ],
    ) {
        if ($this->dsnOrPdo instanceof PDO) {
            $this->pdo = $this->dsnOrPdo;
            $this->options = null;
        }
    }

    /**
     * Get the PDO resource object for this connection.
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * Get PDO options array for this connection.
     *
     * @return null|array<int, int>
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * Returns true if database connection has been established.
     */
    public function isConnected(): bool
    {
        return (null === $this->pdo) ? false : true;
    }

    /**
     * Get the value of DSN.
     */
    public function getDsn(): ?string
    {
        return \is_string($this->dsnOrPdo) ? $this->dsnOrPdo : null;
    }

    /**
     * Get the value of username.
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Static helper function to build DSN string for PDO.
     */
    public static function dsn(
        string $driver,
        string $dbname,
        string $host = null,
        int $port = 3306,
        string $charset = 'utf8mb4'
    ): string {
        if ('sqlite' === mb_strtolower($driver)) {
            return sprintf('%s:%s', $driver, $dbname);
        }

        return sprintf(
            '%s:host=%s;dbname=%s;port=%d;charset=%s',
            $driver,
            $host,
            $dbname,
            $port,
            $charset
        );
    }

    /**
     * Connect to the database if not already connected.
     *
     * @throws RuntimeException
     */
    public function connect(): void
    {
        if (null !== $this->pdo || !\is_string($this->dsnOrPdo)) {
            return;
        }

        try {
            $this->pdo = new PDO(
                $this->dsnOrPdo,
                $this->username,
                $this->password,
                $this->options
            );
        } catch (\PDOException $exception) {
            throw new RuntimeException(
                sprintf('Unable to connect to database: %s', $exception)
            );
        }

        $this->password = '******';
    }

    /**
     * Disconnect from the database.
     */
    public function disconnect(): void
    {
        $this->pdo = null;
    }
}

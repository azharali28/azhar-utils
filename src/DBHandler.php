<?php
namespace AzharUtils;

use PDO;
use PDOException;
use PDOStatement;

class DBHandler {

    private static PDO $pdo;

    public static function generateUuid() {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Set version to 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Set variant to 10xx
        $uuid = vsprintf('%02x%02x%02x%02x-%02x%02x-%02x%02x-%02x%02x-%02x%02x%02x%02x%02x%02x',str_split($data));
        return $uuid;
    }

    public static function selectOne(string $sql, array $params = []): array|false {
        $stmt = self::prepareAndExecute($sql, $params);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    public static function selectAll(string $sql, array $params = []): array|false {
        $stmt = self::prepareAndExecute($sql, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : false;
    }

    public static function selectColumn(string $sql, array $params = []): string|false {
        $stmt = self::prepareAndExecute($sql, $params);
        return $stmt ? $stmt->fetchColumn() : false;
    }

    public static function exists(string $sql, array $params = []): bool {
        $result = self::selectOne($sql, $params);
        return !empty($result); 
    }

    public static function insert(string $sql, array $params = []): string|false {
        $stmt = self::prepareAndExecute($sql, $params);
        return $stmt ? self::$pdo->lastInsertId() : false;
    }

    public static function update(string $sql, array $params = []): int|false {
        $stmt = self::prepareAndExecute($sql, $params);
        return $stmt ? $stmt->rowCount() : false;
    }

    public static function delete(string $sql, array $params = []): int|false {
        $stmt = self::prepareAndExecute($sql, $params);
        return $stmt ? $stmt->rowCount() : false;
    }

    public static function beginTransaction(): bool {
        return self::$pdo->beginTransaction();
    }

    public static function commit(): bool {
        return self::$pdo->commit();
    }

    public static function rollback(): bool {
        return self::$pdo->rollBack();
    }

    public static function connect(): PDO|false {
        $config = require '../config/config.php';
        $db = $config['db'];
        $driver = $db['driver'];
        $name = $db['name'];
        $host = $db['host'];
        $username = $db['username'];
        $password = $db['password'];
        $options = $db['options'];
        $dsn = "$driver:dbname=$name;host=$host";

        try {
            self::$pdo = new PDO($dsn, $username, $password, $options);
            return self::$pdo;
        } catch (PDOException $e) {
            Logger::error(__METHOD__, "Failed to open database connection: " . $e->getMessage());
            exit;
        }
    }

    private static function ensureInit(): void {
        if (!isset(self::$pdo)) {
            self::connect();
            Logger::info(__METHOD__, "PDO Connection established");
        } else {
            Logger::info(__METHOD__, "PDO Connection already open");
        }
    }

    private static function prepareAndExecute(string $sql, array $params = []): PDOStatement|false {
        self::ensureInit();
        try {
            $cleanSql = trim(preg_replace('/[\r\n\t\s]+/', ' ', $sql));

            $stmt = self::$pdo->prepare($sql);
            if (!$stmt) {
                Logger::error(__METHOD__, "Failed to prepare statement '$cleanSql'" . json_encode(self::$pdo->errorInfo()));
                return false;
            }

            $start = microtime(true);
            if (!$stmt->execute($params)) {
                Logger::error(__METHOD__, "Failed to execute statement '$cleanSql'" . json_encode(self::$pdo->errorInfo()));
                return false;
            }
            $duration = microtime(true) - $start;
            Logger::debug(__METHOD__, number_format($duration, 6) . "s '$cleanSql'");

            return $stmt;
        } catch (PDOException $e) {
            Logger::error(__METHOD__, 'PDOException ' . $e->getMessage());
            return false;
        }
    }
}
?>  

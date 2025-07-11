<?php
namespace AzharUtils;

use Exception;

class PathManager {

    public static function ensureFile(string $path, string $filename, string $header = ""): string {
        $pathName = rtrim(self::ensurePath($path, true), DIRECTORY_SEPARATOR);
        $filePath = $pathName . DIRECTORY_SEPARATOR . $filename;
        if (file_exists($filePath)) {
            throw new Exception("File '$filePath' already exists");
        }

        if (!file_put_contents($filePath, $header)) {
            throw new Exception("Failed to create file '$filePath'");
        }
        return $filePath;
    }

    private static function ensurePath(string $path, bool $allowExisting = false, int $permissions = 0755): string {
        if (is_dir($path)) {
            if (!$allowExisting) {
                throw new Exception("The requested path '$path' already exists");
            }
            return $path;
        }

        if (!mkdir($path, $permissions, true) && !is_dir($path)) {
            throw new Exception("Failed to create path '$path'");
        }
        return $path;
    }
}
?>
<?php

/**
 * Legacy database bootstrap.
 *
 * EpubClass currently does not require a database for its core EPUB
 * operations. A PDO connection is created only when database
 * configuration is explicitly provided.
 */

$db = null;

if (
    defined('DB_HOST') &&
    defined('DB_NAME') &&
    defined('DB_USER') &&
    DB_HOST !== '' &&
    DB_NAME !== '' &&
    DB_USER !== ''
) {
    try {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_NAME
        );

        $db = new PDO(
            $dsn,
            DB_USER,
            defined('DB_PASSWORD') ? DB_PASSWORD : '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $exception) {
        throw new RuntimeException(
            'Unable to connect to the EpubClass database.',
            0,
            $exception
        );
    }
}

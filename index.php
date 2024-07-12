<?php

function main(): void
{
    $database = getDatabaseConnection();
    prepareDatabase($database);
    insertUsersToDatabase($database);
    $users = getAllUsers($database);
    $responseBody = transformResponseBody($users);
    sendResponse($responseBody);
}

function getDatabaseConnection(): PDO
{
    try {
        return new PDO('mysql:host=0.0.0.0', 'root', 'root', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        ]);
    } catch (PDOException $exception) {
        die('Não foi possível se conectar ao banco de dados: "' . $exception->getMessage() . '"' . PHP_EOL);
    }
}

function prepareDatabase(PDO $database): void
{
    $database->exec('CREATE DATABASE IF NOT EXISTS advbox_challenge');
    $database->exec('USE advbox_challenge');

    $database->exec("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
}

function insertUsersToDatabase(PDO $database): void
{
    $database->exec('TRUNCATE users');
    $date = new DateTime();

    for ($i = 0; $i < 15; $i++) {
        $database
            ->prepare('INSERT INTO users (name, created_at, updated_at) VALUES (:name, :created_at, :updated_at)')
            ->execute([
                'name' => 'Nome ' . $i + 1,
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s'),
            ]);

        $date->add(new DateInterval('PT1S'));
    }
}

function getAllUsers(PDO $database): array
{
    $users = $database->query('SELECT * FROM users')->fetchAll();

    if (!$users) {
        return [];
    }

    foreach ($users as $user) {
        $userCreatedAtSeconds = (int) substr($user->created_at, -1);

        $isEven = $userCreatedAtSeconds % 2 === 0;
        if (!$isEven) {
            $userCreatedAt = new DateTime($user->created_at);

            $userCreatedAtOneMonthLater = $userCreatedAt
                ->add(new DateInterval('P1M'))
                ->format('Y-m-d H:i:s');

            $user->created_at = $userCreatedAtOneMonthLater;
        }
    }

    return $users;
}

function transformResponseBody(array $users): array
{
    $responseBody = [];

    foreach ($users as $user) {
        $responseBody[] = [
            'id' => $user->id,
            'name' => $user->name,
            'created_at' => $user->created_at,
            'is_updated' => $user->created_at !== $user->updated_at,
        ];
    }

    return $responseBody;
}

function sendResponse(array $body): void
{
    header('Content-type: application/json');
    die(json_encode($body, JSON_PRETTY_PRINT));
}

main();

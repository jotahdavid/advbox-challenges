<?php

try {
    $database = new PDO('mysql:host=0.0.0.0', 'root', 'root', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);
} catch (PDOException $exception) {
    die('Não foi possível se conectar ao banco de dados: "' . $exception->getMessage() . '"' . PHP_EOL);
}

$database->exec('CREATE DATABASE IF NOT EXISTS advbox_challenge');
$database->exec('USE advbox_challenge');

$database->exec("CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$database->exec('DELETE FROM users');

for ($i = 0; $i < 15; $i++) {
    $database
        ->prepare('INSERT INTO users (name) VALUES (:name)')
        ->execute([
            'name' => 'Nome ' . $i + 1,
        ]);

    sleep($i % 2 === 0 ? 3 : 5);
}

$users = $database->query('SELECT * FROM users')->fetchAll();
$responseBody = [];

foreach ($users as $user) {
    $userCreatedAt = new DateTime($user->created_at);
    $userCreatedAtSeconds = (int) $userCreatedAt->format('s');

    $isEven = $userCreatedAtSeconds % 2 === 0;
    if (!$isEven) {
        $userCreatedAtOneMonthLater = $userCreatedAt
            ->add(new DateInterval('P1M'))
            ->format('Y-m-d H:i:s');

        try {
            $database
                ->prepare('UPDATE users SET created_at = :created_at WHERE id = :id')
                ->execute([
                    'id' => $user->id,
                    'created_at' => $userCreatedAtOneMonthLater,
                ]);
        } catch (PDOException $exception) {
            die('Não foi possível atualizar os dados: "' . $exception->getMessage() . '"' . PHP_EOL);
        }

        $statement = $database->prepare('SELECT * FROM users WHERE id = :id');
        $statement->execute([ 'id' => $user->id ]);

        $user = $statement->fetchObject();
    }

    $responseBody[] = [
        'id' => $user->id,
        'name' => $user->name,
        'created_at' => $user->created_at,
        'is_updated' => $user->created_at !== $user->updated_at,
    ];
}

header('Content-type: application/json');
die(json_encode($responseBody, JSON_PRETTY_PRINT));

<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        $config = include 'config.test.php';
        $this->pdo = new PDO(
            "mysql:host={$config['localhost']};dbname={$config['mydb']}",
            $config['root'],
            $config['']
        );
    }

    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(PDO::class, $this->pdo);
    }

    // Aquí puedes añadir más pruebas que interactúen con la base de datos.
}
?>
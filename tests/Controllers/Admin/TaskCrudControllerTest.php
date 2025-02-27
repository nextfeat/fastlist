<?php

namespace App\Tests\Controller\Admin;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskCrudControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Réinitialisation de la base SQLite avant chaque test
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testIndexPageIsAccessible()
    {
        $this->client->request('GET', '/admin/task/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Task'); // Vérifie si le titre contient "Task"
    }

    public function testCreateTask()
    {
        $task = new Task();
        $task->setDescription('Test Task')
             ->setChecked(false);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $this->assertNotNull($task->getId());
    }

    public function testReadTask()
    {
        $task = new Task();
        $task->setDescription('Test Task')->setChecked(false);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $fetchedTask = $this->entityManager->getRepository(Task::class)->findOneBy(['description' => 'Test Task']);
        $this->assertNotNull($fetchedTask);
        $this->assertEquals('Test Task', $fetchedTask->getDescription());
    }

    public function testUpdateTask()
    {
        $task = new Task();
        $task->setDescription('Initial Task')->setChecked(false);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        // Récupération et modification
        $task->setDescription('Updated Task');
        $this->entityManager->flush();

        // Vérification
        $updatedTask = $this->entityManager->getRepository(Task::class)->find($task->getId());
        $this->assertEquals('Updated Task', $updatedTask->getDescription());
    }

    public function testDeleteTask()
    {
        $task = new Task();
        $task->setDescription('Task to Delete')->setChecked(false);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $taskId = $task->getId();

        $this->entityManager->remove($task);
        $this->entityManager->flush();
        $this->entityManager->clear(); // Vider le cache Doctrine

        $deletedTask = $this->entityManager->getRepository(Task::class)->find($taskId);
        $this->assertNull($deletedTask, "La tâche doit être supprimée.");
    }
}

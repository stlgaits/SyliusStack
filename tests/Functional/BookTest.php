<?php

declare(strict_types=1);

namespace MainTests\Sylius\Functional;

use App\Factory\BookFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

final class BookTest extends WebTestCase
{
    use ResetDatabase;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testBrowsingBooks(): void
    {
        BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create()
        ;

        BookFactory::new()
            ->withTitle('Carrie')
            ->withAuthorName('Stephen King')
            ->create()
        ;

        $this->client->request('GET', '/admin/books');

        self::assertResponseIsSuccessful();

        // Validate Header
        self::assertSelectorTextContains('h1.page-title', 'Books');
        self::assertSelectorExists('a:contains("Create")');
    }

    public function testAddingBookContent(): void
    {
        $this->client->request('GET', '/admin/books/new');

        self::assertResponseIsSuccessful();
    }

    public function testEditingBookContent(): void
    {
        $book = BookFactory::new()
            ->withTitle('Shinning')
            ->withAuthorName('Stephen King')
            ->create();

        $this->client->request('GET', sprintf('/admin/books/%s/edit', $book->getId()));

        self::assertResponseIsSuccessful();
    }
}

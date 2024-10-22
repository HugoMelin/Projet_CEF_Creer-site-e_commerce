<?php

namespace App\Tests\Controller;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use App\Entity\SweatShirt;
use Symfony\Component\Security\Core\User\UserInterface;

class CartControllerTest extends WebTestCase
{
    protected $client;
    protected $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testAddToCart()
    {
        $sweatShirtMock = $this->createMock(SweatShirt::class);
        $sweatShirtMock->method('getId')->willReturn(1);
        $sweatShirtMock->method('getName')->willReturn('Test SweatShirt');
        $sweatShirtMock->method('getPrice')->willReturn('29.99');

        $sweatShirtRepositoryMock = $this->createMock(EntityRepository::class);
        $sweatShirtRepositoryMock->method('find')->willReturn($sweatShirtMock);

        $this->client->getContainer()->get('doctrine')->getManager()->getConfiguration()->addEntityNamespace('App', 'App\Entity');
        $this->client->getContainer()->get('doctrine')->getManager()->getRepository(SweatShirt::class)->expects($this->any())
            ->method('find')
            ->willReturn($sweatShirtMock);

        $this->client->request('GET', '/cart/add/1-M');

        $this->assertResponseRedirects('/cart/');

        $crawler = $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('body', 'Test SweatShirt');
    }

    public function testCheckoutWithStripe()
    {
        $userMock = $this->createMock(User::class);
        $userMock->method('getUserIdentifier')->willReturn('test@example.com');
        $userMock->method('getRoles')->willReturn(['ROLE_USER']);
        $userMock->method('getId')->willReturn(1);  // Ajoutez ceci seulement si votre entité User a une méthode getId()

        $token = 'tok_visa';

        $chargeServiceMock = $this->createMock(\Stripe\Service\ChargeService::class);
        $chargeServiceMock->method('create')->willReturn(['id' => 'ch_123456']);

        $stripeMock = $this->getMockBuilder(\Stripe\StripeClient::class)
                          ->disableOriginalConstructor()
                          ->getMock();
        $stripeMock->charges = $chargeServiceMock;

        $this->client->getContainer()->set('stripe_client', $stripeMock);

        $this->client->loginUser($userMock);

        $this->client->request('POST', '/cart/process-payment', [
            'stripeToken' => $token,
        ]);

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
    }
}
<?php

namespace App\Tests\Controller;

use App\Repository\ClientRepository;
use App\Tests\Controller\WebCaseTestTrait;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ClientControllerTest extends WebTestCase
{
    use WebCaseTestTrait;

    /**
     * - Test prospect list(client list)
     */
    public function testIndex(): void
    {
        $client = static::createClient();
        $loginRedirectMessage = 'Redirecting to /login';
        $crawler = $client->request('GET', '/client/');
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Not redirected to login page');
        $this->assertStringContainsString($loginRedirectMessage, $client->getResponse()->getContent());

        $this->login($client, 'user_role_client_edit@app.locale');
        $crawler = $client->request('GET', '/client/');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');
        // $this->assertResponseIsSuccessful();

        $this->login($client, 'user_role_client_view@app.locale');
        $crawler = $client->request('GET', '/client/');
        $this->assertResponseIsSuccessful('unable to access page with required permission');
    }

    /**
     * - Test when create a new prospect(client)
     */
    public function testNew(): void
    {
        $client = static::createClient();

        $this->login($client, 'user_role_client_edit@app.locale');
        $crawler = $client->request('GET', '/client/new');
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_client_add@app.locale');
        $crawler = $client->request('GET', '/client/new');
        $this->assertResponseIsSuccessful();
        // there should be no error message
        $this->assertStringNotContainsString("form-error-message", $client->getResponse()->getContent());

        /**
         * - Test for adding real data for client
         */
        $buttonCrawlerNode = $crawler->filter('button[type="submit"]');

        // - Select the form that contains this button
        $form = $buttonCrawlerNode->form();

        $dynamicMail = 'mail_' .(new \DateTime())->getTimestamp(). '@app.locale';
        $contactsFormattedValues = ['client' => $this->generateClientContact()];

        // submit the Form object with bad credentials
        $formValues = $this->formatFormNames('client', $this->generateClient());
        
        /**
         * First submit should work and redirect to client list 
         */
        $this->submitOverride($client, $form, $formValues, $contactsFormattedValues);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Redirection to client list failed');
        
        /**
         * Second submit should fail and return to the new client page with invalid fields
         */
        $this->submitOverride($client, $form, $formValues, $contactsFormattedValues);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode(), 'Redirect to the new client page because there are something wrong');
        // there should be invalid field with error messages, containing `form-error-message`
        $this->assertStringContainsString("form-error-message", $client->getResponse()->getContent());
    }

    public function testShow()
    {
        $client = static::createClient();

        $this->login($client, 'user_role_client_edit@app.locale');
        $showClientUrl = $this->getClientUrl();
        $crawler = $client->request('GET', $showClientUrl);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_client_view@app.locale');
        $crawler = $client->request('GET', $showClientUrl);
        $this->assertResponseIsSuccessful();
    }

    public function testEdit(): void
    {
        $client = static::createClient();

        $this->login($client, 'user_role_client_delete@app.locale');
        $editClientUrl = $this->getClientUrl('/edit');
        $crawler = $client->request('GET', $editClientUrl);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_client_edit@app.locale');
        $crawler = $client->request('GET', $editClientUrl);
        $this->assertResponseIsSuccessful();

        // test for client editing
        $buttonCrawlerNode = $crawler->filter('button[type="submit"]');

        // - Select the form that contains this button
        $form = $buttonCrawlerNode->form();
        $formValues = $this->formatFormNames('client', $this->generateClient());
        $contactsFormattedValues = $this->generateClientContact();

        // First submit should ok
        $this->submitOverride($client, $form, $formValues, $contactsFormattedValues);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Redirection to client list failed');

    }

    public function testDelete(): void
    {
        $client = static::createClient();

        $this->login($client, 'user_role_client_edit@app.locale');
        $deleteClientUrl = $this->getClientUrl('/delete');
        $crawler = $client->request('DELETE', $deleteClientUrl);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode(), 'Forbidden failed');

        $this->login($client, 'user_role_client_delete@app.locale');
        $crawler = $client->request('DELETE', $deleteClientUrl);
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode(), 'Delete success');
    }

    public function getClientUrl($urlSufix = '', $email = '')
    {
        $clientId = $this->getClientId($email);
        
        return '/client/' .$clientId. '' .$urlSufix;
    }

    public function getClientId($email = '')
    {
        /** store the user id */
        $clientId = null;
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use self::$container to access the service container
        $container = self::$container;

        /** Get clientRepository */
        $clientRepository = $container->get(ClientRepository::class);
        
        $clients = $clientRepository->findAll();
        $clientId = $clients[0]->getId();

        return $clientId;
    }

    public function generateClient()
    {
        return [
            "name" => "MCM",
            "shortName" => "MCM",
            "clientNumber" => "PR0006",
            "activity" => "activity.chemistry",
            "projectDescription" => [
                "projectDescription" => "Test",
                "area" => "Lorem ipsum dolor",
                "department" => "Lorem ipsum dolor",
                "address" => [
                    "postalCode" => "UI",
                    "city" => "Paris",
                    "line1" => "Monthelier",
                    "country" => "FR",
                ],
                "marketType" => "typeMarket.work_on_existing",
            ],
            "billingAddress" => [
                "name" => "Lorem ipsum",
                "phone" => "xxx xx xxx xx",
                "fax" => "xxx xx xxx",
                "line1" => "Lorem ipsum",
                "line2" => "Ipsum dolor",
                "line3" => "Test ipsum",
                "postalCode" => "CS",
                "city" => "Mada",
                "country" => "FR",
            ]
        ];
    }

    public function generateClientContact()
    {
        $dynamicMail = 'mail_' .(new \DateTime())->getTimestamp(). '@app.locale';

        return [
            "contacts" => [
                [
                    "lastName" => "Monkey D",
                    "firstName" => "Dragon",
                    "email" => $dynamicMail,
                    "fax" => "45678",
                    "phone" => "56789",
                    "job" => "RTYUI",
                    "rawAddress" => "Lorem ipsum",
                ],
            ],
        ];
    }
}
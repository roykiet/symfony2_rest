<?php

namespace Acme\BlogBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase;
use Acme\BlogBundle\Tests\Fixtures\Entity\LoadTagData;

class TagControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->auth = array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'userpass',
        );

        $this->client = static::createClient(array(), $this->auth);
    }

    public function testJsonGetTagAction()
    {
        $fixtures = array('Acme\BlogBundle\Tests\Fixtures\Entity\LoadTagData');
        $this->loadFixtures($fixtures);
        $tags = LoadTagData::$tags;
        $tag = array_pop($tags);

        $route =  $this->getUrl('api_1_get_tag', array('id' => $tag->getId(), '_format' => 'json'));

        $this->client->request('GET', $route, array('ACCEPT' => 'application/json'));
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $content = $response->getContent();

        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['id']));
    }

    public function testHeadRoute()
    {
        $fixtures = array('Acme\BlogBundle\Tests\Fixtures\Entity\LoadTagData');
        $this->loadFixtures($fixtures);
        $tags = LoadTagData::$tags;
        $tag = array_pop($tags);

        $this->client->request(
            'HEAD',  
            sprintf('/api/v1/tags/%d.json', $tag->getId()),
            array('ACCEPT' => 'application/json')
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200, false);
    }

    public function testJsonNewTagAction()
    {
        $this->client->request(
            'GET',
            '/api/v1/tags/new.json',
            array(),
            array()
        );

        $this->assertJsonResponse($this->client->getResponse(), 200, true);
        $this->assertEquals(
            '{"children":{"name":{}}}',
            $this->client->getResponse()->getContent(),
            $this->client->getResponse()->getContent()
        );
    }

    public function testJsonPostTagAction()
    {
        $this->client->request(
            'POST',
            '/api/v1/tags.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name":"name1"}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 201, false);
    }

    public function testJsonPostTagActionShouldReturn400WithBadParameters()
    {
        $this->client->request(
            'POST',
            '/api/v1/tags.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name":"name1"}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 400, false);
    }

    public function testJsonPutTagActionShouldModify()
    {
        $fixtures = array('Acme\BlogBundle\Tests\Fixtures\Entity\LoadTagData');
        $this->loadFixtures($fixtures);
        $tags = LoadTagData::$tags;
        $tag = array_pop($tags);

        $this->client->request(
            'GET', 
            sprintf('/api/v1/tags/%d.json',
            $tag->getId()), array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(
            200, 
            $this->client->getResponse()->getStatusCode(), 
            $this->client->getResponse()->getContent()
        );

        $this->client->request(
            'PUT',
            sprintf('/api/v1/tags/%d.json', $tag->getId()),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name":"abc"}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 204, false);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Location',
                sprintf('http://localhost/api/v1/tags/%d.json', $tag->getId())
            ),
            $this->client->getResponse()->headers
        );
    }

    public function testJsonPutTagActionShouldCreate()
    {
        $id = 0;
        $this->client->request('GET', sprintf('/api/v1/tags/%d.json', $id), array('ACCEPT' => 'application/json'));

        $this->assertEquals(
            404, 
            $this->client->getResponse()->getStatusCode(), 
            $this->client->getResponse()->getContent()
        );

        $this->client->request(
            'PUT',
            sprintf('/api/v1/tags/%d.json', $id),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name":"abc"}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 201, false);
    }

    public function testJsonPatchTagAction()
    {
        $fixtures = array('Acme\BlogBundle\Tests\Fixtures\Entity\LoadTagData');
        $this->loadFixtures($fixtures);
        $tags = LoadTagData::$tags;
        $tag = array_pop($tags);

        $this->client->request(
            'PATCH',
            sprintf('/api/v1/tags/%d.json', $tag->getId()),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name":"abc"}'
        );

        $this->assertJsonResponse($this->client->getResponse(), 204, false);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Location',
                sprintf('http://localhost/api/v1/tags/%d.json', $tag->getId())
            ),
            $this->client->getResponse()->headers
        );
    }

    protected function assertJsonResponse(
        $response, 
        $statusCode = 200, 
        $checkValidJson =  true, 
        $contentType = 'application/json'
    )
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', $contentType),
            $response->headers
        );

        if ($checkValidJson) {
            $decode = json_decode($response->getContent());
            $this->assertTrue(($decode != null && $decode != false),
                'is response valid json: [' . $response->getContent() . ']'
            );
        }
    }
}

<?php

use Laravel\Lumen\Testing\DatabaseTransactions;

class ConfigControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetConfig()
    {
        // The test user in our seed
        $user = factory('App\User')->make();

        $this->get('/config/app', [
            'App'           => env('APP_ID'),
            'Client'        => $user->app_id,
            'Authorization' => $user->app_key,
        ]);

        $this->assertEquals('200', $this->response->status());

        $result = json_decode($this->response->getContent());

        $this->assertEquals('object', gettype($result));

        $this->assertEquals(4, count((array) $result));

        $this->assertRegExp(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $result->app_id
        );

        $this->assertObjectHasAttribute('app_id', $result);
        $this->assertObjectHasAttribute('api_url', $result);
        $this->assertObjectHasAttribute('name', $result);
        $this->assertObjectHasAttribute('version', $result);

        $this->assertEquals(env('APP_URL'), $result->api_url);
        $this->assertEquals(env('APP_VERSION'), $result->version);
        $this->assertEquals(env('APP_NAME'), $result->name);
    }

    public function testGetConfigWithoutAppKey()
    {
        $this->get('/config/app');

        $this->assertEquals(
            'Unauthorized.',
            $this->response->getContent()
        );

        $this->assertEquals('401', $this->response->status());
    }
}

<?php
declare(strict_types=1);

namespace Tests\Traits;

use Exception;
use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves
{
    /**
     * @param array $requestData
     * @param array $testDbData
     * @param array|null $testJsonData
     * @return TestResponse
     * @throws Exception
     */
    protected function assertStore(array $requestData, array $testDbData, array $testJsonData = null): TestResponse
    {
        $response = $this->json('POST', $this->routeStore(), $requestData);

        if ($response->status() !== 201) {
            throw new Exception("Response status must be 201, given status {$response->status()}:\n {$response->content()}");
        }

        $this->assertInDatabase($response, $testDbData);
        $this->assertJsonResponseContent($response, $testDbData, $testJsonData);

        return $response;
    }

    /**
     * @param array $requestData
     * @param array $testDbData
     * @param array|null $testJsonData
     * @return TestResponse
     * @throws Exception
     */
    protected function assertUpdate(array $requestData, array $testDbData, array $testJsonData = null): TestResponse
    {
        $response = $this->json('PUT', $this->routeUpdate(), $requestData);

        if ($response->status() !== 200) {
            throw new Exception("Response status must be 200, given status {$response->status()}:\n {$response->content()}");
        }

        $this->assertInDatabase($response, $testDbData);
        $this->assertJsonResponseContent($response, $testDbData, $testJsonData);

        return $response;
    }

    private function assertInDatabase(TestResponse $response, array $testDbData): void
    {
        $model = $this->model();
        $table = (new $model)->getTable();

        $this->assertDatabaseHas($table, $testDbData + ['id' => $response->json('id')]);
    }

    private function assertJsonResponseContent(TestResponse $response, array $testDbData, array $testJsonData = null): void
    {
        $testResponse = $testJsonData ?? $testDbData;

        $response->assertJsonFragment($testResponse + ['id' => $response->json('id')]);
    }
}

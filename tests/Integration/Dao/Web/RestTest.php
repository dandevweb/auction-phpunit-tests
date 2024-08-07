<?php

declare(strict_types=1);

namespace Dandevweb\Auction\Tests\Integration\Dao\Web;

use PHPUnit\Framework\TestCase;

class RestTest extends TestCase
{
    public function testShouldBeAbleReturnArrayOfAuctions()
    {
        $response = file_get_contents('http://localhost:8080/rest.php');
        static::assertStringContainsString('200 OK', $http_response_header[0]);
        static::assertIsArray(json_decode($response));
    }
    
}

<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $database = DB::connection()->getDatabaseName();
        if ($database !== 'admidio_test') {
            throw new RuntimeException("Refusing to execute tests on database [{$database}]. Expected isolated [admidio_test].");
        }
    }
}

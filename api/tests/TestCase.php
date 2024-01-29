<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use JMac\Testing\Traits\AdditionalAssertions;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use AdditionalAssertions;

    protected function setUp(): void
    {
        $this->beforeApplicationDestroyed(function (): void {
            $this->artisan('cache:clear');
        });
        parent::setUp();
    }
}

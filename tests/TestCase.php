<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use HasFactory;
}

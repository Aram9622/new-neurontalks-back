<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicUploadUrlTest extends TestCase
{
    public function test_public_upload_urls_are_same_origin_by_default(): void
    {
        $this->assertSame(
            '/storage/example.png',
            Storage::disk('public')->url('example.png'),
        );
    }
}

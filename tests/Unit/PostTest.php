<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    #[Test]
    public function it_can_create_a_post()
    {
        $postData = [
            'title' => 'Test Title',
            'content' => 'Test Content',
        ];

        Post::create($postData);

        $this->assertDatabaseHas('posts', $postData);
    }

    /** @test */
    #[Test]
    public function it_can_read_a_post()
    {
        $post = Post::factory()->create([
            'title' => 'Read Title',
            'content' => 'Read Content',
        ]);

        $foundPost = Post::find($post->id);

        $this->assertEquals($post->title, $foundPost->title);
        $this->assertEquals($post->content, $foundPost->content);
    }

    /** @test */
    #[Test]
    public function it_can_update_a_post()
    {
        $post = Post::factory()->create([
            'title' => 'Read Title',
            'content' => 'Read Content',
        ]);

        $updatedData = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ];

        $post->update($updatedData);

        $this->assertDatabaseHas('posts', $updatedData);
    }

    /** @test */
    #[Test]
    public function it_can_delete_a_post()
    {
        $post = Post::factory()->create([
            'title' => 'Read Title',
            'content' => 'Read Content',
        ]);

        $post->delete();

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    #[Test]
    public function it_can_upload_a_file()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->image('test.jpg');

        // Create the uploads folder if it doesn't exist
        $uploadsFolder = 'uploads';
        if (!Storage::disk('local')->exists($uploadsFolder)) {
            Storage::disk('local')->makeDirectory($uploadsFolder);
        }

        // Simpan file di disk lokal
        $file->store($uploadsFolder);

        // Assert the file was stored...
        $this->assertFileExists(Storage::disk('local')->path($uploadsFolder . '/' . $file->hashName()));

        // Assert the file does not exist
        $this->assertFileDoesNotExist(Storage::disk('local')->path($uploadsFolder . '/missing.jpg'));
    }
}

<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Post;


class PostControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_store()
    {
        $user = factory(User::class)->create();
        $this->withoutExceptionHandling();
        $response = $this->actingAs($user, 'api')->json('POST', '/api/posts',[
        ]);

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
        ->assertJson(['title' => 'El post de prueba'])
        -assertStatus(201);
        $this->assertDatabaseHas('posts', ['title' => 'El post de prueba']);
    }

    public function test_validate_title()
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user, 'api')->json('POST', '/api/post', [
            'title' => ''
        ]);
            $response->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }
    public function test_show()
    {   
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();
        $response = $this->actingAs($user, 'api')->json('GET', "/api/posts/$post->id");

        $response->assertJsonStructure(['id', 'title', 'created_at', 'updated_at'])
        ->assertJson(['title' => $post->title])
        -assertStatus(200);
    }

    public function test_404_show()
    {
        $user = factory(User::class)->create();
       
        $response = $this->actiangAs($user, 'api')->json('GET', '/api/posts/$posts/1000');

        $response->assertStatus(404);

    }

    public function test_update(){
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();
    }

    public function test_delete(){
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create();
        $response = $this->actingAs($user, 'api')->json('DELETE', "/api/posts/$post->id");
        $response->assertSee(null)
        ->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_index()
    {
        $user = factory(User::class)->create();
        factory(Post::class, 5)->create();
        $response = $this->actingAs($user, 'api')->json('GET', '/api/posts');
        $response->assertJsonStructure([
            'data' => [
            '*' => ['id', 'title', 'created_at', 'updated_at']
        ]
    ])->assertStatus(200);
    }

    public function test_guest(){
        $this->json('GET', '/api/posts')->assertStatus(401);
        $this->json('POST', '/api/posts')->assertStatus(401);
        $this->json('GET', '/api/posts/1000')->assertStatus(401);
        $this->json('PUT', '/api/posts/1000')->assertStatus(401);
        $this->json('DELETE', '/api/posts/1000')->assertStatus(401);
    }

}

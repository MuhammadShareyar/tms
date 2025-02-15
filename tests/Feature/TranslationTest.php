<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Database\Seeders\TagSeeder;
use Database\Seeders\TranslationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */


    public function loginUser()
    {
        $user = User::factory(1)->create()->first();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        return $response->assertStatus(200)
            ->assertJsonStructure(['body' => ['token']])->decodeResponseJson();
    }

    public function seedingTagData()
    {
        $this->seed(TagSeeder::class);
    }

    public function seedingTranslationData()
    {
        $this->seed(TranslationSeeder::class);
    }

    public function createTranslationApi($token, $data)
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])
            ->postJson('/api/translations', $data);

        return $response;
    }

    public function updateTranslationApi($token, $data)
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])
            ->putJson('/api/translations/' . $data['id'], $data);

        return $response;
    }

    public function exportTranslationsApi($token)
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])
            ->getJson('/api/translations/export');

        return $response;
    }


    public function test_unauthenticated_user_cannot_create_translation(): void
    {
        $this->postJson('api/translations')
            ->assertStatus(401)
            ->assertJson([
                "message" => "Unauthenticated."
            ]);
    }


    public function test_create_translation_validations(): void
    {

        $data = $this->loginUser();

        $token = $data['body']['token'];

        $response = $this->createTranslationApi($token, []);

        $response->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "key" => [
                        "The key field is required."
                    ],
                    "content" => [
                        "The content field is required.",
                    ],
                    "locale" => [
                        "The locale field is required.",
                    ],
                    "tag_id" => [
                        "The tag id field is required.",
                    ]
                ]
            ]);
    }

    public function test_create_translation_success(): void
    {
        $this->seedingTagData();

        $data = $this->loginUser();

        $token = $data['body']['token'];

        $response = $this->createTranslationApi($token, [
            'key' => 'test_key',
            'content' => 'test_content',
            'locale' => 'en',
            'tag_id' => 1
        ]);

        $response->assertStatus(200);
    }

    public function test_create_translation_success_has_keys(): void
    {
        $this->seedingTagData();

        $data = $this->loginUser();

        $token = $data['body']['token'];

        $response = $this->createTranslationApi($token, [
            'key' => 'test_key_2',
            'content' => 'test_content',
            'locale' => 'en',
            'tag_id' => 1
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['body' => ['id', 'key', 'content', 'locale', 'tag_id', 'tag']]);
    }

    public function test_create_translation_key_must_be_unique_for_same_locale(): void
    {
        $this->seedingTagData();

        $data = $this->loginUser();

        $token = $data['body']['token'];

        $response = $this->createTranslationApi($token, [
            'key' => 'test_key_2',
            'content' => 'test_content',
            'locale' => 'en',
            'tag_id' => 1
        ]);

        $response = $this->createTranslationApi($token, [
            'key' => 'test_key_2',
            'content' => 'test_content',
            'locale' => 'en',
            'tag_id' => 1
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'key' => ["The key has already been taken."]
                ]
            ]);
    }

    public function test_create_translation_key_can_be_same_for_different_locale(): void
    {
        $this->seedingTagData();

        $data = $this->loginUser();

        $token = $data['body']['token'];

        $response = $this->createTranslationApi($token, [
            'key' => 'test_key_2',
            'content' => 'test_content',
            'locale' => 'en',
            'tag_id' => 1
        ]);

        $response = $this->createTranslationApi($token, [
            'key' => 'test_key_2',
            'content' => 'test_content',
            'locale' => 'fr',
            'tag_id' => 1
        ]);

        $response->assertStatus(200);
    }

    public function test_translation_list(): void
    {
        ini_set('memory_limit', '-1');
        $this->seedingTagData();
        $this->seedingTranslationData();

        $data = $this->loginUser();

        $token = $data['body']['token'];

        $start = microtime(true);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])
            ->getJson('/api/translations', []);

        $duration = (microtime(true) - $start) * 1000;

        $response->assertOk();

        $this->assertLessThan(200, $duration, 'API took too long to respond');
    }

    public function test_export_endpoint_not_exceeding_response_time()
    {
        $data = $this->loginUser();

        $this->seedingTagData();
        $this->seedingTranslationData();

        $token = $data['body']['token'];

        $start = microtime(true);

        $response = $this->exportTranslationsApi($token);

        $duration = (microtime(true) - $start) * 1000;

        $response->assertOk();

        $this->assertLessThan(
            200,
            $duration,
            "The API took {$duration}ms to respond, exceeding the 200ms threshold."
        );
    }

    public function test_export_translation_give_updated_translation_every_time(): void
    {
        $this->seedingTagData();

        $data = $this->loginUser();

        $token = $data['body']['token'];

        $response = $this->createTranslationApi($token, [
            'key' => 'test_key_2',
            'content' => 'test_content',
            'locale' => 'en',
            'tag_id' => 1
        ]);


        $data = $response->decodeResponseJson()['body'];

        $exportResponseBeforeUpdate = $this->exportTranslationsApi($token)->decodeResponseJson();

        $data['content'] = 'test_content_updated';
        $response = $this->updateTranslationApi($token, $data);

        $exportResponseAfterUpdate = $this->exportTranslationsApi($token)->decodeResponseJson();

        $this->assertNotEquals($exportResponseBeforeUpdate, $exportResponseAfterUpdate);
    }
}

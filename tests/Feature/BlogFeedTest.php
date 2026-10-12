<?php

namespace Tests\Feature;

use App\Services\BlogContent;
use App\Services\BlogFeed;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BlogFeedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.blog.key' => 'test-project-key']);
        Http::preventStrayRequests();
    }

    private function article(): array
    {
        return ['id' => 19, 'title' => 'Fresh grocery insights', 'content' => '<h2>Keep produce fresh</h2><p>Stock your shelves.</p>',
            'cover_image' => '/storage/cover.jpg', 'created_at' => '2026-10-12T10:00:00Z'];
    }

    public function test_successful_empty_project_feed_is_not_an_outage(): void
    {
        Http::fake(['blog.metora.in/*' => Http::response(['project' => 'MetoHub', 'blogs' => []])]);
        $this->get('/blogs')->assertOk()->assertSee('No blogs published yet.')
            ->assertSee('<title>MetoHub Blog', false)->assertDontSee('temporarily unavailable');
        Http::assertSent(fn ($request) => $request->hasHeader('x-api-key', 'test-project-key'));
    }

    public function test_feed_is_cached_and_article_fields_and_images_are_normalized(): void
    {
        Http::fake(['blog.metora.in/*' => Http::response(['blogs' => [$this->article()]])]);
        $this->get('/blogs')->assertOk()->assertSee('Fresh grocery insights')
            ->assertSee('https://blog.metora.in/storage/cover.jpg', false)->assertSee('MetoHub team');
        $this->get('/blogs/19')->assertOk()->assertSee('<h2>Keep produce fresh</h2>', false)
            ->assertSee('<title>Fresh grocery insights | MetoHub Blog</title>', false)
            ->assertSee('1 min read')->assertSee('twitter.com/intent/tweet')->assertDontSee('href="#" class="share-btn', false);
        Http::assertSentCount(1);
        $this->get('/blogs/999')->assertNotFound();
    }

    public function test_unavailable_or_malformed_feed_returns_503_instead_of_empty_message(): void
    {
        Http::fake(['blog.metora.in/*' => Http::response(['error' => 'Unauthorized'], 401)]);
        $this->get('/blogs')->assertStatus(503)->assertSee('Articles are temporarily unavailable.')
            ->assertDontSee('No blogs published yet.');
        Http::fake(['blog.metora.in/*' => Http::response(['blogs' => null])]);
        $this->get('/blogs/19')->assertStatus(503);
    }

    public function test_connection_failure_uses_last_successful_articles(): void
    {
        Http::fake(['blog.metora.in/*' => Http::response(['blogs' => [$this->article()]])]);
        app(BlogFeed::class)->load();
        $key = 'blog-feed:'.hash('sha256', config('services.blog.endpoint').'|test-project-key');
        Cache::forget($key);
        Http::fake(fn () => throw new ConnectionException('Timed out'));
        $this->get('/blogs')->assertOk()->assertSee('Fresh grocery insights')->assertSee('latest saved articles');
        $this->get('/blogs/19')->assertOk()->assertSee('latest saved article');
    }

    public function test_missing_key_does_not_make_an_unauthenticated_request(): void
    {
        config(['services.blog.key' => null]);
        Http::fake();
        $this->get('/blogs')->assertStatus(503);
        Http::assertNothingSent();
    }

    public function test_article_html_and_metadata_do_not_execute_feed_markup(): void
    {
        $article = $this->article();
        $article['title'] = 'Article </title><script>alert(1)</script>';
        $article['content'] = '<p onclick="alert(1)">Safe text</p><script>alert(2)</script><iframe src="https://evil.test"></iframe><img src="javascript:alert(3)" onerror="alert(4)"><a href="jav&#x61;script:alert(5)">Unsafe link</a>';
        $article['sections'] = [['text_content' => '<svg onload="alert(6)"></svg><strong>Safe section</strong>']];
        Http::fake(['blog.metora.in/*' => Http::response(['blogs' => [$article]])]);
        $response = $this->get('/blogs/19')->assertOk()->assertSee('<p>Safe text</p>', false)
            ->assertSee('<strong>Safe section</strong>', false)->assertSee('&lt;/title&gt;&lt;script&gt;alert(1)', false);
        foreach (['<script>alert(', 'onclick="alert', 'onerror="alert', 'javascript:alert', '<iframe src="https://evil.test"', '<svg onload'] as $unsafe) {
            $response->assertDontSee($unsafe, false);
        }
    }

    public function test_absolute_images_markdown_and_invalid_dates_are_handled(): void
    {
        $article = $this->article();
        $article['cover_image'] = 'https://cdn.example.com/cover.jpg';
        $article['content'] = '## Grocery tips';
        $article['created_at'] = 'invalid date';
        Http::fake(['blog.metora.in/*' => Http::response(['blogs' => [$article]])]);
        $this->get('/blogs/19')->assertOk()->assertSee('https://cdn.example.com/cover.jpg', false)
            ->assertDontSee('https://blog.metora.inhttps://', false)->assertSee('<h2>Grocery tips</h2>', false);
        $this->assertNull(app(BlogContent::class)->imageUrl('data:text/html,hello'));
    }
}

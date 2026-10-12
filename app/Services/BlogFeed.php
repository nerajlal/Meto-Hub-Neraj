<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BlogFeed
{
    public function load(): array
    {
        $key = config('services.blog.key');
        if (! $key) {
            return ['blogs' => [], 'state' => 'unavailable'];
        }
        $cacheKey = 'blog-feed:'.hash('sha256', config('services.blog.endpoint').'|'.$key);
        if ($cached = Cache::get($cacheKey)) {
            return ['blogs' => $cached['blogs'], 'state' => 'available'];
        }

        try {
            $response = Http::acceptJson()->withHeaders(['x-api-key' => $key])
                ->connectTimeout(3)->timeout(8)->get(config('services.blog.endpoint'));
            if (! $response->successful() || ! is_array($response->json('blogs')) || ! array_is_list($response->json('blogs'))) {
                throw new \RuntimeException('Invalid blog feed response');
            }
            $blogs = collect($response->json('blogs'))->filter(fn ($blog) => is_array($blog)
                && filter_var($blog['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])
                && is_string($blog['title'] ?? null))->map(fn ($blog) => $this->normalize($blog))
                ->sortByDesc('published_at')->values()->all();
            if ($response->json('blogs') && ! $blogs) {
                throw new \RuntimeException('Invalid blog entries');
            }
            Cache::put($cacheKey, ['blogs' => $blogs], now()->addMinutes(5));
            Cache::put($cacheKey.':last-good', ['blogs' => $blogs], now()->addDay());

            return ['blogs' => $blogs, 'state' => 'available'];
        } catch (\Throwable $error) {
            Log::warning('Blog feed unavailable; using last successful response if available.');
            $stale = Cache::get($cacheKey.':last-good');

            return ['blogs' => $stale['blogs'] ?? [], 'state' => $stale ? 'stale' : 'unavailable'];
        }
    }

    private function normalize(array $blog): array
    {
        $renderer = app(BlogContent::class);
        $content = $renderer->html(is_string($blog['content'] ?? null) ? $blog['content'] : '');
        $sections = collect(is_array($blog['sections'] ?? null) ? $blog['sections'] : [])
            ->filter(fn ($section) => is_array($section))->map(fn ($section) => [
                'image_path' => $renderer->imageUrl(is_string($section['image_path'] ?? null) ? $section['image_path'] : null),
                'text_content' => $renderer->html(is_string($section['text_content'] ?? null) ? $section['text_content'] : ''),
            ])->values()->all();
        $text = html_entity_decode(strip_tags($content.' '.implode(' ', array_column($sections, 'text_content'))), ENT_QUOTES, 'UTF-8');
        try {
            $date = empty($blog['created_at']) ? null : Carbon::parse($blog['created_at']);
        } catch (\Throwable $error) {
            $date = null;
        }

        return [
            'id' => (int) $blog['id'], 'title' => $blog['title'], 'content' => $content, 'sections' => $sections,
            'excerpt' => Str::limit(is_string($blog['excerpt'] ?? null) && $blog['excerpt'] !== '' ? strip_tags($blog['excerpt']) : $text, 160),
            'cover_image' => $renderer->imageUrl(is_string($blog['cover_image'] ?? null) ? $blog['cover_image'] : null),
            'author_avatar' => $renderer->imageUrl(is_string($blog['author_avatar'] ?? null) ? $blog['author_avatar'] : null),
            'author_name' => is_string($blog['author_name'] ?? null) && $blog['author_name'] !== '' ? $blog['author_name'] : 'MetoHub team',
            'author_title' => is_string($blog['author_title'] ?? null) ? $blog['author_title'] : 'Author',
            'image_credits' => is_string($blog['image_credits'] ?? null) ? $blog['image_credits'] : null,
            'published_at' => $date?->timestamp ?? 0, 'date_label' => $date?->format('M d, Y') ?? '',
            'reading_minutes' => max(1, (int) ceil(str_word_count($text) / 200)),
        ];
    }
}

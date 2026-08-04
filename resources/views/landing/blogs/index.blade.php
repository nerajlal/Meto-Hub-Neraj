@extends('landing.blogs.layout')

@section('content')
<style>
  .blogs-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 2rem;
  }
  .blogs-header {
    text-align: center;
    margin-bottom: 4rem;
  }
  .blogs-header h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
  }
  .blogs-header p {
    font-size: 1.2rem;
    color: var(--text-light);
  }
  .blogs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
  }
  .blog-card {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(31, 41, 55, 0.06);
    transition: all 0.3s ease;
    border: 1px solid rgba(31, 41, 55, 0.08);
    display: flex;
    flex-direction: column;
  }
  .blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 32px rgba(31, 41, 55, 0.10);
  }
  .blog-image {
    width: 100%;
    height: 200px;
    background-color: var(--bg);
    object-fit: cover;
    border-bottom: 1px solid rgba(31, 41, 55, 0.08);
  }
  .blog-content {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }
  .blog-date {
    font-size: 0.85rem;
    color: #5B6B60;
    margin-bottom: 0.5rem;
    font-weight: 500;
  }
  .blog-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--dark);
    line-height: 1.4;
  }
  .blog-title a {
    color: inherit;
    text-decoration: none;
  }
  .blog-title a:hover {
    color: var(--primary);
  }
  .blog-excerpt {
    font-size: 0.95rem;
    color: var(--text-light);
    margin-bottom: 1.5rem;
    line-height: 1.6;
    flex-grow: 1;
  }
  .blog-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-top: auto;
    border-top: 1px solid rgba(31, 41, 55, 0.08);
    padding-top: 1rem;
  }
  .blog-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
  }
  .blog-author {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--ink);
  }
  .blog-author span {
    display: block;
    font-size: 0.75rem;
    color: #5B6B60;
    font-weight: 400;
  }
</style>

<div class="blogs-container">
  <div class="blogs-header">
    <h1>Our Blog</h1>
    <p>Insights, updates, and stories from the GoSlot Store team.</p>
  </div>

  @if(count($blogs) > 0)
    <div class="blogs-grid">
      @foreach($blogs as $blog)
        <div class="blog-card">
          @if($blog['cover_image'])
            <img src="{{ 'https://lightgoldenrodyellow-mink-721714.hostingersite.com' . $blog['cover_image'] }}" alt="{{ $blog['title'] }}" class="blog-image">
          @else
            <div class="blog-image" style="display: flex; align-items: center; justify-content: center; background: #e2e8f0; color: #94a3b8;">
              <i class="fa-solid fa-image fa-2x"></i>
            </div>
          @endif
          <div class="blog-content">
            <div class="blog-date">{{ \Carbon\Carbon::parse($blog['created_at'])->format('M d, Y') }}</div>
            <h3 class="blog-title"><a href="{{ route('landing.blog.show', $blog['id']) }}">{{ $blog['title'] }}</a></h3>
            <p class="blog-excerpt">{{ $blog['excerpt'] ?? Str::limit(strip_tags(Str::markdown($blog['content'])), 120) }}</p>
            <div class="blog-meta">
              @if($blog['author_avatar'])
                <img src="{{ 'https://lightgoldenrodyellow-mink-721714.hostingersite.com' . $blog['author_avatar'] }}" alt="{{ $blog['author_name'] }}" class="blog-avatar">
              @else
                <div class="blog-avatar" style="background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;">
                  {{ substr($blog['author_name'], 0, 1) }}
                </div>
              @endif
              <div class="blog-author">
                {{ $blog['author_name'] }}
                <span>{{ $blog['author_title'] ?? 'Author' }}</span>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @else
    <div style="text-align: center; padding: 4rem; color: var(--muted);">
      <p>No blogs published yet. Check back soon!</p>
    </div>
  @endif
</div>
@endsection

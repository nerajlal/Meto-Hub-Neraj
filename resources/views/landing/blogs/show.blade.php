@extends('landing.blogs.layout')

@section('content')
<style>
  .blog-single-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 4rem 2rem;
  }
  .blog-single-header {
    text-align: center;
    margin-bottom: 3rem;
  }
  .blog-single-meta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
    font-size: 0.95rem;
    color: var(--muted);
  }
  .blog-single-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
  }
  .blog-single-author {
    text-align: left;
  }
  .blog-single-author strong {
    display: block;
    color: var(--dark);
    font-weight: 600;
  }
  .blog-single-title {
    font-size: 3rem;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    color: var(--dark);
    letter-spacing: -0.02em;
  }
  .blog-single-cover {
    width: 100%;
    height: auto;
    max-height: 500px;
    border-radius: 16px;
    object-fit: cover;
    margin-bottom: 3rem;
    box-shadow: 0 12px 32px rgba(31, 41, 55, 0.10);
  }
  .blog-single-content {
    font-size: 1.125rem;
    line-height: 1.8;
    color: var(--ink);
  }
  .blog-single-content h2, 
  .blog-single-content h3 {
    margin-top: 3rem;
    margin-bottom: 1rem;
    color: var(--dark);
  }
  .blog-single-content p {
    margin-bottom: 1.5rem;
  }
  .blog-single-content ul, 
  .blog-single-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
  }
  .blog-single-content li {
    margin-bottom: 0.5rem;
  }
  .blog-single-content img {
    max-width: 100%;
    border-radius: 8px;
    margin: 2rem 0;
  }
  .blog-single-content blockquote {
    border-left: 4px solid var(--primary);
    padding-left: 1.5rem;
    font-style: italic;
    color: var(--muted);
    margin: 2rem 0;
  }
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: var(--primary);
    font-weight: 600;
    margin-bottom: 2rem;
    transition: color 0.2s;
  }
  .back-link:hover {
    color: var(--primary-dark);
  }
</style>

<div class="blog-single-container">
  <a href="{{ route('landing.blogs') }}" class="back-link">
    <i class="fa-solid fa-arrow-left"></i> Back to all blogs
  </a>
  
  <div class="blog-single-header">
    <div class="blog-single-meta">
      @if($blog['author_avatar'])
        <img src="{{ 'https://lightgoldenrodyellow-mink-721714.hostingersite.com' . $blog['author_avatar'] }}" alt="{{ $blog['author_name'] }}" class="blog-single-avatar">
      @else
        <div class="blog-single-avatar" style="background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem;">
          {{ substr($blog['author_name'], 0, 1) }}
        </div>
      @endif
      <div class="blog-single-author">
        <strong>{{ $blog['author_name'] }}</strong>
        <span>{{ \Carbon\Carbon::parse($blog['created_at'])->format('M d, Y') }} • {{ $blog['author_title'] ?? 'Author' }}</span>
      </div>
    </div>
    <h1 class="blog-single-title">{{ $blog['title'] }}</h1>
  </div>

  @if($blog['cover_image'])
    <img src="{{ 'https://lightgoldenrodyellow-mink-721714.hostingersite.com' . $blog['cover_image'] }}" alt="{{ $blog['title'] }}" class="blog-single-cover">
    @if($blog['image_credits'])
      <div style="text-align: center; font-size: 0.85rem; color: var(--muted); margin-top: -2rem; margin-bottom: 3rem;">
        Image credits: {{ $blog['image_credits'] }}
      </div>
    @endif
  @endif

  <div class="blog-single-content">
    {!! Str::markdown($blog['content']) !!}
  </div>
</div>
@endsection

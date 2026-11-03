@extends('layouts.landing')

@section('page_title', $blog['title'].' | MetoHub Blog')
@section('meta_description', $blog['seo_description'] ?? $blog['excerpt'])
@section('og_type', 'article')
@section('meta_image', $blog['cover_image'] ?? asset('images/landing/social-card.png'))

@section('content')
<style>
  .blog-hero {
    padding: 7.5rem 1.5rem 2.5rem;
    background: #fbfaf7;
    border-bottom: 1px solid #e8e4db;
    text-align: center;
  }
  .blog-hero-inner {
    max-width: 860px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .blog-breadcrumbs {
    display: inline-flex;
    align-items: center;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: var(--muted);
    margin-bottom: 0.75rem;
  }
  .blog-breadcrumbs a {
    color: var(--muted);
    text-decoration: none;
    transition: color 0.2s;
  }
  .blog-breadcrumbs a:hover {
    color: var(--primary);
  }
  .blog-breadcrumbs span[aria-current="page"] {
    color: var(--ink);
    font-weight: 500;
    max-width: 320px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: var(--primary);
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 1.25rem;
    transition: color 0.2s, transform 0.2s;
  }
  .back-link:hover {
    color: var(--primary-dark);
    transform: translateX(-3px);
  }
  .blog-title {
    font-size: clamp(2rem, 4.5vw, 2.75rem);
    font-weight: 800;
    color: var(--dark);
    line-height: 1.25;
    margin: 0;
    letter-spacing: -0.025em;
  }
  .blog-meta-top {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    color: var(--muted);
    font-size: 0.95rem;
    margin-top: 1.25rem;
  }
  .blog-meta-top span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .blog-layout {
    max-width: 1200px;
    margin: 0 auto;
    padding: 3rem clamp(1rem, 4vw, 2rem);
    display: grid;
    grid-template-columns: minmax(0, 1fr) 320px;
    gap: 2.5rem;
    align-items: start;
  }
  
  @media (max-width: 992px) {
    .blog-layout {
      grid-template-columns: 1fr;
    }
  }

  .blog-main {
    min-width: 0;
    overflow-wrap: anywhere;
  }
  
  .blog-cover {
    width: 100%;
    height: auto;
    max-height: 550px;
    border-radius: 16px;
    object-fit: cover;
    margin-bottom: 1rem;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
  }
  
  .image-credits {
    text-align: center;
    font-size: 0.85rem;
    color: var(--muted);
    margin-bottom: 3rem;
  }

  .blog-content {
    font-size: 1.125rem;
    line-height: 1.8;
    color: #6f6b63;
  }
  
  .blog-content h2 {
    font-size: 2rem;
    margin-top: 3rem;
    margin-bottom: 1.25rem;
    color: var(--dark);
    font-weight: 700;
  }
  
  .blog-content h3 {
    font-size: 1.5rem;
    margin-top: 2.5rem;
    margin-bottom: 1rem;
    color: var(--dark);
    font-weight: 600;
  }
  
  .blog-content p {
    margin-bottom: 1.5rem;
  }
  
  .blog-content ul, .blog-content ol {
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
  }
  
  .blog-content li {
    margin-bottom: 0.5rem;
  }
  
  .blog-content blockquote {
    border-left: 4px solid var(--primary);
    padding: 1.5rem;
    background: #fbfaf7;
    border-radius: 0 8px 8px 0;
    font-style: italic;
    color: #475569;
    margin: 2rem 0;
    font-size: 1.25rem;
  }

  .blog-content img { max-width: 100%; height: auto; }
  .blog-content pre { overflow-x: auto; max-width: 100%; }
  .blog-content table { display: block; overflow-x: auto; max-width: 100%; }

  /* Sidebar Styles */
  .blog-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
    position: sticky;
    top: 96px;
    align-self: start;
    z-index: 10;
  }
  
  .sidebar-widget {
    background: #ffffff;
    border: 1px solid #e8e4db;
    border-radius: 16px;
    padding: 1.75rem 1.5rem;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
  }

  .sidebar-widget-cta {
    background: linear-gradient(145deg, var(--primary), var(--primary-dark)) !important;
    color: #ffffff !important;
    text-align: center;
    border: none !important;
    box-shadow: 0 10px 28px rgba(36, 74, 59, 0.22) !important;
  }
  
  .widget-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .author-card {
    text-align: center;
  }
  
  .author-avatar {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 1rem;
    border: 3px solid #fbfaf7;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  }
  
  .author-name {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 0.25rem;
  }
  
  .author-title {
    font-size: 0.88rem;
    color: var(--primary);
    font-weight: 600;
    margin-bottom: 1rem;
  }

  .recent-blog {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f3f0e9;
  }
  .recent-blog:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }
  
  .recent-blog-img {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
  }
  
  .recent-blog-info h4 {
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 0.5rem;
  }
  
  .recent-blog-info h4 a {
    color: var(--dark);
    text-decoration: none;
    transition: color 0.2s;
  }
  
  .recent-blog-info h4 a:hover {
    color: var(--primary);
  }
  
  .recent-blog-date {
    font-size: 0.8rem;
    color: var(--muted);
  }

  .share-buttons {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
  }
  .share-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    text-decoration: none;
    transition: transform 0.2s, opacity 0.2s;
  }
  .share-btn:hover {
    transform: translateY(-2px);
    opacity: 0.9;
  }
  .share-tw { background: #0f1419; }
  .share-fb { background: #1877f2; }
  .share-in { background: #0a66c2; }

  .btn-white-cta {
    display: inline-block;
    width: 100%;
    background: #ffffff;
    color: var(--primary-dark) !important;
    font-weight: 700;
    padding: 12px 20px;
    border-radius: 999px;
    text-decoration: none;
    font-size: 0.95rem;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    transition: transform 0.2s, box-shadow 0.2s, background 0.2s;
  }
  .btn-white-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.18);
    background: #fdfdfd;
  }

  .blog-useful-links {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1.25rem;
    margin-top: 1.25rem;
    font-size: 0.85rem;
  }
  .blog-useful-links a {
    color: rgba(255, 255, 255, 0.85);
    text-decoration: underline;
    text-underline-offset: 3px;
    transition: color 0.2s;
  }
  .blog-useful-links a:hover {
    color: #ffffff;
  }
  


  .bottom-articles {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem clamp(1rem, 4vw, 2rem) 4rem;
  }
  .bottom-articles h3 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 2rem;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .bottom-articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr));
    gap: 2rem;
  }
  .bottom-article-card {
    background: white;
    border: 1px solid #e4ded3;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
  }
  .bottom-article-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08);
  }
  .bottom-article-img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    background: #f3f0e9;
  }
  .bottom-article-content {
    padding: 1.5rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }
  .bottom-article-content h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.4;
  }
  .bottom-article-content h4 a {
    color: var(--dark);
    text-decoration: none;
  }
  .bottom-article-content h4 a:hover {
    color: var(--primary);
  }
  .bottom-article-date {
    font-size: 0.85rem;
    color: var(--muted);
    margin-top: auto;
  }
</style>

<div class="blog-hero">
  <div class="blog-hero-inner">
    <nav class="blog-breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('landing') }}">Home</a><span aria-hidden="true">/</span><a href="{{ route('landing.blogs') }}">Blog</a><span aria-hidden="true">/</span><span aria-current="page">{{ $blog['title'] }}</span></nav>
    <a href="{{ route('landing.blogs') }}" class="back-link">
      <i class="fa-solid fa-arrow-left"></i> Back to all articles
    </a>
    <h1 class="blog-title">{{ $blog['title'] }}</h1>
  </div>
</div>

@if($feedState === 'stale')
<p role="status" style="text-align:center; padding:1rem;">Showing the latest saved article while updates are temporarily unavailable.</p>
@endif
<div class="blog-layout">
  <div class="blog-main">
    @if($blog['cover_image'])
      <img src="{{ $blog['cover_image'] }}" alt="{{ $blog['title'] }}" class="blog-cover" width="1200" height="675" decoding="async" fetchpriority="high">
      @if($blog['image_credits'])
        <div class="image-credits">
          Image credits: {{ $blog['image_credits'] }}
        </div>
      @endif
    @endif

    <div class="blog-content">
      @if(!empty($blog['content']))
        {!! $blog['content'] !!}
      @endif
      
      @if(isset($blog['sections']) && count($blog['sections']) > 0)
        @foreach($blog['sections'] as $section)
          @if(!empty($section['image_path']))
            <img src="{{ $section['image_path'] }}" alt="{{ $blog['title'] }} — section {{ $loop->iteration }}" loading="lazy" decoding="async" style="width: 100%; border-radius: 12px; margin: 2rem 0; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
          @endif
          @if(!empty($section['text_content']))
            <div style="margin-bottom: 1.5rem;">{!! $section['text_content'] !!}</div>
          @endif
        @endforeach
      @endif
    </div>
  </div>
  
  <aside class="blog-sidebar">
    <div class="sidebar-widget author-card">
      @if($blog['author_avatar'])
        <img src="{{ $blog['author_avatar'] }}" alt="{{ $blog['author_name'] }}" class="author-avatar" width="80" height="80" loading="lazy" decoding="async">
      @else
        <div class="author-avatar" style="background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 2.5rem;">
          {{ mb_substr($blog['author_name'], 0, 1) }}
        </div>
      @endif
      <h3 class="author-name">{{ $blog['author_name'] }}</h3>
      <div class="author-title" style="margin-bottom: 0.5rem;">{{ $blog['author_title'] ?? 'Author' }}</div>
      <div style="display:flex; justify-content:center; gap: 1rem; color: var(--muted); font-size: 0.85rem; margin-bottom: 1.5rem; font-weight: 500;">
        @if(($blog['published_at'] ?? 0) > 0 && $blog['published_at'] <= now()->timestamp)
        <time datetime="{{ gmdate('Y-m-d\TH:i:s\Z', (int) $blog['published_at']) }}"><i class="fa-regular fa-calendar" aria-hidden="true"></i> {{ $blog['date_label'] }}</time>
        @endif
        <span><i class="fa-regular fa-clock"></i> {{ $blog['reading_minutes'] }} min read</span>
      </div>
      <p style="font-size: 0.95rem; color: var(--muted); margin-bottom: 1.5rem;">
        Sharing insights on grocery technology, eCommerce trends, and retail management.
      </p>
      <div class="share-buttons">
        <a href="https://twitter.com/intent/tweet?url={{ urlencode(rtrim(config('app.url'), '/').'/blogs/'.($blog['slug'] ?? $blog['id'])) }}&amp;text={{ urlencode($blog['title']) }}" aria-label="Share on X" target="_blank" rel="noopener noreferrer" class="share-btn share-tw"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(rtrim(config('app.url'), '/').'/blogs/'.($blog['slug'] ?? $blog['id'])) }}" aria-label="Share on Facebook" target="_blank" rel="noopener noreferrer" class="share-btn share-fb"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(rtrim(config('app.url'), '/').'/blogs/'.($blog['slug'] ?? $blog['id'])) }}" aria-label="Share on LinkedIn" target="_blank" rel="noopener noreferrer" class="share-btn share-in"><i class="fa-brands fa-linkedin-in"></i></a>
      </div>
    </div>
    
    <div class="sidebar-widget sidebar-widget-cta">
      <h3 style="color: #ffffff; font-size: 1.4rem; margin-bottom: 0.75rem; font-weight: 700; line-height: 1.3;">Start selling online today</h3>
      <p style="color: rgba(255,255,255,0.9); margin-bottom: 1.5rem; font-size: 0.92rem; line-height: 1.5;">Give your grocery products an independent store of their own. Explore themes and choose a plan for your brand.</p>
      <a href="{{ route('landing', ['get_started' => 1, 'plan' => 'base']) }}" class="btn-white-cta">Start {{ config('saas.trial_days') }}-day free trial</a>
      <div class="blog-useful-links"><a href="{{ route('landing.themes') }}">Explore store themes</a><a href="{{ route('landing') }}#pricing">Compare plans</a></div>
    </div>
  </aside>
</div>

@if(isset($recentBlogs) && count($recentBlogs) > 0)
<div class="bottom-articles">
  <h3><i class="fa-solid fa-newspaper text-primary"></i> More Articles</h3>
  <div class="bottom-articles-grid">
    @foreach($recentBlogs as $recent)
    <div class="bottom-article-card">
      @if($recent['cover_image'])
        <img src="{{ $recent['cover_image'] }}" class="bottom-article-img" alt="{{ $recent['title'] }}" width="640" height="400" loading="lazy" decoding="async">
      @else
         <div class="bottom-article-img" style="display:flex; align-items:center; justify-content:center; color:#8c857a;">
           <i class="fa-solid fa-image fa-2x"></i>
         </div>
      @endif
      <div class="bottom-article-content">
        <h4><a href="{{ route('landing.blog.show', $recent['slug'] ?? $recent['id']) }}">{{ Str::limit($recent['title'], 60) }}</a></h4>
        <div class="bottom-article-date">{{ $recent['date_label'] }}</div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

@endsection

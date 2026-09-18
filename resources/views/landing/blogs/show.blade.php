@extends('landing.blogs.layout')

@section('content')
<style>
  .blog-hero {
    padding: 2rem 2rem 1.5rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    text-align: center;
  }
  .blog-hero-inner {
    max-width: 900px;
    margin: 0 auto;
  }
  .blog-title {
    font-size: 2.75rem;
    font-weight: 800;
    color: var(--dark);
    line-height: 1.2;
    margin-bottom: 0.5rem;
    letter-spacing: -0.03em;
  }
  .blog-meta-top {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    color: var(--muted);
    font-size: 1rem;
    margin-bottom: 2rem;
  }
  .blog-meta-top span {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .blog-layout {
    max-width: 1200px;
    margin: 0 auto;
    padding: 4rem 2rem;
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 4rem;
  }
  
  @media (max-width: 992px) {
    .blog-layout {
      grid-template-columns: 1fr;
    }
  }

  .blog-main {
    min-width: 0;
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
    color: #334155;
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
    background: #f8fafc;
    border-radius: 0 8px 8px 0;
    font-style: italic;
    color: #475569;
    margin: 2rem 0;
    font-size: 1.25rem;
  }

  /* Sidebar Styles */
  .blog-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    position: sticky;
    top: 120px;
    align-self: start;
  }
  
  .sidebar-widget {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
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
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 1rem;
    border: 4px solid #f8fafc;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  
  .author-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 0.25rem;
  }
  
  .author-title {
    font-size: 0.9rem;
    color: var(--primary);
    font-weight: 600;
    margin-bottom: 1rem;
  }

  .recent-blog {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
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
    gap: 1rem;
    justify-content: center;
  }
  .share-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
    text-decoration: none;
    transition: transform 0.2s;
  }
  .share-btn:hover {
    transform: translateY(-3px);
  }
  .share-tw { background: #0f1419; }
  .share-fb { background: #1877f2; }
  .share-in { background: #0a66c2; }
  
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

  .bottom-articles {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 2rem 6rem;
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
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
  }
  .bottom-article-card {
    background: white;
    border: 1px solid #e2e8f0;
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
    background: #f1f5f9;
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
    <a href="{{ route('landing.blogs') }}" class="back-link">
      <i class="fa-solid fa-arrow-left"></i> Back to all articles
    </a>
    <h1 class="blog-title" style="margin-top: 1rem;">{{ $blog['title'] }}</h1>
  </div>
</div>

<div class="blog-layout">
  <div class="blog-main">
    @if($blog['cover_image'])
      <img src="{{ 'https://blogs.task19.com' . $blog['cover_image'] }}" alt="{{ $blog['title'] }}" class="blog-cover">
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
            <img src="{{ 'https://blogs.task19.com' . $section['image_path'] }}" alt="Section image" style="width: 100%; border-radius: 12px; margin: 2rem 0; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
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
        <img src="{{ 'https://blogs.task19.com' . $blog['author_avatar'] }}" alt="{{ $blog['author_name'] }}" class="author-avatar">
      @else
        <div class="author-avatar" style="background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 2.5rem;">
          {{ substr($blog['author_name'], 0, 1) }}
        </div>
      @endif
      <h3 class="author-name">{{ $blog['author_name'] }}</h3>
      <div class="author-title" style="margin-bottom: 0.5rem;">{{ $blog['author_title'] ?? 'Author' }}</div>
      <div style="display:flex; justify-content:center; gap: 1rem; color: var(--muted); font-size: 0.85rem; margin-bottom: 1.5rem; font-weight: 500;">
        <span><i class="fa-regular fa-calendar"></i> {{ \Carbon\Carbon::parse($blog['created_at'])->format('M d, Y') }}</span>
        <span><i class="fa-regular fa-clock"></i> 5 min read</span>
      </div>
      <p style="font-size: 0.95rem; color: var(--muted); margin-bottom: 1.5rem;">
        Sharing insights on grocery technology, eCommerce trends, and retail management.
      </p>
      <div class="share-buttons">
        <a href="#" class="share-btn share-tw"><i class="fa-brands fa-x-twitter"></i></a>
        <a href="#" class="share-btn share-fb"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="#" class="share-btn share-in"><i class="fa-brands fa-linkedin-in"></i></a>
      </div>
    </div>


    
    <div class="sidebar-widget" style="background: var(--primary); color: white; text-align: center;">
      <h3 style="color: white; font-size: 1.5rem; margin-bottom: 1rem; font-weight: 700;">Start selling online today</h3>
      <p style="color: rgba(255,255,255,0.9); margin-bottom: 1.5rem; font-size: 0.95rem;">Join thousands of grocery stores upgrading to GoSlot Store.</p>
      <a href="/#pricing" class="btn btn-white" style="width: 100%;">Start Free Trial</a>
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
        <img src="{{ 'https://blogs.task19.com' . $recent['cover_image'] }}" class="bottom-article-img" alt="Blog cover">
      @else
         <div class="bottom-article-img" style="display:flex; align-items:center; justify-content:center; color:#94a3b8;">
           <i class="fa-solid fa-image fa-2x"></i>
         </div>
      @endif
      <div class="bottom-article-content">
        <h4><a href="{{ route('landing.blog.show', $recent['id']) }}">{{ Str::limit($recent['title'], 60) }}</a></h4>
        <div class="bottom-article-date">{{ \Carbon\Carbon::parse($recent['created_at'])->format('M d, Y') }}</div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

@endsection

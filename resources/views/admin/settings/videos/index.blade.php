@extends('layouts.admin')

@section('title', 'Showcase Videos Settings')

@section('content')
<div class="container-fluid py-4" style="background: var(--sf-bg); min-height: 100vh;">

    <!-- Premium Header -->
    <div class="mb-5 position-relative overflow-hidden p-4 rounded-4" style="background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%); border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div class="position-absolute rounded-circle" style="width: 250px; height: 250px; background: rgba(255, 255, 255, 0.15); filter: blur(80px); top: -100px; right: -50px; pointer-events: none;"></div>
        
        <div class="position-relative z-1">
            <span class="badge mb-2 px-3 py-1.5 rounded-pill uppercase tracking-wider text-white-50" style="background: rgba(255,255,255,0.08); font-size: 10px; font-weight: 600; letter-spacing: 1.2px; border: 1px solid rgba(255,255,255,0.12);">ESTHETIC MANAGEMENT</span>
            <h1 class="h2 text-white mb-2 fw-bold" style="font-family: 'Playfair Display', Georgia, serif; letter-spacing: -0.5px;">Custom Showcase Videos</h1>
            <p class="text-white-50 small mb-0" style="max-width: 600px; line-height: 1.6;">Configure the 5 YouTube videos displayed in the home page showcase of your theme. Enter the full YouTube URLs below.</p>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4 p-3 shadow-sm" role="alert" style="background: #ede9fe; border-left: 5px solid #8b5cf6 !important; color: #6d28d9;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle" style="color: #8b5cf6; font-size: 18px;"></i>
                <div>
                    <strong class="fw-semibold">Success!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 rounded-4 shadow-sm p-4" style="background: #ffffff;">
        <form action="{{ route('admin.settings.videos.update') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <!-- Video 1 -->
                <div class="col-md-6 col-12">
                    <div class="card p-3 border rounded-3 bg-light-subtle shadow-sm h-100">
                        <div class="form-group mb-3">
                            <label for="video1_url" class="form-label fw-semibold text-dark small">Video 1 (Celebrity 1 / Story 1) URL</label>
                            <input type="text" name="video1_url" id="video1_url" class="form-control rounded-3 py-2 px-3 @error('video1_url') is-invalid @enderror" value="{{ old('video1_url', $videoSetting->video1_url) }}" placeholder="e.g. https://www.youtube.com/watch?v=167AIKitcZs">
                            <div class="form-text text-muted small">Defaults to video 1 if left empty.</div>
                            @error('video1_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="video1_collection_id" class="form-label fw-semibold text-dark small">Redirect Collection for Video 1</label>
                            <select name="video1_collection_id" id="video1_collection_id" class="form-select rounded-3 py-2 px-3 @error('video1_collection_id') is-invalid @enderror">
                                <option value="">-- Redirect to All Products (Default) --</option>
                                @foreach($collections as $col)
                                    <option value="{{ $col->id }}" {{ old('video1_collection_id', $videoSetting->video1_collection_id) == $col->id ? 'selected' : '' }}>{{ $col->name }}</option>
                                @endforeach
                            </select>
                            @error('video1_collection_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Video 2 -->
                <div class="col-md-6 col-12">
                    <div class="card p-3 border rounded-3 bg-light-subtle shadow-sm h-100">
                        <div class="form-group mb-3">
                            <label for="video2_url" class="form-label fw-semibold text-dark small">Video 2 (Celebrity 2 / Story 2) URL</label>
                            <input type="text" name="video2_url" id="video2_url" class="form-control rounded-3 py-2 px-3 @error('video2_url') is-invalid @enderror" value="{{ old('video2_url', $videoSetting->video2_url) }}" placeholder="e.g. https://www.youtube.com/watch?v=QM18rD-zrCs">
                            <div class="form-text text-muted small">Defaults to video 2 if left empty.</div>
                            @error('video2_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="video2_collection_id" class="form-label fw-semibold text-dark small">Redirect Collection for Video 2</label>
                            <select name="video2_collection_id" id="video2_collection_id" class="form-select rounded-3 py-2 px-3 @error('video2_collection_id') is-invalid @enderror">
                                <option value="">-- Redirect to All Products (Default) --</option>
                                @foreach($collections as $col)
                                    <option value="{{ $col->id }}" {{ old('video2_collection_id', $videoSetting->video2_collection_id) == $col->id ? 'selected' : '' }}>{{ $col->name }}</option>
                                @endforeach
                            </select>
                            @error('video2_collection_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Video 3 -->
                <div class="col-md-6 col-12">
                    <div class="card p-3 border rounded-3 bg-light-subtle shadow-sm h-100">
                        <div class="form-group mb-3">
                            <label for="video3_url" class="form-label fw-semibold text-dark small">Video 3 (Celebrity 3 / Story 3) URL</label>
                            <input type="text" name="video3_url" id="video3_url" class="form-control rounded-3 py-2 px-3 @error('video3_url') is-invalid @enderror" value="{{ old('video3_url', $videoSetting->video3_url) }}" placeholder="e.g. https://www.youtube.com/watch?v=P7MxjMYwU_g">
                            <div class="form-text text-muted small">Defaults to video 3 if left empty.</div>
                            @error('video3_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="video3_collection_id" class="form-label fw-semibold text-dark small">Redirect Collection for Video 3</label>
                            <select name="video3_collection_id" id="video3_collection_id" class="form-select rounded-3 py-2 px-3 @error('video3_collection_id') is-invalid @enderror">
                                <option value="">-- Redirect to All Products (Default) --</option>
                                @foreach($collections as $col)
                                    <option value="{{ $col->id }}" {{ old('video3_collection_id', $videoSetting->video3_collection_id) == $col->id ? 'selected' : '' }}>{{ $col->name }}</option>
                                @endforeach
                            </select>
                            @error('video3_collection_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Video 4 -->
                <div class="col-md-6 col-12">
                    <div class="card p-3 border rounded-3 bg-light-subtle shadow-sm h-100">
                        <div class="form-group mb-3">
                            <label for="video4_url" class="form-label fw-semibold text-dark small">Video 4 (Celebrity 4 / Story 4) URL</label>
                            <input type="text" name="video4_url" id="video4_url" class="form-control rounded-3 py-2 px-3 @error('video4_url') is-invalid @enderror" value="{{ old('video4_url', $videoSetting->video4_url) }}" placeholder="e.g. https://www.youtube.com/watch?v=UujTjwkuqbE">
                            <div class="form-text text-muted small">Defaults to video 4 if left empty.</div>
                            @error('video4_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="video4_collection_id" class="form-label fw-semibold text-dark small">Redirect Collection for Video 4</label>
                            <select name="video4_collection_id" id="video4_collection_id" class="form-select rounded-3 py-2 px-3 @error('video4_collection_id') is-invalid @enderror">
                                <option value="">-- Redirect to All Products (Default) --</option>
                                @foreach($collections as $col)
                                    <option value="{{ $col->id }}" {{ old('video4_collection_id', $videoSetting->video4_collection_id) == $col->id ? 'selected' : '' }}>{{ $col->name }}</option>
                                @endforeach
                            </select>
                            @error('video4_collection_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Video 5 -->
                <div class="col-md-6 col-12">
                    <div class="card p-3 border rounded-3 bg-light-subtle shadow-sm h-100">
                        <div class="form-group mb-3">
                            <label for="video5_url" class="form-label fw-semibold text-dark small">Video 5 (Celebrity 5 / Story 5) URL</label>
                            <input type="text" name="video5_url" id="video5_url" class="form-control rounded-3 py-2 px-3 @error('video5_url') is-invalid @enderror" value="{{ old('video5_url', $videoSetting->video5_url) }}" placeholder="e.g. https://www.youtube.com/watch?v=WamyeDrjaVA">
                            <div class="form-text text-muted small">Defaults to video 5 if left empty.</div>
                            @error('video5_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="video5_collection_id" class="form-label fw-semibold text-dark small">Redirect Collection for Video 5</label>
                            <select name="video5_collection_id" id="video5_collection_id" class="form-select rounded-3 py-2 px-3 @error('video5_collection_id') is-invalid @enderror">
                                <option value="">-- Redirect to All Products (Default) --</option>
                                @foreach($collections as $col)
                                    <option value="{{ $col->id }}" {{ old('video5_collection_id', $videoSetting->video5_collection_id) == $col->id ? 'selected' : '' }}>{{ $col->name }}</option>
                                @endforeach
                            </select>
                            @error('video5_collection_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                <button type="submit" class="btn btn-primary text-white py-2 px-4 fw-semibold" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important; border: none !important; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 128, 96, 0.15);">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

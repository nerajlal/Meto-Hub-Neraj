@extends('layouts.admin')

@section('title', 'Add Customer')

@section('content')
@section('content')
<div class="container pb-5" style="max-width: 900px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('admin.customers') }}" class="text-secondary hover-text-dark">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="h3 mb-0 text-primary">Add customer</h1>
    </div>

    <form action="#" method="POST">
        
        <!-- Customer Overview -->
        <div class="card border shadow-sm p-4 mb-4">
             <h2 class="h6 fw-bold text-secondary mb-4">Customer Overview</h2>
             <div class="row g-3">
                 <div class="col-12 col-md-6">
                    <label class="form-label fw-medium text-secondary small">First name</label>
                    <input type="text" class="form-control">
                 </div>
                 <div class="col-12 col-md-6">
                    <label class="form-label fw-medium text-secondary small">Last name</label>
                    <input type="text" class="form-control">
                 </div>
                 <div class="col-12">
                    <label class="form-label fw-medium text-secondary small">Email</label>
                    <input type="email" class="form-control">
                 </div>
                  <div class="col-12">
                    <label class="form-label fw-medium text-secondary small">Phone number</label>
                    <input type="tel" class="form-control">
                 </div>
                 
                 <div class="col-12">
                     <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="marketing">
                        <label class="form-check-label text-secondary small" for="marketing">
                            Customer agreed to receive marketing emails.
                        </label>
                     </div>
                 </div>
                 
                  <div class="col-12">
                     <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="sms_marketing">
                        <label class="form-check-label text-secondary small" for="sms_marketing">
                            Customer agreed to receive SMS marketing.
                        </label>
                     </div>
                 </div>
             </div>
        </div>

        <!-- Address -->
        <div class="card border shadow-sm p-4 mb-4">
             <h2 class="h6 fw-bold text-secondary mb-4">Address</h2>
             <div class="row g-3">
                 <div class="col-12">
                    <label class="form-label fw-medium text-secondary small">Country/Region</label>
                    <select class="form-select">
                        <option>India</option>
                        <option>United States</option>
                        <option>United Kingdom</option>
                        <option>UAE</option>
                    </select>
                 </div>
                  <div class="col-12">
                    <label class="form-label fw-medium text-secondary small">Company</label>
                    <input type="text" class="form-control">
                 </div>
                 <div class="col-12">
                    <label class="form-label fw-medium text-secondary small">Address</label>
                    <input type="text" class="form-control">
                 </div>
                 <div class="col-12">
                    <label class="form-label fw-medium text-secondary small">Apartment, suite, etc.</label>
                    <input type="text" class="form-control">
                 </div>
                 <div class="col-12 col-md-4">
                    <label class="form-label fw-medium text-secondary small">City</label>
                    <input type="text" class="form-control">
                 </div>
                 <div class="col-12 col-md-4">
                    <label class="form-label fw-medium text-secondary small">State</label>
                     <select class="form-select">
                        <option>Maharashtra</option>
                        <option>Delhi</option>
                        <option>Karnataka</option>
                        <option>Tamil Nadu</option>
                    </select>
                 </div>
                 <div class="col-12 col-md-4">
                    <label class="form-label fw-medium text-secondary small">PIN code</label>
                    <input type="text" class="form-control">
                 </div>
             </div>
        </div>

        <!-- Notes -->
         <div class="card border shadow-sm p-4 mb-4">
             <h2 class="h6 fw-bold text-secondary mb-4">Notes</h2>
             <div>
                <label class="form-label fw-medium text-secondary small">Note</label>
                <textarea rows="3" class="form-control" placeholder="Add a note about this customer"></textarea>
             </div>
        </div>
        
        <div class="d-flex justify-content-end gap-3 pt-4 border-top">
             <button type="button" class="btn btn-white border text-secondary shadow-sm">Discard</button>
            <button type="submit" class="btn btn-primary shadow-sm">Save</button>
        </div>

    </form>
</div>
<style>
    .hover-text-dark:hover { color: #343a40 !important; }
</style>
@endsection
@endsection

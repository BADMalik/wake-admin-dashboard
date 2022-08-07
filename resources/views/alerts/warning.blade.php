@if (session($key ?? 'status'))
    <div class="alert alert-warning" role="alert">
        {{ session($key ?? 'status') }}
    </div>
@endif
@if ($errors->any())
    <div class="form-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li class="error-item">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

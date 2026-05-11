 


<div>
    {{ $this->form }}
    
    @if (count($actions = $this->getCachedHeaderActions()))
        <div class="fi-header-actions">
            @foreach ($actions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
</div>
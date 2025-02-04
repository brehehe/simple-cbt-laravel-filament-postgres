<div>
    <span>{{$alphabet}} . </span>
    @if ($imageUrl)
        <div class="flex items-center space-x-2">
            <img src="{{ asset('storage/'.$imageUrl) }}" alt="{{$title}}" class="object-cover" style="width: 175px; margin-left: 15px;">
        </div>
        <br>
    @endif
    <span>{{$title}}</span>
</div>

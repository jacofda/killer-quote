
@if($activeLangs)
    @foreach($activeLangs as $key => $locale)
        <a class="btn btn-default" href="{{url('killerquotes/settings/'.$locale)}}"><i class="fas fa-cog"></i> {{$locale}}</a>
    @endforeach
@endif

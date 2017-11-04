<div class="errors alert alert-danger">
    @foreach($errors->all() as $error)
        <p>{{$error}}</p>
    @endforeach
</div>
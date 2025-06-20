<!DOCTYPE HTML>

<html>
<head>
    @if(isset($seoTagsWithValues) && count($seoTagsWithValues))
        @foreach($seoTagsWithValues as $seoTagsWithValue)
            {!! $seoTagsWithValue !!}
    @endforeach
@endif
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
      rel="stylesheet">
<!-- Additional CSS Files -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/owl.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/animated.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/main.css')}}">
<link rel="stylesheet" href="{{ asset('assets/css/fontawesome.css')}}">
</head>
<body>


@if(isset($homePage) && $homePage->contents && $homePage->contents->count())
    @foreach($homePage->contents as $content)
        {!! $content->content !!}
    @endforeach
@endif


<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/owl-carousel.js') }}"></script>
<script src="{{ asset('assets/js/animation.js') }}"></script>
<script src="{{ asset('assets/js/imagesloaded.js') }}"></script>
<script src="{{ asset('assets/js/templatemo-custom.js') }}"></script>
</body>
</html>

<!DOCTYPE HTML>

<html>
<head>
    @if(isset($seoTagsWithValues) && count($seoTagsWithValues))
        @foreach($seoTagsWithValues as $seoTagsWithValue)
            {!! $seoTagsWithValue !!}
    @endforeach
@endif
<link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('assets/css/main.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">

</head>
<body>


@if(isset($homePage) && $homePage->contents && $homePage->contents->count())
    @foreach($homePage->contents as $content)
        {!! $content->content !!}
    @endforeach
@endif

<a href="#top" class="back-top-btn" aria-label="înapoi sus" data-back-top-btn>
    <ion-icon name="caret-up" aria-hidden="true"></ion-icon>
</a>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>


<script src="{{ asset('assets/js/script.js') }}"></script>

</body>
</html>

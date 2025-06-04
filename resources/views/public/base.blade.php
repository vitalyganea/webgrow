<!DOCTYPE HTML>
<!--
	Helios by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
<head>
    <title>Helios by HTML5 UP</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
    <noscript><link rel="stylesheet" href="{{ asset('assets/css/noscript.css'}}" /></noscript>
</head>
<body class="homepage is-preload">
<div id="page-wrapper">

        <!-- Layout -->
        {{ $slot }}
        <!-- /Layout -->

        {{-- <script src="{{ asset('js/theme.js') }}"></script> --}}
</div>
    <script src="../../../public/assets/js/jquery.min.js"></script>
    <script src="../../../public/assets/js/jquery.dropotron.min.js"></script>
    <script src="../../../public/assets/js/jquery.scrolly.min.js"></script>
    <script src="../../../public/assets/js/jquery.scrollex.min.js"></script>
    <script src="../../../public/assets/js/browser.min.js"></script>
    <script src="../../../public/assets/js/breakpoints.min.js"></script>
    <script src="../../../public/assets/js/util.js"></script>
    <script src="../../../public/assets/js/main.js"></script>

</body>
</html>

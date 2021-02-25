@if (isset($iconFolder))
    <link rel="apple-touch-icon" sizes="180x180" href="/{{$iconFolder}}/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/{{$iconFolder}}/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/{{$iconFolder}}/favicon-16x16.png">
    <link rel="manifest" href="/{{$iconFolder}}/site.webmanifest">
    <link rel="mask-icon" href="/{{$iconFolder}}/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/{{$iconFolder}}/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="/{{$iconFolder}}/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
@else
    <!-- use the default icon in the public folder -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#ffc40d">
    <meta name="theme-color" content="#ffffff">
@endif

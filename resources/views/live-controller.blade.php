@props([
    'liveMarkup' => '',
    'fallbackImage' => '',
    'fallbackVideo' => '',
])

<html>

<body style="margin: 0; padding: 0">
    @if ($liveMarkup)
        {!! $liveMarkup !!}
    @elseif ($fallbackVideo)
        {!! $fallbackVideo !!}
    @elseif ($fallbackImage)
        <img src="{{ $fallbackImage }}" style="width:100%; height:auto;" />
    @else
        No live content; please check back later.
    @endif

    <script>
        window.addEventListener('load', function() {
            window.top.postMessage({
                height: document.body.scrollHeight,
                width: document.body.scrollWidth
            }, '*');
        });
    </script>
</body>

</html>

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
        <img src="{{ $fallbackImage }}" />
    @else
        No live content; please check back later.
    @endif
</body>

</html>

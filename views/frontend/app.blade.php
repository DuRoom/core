<!doctype html>
<html @if ($direction) dir="{{ $direction }}" @endif
      @if ($language) lang="{{ $language }}" @endif>
    <head>
        <meta charset="utf-8">
        <title>{{ $title }}</title>

        {!! $head !!}
    </head>

    <body>
        {!! $layout !!}

        <div id="modal"></div>
        <div id="alerts"></div>

        <script>
            document.getElementById('duroom-loading').style.display = 'block';
            var duroom = {extensions: {}};
        </script>

        {!! $js !!}

        <script>
            document.getElementById('duroom-loading').style.display = 'none';

            try {
                duroom.core.app.load(@json($payload));
                duroom.core.app.bootExtensions(duroom.extensions);
                duroom.core.app.boot();
            } catch (e) {
                var error = document.getElementById('duroom-loading-error');
                error.innerHTML += document.getElementById('duroom-content').textContent;
                error.style.display = 'block';
                throw e;
            }
        </script>

        {!! $foot !!}
    </body>
</html>

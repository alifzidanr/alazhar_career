@php
    /*
     * One shared error page for every error. The markup lives in
     * public/errors/kesalahan-teknis.html so nginx can serve the exact same page
     * for errors that never reach Laravel (413, 502, 504, ...). Here the nginx SSI
     * block that prints the status code is swapped for the code Laravel knows,
     * and a view may pass $judul / $pesan to replace the default heading and text.
     */
    $statusCode = isset($exception) && method_exists($exception, 'getStatusCode')
        ? $exception->getStatusCode()
        : 500;

    $page = file_get_contents(public_path('errors/kesalahan-teknis.html'));

    $page = preg_replace(
        '/<!--# if expr=.*?<!--# endif -->/s',
        '<p class="code">Kode galat: '.e($statusCode).'</p>',
        $page
    );

    if (isset($judul)) {
        $page = str_replace(
            ['<title>Terjadi Kendala Teknis', '<h1>Terjadi Kendala Teknis</h1>'],
            ['<title>'.e($judul), '<h1>'.e($judul).'</h1>'],
            $page
        );
    }

    if (isset($pesan)) {
        $page = preg_replace('/<p class="lead">.*?<\/p>/s', '<p class="lead">'.e($pesan).'</p>', $page);
    }
@endphp
{!! $page !!}

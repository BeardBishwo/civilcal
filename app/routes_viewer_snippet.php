
// File Viewer Routes
$router->add('GET', '/library/view/{id}', 'ViewerController@view');
$router->add('GET', '/api/library/stream', 'Api\LibraryApiController@stream'); // Need this for PDF.js/Image
$router->add('GET', '/api/library/preview-image', 'Api\LibraryApiController@previewImage');

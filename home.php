<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <title>Odoo Manager</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Odoo Manager</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" type="button" onclick="loadModule('branches')">Branches</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" type="button">Status</a>
                    </li>                    
                </ul>
            </div>
        </div>
    </nav>

    <div class="modal" id="modal_base" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                
            </div>
        </div>
    </div>

    <div class="container mt-4" id="content_area"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src=/js/jquery.sessions.js></script>
    <script src="/js/modal.js"></script>
    <script>

        $(document).ready(function() {
            urlClear();
        });

        function loadModule(module_to_load) {            
            requestModule(module_to_load)
                .then((content) => {
                    $('#content_area').html(content);
                });
        }

        async function requestModule(requestModule) {
            let content = await $.ajax({
                url: `/modules/${requestModule}/index.php`,
                type: 'GET'
            });            

            return content;
        }

        function urlClear() {
            if (window.location.href.indexOf('#') > -1) {
                window.location.href = window.location.href.split('#')[0];
            }
        }

    </script>
    
</body>
</html>
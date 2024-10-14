<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <style>
        body {
            height: 100vh; /* Para centralizar verticalmente */
        }

        .badge-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
    <title>Personal Odoo Manager</title>
</head>
<body class="d-flex align-items-center justify-content-center">

    <div class="card" style="width: 22rem;">
        <div class="card-header badge-container d-none">
            <span id="badge_success" class="badge bg-success d-none">Login efetuado com sucesso!</span>

            <span id="badge_danger" class="badge bg-danger d-none">Email ou senha inválidos</span>

            <span id="badge_warning" class="badge bg-warning text-dark d-none">Erro de autenticação, tente novamente mais tarde</span>
        </div>
        <div class="card-body">
            <form id="login_form">
                <div class="mb-3">
                    <label for="InputEmail" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="InputEmail" aria-describedby="emailHelp">
                    <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
                </div>
                <div class="mb-3">
                    <label for="InputPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="InputPassword">
                </div>          

                <button onclick="loginSubmit()" type="button" class="btn btn-primary btn-sm">Login</button>

                <div class="mb-3 float-end">
                    <a type="button" onclick="signUp()">Sign up</a>
                </div> 
            </form>
        </div>
    </div>    

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src=/js/jquery.sessions.js></script>
    <script>

        let base_url = "http://localhost";

        $(document).ready(function(){
            getSalt()
                .then((response) => {
                    // if (response.TYPE === "SUCCESS") {
                    //     $("#badge_success").removeClass("d-none");
                    // } else if(response.TYPE === "ERROR") {
                    //     $("#badge_danger").removeClass("d-none");
                    // } else {
                    //     $("#badge_warning").removeClass("d-none");

                    // }

                    // $(".card-header").removeClass("d-none");
                });
        });

        function homeRedirect() {
            window.location.href = `${base_url}/home.php`;
        }

        function signUp() {
            requestSignUp()
        }

        async function requestSignUp() {
            let response = await $.ajax({
                url: `${base_url}/api/auth/signUp`,
                method: "POST",
                data: {
                    email: $("#InputEmail").val(),
                    password: $("#InputPassword").val(),
                    salt: $.session.get('sessionID')
                }
            });

            return response;
        }

        function loginSubmit() {
            console.log("Login submit");

            loginRequest()
                .then((response) => {
                    $('.card-header').removeClass('d-none');

                    if (response.TYPE == 'SUCCESS') {
                        $.session.set('token', response.TOKEN);                        
                        $('#badge_success').removeClass('d-none');

                        setTimeout(() => {
                            homeRedirect();
                        }, 1500);
                    } else if(response.TYPE == 'ERROR') {
                        console.log(response.MSG);
                        $('#badge_danger').removeClass('d-none');
                    } else {
                        $('#badge_warning').removeClass('d-none');
                    }
                });
        }

        async function loginRequest() {
            let response = await $.ajax({
                url: `${base_url}/api/auth/login`,
                method: "POST",
                data: {
                    email: $("#InputEmail").val(),
                    password: $("#InputPassword").val(),
                    salt: $.session.get('sessionID')
                }
            });

            return response;
        }

        function setSession(sessionID) {
            $.session.set('sessionID', sessionID);
        }

        async function getSalt() {            
            let response = await $.ajax({
                url: `${base_url}/api/auth/getSessionID`,
                method: "GET"                
            });

            setSession(response.DATA);
            
            return response;
        }

    </script>
</body>
</html>
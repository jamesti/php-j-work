<style type="text/css">
    body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
    }

    .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
        -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
        box-shadow: 0 1px 2px rgba(0,0,0,.05);
    }
    .form-signin .form-signin-heading,
    .form-signin .checkbox {
        margin-bottom: 10px;
    }
    .form-signin input[type="text"],
    .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
    }

</style>

<div class="container">

    <?php
    $token = md5('olympus' . time());

    $_SESSION['form_token'] = $token;
    ?>

    <form class="form-signin" method="post" action="<?= htmlspecialchars('?action=' . md5($token . $_SESSION['token'])) ?>">
        <input type="hidden" name="token" value="<?= $token ?>" />
        <h2 class="form-signin-heading">Login do Sistema</h2>
        <input name="login" type="text" class="input-block-level" placeholder="Login" required>
        <input name="senha" type="password" class="input-block-level" placeholder="Senha" required>
        <label class="checkbox">
            <input type="checkbox" value="remember-me"> Lembre-me
        </label>
        <button class="btn btn-large btn-primary" type="submit">Logar</button>
    </form>

</div>
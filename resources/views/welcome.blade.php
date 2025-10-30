<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Academia de Karate</title>
  
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    body {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f5f5f5;
    }
    .login-card {
      width: 100%;
      max-width: 400px;
      padding: 2rem;
      background: #fff;
      border-radius: 0.75rem;
      box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15);
    }
    .logo {
      display: block;
      margin: 0 auto 1.5rem;
      max-width: 150px;
    }
  </style>
</head>
<body>

  <div class="login-card">
    <img src="images/logo_branco.png" alt="Academia de Karate" class="logo">
    <h4 class="text-center mb-4">Acesso ao Sistema</h4>
    
    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input id="email" class="form-control" type="email" name="email" required autofocus>
        @if($errors->has('email'))
          <div class="text-danger mt-1">{{ $errors->first('email') }}</div>
        @endif
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Senha</label>
        <input id="password" class="form-control" type="password" name="password" required>
        @if($errors->has('password'))
          <div class="text-danger mt-1">{{ $errors->first('password') }}</div>
        @endif
      </div>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
          <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
          <label for="remember_me" class="form-check-label">Lembrar-me</label>
        </div>
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="small">Esqueceu sua senha?</a>
        @endif
      </div>
      <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

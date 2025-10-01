<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Academia de Karate</title>
  
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <!-- CSS Externo -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <!-- Modal de Login -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg">
        <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">Acesso</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
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
            <div class="form-check mb-3">
              <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
              <label for="remember_me" class="form-check-label">Lembrar-me</label>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small">Esqueceu sua senha?</a>
              @endif
              <button type="submit" class="btn btn-primary px-4">Entrar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img src="images/logo_branco.png" alt="Academia de Karate" class="img-fluid logo-navbar">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="#sobre">Sobre</a></li>
          <li class="nav-item"><a class="nav-link" href="#treinos">Treinamentos</a></li>
          <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>
          <li class="nav-item">
            <a class="nav-link" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#loginModal">Entrar</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Hero -->
  <section class="hero">
    <div class="content text-center" data-aos="fade-up">
      <h1 class="display-4 fw-bold">Treine Karate com Excelência</h1>
      <p class="lead">Disciplina, força e equilíbrio para todas as idades</p>
    </div>
  </section>

  <!-- Sobre -->
  <section id="sobre" class="py-5">
    <div class="container">
      <h2 class="section-title">Sobre Nossa Academia</h2>
      <p class="text-center" data-aos="fade-up">
        Nossa academia oferece aulas de Karate para iniciantes e avançados, focando na disciplina, força física e mental, além de respeito e valores tradicionais das artes marciais.
      </p>
    </div>
  </section>

  <!-- Treinos -->
  <section id="treinos" class="py-5">
    <div class="container">
      <h2 class="section-title">Treinamentos</h2>
      <div class="row g-4">
        <div class="col-md-4" data-aos="fade-up">
          <div class="card shadow-sm">
            <img src="images/karate-kata.jpg" class="card-img-top" alt="Kata">
            <div class="card-body">
              <h5 class="card-title">Kata</h5>
              <p class="card-text">Aprenda sequências de movimentos formais para desenvolver técnica e concentração.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
          <div class="card shadow-sm">
            <img src="images/karate-kumite.jpg" class="card-img-top" alt="Kumite">
            <div class="card-body">
              <h5 class="card-title">Kumite</h5>
              <p class="card-text">Treinamento de combate controlado para desenvolver reflexos e estratégia.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
          <div class="card shadow-sm">
            <img src="images/karate-fitness.jpg" class="card-img-top" alt="Fitness">
            <div class="card-body">
              <h5 class="card-title">Fitness</h5>
              <p class="card-text">Exercícios de condicionamento físico adaptados ao Karate para todas as idades.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Contato -->
  <section id="contato" class="py-5">
    <div class="container">
      <h2 class="section-title">Fale Conosco</h2>
      <form id="contactForm">
        <div class="mb-3">
          <label class="form-label">Nome</label>
          <input type="text" class="form-control" name="nome" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Mensagem</label>
          <textarea class="form-control" name="mensagem" rows="4" required></textarea>
        </div>
        <div id="formStatus" class="mb-3 text-success" style="display:none;">Mensagem enviada com sucesso!</div>
        <button type="submit" class="btn btn-primary">Enviar</button>
      </form>
    </div>
  </section>

  <!-- Rodapé -->
  <footer class="text-center">
    <div class="container">
      <p>&copy; 2025 Academia de Karate. Desenvolvido por Wanessa Motta.</p>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init();
    document.getElementById('contactForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const form = e.target;
      setTimeout(() => {
        form.reset();
        document.getElementById('formStatus').style.display = 'block';
      }, 1000);
    });
  </script>
</body>
</html>

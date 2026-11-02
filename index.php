<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CicloTech</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-ciclotech">
        <div class="container">

            <a class="navbar-brand" href="index.php">
                ♻ CicloTech
            </a>

            <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menuPrincipal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">

                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Início</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#sobre">Sobre</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#servicos">Serviços</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#contato">Contato</a>
                    </li>

                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <a href="admin/login.php" class="btn btn-outline-light btn-sm">
                            Área administrativa
                        </a>
                    </li>

                </ul>

            </div>

        </div>
    </nav>

    <main>


        <!-- Apresentação principal -->
        <section class="container py-5 hero-ciclotech" id="inicio">

            <div class="row">
                <div class="col-lg-10 col-xl-9">

                    <div class="mb-3">
                        <span class="selo-ciclotech">
                            Tecnologia e sustentabilidade
                        </span>
                    </div>

                    <h1 class="display-4 fw-bold">
                        Tecnologia que ganha
                        <span class="text-success">um novo ciclo.</span>
                    </h1>

                    <p class="lead mt-3 hero-descricao">
                        A CicloTech busca promover a economia circular
                        por meio da recuperação, reutilização e destinação
                        adequada de equipamentos eletrônicos.
                    </p>

                    <a href="#sobre" class="btn btn-success btn-lg mt-3">
                        Conheça nosso projeto
                    </a>

                </div>
            </div>

        </section>


        <!-- Pilares da CicloTech -->
        <section class="container py-5" id="sobre">

            <div class="text-center mb-5">

                <h2 class="fw-bold">Um novo destino para a tecnologia</h2>

                <p class="text-secondary">
                    Conheça as principais etapas da nossa proposta.
                </p>

            </div>

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-success">Reutilização</h5>
                            <p class="card-text">
                                Equipamentos em boas condições podem
                                ganhar uma nova vida por meio da reutilização.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-success">Recuperação</h5>
                            <p class="card-text">
                                Equipamentos e componentes aproveitáveis
                                podem passar por manutenção e recuperação.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-success">Reciclagem</h5>
                            <p class="card-text">
                                Materiais sem possibilidade de reutilização
                                devem receber destinação ambientalmente adequada.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </section>

        <!-- Serviços da CicloTech -->
        <section class="container py-5" id="servicos">

            <div class="text-center mb-5">

                <h2 class="fw-bold">Nossos serviços</h2>

                <p class="text-secondary">
                    Soluções para prolongar a vida útil dos equipamentos eletrônicos.
                </p>

            </div>

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-success">Manutenção</h5>
                            <p class="card-text">
                                Serviços de manutenção em computadores e notebooks,
                                incluindo limpeza, substituição de peças e testes.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-success">Recuperação de equipamentos</h5>
                            <p class="card-text">
                                Avaliação e recuperação de equipamentos que
                                apresentam possibilidade de reutilização.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-success">Reaproveitamento de componentes</h5>
                            <p class="card-text">
                                Aproveitamento de peças em boas condições
                                para utilização em outros equipamentos.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

        </section>

    </main>

    <!-- Rodapé -->
    <footer class="navbar-ciclotech text-white mt-5 py-4" id="contato">

        <div class="container text-center">

            <h5>CicloTech</h5>

            <p class="mb-2">
                Tecnologia, sustentabilidade e economia circular.
            </p>

            <p class="mb-0 small">
                Projeto acadêmico - IFSC | Jaraguá do Sul - SC
            </p>

            <p class="mb-0 small">
                &copy; 2026 CicloTech. Todos os direitos reservados.
            </p>

        </div>

    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
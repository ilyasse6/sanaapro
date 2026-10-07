<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Connexion — SanaaPro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --pg-green: #198754;
            --pg-green-dark: #146c43;
            --pg-navy: #17324D;
        }

        html, body {
            margin: 0;
            min-height: 100%;
            font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
        }

        .login-wrap {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
            background:
                radial-gradient(circle at top right, rgba(25, 135, 84, .18), transparent 40%),
                linear-gradient(135deg, #17324D 0%, #1a3d5c 45%, #0f2438 100%);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .25);
        }

        .login-logo {
            width: 72px;
            height: 72px;
            object-fit: contain;
            border-radius: 16px;
            border: 1px solid #e8edf2;
            background: #fff;
            display: block;
            margin-inline: auto;
            padding: 6px;
        }

        .btn-pg {
            background: var(--pg-green);
            border-color: var(--pg-green);
            color: #fff;
        }

        .btn-pg:hover,
        .btn-pg:focus {
            background: var(--pg-green-dark);
            border-color: var(--pg-green-dark);
            color: #fff;
        }

        .alert-pg {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="text-center mb-4">
                <img src="{{ asset('assets/imgs/afifalu-logo.png') }}" alt="AfifAlu" class="login-logo mb-3">
                <h1 class="h4 fw-bold mb-1" style="color:#17324D">SANAAPRO</h1>
                <p class="text-muted small mb-0">Gestion d'entreprise</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success alert-pg py-2 small">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-pg py-2 small">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                        <input id="username" type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required autofocus autocomplete="username" placeholder="Admin">
                    </div>
                    @error('username')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password" placeholder="••••••••">
                    </div>
                    @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                        <label for="remember_me" class="form-check-label small">Se souvenir de moi</label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="small text-decoration-none" style="color:#198754">Mot de passe oublié ?</a>
                    @endif
                </div>

                <button type="submit" class="btn btn-pg w-100 btn-lg" id="loginButton">Se connecter</button>
            </form>

            <p class="text-center text-muted small mt-4 mb-0">&copy; {{ date('Y') }} SanaaPro</p>
        </div>
    </div>
</body>
</html>

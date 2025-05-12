<!DOCTYPE html>
<html lang="fr" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Interface de connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            background: #f4f4f4;
            /* Couleur de fond neutre */
            text-align: center;
        }

        .formulaire-de-connexion {
            position: relative;
            width: 370px;
            background: #ffffff;
            /* Fond blanc */
            padding: 40px 35px 60px;
            border: 1px solid #ddd;
            /* Bordure légère */
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Ombre douce */
        }

        .texte {
            font-size: 30px;
            color: #333;
            /* Texte sombre */
            font-weight: 600;
            letter-spacing: 2px;
        }

        form {
            margin-top: 40px;
        }

        .field {
            display: flex;
            margin-top: 20px;
        }

        .champ .fas {
            width: 60px;
            height: 50px;
            line-height: 50px;
            color: #555;
            /* Icône grise */
            font-size: 20px;
            border: 1px solid #ddd;
            border-right: none;
            border-radius: 5px 0 0 5px;
            background: #f9f9f9;
            /* Fond clair */
            text-align: center;
        }

        .field input,
        form button {
            height: 50px;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 0 5px 5px 0;
            padding: 0 15px;
            font-size: 16px;
            color: #333;
            /* Texte sombre */
            background: #fff;
            /* Fond blanc */
            outline: none;
        }

        .field input:focus {
            border-color: #00b34b;
            /* Couleur de focus */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        form button {
            margin-top: 30px;
            font-weight: 600;
            letter-spacing: 1px;
            border-radius: 5px !important;
            cursor: pointer;
            background: #008000;
            /* Bouton bleu */
            border: none;
            color: #fff;
            /* Texte blanc */
            transition: background 0.3s ease;
        }

        form button:hover {
            background: #00b34b;
            /* Bleu plus foncé au survol */
        }

        .lien {
            margin-top: 25px;
            color: #555;
            /* Texte gris */
        }

        .lien a {
            color: #007bff;
            /* Lien bleu */
            text-decoration: none;
        }

        .lien a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="formulaire-de-connexion">
        <div class="texte">CONNECTEZ-VOUS</div>

        {{-- Affichage des messages d'erreur --}}
        @if ($errors->any())
            <div style="color: red; margin-bottom: 20px;">
                <ul style="list-style: none; padding: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="field">
                <div class="champ">
                    <i class="fas fa-database"></i>
                </div>
                <select id="database" name="database" class="form-control" required>
                    <option value="">Sélectionnez une base de données</option>
                    @foreach ($databases as $database)
                        <option value="{{ $database }}">{{ $database }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <div class="champ">
                    <i class="fas fa-envelope"></i>
                </div>
                <input id="mail" name="mail" type="email" placeholder="E-mail ou téléphone" required>
            </div>
            <div class="field">
                <div class="champ">
                    <i class="fas fa-lock"></i>
                </div>
                <input id="password" name="password" type="password" placeholder="Mot de passe" required>
            </div>
            <button type="submit">CONNEXION</button>
        </form>
    </div>
</body>

</html>

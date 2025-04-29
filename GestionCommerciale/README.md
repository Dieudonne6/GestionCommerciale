# GestionCommerciale

Ce projet est une application de gestion des réceptions et des commandes d'achat. Il permet aux utilisateurs de gérer efficacement les réceptions de commandes, y compris la création, la modification et la suppression de réceptions.

## Structure du projet

- **app/Http/Controllers**
  - `ReceptionCmdAchatController.php`: Gère les opérations liées aux réceptions de commandes d'achat.
  - `CommandeAchatController.php`: Gère les opérations liées aux commandes d'achat.

- **resources/views/layouts**
  - `master.blade.php`: Modèle principal utilisé pour les vues.

- **resources/views/pages/Fournisseur&Achat**
  - `gestion_receptions.blade.php`: Vue pour la gestion des réceptions.
  - `gestion_commandes.blade.php`: Vue pour la gestion des commandes d'achat.
  - `reception.blade.php`: Vue pour créer une nouvelle réception de commande d'achat.
  - `edit_reception.blade.php`: Vue pour modifier une réception existante.
  - `edit_commande.blade.php`: Vue pour modifier une commande d'achat existante.

- **resources/views/pages**
  - `dashboard.blade.php`: Vue pour le tableau de bord de l'application.

- **routes/web.php**: Définit les routes de l'application.

- **public**
  - `css`: Dossier contenant les fichiers CSS.
  - `js`: Dossier contenant les fichiers JavaScript.
  - `images`: Dossier contenant les images.

- **database**
  - `migrations`: Dossier contenant les fichiers de migration pour la base de données.
  - `seeders`: Dossier contenant les fichiers de seed pour peupler la base de données.
  - `factories`: Dossier contenant les fichiers de factory pour générer des données de test.

- **tests**
  - `Feature`: Dossier contenant les tests fonctionnels.
  - `Unit`: Dossier contenant les tests unitaires.

- **package.json**: Fichier de configuration pour npm.
- **composer.json**: Fichier de configuration pour Composer.

## Installation

1. Clonez le dépôt:
   ```
   git clone <url-du-depot>
   ```

2. Installez les dépendances PHP:
   ```
   composer install
   ```

3. Installez les dépendances JavaScript:
   ```
   npm install
   ```

4. Configurez votre fichier `.env` avec les informations de votre base de données.

5. Exécutez les migrations:
   ```
   php artisan migrate
   ```

6. Démarrez le serveur:
   ```
   php artisan serve
   ```

## Utilisation

- Accédez à l'application via `http://localhost:8000`.
- Utilisez le tableau de bord pour naviguer vers les sections de gestion des réceptions et des commandes d'achat.

## Contribuer

Les contributions sont les bienvenues! Veuillez soumettre une demande de tirage pour toute amélioration ou correction.

## License

Ce projet est sous licence MIT.
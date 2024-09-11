# Movie Review

Bon à la base c'est un site prévu pour que les utilisateurs écrive des courtes critiques sur des films, mais avec mes problème de matériel, j'ai pris du retard et je n'ai pas encore eu le temps de terminer la mise en place du système de commentaire.

## Installation

1. Il faut créer un dossier .env.local et n'y mettre que la ligne de définition de la BDD

2. Lancer un ```bash composer install```

3. Executer ```bash php bin/conole d:m:m```

4. Puis charger les fixtures ```bash php bin/conole d:f:l```

5. Démarrer le serveur : ```bash symfony serve --no-tls```

## Le projet est maintenant utilisable.

Il permet de naviguer pour afficher des films par genre, par acteur ou grâce à une barre de recherche.
Pour ça nous avons la page d'accueil, le menu film et les éléments cliquable dans les pages de listes et les pages de film individuelles.

#### Se connecter en cliquant sur l'icone utilisateur en haut à droite de l'écran. (voir les log dans les fixtures)

1. En tant qu'admin on peut éditer les films et les genres. Des boutons apparaîssent au niveau des listes pour permettre l'édition après une recherche par exemple.
Un bouton modifier apparait aussi pour modifier un film depuis sa page.

2. J'ai prévu de faire apparaître un petit formulaire permettant d'écrire un avis sur le film qui n'apparaîtra que quand un utilisateur est connecté. Il faudra aussi mettre en place un "voter" je pense, pour que seul l'auteur du commentaire ait la possibilité de le modifier.
Les avis 'récoltés' apparaitront dans une section en dessous du synopsis, et le formulaire sous les caractéristique.

3. J'aimerais aussi mettre en place un petit espace de profil ou l'utilisateur pourra gérer ses commentaires.

4. Je n'ai pas encore mis en place le CRUD utilisateur non plus, pour qu'un nouvel utilisateur puisse s'inscrire.

## Voilà, voilà, encore pas mal de taf dessus. 

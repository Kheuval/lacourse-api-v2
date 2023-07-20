Stack Docker normalisée
=======================
`Version 1.0.31`

### En environnement dev

**Le projet est-il instancié ?**

Un projet déjà instancié dispose des principales variables du projet.

Pour voir si un projet est instancié :

    cat ./docker/config/project/project.base|grep PROJECT_NAME
    # PROJECT_NAME=monprojet

#### Si le projet est déjà instancié

    cd ./docker/environment/bin
    ENV=dev CONTAINER_REGISTRY_SECRET=xx_ASK_FOR_IT_xx ./init-env.sh

**Puis**
    ./generate-dotproject.sh
    ./generate-dotenv.sh
    ./generate-build.sh
    ./prepare-ssh.sh
    ./generate-dev-certificates.sh
    ./check-mounts.sh
    # should be [ALL OK]
    ./registry-login.sh
    ./registry-pull-images.sh
    ./set-permissions.sh
    cd ../../
    docker-compose up -d --build

    Et voilà!

#### Si c'est une première instanciation

    **Demander à Florent ou Loïc de créer un namespace sur scaleway**
    cd ./docker/environment/bin
    ENV=dev PROJECT_NAME=mon_projet PROJECT_PUBLIC_NAME="Mon bô projet" CONTAINER_REGISTRY_SECRET=xx_ASK_FOR_IT_xx ./init-env.sh

_Note : selon la configuration du projet, d'autres variables secrètes peuvent être demandées_

**Puis**
    ./generate-dotproject.sh
    ./generate-dotenv.sh
    ./generate-build.sh
    ./prepare-ssh.sh
    ./generate-dev-certificates.sh
    ./check-mounts.sh
    # should be [ALL OK]
    ./build-images.sh
    ./registry-login.sh
    ./registry-push-images.sh
    ./set-permissions.sh
    cd ../../
    docker-compose up -d --build

    Ajouter les fichiers .gitignore au dépôt

    Et voilà!

### En environnemnent remote (aka preprod, staging, prod...)

En environnement remote, d'autres actions sont nécessaire

* Ajouter un utilisateur du nom du projet `monprojet` et créer son /home/monprojet
* Instancier le repo git dans `/home/monprojet/projects/monprojet`

Puis lancer les commandes d'initialisation (cf

    cd /home/monprojet/projects/monprojet/
    cd ./docker/environment/bin
    ENV=[staging|preprod|prod|whatever] PROJECT_NAME=monprojet PROJECT_PUBLIC_NAME="Mon bô projet" CONTAINER_REGISTRY_SECRET=xxxx_DEMANDE_TA_CLE_xxxxxxxx ./init-env.sh
    ./generate-dotproject.sh
    ./generate-dotenv.sh
    ./generate-build.sh
    ./prepare-ssh.sh
    ./generate-dummy-certificates.sh
    ./check-mounts.sh
    # should be [ALL OK]
    ./registry-login.sh
    ./registry-pull-images.sh
    ./set-permissions.sh
    cd ../../
    docker-compose up -d --build
    # All should be up
    cd ./docker/environment/bin
    ./generate-acme-certificates.sh
    cd ../../
    docker-compose exec nginx nginx -s reload
    # should be ok

_Note : la commande generate-dummy-certificates.sh génère un certificat ssl autosigné nécessaire au premier démarrage de nginx, généralement remplacé par des certificats acme_

### Reconstruire les images

#### Reconstruire une seule image

`_IMAGE=ssh ./build-images.sh` reconstruit uniquement l'image `ssh` pour tous les environnements  
`_ENV=dev _IMAGE=ssh ./build-images.sh` reconstruit uniquement l'image `ssh` pour l'environnement `dev`

@todo, à reprendre !

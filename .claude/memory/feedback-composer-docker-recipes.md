---
name: feedback-composer-docker-recipes
description: "composer install/require : refuser toute modification du docker-compose.yml par les recettes Flex, rétablir le fichier si modifié"
metadata: 
  node_type: memory
  type: feedback
---

Lors d'un `composer install`, `composer require` ou `composer update`, les recettes Symfony Flex peuvent modifier `docker-compose.yml` (ou créer `compose.yaml` / `compose.override.yaml`). **Toujours refuser ces changements.** Dans le doute, demander validation à l'utilisateur avant d'agir.

**Why:** La configuration Docker du projet est maîtrisée à la main (services `nginx`, `db`, `maildev`, `phpmyadmin`, conteneur `claudeai`, voir [[docker-information]]) ; une recette qui l'altère casse l'environnement de dev.

**How to apply:**
- **Avant** la commande : noter l'état du fichier (`git status --short docker-compose.yml`). S'il a déjà des modifications non commitées, en faire une copie (`cp docker-compose.yml <scratchpad>/docker-compose.yml.bak`) : `git checkout` les effacerait.
- **Pendant** : à la question Flex « Do you want to include Docker configuration from recipes? », répondre `n` (non). En mode non interactif, la réponse par défaut peut être oui : la vérification d'après est obligatoire.
- **Après** : `git status --short docker-compose.yml compose*.yaml`.
  - Fichier modifié → le rétablir à son état d'avant la commande (`git checkout -- docker-compose.yml`, ou la copie si le fichier avait déjà des modifications).
  - Fichiers `compose.yaml` / `compose.override.yaml` créés par la recette → les supprimer.
  - Signaler dans le rapport ce qui a été rétabli.
- **Doute** (fichier modifié pour une autre raison, changement qui semble voulu, impossible de savoir l'état d'origine) → s'arrêter et demander validation à l'utilisateur avant de rétablir ou supprimer quoi que ce soit.
- Sous-agents (`backend-dev`…) : appliquer la même règle ; en cas de doute, s'arrêter et remonter la question au lead-dev.

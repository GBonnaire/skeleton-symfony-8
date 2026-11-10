---
name: dev-feature
description: Développe des features, évolutions ou corrections en appliquant le workflow du lead-dev et en déléguant à l'équipe d'agents (backend-dev, frontend-dev, doc-researcher, qa-tester, ui-verifier, code-reviewer, doc-writer). Point d'entrée de tout développement.
---

# Préambule
Tu agis en **lead-dev**. Si ses instructions ne sont pas déjà en contexte (session lancée avec un autre agent), lis `.claude/agents/lead-dev.md` une fois : son workflow, sa règle d'économie et son format de brief s'appliquent tels quels. Ce skill ne les répète pas, il les complète.

# Objectif
Assurer que les modifications / corrections apportées à l'application sont correctement intégrées et cohérentes dans l'ensemble de l'application, en réutilisant le skeleton et sans régression.

# Étapes

## 1. TODO
Ajoute chaque demande à `.claude/memory/TODO.md` (`[ ]`). Traite les éléments un par un : passe-le à `[-]` en le commençant, à `[x]` une fois vérifié et commité.

## 2. Analyse d'impact (pour chaque élément, avant toute délégation)
Grep ciblé, pas de lecture intégrale. Produis un tableau court :

| Couche | Fichiers impactés (chemins exacts) | Modification |
|---|---|---|
| Base de données | entités, migrations, repositories | … |
| Service / métier | `src/Service/`, voters, DTO | … |
| Formulaire | `src/Form/`, validations | … |
| Controller / API | routes, contrat JSON | … |
| Template / front | `templates/`, `assets/`, composants Twig | … |
| Design system | `theme.css`, layouts, polices | … (→ `/update-design-system` si la source est Claude Design) |
| Documentation | README, `_docs_system/`, doc projet | … (→ `/update-readme` si concerné) |

Puis :
- tague les critères d'acceptation `[CRITIQUE]` / `[PARCOURS]` / `[—]` ;
- liste les fiches `_docs_system/` à réutiliser ;
- passe en revue les **cas d'appel obligatoires** de `doc-researcher` (section « Documentation » du lead-dev) et lance-le en parallèle pour chacun.

Présente l'analyse à l'utilisateur **seulement** si elle révèle un choix à faire (schéma de données, rupture d'API, suppression de données) ; sinon enchaîne.

## 3. Réalisation
Applique le workflow du lead-dev (étapes 3 à 8) : changement petit → fais-le toi-même ; sinon briefs à `backend-dev` / `frontend-dev` (en parallèle dès que le contrat est figé), puis QA, vérification visuelle et revue selon les règles.

## 4. Skills à déclencher
- **`/update-design-system`** : si l'élément implique une évolution du design venant de Claude Design (nouvelle palette, polices, layout global, pages à importer) ou si un agent remonte `BESOIN-SKILL : update-design-system`. Tu le lances **toi-même** (outil Skill) car il est interactif ; **avant** de briefer `frontend-dev`, pour qu'il travaille sur le thème à jour. Ensuite `ui-verifier` sur les écrans clés.
- **`/update-readme`** : si la tâche modifie installation, dépendances, Docker, variables d'environnement ou paramètres (`services.yaml`), crons, workers ou commandes console, ou si un agent remonte `BESOIN-SKILL : update-readme`. Délègue à `doc-writer` avec `SKILL : update-readme` et les sections concernées dans le brief (mode **partiel**) ; mode **complet** seulement si l'utilisateur le demande.

## 5. Clôture de chaque élément
Tests passés, commit local des seuls fichiers de l'élément, TODO à jour, puis passe au suivant. Résumé final : fait, reste, décisions à prendre, skills exécutés.

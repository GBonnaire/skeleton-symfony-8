# Architecture API

## Conventions générales

- **Tous les endpoints sont en anglais** (noms de ressources, paramètres, slugs)
- Séparateur de mots : tiret `-` (kebab-case) pour les segments d'URL
- **Jamais d'`id` auto-incrémenté dans une URL ni dans le JSON** : identifier une ressource par son **UUID v7** (par défaut) ou son **slug** (ressource publique lisible). Règles et exemples : `guide-symfony.md` § Routes & URLs. Seule exception : les URLs générées par EasyAdmin (back-office) gardent l'`id` ; elle ne s'applique jamais à l'API
- De base la version de l'API est V1 et pourra avoir plusieurs version en fonction des évolutions majeures

---

## Structure des URLs

Suivre le modèle REST imbriqué : `/v1/<collection>[/<uuid|slug>[/<collection>[/<uuid|slug>]...]]`

```
GET  /v1/products
GET  /v1/products/blue-cotton-shirt
GET  /v1/products/blue-cotton-shirt/variants
GET  /v1/products/blue-cotton-shirt/variants/01933b6e-7c1a-7d2f-9a4b-3f8e2c1d5a60
GET  /v1/users/01933b6e-5f20-7a11-8c3d-9e4f1a2b3c4d/alerts
GET  /v1/users/01933b6e-5f20-7a11-8c3d-9e4f1a2b3c4d/alerts/01933b6f-0a12-7b34-8c56-7d8e9f0a1b2c/matches
```

Règles :
- Un segment de collection est toujours un **pluriel** en anglais (`products`, `alerts`, `variants`)
- L'identifiant (`uuid` ou `slug`) suit immédiatement la collection à laquelle il appartient ; jamais l'`id` interne
- Ne pas dépasser 5 niveaux d'imbrication ; aplatir si nécessaire

---

## Format de réponse JSON

Toutes les réponses API sont du JSON avec la structure suivante :

```json
{
  "status": "success | error",
  "message": "ok | <message d'erreur>",
  "data": {}
}
```

| Propriété | Type | Présence | Description |
|---|---|---|---|
| `status` | `"success"` \| `"error"` | Toujours | Résultat global de la requête |
| `message` | `string` | Toujours | `"ok"` si succès ; message explicite si erreur |
| `data` | `object` \| `array` | GET uniquement | Données retournées |

### Exemples

**GET réussi :**
```json
{
  "status": "success",
  "message": "ok",
  "data": {
    "uuid": "01933b6e-7c1a-7d2f-9a4b-3f8e2c1d5a60",
    "name": "Dr. Martin",
    "specialty": "cardiologist"
  }
}
```

**GET liste :**
```json
{
  "status": "success",
  "message": "ok",
  "data": [
    { "uuid": "01933b6e-7c1a-7d2f-9a4b-3f8e2c1d5a60", "name": "Dr. Martin" },
    { "uuid": "01933b6e-8d2b-7e3f-8b5c-4a9f3d2e6b71", "name": "Dr. Dupont" }
  ]
}
```

**POST / PUT / DELETE réussi :**
```json
{
  "status": "success",
  "message": "ok", 
  "data": {}
}
```

**Erreur :**
```json
{
  "status": "error",
  "message": "Practitioner not found.",
  "data": {}
}
```

---

## Codes HTTP

| Code | Usage |
|---|---|
| `200` | Succès GET, PUT, PATCH |
| `201` | Succès POST (ressource créée) |
| `204` | Succès DELETE (pas de corps) |
| `400` | Requête invalide (validation échouée) |
| `401` | Non authentifié |
| `403` | Authentifié mais non autorisé |
| `404` | Ressource introuvable |
| `422` | Données sémantiquement incorrectes |
| `500` | Erreur serveur |

---

## Routes Symfony

- Préfixer toutes les routes API avec `/api`
- Utiliser les attributs PHP `#[Route]` avec `methods:` explicites

- Paramètre contraint (`Requirement::UUID_V7` / `Requirement::ASCII_SLUG`) et lié à l'entité par `{param:argument}`

```php
#[Route('/api/v1/products/{slug:product}/variants', name: 'api_product_variants', requirements: ['slug' => Requirement::ASCII_SLUG], methods: ['GET'])]
```

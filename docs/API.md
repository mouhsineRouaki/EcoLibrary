# Documentation API EcoLibrary

## Base URL

Toutes les routes de cette API sont prefixees par :

`/api`

Exemple local :

`http://127.0.0.1:8000/api`

## Authentification

L'API utilise Laravel Sanctum avec un token Bearer.

Flux standard :

1. Creer un compte avec `POST /api/register`
2. Se connecter avec `POST /api/login`
3. Recuperer le champ `token`
4. Envoyer ce header sur les routes protegees :

```http
Authorization: Bearer VOTRE_TOKEN
Accept: application/json
```

## Roles

- `reader` : peut consulter les livres, chercher, filtrer par categorie et voir les nouveautes/populaires.
- `admin` : peut acceder a tous les endpoints reader + gerer livres/categories + consulter les statistiques.

Si un utilisateur non admin appelle une route admin, l'API retourne generalement :

```json
{
  "message": "Failed: admin access only"
}
```

avec le statut HTTP `403`.

## Format de reponse

L'API retourne des reponses JSON.

Exemples courants :

Succes :

```json
{
  "message": "Book created successfully",
  "book": {
    "id": 1,
    "title": "Clean Architecture"
  }
}
```

Erreur :

```json
{
  "message": "Failed: book not found"
}
```

## Endpoints publics

### Inscription

`POST /api/register`

Body JSON :

```json
{
  "name": "John Reader",
  "email": "john@example.com",
  "password": "password123"
}
```

Reponse :

- `201 Created`

```json
{
  "message": "User created successfully",
  "user": {
    "id": 1,
    "name": "John Reader",
    "email": "john@example.com",
    "role": "reader"
  }
}
```

### Connexion

`POST /api/login`

Body JSON :

```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

Reponse :

- `200 OK`

```json
{
  "message": "Logged in successfully",
  "user": {
    "id": 1,
    "name": "John Reader",
    "email": "john@example.com",
    "role": "reader"
  },
  "token": "1|sanctum_token_exemple"
}
```

## Endpoints authentifies

Ces routes exigent `auth:sanctum`.

### Profil utilisateur courant

`GET /api/me`

Reponse :

```json
{
  "user": {
    "id": 1,
    "name": "John Reader",
    "email": "john@example.com",
    "role": "reader"
  }
}
```

### Deconnexion

`POST /api/logout`

Reponse :

```json
{
  "message": "Logged out successfully"
}
```

### Lister tous les livres

`GET /api/books`

Reponse :

```json
{
  "books": [
    {
      "id": 1,
      "category_id": 2,
      "title": "Laravel Clean Code",
      "slug": "laravel-clean-code",
      "description": "Guide pratique Laravel",
      "author": "Author Name",
      "total_quantity": 5,
      "available_quantity": 3,
      "is_active": true
    }
  ]
}
```

Si aucun livre n'existe :

- `404 Not Found`

```json
{
  "message": "Failed: no books found"
}
```

### Rechercher des livres

`GET /api/books/search`

Parametres query supportes :

- `title` : filtre par titre
- `category` : filtre par nom de categorie ou par identifiant de categorie

Exemples :

- `GET /api/books/search?title=clean`
- `GET /api/books/search?category=science`
- `GET /api/books/search?title=laravel&category=1`

Reponse :

```json
{
  "books": [
    {
      "id": 1,
      "title": "Laravel Clean Code",
      "category_id": 1
    }
  ]
}
```

Si aucune correspondance n'existe :

```json
{
  "message": "Failed: no matching books found"
}
```

### Detail d'un livre

`GET /api/books/{id}`

Exemple :

`GET /api/books/1`

Cette route enregistre aussi une vue dans `book_views`.

Reponse :

```json
{
  "book": {
    "id": 1,
    "title": "Laravel Clean Code",
    "category": {
      "id": 1,
      "name": "Science"
    }
  }
}
```

Si le livre n'existe pas :

```json
{
  "message": "Failed: book not found"
}
```

### Livres disponibles par categorie

`GET /api/categories/{id}/books`

Retourne seulement les livres :

- actifs
- avec `available_quantity > 0`

### Livres populaires par categorie

`GET /api/categories/{id}/books/popular`

Retourne jusqu'a 10 livres tries par nombre de vues.

### Nouveaux livres par categorie

`GET /api/categories/{id}/books/new`

Retourne jusqu'a 10 livres tries par date de creation descendante.

## Endpoints admin

Ces routes exigent :

- `auth:sanctum`
- middleware `admin`

Le prefixe est :

`/api/admin`

### Categories

#### Lister les categories

`GET /api/admin/categories`

#### Afficher une categorie

`GET /api/admin/categories/{id}`

#### Creer une categorie

`POST /api/admin/categories`

Body JSON :

```json
{
  "name": "Science",
  "slug": "science",
  "description": "Science books"
}
```

#### Modifier une categorie

`PUT /api/admin/categories/{id}`

Body JSON :

```json
{
  "name": "Science Updated",
  "slug": "science-updated",
  "description": "Updated description"
}
```

#### Supprimer une categorie

`DELETE /api/admin/categories/{id}`

### Livres

#### Lister les livres

`GET /api/admin/books`

#### Creer un livre

`POST /api/admin/books`

Body JSON :

```json
{
  "category_id": 1,
  "title": "Clean Architecture",
  "slug": "clean-architecture",
  "description": "Software design principles",
  "author": "Robert C. Martin",
  "total_quantity": 12,
  "available_quantity": 9,
  "is_active": true
}
```

#### Modifier un livre

`PUT /api/admin/books/{id}`

Body JSON :

```json
{
  "category_id": 1,
  "title": "Clean Architecture 2nd Edition",
  "slug": "clean-architecture-2",
  "description": "Updated edition",
  "author": "Robert C. Martin",
  "total_quantity": 15,
  "available_quantity": 10,
  "is_active": true
}
```

#### Supprimer un livre

`DELETE /api/admin/books/{id}`

### Statistiques

#### Statistiques globales de la collection

`GET /api/admin/stats/collection`

Reponse :

```json
{
  "collection": {
    "total_books": 20,
    "total_quantity": 150,
    "total_available": 110,
    "total_degraded": 40,
    "active_books": 18,
    "inactive_books": 2
  },
  "top_viewed_books": [
    {
      "id": 1,
      "title": "Clean Architecture",
      "views_count": 17
    }
  ]
}
```

#### Livres degrades

`GET /api/admin/stats/degraded-books`

Reponse :

```json
{
  "books": [
    {
      "id": 1,
      "title": "Clean Architecture",
      "degraded_quantity": 3
    }
  ],
  "total_degraded_quantity": 3
}
```

## Codes HTTP frequents

- `200 OK` : lecture ou mise a jour reussie
- `201 Created` : creation reussie
- `403 Forbidden` : acces interdit
- `404 Not Found` : ressource absente
- `422 Unprocessable Entity` : erreur de validation

## Comptes de test

Apres :

`php artisan migrate:fresh --seed`

Comptes disponibles :

- Admin : `admin@example.com` / `password`
- Reader : `test@example.com` / `password`

## Outils de documentation recommandes

Si tu veux une documentation plus propre et maintenable que du Markdown manuel, les deux options les plus simples pour Laravel sont :

1. `Scribe` pour generer une documentation HTML + exemples de requetes
2. `Postman` pour partager une collection testable

Dans ce projet, tu as deja une collection ici :

`docs/postman/EcoLibrary.postman_collection.json`

Si tu veux, je peux aussi te mettre en place une vraie documentation auto-generee avec Scribe dans le projet.

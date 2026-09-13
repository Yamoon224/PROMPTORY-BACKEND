<?php

/*
|--------------------------------------------------------------------------
| Messages de validation
|--------------------------------------------------------------------------
|
| Traduction complete du fichier de messages par defaut de Laravel. Sans ce
| fichier, une regle de validation en echec renvoyait sa cle brute
| (« validation.required ») plutot qu'un message lisible : `APP_LOCALE`
| valait `fr` et `APP_FALLBACK_LOCALE` valait egalement `fr`, sans qu'aucun
| fichier `fr` n'existe pour que le traducteur y trouve quoi que ce soit.
| `APP_FALLBACK_LOCALE` est desormais `en` (voir `lang/en`, publie par
| `artisan lang:publish`) : une cle qui manquerait ici retomberait sur
| l'anglais plutot que de s'afficher brute.
|
*/

return [

    'accepted' => 'Le champ :attribute doit etre accepte.',
    'accepted_if' => 'Le champ :attribute doit etre accepte lorsque :other vaut :value.',
    'active_url' => 'Le champ :attribute doit etre une URL valide.',
    'after' => 'Le champ :attribute doit etre une date posterieure au :date.',
    'after_or_equal' => 'Le champ :attribute doit etre une date posterieure ou egale au :date.',
    'alpha' => 'Le champ :attribute ne doit contenir que des lettres.',
    'alpha_dash' => 'Le champ :attribute ne doit contenir que des lettres, chiffres, tirets et underscores.',
    'alpha_num' => 'Le champ :attribute ne doit contenir que des lettres et des chiffres.',
    'any_of' => 'Le champ :attribute est invalide.',
    'array' => 'Le champ :attribute doit etre une liste.',
    'array_keys' => 'Le champ :attribute ne doit contenir que les cles suivantes : :values.',
    'ascii' => 'Le champ :attribute ne doit contenir que des caracteres alphanumeriques et symboles sur un octet.',
    'base64' => 'Le champ :attribute doit etre une chaine Base64 valide.',
    'before' => 'Le champ :attribute doit etre une date anterieure au :date.',
    'before_or_equal' => 'Le champ :attribute doit etre une date anterieure ou egale au :date.',
    'between' => [
        'array' => 'Le champ :attribute doit contenir entre :min et :max elements.',
        'file' => 'Le champ :attribute doit peser entre :min et :max kilo-octets.',
        'numeric' => 'Le champ :attribute doit etre compris entre :min et :max.',
        'string' => 'Le champ :attribute doit contenir entre :min et :max caracteres.',
    ],
    'boolean' => 'Le champ :attribute doit valoir vrai ou faux.',
    'can' => 'Le champ :attribute contient une valeur non autorisee.',
    'confirmed' => 'La confirmation du champ :attribute ne correspond pas.',
    'contains' => 'Le champ :attribute doit contenir une valeur requise.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ :attribute doit etre une date valide.',
    'date_equals' => 'Le champ :attribute doit etre une date egale au :date.',
    'date_format' => 'Le champ :attribute ne correspond pas au format :format.',
    'decimal' => 'Le champ :attribute doit comporter :decimal decimales.',
    'declined' => 'Le champ :attribute doit etre refuse.',
    'declined_if' => 'Le champ :attribute doit etre refuse lorsque :other vaut :value.',
    'different' => 'Les champs :attribute et :other doivent etre differents.',
    'digits' => 'Le champ :attribute doit comporter :digits chiffres.',
    'digits_between' => 'Le champ :attribute doit comporter entre :min et :max chiffres.',
    'dimensions' => 'Le champ :attribute a des dimensions d\'image invalides.',
    'distinct' => 'Le champ :attribute contient une valeur en double.',
    'doesnt_contain' => 'Le champ :attribute ne doit contenir aucune des valeurs suivantes : :values.',
    'doesnt_end_with' => 'Le champ :attribute ne doit pas se terminer par l\'une des valeurs suivantes : :values.',
    'doesnt_start_with' => 'Le champ :attribute ne doit pas commencer par l\'une des valeurs suivantes : :values.',
    'email' => 'Le champ :attribute doit etre une adresse e-mail valide.',
    'encoding' => 'Le champ :attribute doit etre encode en :encoding.',
    'ends_with' => 'Le champ :attribute doit se terminer par l\'une des valeurs suivantes : :values.',
    'enum' => 'La valeur selectionnee pour :attribute est invalide.',
    'exists' => 'La valeur selectionnee pour :attribute est invalide.',
    'extensions' => 'Le champ :attribute doit avoir l\'une des extensions suivantes : :values.',
    'file' => 'Le champ :attribute doit etre un fichier.',
    'filled' => 'Le champ :attribute doit avoir une valeur.',
    'gt' => [
        'array' => 'Le champ :attribute doit contenir plus de :value elements.',
        'file' => 'Le champ :attribute doit peser plus de :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit etre superieur a :value.',
        'string' => 'Le champ :attribute doit contenir plus de :value caracteres.',
    ],
    'gte' => [
        'array' => 'Le champ :attribute doit contenir :value elements ou plus.',
        'file' => 'Le champ :attribute doit peser au moins :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit etre superieur ou egal a :value.',
        'string' => 'Le champ :attribute doit contenir au moins :value caracteres.',
    ],
    'hex_color' => 'Le champ :attribute doit etre une couleur hexadecimale valide.',
    'image' => 'Le champ :attribute doit etre une image.',
    'in' => 'La valeur selectionnee pour :attribute est invalide.',
    'in_array' => 'Le champ :attribute doit exister dans :other.',
    'in_array_keys' => 'Le champ :attribute doit contenir au moins l\'une des cles suivantes : :values.',
    'integer' => 'Le champ :attribute doit etre un nombre entier.',
    'ip' => 'Le champ :attribute doit etre une adresse IP valide.',
    'ipv4' => 'Le champ :attribute doit etre une adresse IPv4 valide.',
    'ipv6' => 'Le champ :attribute doit etre une adresse IPv6 valide.',
    'json' => 'Le champ :attribute doit etre une chaine JSON valide.',
    'list' => 'Le champ :attribute doit etre une liste.',
    'lowercase' => 'Le champ :attribute doit etre en minuscules.',
    'lt' => [
        'array' => 'Le champ :attribute doit contenir moins de :value elements.',
        'file' => 'Le champ :attribute doit peser moins de :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit etre inferieur a :value.',
        'string' => 'Le champ :attribute doit contenir moins de :value caracteres.',
    ],
    'lte' => [
        'array' => 'Le champ :attribute ne doit pas contenir plus de :value elements.',
        'file' => 'Le champ :attribute doit peser au maximum :value kilo-octets.',
        'numeric' => 'Le champ :attribute doit etre inferieur ou egal a :value.',
        'string' => 'Le champ :attribute doit contenir au maximum :value caracteres.',
    ],
    'mac_address' => 'Le champ :attribute doit etre une adresse MAC valide.',
    'max' => [
        'array' => 'Le champ :attribute ne doit pas contenir plus de :max elements.',
        'file' => 'Le champ :attribute ne doit pas depasser :max kilo-octets.',
        'numeric' => 'Le champ :attribute ne doit pas etre superieur a :max.',
        'string' => 'Le champ :attribute ne doit pas depasser :max caracteres.',
    ],
    'max_digits' => 'Le champ :attribute ne doit pas comporter plus de :max chiffres.',
    'mimes' => 'Le champ :attribute doit etre un fichier de type : :values.',
    'mimetypes' => 'Le champ :attribute doit etre un fichier de type : :values.',
    'min' => [
        'array' => 'Le champ :attribute doit contenir au moins :min elements.',
        'file' => 'Le champ :attribute doit peser au moins :min kilo-octets.',
        'numeric' => 'Le champ :attribute doit etre au moins :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caracteres.',
    ],
    'min_digits' => 'Le champ :attribute doit comporter au moins :min chiffres.',
    'missing' => 'Le champ :attribute doit etre absent.',
    'missing_if' => 'Le champ :attribute doit etre absent lorsque :other vaut :value.',
    'missing_unless' => 'Le champ :attribute doit etre absent sauf si :other vaut :value.',
    'missing_with' => 'Le champ :attribute doit etre absent lorsque :values est present.',
    'missing_with_all' => 'Le champ :attribute doit etre absent lorsque :values sont presents.',
    'multiple_of' => 'Le champ :attribute doit etre un multiple de :value.',
    'not_in' => 'La valeur selectionnee pour :attribute est invalide.',
    'not_regex' => 'Le format du champ :attribute est invalide.',
    'numeric' => 'Le champ :attribute doit etre un nombre.',
    'password' => [
        'letters' => 'Le champ :attribute doit contenir au moins une lettre.',
        'mixed' => 'Le champ :attribute doit contenir au moins une majuscule et une minuscule.',
        'numbers' => 'Le champ :attribute doit contenir au moins un chiffre.',
        'symbols' => 'Le champ :attribute doit contenir au moins un symbole.',
        'uncompromised' => 'Le :attribute indique a fuite dans une base de donnees compromise. Choisissez-en un autre.',
    ],
    'present' => 'Le champ :attribute doit etre present.',
    'present_if' => 'Le champ :attribute doit etre present lorsque :other vaut :value.',
    'present_unless' => 'Le champ :attribute doit etre present sauf si :other vaut :value.',
    'present_with' => 'Le champ :attribute doit etre present lorsque :values est present.',
    'present_with_all' => 'Le champ :attribute doit etre present lorsque :values sont presents.',
    'prohibited' => 'Le champ :attribute est interdit.',
    'prohibited_if' => 'Le champ :attribute est interdit lorsque :other vaut :value.',
    'prohibited_if_accepted' => 'Le champ :attribute est interdit lorsque :other est accepte.',
    'prohibited_if_declined' => 'Le champ :attribute est interdit lorsque :other est refuse.',
    'prohibited_unless' => 'Le champ :attribute est interdit sauf si :other figure dans :values.',
    'prohibits' => 'Le champ :attribute interdit la presence de :other.',
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'required_array_keys' => 'Le champ :attribute doit contenir une entree pour : :values.',
    'required_if' => 'Le champ :attribute est obligatoire lorsque :other vaut :value.',
    'required_if_accepted' => 'Le champ :attribute est obligatoire lorsque :other est accepte.',
    'required_if_declined' => 'Le champ :attribute est obligatoire lorsque :other est refuse.',
    'required_unless' => 'Le champ :attribute est obligatoire sauf si :other figure dans :values.',
    'required_with' => 'Le champ :attribute est obligatoire lorsque :values est present.',
    'required_with_all' => 'Le champ :attribute est obligatoire lorsque :values sont presents.',
    'required_without' => 'Le champ :attribute est obligatoire lorsque :values n\'est pas present.',
    'required_without_all' => 'Le champ :attribute est obligatoire lorsqu\'aucun de :values n\'est present.',
    'same' => 'Les champs :attribute et :other doivent correspondre.',
    'size' => [
        'array' => 'Le champ :attribute doit contenir :size elements.',
        'file' => 'Le champ :attribute doit peser :size kilo-octets.',
        'numeric' => 'Le champ :attribute doit etre :size.',
        'string' => 'Le champ :attribute doit contenir :size caracteres.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer par l\'une des valeurs suivantes : :values.',
    'string' => 'Le champ :attribute doit etre une chaine de caracteres.',
    'timezone' => 'Le champ :attribute doit etre un fuseau horaire valide.',
    'unique' => 'Cette valeur pour :attribute est deja utilisee.',
    'uploaded' => 'L\'envoi du fichier :attribute a echoue.',
    'uppercase' => 'Le champ :attribute doit etre en majuscules.',
    'url' => 'Le champ :attribute doit etre une URL valide.',
    'ulid' => 'Le champ :attribute doit etre un ULID valide.',
    'uuid' => 'Le champ :attribute doit etre un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Messages personnalises
    |--------------------------------------------------------------------------
    */

    'custom' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Noms d'attributs, en francais et sans franglais technique
    |--------------------------------------------------------------------------
    */

    'attributes' => [
        'name' => 'nom',
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
        'current_password' => 'mot de passe actuel',
        'title' => 'titre',
        'content' => 'contenu',
        'price' => 'prix',
        'description' => 'description',
        'status' => 'statut',
        'roles' => 'roles',
        'folder_id' => 'dossier',
        'parent_id' => 'parent',
        'category_id' => 'categorie',
        'tags' => 'tags',
        'categories' => 'categories',
        'ia_models' => 'outils IA',
        'prompts' => 'prompts',
        'rating' => 'note',
        'comment' => 'commentaire',
        'reason' => 'motif',
        'type' => 'formule',
        'payment_method' => 'moyen de paiement',
        'payment_token' => 'jeton de paiement',
        'token' => 'jeton',
        'is_active' => 'actif',
    ],

];

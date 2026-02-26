<?php

declare(strict_types=1);

function chargerCollectionDepuisJson(string $fichierJson): array
{
    if (!is_file($fichierJson)) {
        throw new RuntimeException("Fichier JSON introuvable: {$fichierJson}");
    }

    $contenu = file_get_contents($fichierJson);
    if ($contenu === false) {
        throw new RuntimeException('Impossible de lire le fichier JSON Morse.');
    }

    $collection = json_decode($contenu, true);
    if (!is_array($collection)) {
        throw new RuntimeException('Le contenu JSON Morse est invalide.');
    }

    return $collection;
}

function creerMapDecodage(array $collection): array
{
    $map = [];
    foreach ($collection as $caractere => $symbole) {
        if (!is_string($caractere) || !is_string($symbole) || $symbole === '') {
            continue;
        }
        if (!array_key_exists($symbole, $map)) {
            $map[$symbole] = $caractere;
        }
    }

    return $map;
}

function separerCaracteresUnicode(string $texte): array
{
    $chars = preg_split('//u', $texte, -1, PREG_SPLIT_NO_EMPTY);
    return $chars === false ? [] : $chars;
}

function coderTexte(string $texte, array $collection): string
{
    $resultats = [];

    foreach (separerCaracteresUnicode($texte) as $caractere) {
        if ($caractere === ' ') {
            $resultats[] = '/';
            continue;
        }

        $cle = mb_strtoupper($caractere, 'UTF-8');
        $resultats[] = $collection[$cle] ?? '?';
    }

    return implode(' ', $resultats);
}

function decoderMorse(string $morse, array $mapDecodage): string
{
    $symboles = preg_split('/\s+/', trim($morse));
    if ($symboles === false) {
        return '';
    }

    $resultat = '';
    foreach ($symboles as $symbole) {
        if ($symbole === '/' || $symbole === '|') {
            $resultat .= ' ';
            continue;
        }

        $resultat .= $mapDecodage[$symbole] ?? '?';
    }

    return $resultat;
}

header('Content-Type: text/plain; charset=utf-8');

try {
    $collection = chargerCollectionDepuisJson(__DIR__ . '/morse_collection.json');
    $mapDecodage = creerMapDecodage($collection);
} catch (RuntimeException $exception) {
    http_response_code(500);
    echo $exception->getMessage();
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    echo "Utilisez une requête POST avec 'texte' ou 'morse'.";
    exit;
}

$texte = trim((string)($_POST['texte'] ?? ''));
$morse = trim((string)($_POST['morse'] ?? ''));

if ($texte !== '') {
    echo coderTexte($texte, $collection);
    exit;
}

if ($morse !== '') {
    echo decoderMorse($morse, $mapDecodage);
    exit;
}

echo 'Veuillez saisir un texte ou un code morse.';

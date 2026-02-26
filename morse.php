<?php

define('NB', 37);

class TMorse
{
    public string $texte;
    public string $symbole;
}

function creerCollection(): array
{
    $entries = [
        ['A', '.-'],
        ['B', '-...'],
        ['C', '-.-.'],
        ['D', '-..'],
        ['E', '.'],
        ['F', '..-.'],
        ['G', '--.'],
        ['H', '....'],
        ['I', '..'],
        ['J', '.---'],
        ['K', '-.-'],
        ['L', '.-..'],
        ['M', '--'],
        ['N', '-.'],
        ['O', '---'],
        ['P', '.--.'],
        ['Q', '--.-'],
        ['R', '.-.'],
        ['S', '...'],
        ['T', '-'],
        ['U', '..-'],
        ['V', '...-'],
        ['W', '.--'],
        ['X', '-..-'],
        ['Y', '-.--'],
        ['Z', '--..'],
        ['1', '.----'],
        ['2', '..---'],
        ['3', '...--'],
        ['4', '....-'],
        ['5', '.....'],
        ['6', '-....'],
        ['7', '--...'],
        ['8', '---..'],
        ['9', '----.'],
        ['0', '-----'],
        ['.', '.-.-.-'],
    ];

    $collection = [];
    foreach ($entries as [$texte, $symbole]) {
        $item = new TMorse();
        $item->texte = $texte;
        $item->symbole = $symbole;
        $collection[] = $item;
    }

    return $collection;
}

function coderCaractere(array $collection, string $caractere): string
{
    foreach ($collection as $item) {
        if (strtoupper($caractere) === strtoupper($item->texte)) {
            return $item->symbole;
        }
    }

    return '?';
}

function decoderSymbole(array $collection, string $symbole): string
{
    foreach ($collection as $item) {
        if (strtoupper($symbole) === strtoupper($item->symbole)) {
            return $item->texte;
        }
    }

    return '?';
}

function coderTexte(string $texte): string
{
    $collection = creerCollection();
    $resultats = [];

    for ($i = 0; $i < strlen($texte); $i++) {
        $resultats[] = coderCaractere($collection, $texte[$i]);
    }

    return implode(' ', $resultats);
}

function decoderMorse(string $morse): string
{
    $collection = creerCollection();
    $symboles = preg_split('/\s+/', trim($morse));
    $resultat = '';

    foreach ($symboles as $symbole) {
        if ($symbole === '') {
            continue;
        }
        $resultat .= decoderSymbole($collection, $symbole);
    }

    return $resultat;
}

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Utilisez une requête POST avec 'texte' ou 'morse'.";
    exit;
}

$texte = trim($_POST['texte'] ?? '');
$morse = trim($_POST['morse'] ?? '');

if ($texte !== '') {
    echo coderTexte($texte);
    exit;
}

if ($morse !== '') {
    echo decoderMorse($morse);
    exit;
}

echo "Veuillez saisir un texte ou un code morse.";

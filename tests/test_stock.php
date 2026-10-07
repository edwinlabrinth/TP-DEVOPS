<?php

$stock = new Stock();
$stock->ajouter(new Produit('P1', 'Stylo', 2.0, 10));
$stock->ajouter(new Produit('P2', 'Cahier', 5.0, 0));
$stock->ajouter(new Produit('P3', 'Règle', 3.0, 4));

verifier($stock->compter() === 3, 'compter() retourne 3');
verifier(count($stock->tous()) === 3, 'tous() retourne 3 produits');

verifier($stock->trouver('P1') !== null, 'trouver() retrouve P1');
verifier($stock->trouver('XXX') === null, 'trouver() retourne null si inconnu');

$doublon = false;
try {
    $stock->ajouter(new Produit('P1', 'Autre', 1.0, 1));
} catch (Exception $e) {
    $doublon = true;
}
verifier($doublon, 'ajouter() lève une exception si la référence existe');

// 2*10 + 5*0 + 3*4 = 32
verifier($stock->valeurTotale() === 32.0, 'valeurTotale() = 32');

$rupture = $stock->produitsEnRupture();
verifier(count($rupture) === 1 && $rupture[0]->getReference() === 'P2', 'produitsEnRupture() = P2');

// quantité strictement < 4 : seulement P2 (0), pas P3 (4)
verifier(count($stock->produitsSousSeuil(4)) === 1, 'produitsSousSeuil(4) est strict');
verifier(count($stock->produitsSousSeuil(5)) === 2, 'produitsSousSeuil(5) = 2 produits');
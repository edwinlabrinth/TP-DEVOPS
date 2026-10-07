<?php

// 1. Tests de base fournis dans l'exemple du TP
$p = new Produit('P001', 'Clavier', 150, 10);
verifier($p->getQuantite() === 10, 'La quantité initiale est 10');
$p->retirerQuantite(3);
verifier($p->getQuantite() === 7, 'Après retrait de 3, il en reste 7');
verifier(abs($p->valeurStock() - 1050) < 0.001, 'La valeur du stock vaut 7 x 150');

// 2. Tests des accesseurs (Getters)
verifier($p->getReference() === 'P001', 'getReference() retourne la bonne référence');
verifier($p->getNom() === 'Clavier', 'getNom() retourne le bon nom');
verifier($p->getPrix() === 150.0, 'getPrix() retourne le bon prix');

// 3. Test de l'ajout de quantité
$p->ajouterQuantite(5);
verifier($p->getQuantite() === 12, 'Après ajout de 5, la quantité passe à 12');

// 4. Test des exceptions demandées dans le contrat d'interface
$testExceptionConstructeur = false;
try {
    // Tentative de création avec un prix négatif
    new Produit('P002', 'Souris', -25.5, 10);
} catch (InvalidArgumentException $e) {
    $testExceptionConstructeur = true;
}
verifier($testExceptionConstructeur, 'Exception levée : le prix ne peut pas être négatif à la création');

$testExceptionStock = false;
try {
    // Tentative de retirer plus que la quantité disponible (12)
    $p->retirerQuantite(20);
} catch (Exception $e) {
    $testExceptionStock = true;
}
verifier($testExceptionStock, 'Exception levée : impossible de retirer une quantité supérieure au stock');

$testExceptionAjoutNegatif = false;
try {
    // Tentative d'ajouter une quantité négative
    $p->ajouterQuantite(-5);
} catch (Exception $e) {
    $testExceptionAjoutNegatif = true;
}
verifier($testExceptionAjoutNegatif, 'Exception levée : la quantité à ajouter doit être positive');
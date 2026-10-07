<?php

class Commande
{
    private int $numero;
    /** @var array<string, array{produit: Produit, quantite: int}> lignes indexees par reference produit */
    private array $lignes = [];
    private bool $validee = false;

    public function __construct(int $numero)
    {
        $this->numero = $numero;
    }

    public function getNumero(): int
    {
        return $this->numero;
    }

    public function ajouterLigne(Produit $p, int $quantite): void
    {
        if ($this->validee) {
            throw new LogicException("La commande n°{$this->numero} est deja validee.");
        }
        if ($quantite <= 0) {
            throw new InvalidArgumentException("La quantite doit etre positive.");
        }

        // Si le produit est deja dans la commande, on cumule les quantites
        $reference = $p->getReference();
        $dejaCommande = isset($this->lignes[$reference]) ? $this->lignes[$reference]['quantite'] : 0;

        if ($dejaCommande + $quantite > $p->getQuantite()) {
            throw new InvalidArgumentException(
                "Stock insuffisant pour {$p->getNom()} : {$p->getQuantite()} disponible(s)."
            );
        }

        $this->lignes[$reference] = ['produit' => $p, 'quantite' => $dejaCommande + $quantite];
    }

    public function total(): float
    {
        $total = 0.0;
        foreach ($this->lignes as $ligne) {
            $total += $ligne['produit']->getPrix() * $ligne['quantite'];
        }
        return $total;
    }

    public function valider(): void
    {
        if ($this->validee) {
            throw new LogicException("La commande n°{$this->numero} est deja validee.");
        }
        if (empty($this->lignes)) {
            throw new LogicException("Impossible de valider une commande vide.");
        }

        // On verifie tout le stock avant de retirer quoi que ce soit (pas de retrait partiel)
        foreach ($this->lignes as $ligne) {
            if ($ligne['quantite'] > $ligne['produit']->getQuantite()) {
                throw new LogicException("Stock insuffisant pour {$ligne['produit']->getNom()}.");
            }
        }
        foreach ($this->lignes as $ligne) {
            $ligne['produit']->retirerQuantite($ligne['quantite']);
        }

        $this->validee = true;
    }

    public function estValidee(): bool
    {
        return $this->validee;
    }

    public function afficher(): string
    {
        $separateur = str_repeat('-', 56) . "\n";
        $texte = "FACTURE - Commande n°{$this->numero}\n" . $separateur;
        $texte .= sprintf("%-8s %-20s %5s %9s %10s\n", 'Ref', 'Produit', 'Qte', 'Prix', 'Sous-total');
        $texte .= $separateur;

        foreach ($this->lignes as $ligne) {
            $p = $ligne['produit'];
            $texte .= sprintf(
                "%-8s %-20s %5d %9.2f %10.2f\n",
                $p->getReference(),
                $p->getNom(),
                $ligne['quantite'],
                $p->getPrix(),
                $p->getPrix() * $ligne['quantite']
            );
        }

        $texte .= $separateur;
        $texte .= sprintf("%-45s %10.2f\n", 'TOTAL', $this->total());
        $texte .= 'Statut : ' . ($this->validee ? 'validee' : 'en attente') . "\n";
        return $texte;
    }
}

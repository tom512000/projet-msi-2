<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmpruntController extends AbstractController
{
    #[Route('/emprunt', name: 'app_emprunt')]
    public function index(): Response
    {
        return $this->render('emprunt/index.html.twig', [
            'controller_name' => 'EmpruntController',
        ]);
    }

    #[Route('/emprunt/calcul', name: 'app_emprunt_calcul', methods: 'POST')]
    public function calcul(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $valeurDep = $request->request->get('valeurDep'); // V0
            $tauxInteretAnnuel = $request->request->get('tauxInteret') / 100; // i (converti en pourcentage)
            $dureeEnAnnees = $request->request->get('duree'); // n
            $choixDuree = $request->request->get('Choix');

            // Ajustement de la période et du taux d'intérêt
            $tauxInteretPeriode = $tauxInteretAnnuel;
            $nombreDePeriodes = $dureeEnAnnees;

            switch ($choixDuree) {
                case 'Mois':
                    $nombreDePeriodes *= 12;
                    $tauxInteretPeriode = pow(1 + $tauxInteretAnnuel, 1 / 12) - 1;
                    break;
                case 'Trimestre':
                    $nombreDePeriodes *= 4;
                    $tauxInteretPeriode = pow(1 + $tauxInteretAnnuel, 1 / 4) - 1;
                    break;
                case 'Semestre':
                    $nombreDePeriodes *= 2;
                    $tauxInteretPeriode = pow(1 + $tauxInteretAnnuel, 1 / 2) - 1;
                    break;
            }
            $annuite = $valeurDep * $tauxInteretPeriode / (1 - pow((1 + $tauxInteretPeriode), -$nombreDePeriodes));

            $tableauEmprunt = [];
            $capitalRestant = $valeurDep; // Initialiser le capital restant

            for ($periode = 1; $periode <= $nombreDePeriodes; $periode++) {
                $interets = $capitalRestant * $tauxInteretPeriode; // Intérêts pour la période
                $capitalAmorti = $annuite - $interets; // Capital amorti pendant la période
                $capitalRestant -= $capitalAmorti; // Capital restant dû en fin de période

                $tableauEmprunt[] = [
                    'periode' => $periode,
                    'capitalRestantDu' => $capitalRestant + $capitalAmorti,
                    'interets' => $interets,
                    'capitalRembourse' => $capitalAmorti,
                    'annuite' => $annuite,
                    'capitalFin' => max(0, $capitalRestant) // Pour éviter un nombre négatif
                ];
            }

            $dernierePeriode = end($tableauEmprunt);
            if ($dernierePeriode['capitalFin'] < 0) {
                $tableauEmprunt[key($tableauEmprunt)]['capitalRembourse'] += $dernierePeriode['capitalFin'];
                $tableauEmprunt[key($tableauEmprunt)]['capitalFin'] = 0;
            }
        }

        $session = $request->getSession();
        $session->set('tableauEmprunt', $tableauEmprunt);

        return $this->redirectToRoute('app_emprunt_resultats');
    }
    #[Route('/emprunt/resultats', name: 'app_emprunt_resultats')]
    public function resultats(Request $request): Response
    {
        $session = $request->getSession();
        $tableauEmprunt = $session->get('tableauEmprunt', []);

        return $this->render('emprunt/resultats.html.twig', [
            'tableauEmprunt' => $tableauEmprunt,
        ]);
    }
}

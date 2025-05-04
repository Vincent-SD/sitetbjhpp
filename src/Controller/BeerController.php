<?php

namespace App\Controller;

use App\Entity\Beer;
use App\Entity\Rank;
use App\Form\BeerType;
use App\Form\SearchType;
use App\Repository\BeerRepository;
use App\Repository\RankRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/beer")
 */
class BeerController extends AbstractController
{

    /**
     * @Route("/", name="beer_index", methods={"GET"})
     */
    public function index(Request $request, BeerRepository $beerRepository, RankRepository $rankRepository): Response
    {
        // Formulaire de recherche (inutile de handleRequest si on lit via query)
        $searchForm = $this->createForm(SearchType::class);

        $ranks = $rankRepository->findAll();
        $rankBeers = [];

        $query = $request->query->get('query');

        foreach ($ranks as $rank) {
            // Filtrage des bières par nom ou description
            if (!empty($query)) {
                $filteredBeers = $rank->getBeers()->filter(function (Beer $b) use ($query) {
                    return stripos($b->getName(), $query) !== false ||
                        stripos($b->getDescription(), $query) !== false;
                })->toArray();

                $filteredBeers = array_values($filteredBeers); // réindexation
            } else {
                $filteredBeers = $rank->getBeers()->toArray();
            }

            // Mise en gras du mot recherché (à l'arrache dans le contrôleur)
            foreach ($filteredBeers as &$beer) {
                $highlighted = '<strong>' . htmlspecialchars($query) . '</strong>';

                $beerName = $beer->getName();
                $beerDesc = $beer->getDescription();

                $beer->highlightedName = $query
                    ? str_ireplace($query, $highlighted, htmlspecialchars($beerName))
                    : htmlspecialchars($beerName);

                $beer->highlightedDescription = $query
                    ? str_ireplace($query, $highlighted, htmlspecialchars($beerDesc))
                    : htmlspecialchars($beerDesc);
            }

            $rankBeers[$rank->getName()] = $filteredBeers;
        }

        return $this->render('beer/index.html.twig', [
            'rankBeers' => $rankBeers,
            'ranks' => $ranks,
            'nbBeers' => $this->getNumberOfBeers($beerRepository),
            'searchForm' => $searchForm->createView(),
            'query' => $query,
        ]);
    }

    /**
     * @Route("/new", name="beer_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $beer = new Beer();
        $form = $this->createForm(BeerType::class, $beer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($beer);
            $entityManager->flush();

            return $this->redirectToRoute('beer_index');
        }

        return $this->render('beer/new.html.twig', [
            'beer' => $beer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/export", name="beer_export", methods={"GET"})
     */
    public function exportToCsv(RankRepository $rankRepository): Response
    {

        $ranks = $rankRepository->findAll();
        $rankBeers =array();

        $str = "id, Name, Description, Tier\n";

        foreach ($ranks as $rank){
            $rankBeers[$rank->getName()] = $rank;
        }

        foreach ($rankBeers as $rank => $beer){
            foreach ($beer->getBeers() as $b){
                $str .= $b->getId() . ", " .  $b->getName() . ", " . str_replace(",","-", $b->getDescription()) . ", " . $rank . "\n";
            }
        }

        $content = utf8_decode($str);

        return new Response($content, 200, array(
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="beer_export.csv"'
        ));
    }

    /**
     * @Route("/{id}", name="beer_show", methods={"GET"})
     */
    public function show(Beer $beer): Response
    {
        return $this->render('beer/show.html.twig', [
            'beer' => $beer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="beer_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Beer $beer): Response
    {
        $form = $this->createForm(BeerType::class, $beer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('beer_index');
        }

        return $this->render('beer/edit.html.twig', [
            'beer' => $beer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="beer_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Beer $beer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$beer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($beer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('beer_index');
    }

    /**
     * @param BeerRepository $beerRepository
     * @return int LE NOMBRE DE BIERASSES DANS LA BASE
     */
    public function getNumberOfBeers(BeerRepository $beerRepository): int{
        try {
            return $beerRepository->nbBeers();
        } catch (NoResultException | NonUniqueResultException $e) {
        }
        return 0;
    }

    /**
     * @Route("/search", name="search_results")
     */
    public function search(Request $request, BeerRepository $beerRepository): Response
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $results = [];
        $query = '';

        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->get('query')->getData();
            $results = $beerRepository->searchByName($query);
        }

        return $this->render('search/results.html.twig', [
            'results' => $results,
            'query' => $query,
            'searchForm' => $form->createView(),
        ]);
    }


}

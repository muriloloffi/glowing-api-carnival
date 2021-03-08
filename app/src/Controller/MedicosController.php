<?php


namespace App\Controller;

use App\Entity\Medico;
use App\Helper\MedicoFactory;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private MedicoFactory $medicoFactory;

    //Symfony já resolve o pedido de EntityManager e passa para o construtor
    public function __construct(
        EntityManagerInterface $entityManager,
        MedicoFactory $medicoFactory
    ) {
        $this->entityManager = $entityManager;
        $this->medicoFactory = $medicoFactory;
    }
    
    /**
     * @Route("/medicos", methods={"POST"})
     */
    public function novo(Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $medico = $this->medicoFactory->criarMedico($corpoRequisicao);
        
        $this->entityManager->persist($medico);
        $this->entityManager->flush();

        return new JsonResponse($medico);
    }

    /**
     * @Route("/medicos", methods={"GET"})
     */
    public function buscarTodos(): Response
    {
        $repositorioDeMedicos = $this
            ->getDoctrine()
            ->getRepository(Medico::class);
        $medicoList = $repositorioDeMedicos->findAll();

        return new JsonResponse($medicoList);
    }

    /**
     * @Route("/medicos/{id}", methods={"GET"})
     */
    public function buscarUm(int $id): Response
    {
        $medico = $this->buscaMedico($id);
        $codigoRetorno = is_null($medico) ? Response::HTTP_NO_CONTENT : 200;

        return new JsonResponse($medico, $codigoRetorno);
    }

    /**
     * @Route("/medicos/{id}", methods={"PUT"})
     */
    public function atualiza(int $id, Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $medicoEnviado = $this->medicoFactory->criarMedico($corpoRequisicao);

        $medicoExistente = $this
            ->entityManager
            ->getReference(Medico::class, $id);

        $medicoExistente->crm = $medicoEnviado->crm;
        $medicoExistente->nome = $medicoEnviado->nome;

        $this->entityManager->flush();

        return new JsonResponse($medicoExistente);
    }

    /**
     * @Route("/medicos/{id}", methods={"DELETE"})
     */
    public function remove(int $id): Response
    {   
        $medico = $this
            ->entityManager
            ->getReference(Medico::class, $id);

        $this->entityManager->remove($medico);
        $this->entityManager->flush();
        
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    private function buscaMedico(int $id): ?Medico
    {
        $repositorioDeMedicos = $this
            ->getDoctrine()
            ->getRepository(Medico::class);
        $medico = $repositorioDeMedicos->find($id);
        return $medico;
    }
}
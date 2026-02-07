<?php

namespace App\Controller\Api;

use App\Entity\ContenidoUsuario;
use App\Repository\ContenidoUsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/contenido')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ContenidoUsuarioController extends AbstractController
{
    #[Route('', name: 'contenido_save', methods: ['POST'])]
public function save(
    Request $request,
    ContenidoUsuarioRepository $repository,
    EntityManagerInterface $em
): JsonResponse {
    $data = json_decode($request->getContent(), true);

    if (!isset($data['tmdbId'], $data['esPelicula'])) {
        return $this->json(['error' => 'Datos obligatorios'], 400);
    }

    $user = $this->getUser();

    $contenido = $repository->findOneBy([
        'user' => $user,
        'tmdbId' => $data['tmdbId'],
    ]);

    // 🔴 CONDICIÓN DE BORRADO
    $sinFavorito = empty($data['favorito']);
    $sinVista = !array_key_exists('vista', $data) || $data['vista'] === null;
    $sinNota = empty($data['notaUsuario']);

    if ($sinFavorito && $sinVista && $sinNota) {

        if ($contenido) {
            $em->remove($contenido);
            $em->flush();
        }

        return $this->json([
            'message' => 'Contenido eliminado (estado vacío)'
        ]);
    }

    // 🟢 Si no existe, se crea
    if (!$contenido) {
        $contenido = new ContenidoUsuario();
        $contenido->setUser($user);
        $contenido->setTmdbId($data['tmdbId']);
        $contenido->setEsPelicula((bool) $data['esPelicula']);
    }

    // FAVORITO
    if (array_key_exists('favorito', $data)) {
        $contenido->setFavorito((bool) $data['favorito']);
    }

    // VISTA (true / false / null)
    if (array_key_exists('vista', $data)) {
        $contenido->setVista($data['vista']);
    }

    // NOTA
    if (array_key_exists('notaUsuario', $data)) {
        $contenido->setNotaUsuario($data['notaUsuario']);
    }

    $em->persist($contenido);
    $em->flush();

    return $this->json([
        'message' => 'Contenido guardado'
    ]);
}



    #[Route('', name: 'contenido_list', methods: ['GET'])]
    public function list(
        ContenidoUsuarioRepository $repository
    ): JsonResponse {
        $user = $this->getUser();

        $contenidos = $repository->findBy(['user' => $user]);

        return $this->json($contenidos);
    }
    #[Route('/favoritos', name: 'contenido_favoritos', methods: ['GET'])]
        public function favoritos(
            ContenidoUsuarioRepository $repository
        ): JsonResponse {
            $user = $this->getUser();

            $contenidos = $repository->findBy([
                'user' => $user,
                'favorito' => true,
            ]);

            return $this->json($contenidos);
        }
        #[Route('/vistos', name: 'contenido_vistos', methods: ['GET'])]
            public function vistos(
                ContenidoUsuarioRepository $repository
            ): JsonResponse {
                $user = $this->getUser();

                return $this->json(
                    $repository->findBy([
                        'user' => $user,
                        'vista' => true,
                    ])
                );
            }

            #[Route('/pendientes', name: 'contenido_pendientes', methods: ['GET'])]
            public function pendientes(
                ContenidoUsuarioRepository $repository
            ): JsonResponse {
                $user = $this->getUser();

                return $this->json(
                    $repository->findBy([
                        'user' => $user,
                        'vista' => false,
                    ])
                );
            }
}
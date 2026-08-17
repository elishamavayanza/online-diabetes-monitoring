<?php

namespace App\Service\Nutrition;

use App\DTO\Feedback;
use App\DTO\Request\Nutrition\FoodCategoryRequestDTO;
use App\Entity\Nutrition\FoodCategory;
use App\Mapper\Nutrition\FoodCategoryMapper;
use App\Repository\Nutrition\FoodCategoryRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FoodCategoryService
{
    public function __construct(
        private readonly FoodCategoryRepository $repository,
        private readonly FoodCategoryMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService
    ) {
    }

    public function all(): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD_CATEGORY
            );

            $categories = $this->repository->findAll();
            $data = array_map(fn(FoodCategory $category) => $this->mapper->mapEntityToResponse($category), $categories);

            return $feedback
                ->setData($data)
                ->setFlushDescription('Liste des catégories d’aliments récupérée avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function getById(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD_CATEGORY
            );

            $category = $this->repository->find($id);

            if (!$category) {
                return $feedback
                    ->setErrorFlushDescription('Catégorie d’aliment introuvable.')
                    ->autoInitFlush();
            }

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($category))
                ->setFlushDescription('Catégorie d’aliment récupérée avec succès.')
                ->autoInitFlush();
        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function create(FoodCategoryRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD_CATEGORY
            );

            $category = $this->mapper->mapRequestToEntity($dto);

            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return $feedback
                ->setData(
                    $this->mapper->mapEntityToResponse($category)
                )
                ->setFlushDescription(
                    'Catégorie d’aliment créée avec succès.'
                )
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Accès refusé : ' . $e->getMessage()
                )
                ->autoInitFlush();

        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription(
                    'Erreur : ' . $e->getMessage()
                )
                ->autoInitFlush();
        }
    }

    public function update(int $id, FoodCategoryRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD_CATEGORY
            );

            $category = $this->repository->find($id);

            if (!$category) {
                return $feedback
                    ->setErrorFlushDescription('Catégorie d’aliment introuvable.')
                    ->autoInitFlush();
            }

            $category = $this->mapper->mapRequestToEntity($dto, $category);

            $this->entityManager->flush();

            return $feedback
                ->setData($this->mapper->mapEntityToResponse($category))
                ->setFlushDescription('Catégorie d’aliment mise à jour avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }

    public function delete(int $id): Feedback
    {
        $feedback = new Feedback();

        try {
            $this->securityService->checkProfessionalAccess(
                SecurityAction::MANAGE_FOOD_CATEGORY
            );

            $category = $this->repository->find($id);

            if (!$category) {
                return $feedback
                    ->setErrorFlushDescription('Catégorie d’aliment introuvable.')
                    ->autoInitFlush();
            }

            $this->entityManager->remove($category);
            $this->entityManager->flush();

            return $feedback
                ->setFlushDescription('Catégorie d’aliment supprimée avec succès.')
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            return $feedback
                ->setErrorFlushDescription('Accès refusé : ' . $e->getMessage())
                ->autoInitFlush();
        } catch (\Throwable $e) {
            return $feedback
                ->setErrorFlushDescription('Erreur : ' . $e->getMessage())
                ->autoInitFlush();
        }
    }
}

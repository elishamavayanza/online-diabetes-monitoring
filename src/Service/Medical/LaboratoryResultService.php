<?php

namespace App\Service\Medical;

use App\DTO\Feedback;
use App\DTO\Request\Medical\LaboratoryResultRequestDTO;
use App\Mapper\Medical\LaboratoryResultMapper;
use App\Repository\Medical\LaboratoryResultRepository;
use App\Repository\Identity\PatientRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use App\Service\File\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class LaboratoryResultService
{
    public function __construct(
        private readonly LaboratoryResultRepository $repository,
        private readonly PatientRepository $patientRepository,
        private readonly LaboratoryResultMapper $mapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService,
        private readonly FileUploaderService $fileUploaderService
    ) {}

    public function create(string $patientId, LaboratoryResultRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::UPLOAD_LABORATORY_RESULT);

            $user = $this->securityService->getCurrentUser();
            $result = $this->mapper->mapRequestToEntity($dto, $patient);
            $result->setIssuer($user);

            // Gestion de l'upload du fichier physique
            if ($dto->file) {
                $fileName = $this->fileUploaderService->upload($dto->file, 'laboratory_results');
                $result->setFileUrl($fileName);
            }

            $this->entityManager->persist($result);
            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($result))
                ->setFlushDescription("Résultat de laboratoire enregistré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function getByPatient(string $patientId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            // Utilisation de l'action de lecture appropriée
            $this->securityService->checkPatientAccess($patient, SecurityAction::VIEW_LABORATORY_RESULT);

            $results = $this->repository->findBy(['patient' => $patient]);
            $responseDTOs = array_map(fn($result) => $this->mapper->mapEntityToResponse($result), $results);

            $feedback->setData($responseDTOs)
                ->setFlushDescription("Résultats de laboratoire récupérés avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function update(string $patientId, string $resultId, LaboratoryResultRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::UPLOAD_LABORATORY_RESULT);

            $result = $this->repository->find($resultId);
            if (!$result || $result->getPatient()->getId() !== $patient->getId()) {
                return $feedback->setErrorFlushDescription("Résultat de laboratoire introuvable pour ce patient.")->autoInitFlush();
            }

            $this->mapper->mapRequestToEntity($dto, $patient, $result);

            // Gestion de la mise à jour du fichier physique si un nouveau fichier est fourni
            if ($dto->file) {
                if ($result->getFileUrl()) {
                    $this->fileUploaderService->remove($result->getFileUrl(), 'laboratory_results');
                }

                $fileName = $this->fileUploaderService->upload($dto->file, 'laboratory_results');
                $result->setFileUrl($fileName);
            }

            $this->entityManager->flush();

            $feedback->setData($this->mapper->mapEntityToResponse($result))
                ->setFlushDescription("Résultat de laboratoire mis à jour avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }

    public function delete(string $patientId, string $resultId): Feedback
    {
        $feedback = new Feedback();

        try {
            $patient = $this->patientRepository->find($patientId);
            if (!$patient) {
                return $feedback->setErrorFlushDescription("Patient introuvable.")->autoInitFlush();
            }

            $this->securityService->checkPatientAccess($patient, SecurityAction::UPLOAD_LABORATORY_RESULT);

            $result = $this->repository->find($resultId);
            if (!$result || $result->getPatient()->getId() !== $patient->getId()) {
                return $feedback->setErrorFlushDescription("Résultat de laboratoire introuvable pour ce patient.")->autoInitFlush();
            }

            // Suppression du fichier physique du serveur avant de supprimer l'entité
            if ($result->getFileUrl()) {
                $this->fileUploaderService->remove($result->getFileUrl(), 'laboratory_results');
            }

            $this->entityManager->remove($result);
            $this->entityManager->flush();

            $feedback->setFlushDescription("Résultat de laboratoire supprimé avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())->autoInitFlush();
        }

        return $feedback;
    }
}

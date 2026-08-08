<?php

namespace App\Service\Common;

use App\DTO\Feedback;
use App\DTO\Request\Common\FileAttachmentRequestDTO;
use App\Mapper\Common\FileAttachmentMapper;
use App\Repository\Common\FileAttachmentRepository;
use App\Security\SecurityAction;
use App\Security\SecurityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FileAttachmentService
{
    public function __construct(
        private readonly FileAttachmentRepository $fileAttachmentRepository,
        private readonly FileAttachmentMapper $fileAttachmentMapper,
        private readonly EntityManagerInterface $entityManager,
        private readonly SecurityServiceInterface $securityService // 1. Injection du service de sécurité
    ) {}

    public function create(FileAttachmentRequestDTO $dto): Feedback
    {
        $feedback = new Feedback();

        try {
            // 2. Vérification de la sécurité (exemple : action d'upload ou de création)
            // Vous pouvez ajuster l'action selon votre contexte métier exact (ex: UPLOAD_LABORATORY_RESULT)
            $this->securityService->checkPermission(SecurityAction::UPLOAD_LABORATORY_RESULT->value);

            // Si le fichier est lié à un patient ou une entité spécifique,
            // vous pouvez aussi appeler checkPatientAccess() ou checkOrganizationAccess() ici.

            $fileAttachment = $this->fileAttachmentMapper->mapRequestToEntity($dto);

            $this->entityManager->persist($fileAttachment);
            $this->entityManager->flush();

            $responseDTO = $this->fileAttachmentMapper->mapEntityToResponse($fileAttachment);

            $feedback->setFlushDescription("Fichier attaché avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            // Propagation ou gestion propre de l'accès refusé
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur lors de l'enregistrement du fichier : " . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }

    public function getById(string $id): ?Feedback
    {
        $feedback = new Feedback();

        try {
            // 3. Vérification de l'action de téléchargement/lecture
            $this->securityService->checkPermission(SecurityAction::DOWNLOAD_ATTACHMENT->value);

            $fileAttachment = $this->fileAttachmentRepository->find($id);

            if (!$fileAttachment) {
                $feedback->setErrorFlushDescription("Fichier introuvable.")
                    ->autoInitFlush();
                return $feedback;
            }

            // TODO : Si le fichier appartient à un patient, vérifier l'accès au patient avec :
            // $this->securityService->checkPatientAccess($fileAttachment->getPatient(), SecurityAction::DOWNLOAD_ATTACHMENT);

            $feedback->setFlushDescription("Fichier récupéré avec succès.")
                ->autoInitFlush();

        } catch (AccessDeniedException $e) {
            $feedback->setErrorFlushDescription("Accès refusé : " . $e->getMessage())
                ->autoInitFlush();
        } catch (\Exception $e) {
            $feedback->setErrorFlushDescription("Erreur : " . $e->getMessage())
                ->autoInitFlush();
        }

        return $feedback;
    }
}

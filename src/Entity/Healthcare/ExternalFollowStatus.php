<?php

namespace App\Entity\Healthcare;

/**
 * Statut d'une invitation à suivre un patient émis à un professionnel
 * d'une autre organisation.
 */
enum ExternalFollowStatus: string
{
    /** L'invité n'a pas encore répondu à l'invitation. */
    case PENDING = 'PENDING';

    /** L'invité a accepté l'invitation et accède au dossier. */
    case ACCEPTED = 'ACCEPTED';

    /** L'invité a refusé l'invitation. */
    case DECLINED = 'DECLINED';

    /** L'administrateur a coupé l'accès avant son terme. */
    case REVOKED = 'REVOKED';

    /** Le délai de l'invitation est atteint. */
    case EXPIRED = 'EXPIRED';

    public function isActive(): bool
    {
        return $this === self::ACCEPTED;
    }
}
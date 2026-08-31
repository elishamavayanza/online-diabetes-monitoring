import { tokenStorage } from '@/services/storage/storage.service';
import { decodeJwtPayload } from '@/services/security/security.utils';

export function getOrganizationIdFromToken(): string | null {
    const token = tokenStorage.getAccessToken();
    if (!token) return null;
    try {
        const payload = decodeJwtPayload(token);
        const orgs = payload?.organizations;
        if (Array.isArray(orgs) && orgs.length > 0 && orgs[0]?.organization_id) {
            return String(orgs[0].organization_id);
        }
    } catch {
        return null;
    }
    return null;
}

export function getCurrentUserIdFromToken(): string | null {
    const token = tokenStorage.getAccessToken();
    if (!token) return null;
    try {
        const payload = decodeJwtPayload(token);
        if (payload?.sub) return String(payload.sub);
        if (payload?.user_id) return String(payload.user_id);
        if (payload?.id) return String(payload.id);
    } catch {
        return null;
    }
    return null;
}

import { getCurrentUserIdFromToken } from '@/react/utils/authUtils';

export interface RecordOwnershipInfo {
    createdById?: string;
    createdByName?: string;
    authorId?: string;
    authorName?: string;
    issuerId?: string;
    issuerName?: string;
    modifiedById?: string;
    modifiedByName?: string;
    professionalId?: string;
    professionalName?: string;
}

export function getRecordCreatorId(record: RecordOwnershipInfo): string | undefined {
    return (
        record.createdById ??
        record.authorId ??
        record.issuerId ??
        record.modifiedById ??
        record.professionalId
    );
}

export function getRecordCreatorName(record: RecordOwnershipInfo): string | undefined {
    return (
        record.createdByName ??
        record.authorName ??
        record.issuerName ??
        record.modifiedByName ??
        record.professionalName
    );
}

export function isRecordCreator(record: RecordOwnershipInfo): boolean {
    const currentUserId = getCurrentUserIdFromToken();
    if (!currentUserId) {
        return false;
    }
    const creatorId = getRecordCreatorId(record);
    return creatorId !== undefined && creatorId === currentUserId;
}
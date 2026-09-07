import { getRecordCreatorName, RecordOwnershipInfo } from '../utils/ownershipUtils';

interface RecordAuthorProps {
    record: RecordOwnershipInfo;
    label?: string;
    className?: string;
}

export function RecordAuthor({
    record,
    label = 'Créé par',
    className = 'record-author',
}: RecordAuthorProps) {
    const name = getRecordCreatorName(record);
    if (!name) {
        return null;
    }
    return (
        <span className={className}>
            {label} {name}
        </span>
    );
}